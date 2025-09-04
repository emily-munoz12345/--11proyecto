<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos
if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Obtener ID del cliente
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Obtener datos del cliente
try {
    $stmt = $conex->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch();
    
    if (!$cliente) {
        header('Location: index.php?error=Cliente no encontrado');
        exit;
    }
} catch (PDOException $e) {
    header('Location: index.php?error=Error al obtener cliente');
    exit;
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $notas = trim($_POST['notas']);
    
    // Validar campos obligatorios
    if (empty($nombre) || empty($correo) || empty($telefono) || empty($direccion)) {
        $error = "Todos los campos marcados con * son obligatorios";
    } else {
        try {
            // Iniciar transacción
            $conex->beginTransaction();
            
            // Obtener datos actuales para comparar
            $stmt = $conex->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
            $stmt->execute([$id]);
            $cliente_actual = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Actualizar el cliente
            $stmt = $conex->prepare("UPDATE clientes SET nombre_cliente = ?, correo_cliente = ?, telefono_cliente = ?, direccion_cliente = ?, notas_cliente = ?, fecha_actualizacion = NOW() WHERE id_cliente = ?");
            $stmt->execute([$nombre, $correo, $telefono, $direccion, $notas, $id]);
            
            // Registrar cambios en registro_eliminaciones
            $cambios = [];
            
            if ($cliente_actual['nombre_cliente'] !== $nombre) {
                $cambios[] = "nombre: " . $cliente_actual['nombre_cliente'] . " → " . $nombre;
            }
            if ($cliente_actual['correo_cliente'] !== $correo) {
                $cambios[] = "correo: " . $cliente_actual['correo_cliente'] . " → " . $correo;
            }
            if ($cliente_actual['telefono_cliente'] !== $telefono) {
                $cambios[] = "teléfono: " . $cliente_actual['telefono_cliente'] . " → " . $telefono;
            }
            if ($cliente_actual['direccion_cliente'] !== $direccion) {
                $cambios[] = "dirección: " . $cliente_actual['direccion_cliente'] . " → " . $direccion;
            }
            if ($cliente_actual['notas_cliente'] !== $notas) {
                $cambios[] = "notas: " . $cliente_actual['notas_cliente'] . " → " . $notas;
            }
            
            if (!empty($cambios)) {
                $id_usuario = $_SESSION['user_id'] ?? null;
                $datos_cambios = implode("; ", $cambios);
                
                $stmt = $conex->prepare("INSERT INTO registro_eliminaciones (tabla, id_registro, eliminado_por, accion, datos, datos_anteriores, datos_nuevos) 
                                        VALUES ('clientes', ?, ?, 'MODIFICACION', ?, ?, ?)");
                $stmt->execute([
                    $id, 
                    $id_usuario, 
                    "Cliente modificado: " . $nombre,
                    json_encode($cliente_actual, JSON_UNESCAPED_UNICODE),
                    json_encode([
                        'nombre_cliente' => $nombre,
                        'correo_cliente' => $correo,
                        'telefono_cliente' => $telefono,
                        'direccion_cliente' => $direccion,
                        'notas_cliente' => $notas
                    ], JSON_UNESCAPED_UNICODE)
                ]);
            }
            
            $conex->commit();
            
            $_SESSION['mensaje'] = "Cliente actualizado correctamente";
            $_SESSION['tipo_mensaje'] = "success";
            header('Location: index.php');
            exit;
            
        } catch (PDOException $e) {
            $conex->rollBack();
            $error = "Error al actualizar el cliente: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../../includes/head.php';
$title = 'Editar Cliente | Nacional Tapizados';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../../includes/head.php'; ?>
    <title>Editar Cliente | Nacional Tapizados</title>
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.8);
            --primary-hover: rgba(140, 74, 63, 1);
            --secondary-color: rgba(108, 117, 125, 0.8);
            --text-color: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --bg-transparent: rgba(255, 255, 255, 0.1);
            --bg-transparent-light: rgba(255, 255, 255, 0.15);
            --border-color: rgba(255, 255, 255, 0.2);
            --success-color: rgba(25, 135, 84, 0.8);
            --danger-color: rgba(220, 53, 69, 0.8);
            --warning-color: rgba(255, 193, 7, 0.8);
            --info-color: rgba(13, 202, 240, 0.8);
        }

        body {
            background-image: url('https://pfst.cf2.poecdn.net/base/image/60ab54eef562f30f85a67bde31f924f078199dae0b7bc6c333dfb467a2c13471?w=1024&h=768&pmaid=442168253');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            min-height: 100vh;
        }

        .main-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--bg-transparent);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .page-title i {
            margin-right: 12px;
            color: var(--primary-color);
        }

        .card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
            border-radius: 12px;
        }

        .card-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            backdrop-filter: blur(5px);
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(140, 74, 63, 0.25);
            color: var(--text-color);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-outline-secondary {
            background-color: transparent;
            border: 1px solid var(--secondary-color);
            color: var(--text-color);
        }

        .btn-outline-secondary:hover {
            background-color: var(--secondary-color);
            color: white;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(5px);
            border-left: 4px solid var(--danger-color);
            background-color: rgba(220, 53, 69, 0.2);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                margin: 1rem;
                padding: 1.5rem;
            }

            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-title {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 1.5rem;
            }

            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .d-md-flex {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <div class="header-section">
            <h1 class="page-title"><i class="fas fa-user-edit"></i>Editar Cliente</h1>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="card shadow">
            <div class="card-body">
                <form action="editar.php?id=<?= $id ?>" method="POST">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="id" value="<?= $cliente['id_cliente'] ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?= htmlspecialchars($cliente['nombre_cliente']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="correo" class="form-label">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="correo" name="correo" 
                                   value="<?= htmlspecialchars($cliente['correo_cliente']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono *</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                   value="<?= htmlspecialchars($cliente['telefono_cliente']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="direccion" class="form-label">Dirección *</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" 
                                   value="<?= htmlspecialchars($cliente['direccion_cliente']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notas" class="form-label">Notas Adicionales</label>
                        <textarea class="form-control" id="notas" name="notas" rows="4"><?= 
                            htmlspecialchars($cliente['notas_cliente']) 
                        ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Actualizar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>