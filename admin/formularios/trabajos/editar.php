<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin() && !isTechnician()) {
    header('Location: ../dashboard.php');
    exit;
}

// Inicializar variables de sesión para mensajes
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
    $_SESSION['tipo_mensaje'] = '';
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

try {
    // Obtener datos del trabajo
    $sql = "SELECT t.*, c.id_cotizacion, cl.nombre_cliente, 
            v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo
            FROM trabajos t
            JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
            JOIN clientes cl ON c.id_cliente = cl.id_cliente
            JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
            WHERE t.id_trabajos = ?";
    $stmt = $conex->prepare($sql);
    $stmt->execute([$id]);
    $trabajo = $stmt->fetch();

    if (!$trabajo) {
        header('Location: index.php?error=Trabajo no encontrado');
        exit;
    }

    // Obtener fotos (si existen)
    $sqlFotos = "SELECT id_foto, ruta_foto FROM fotos_trabajos WHERE id_trabajos = ?";
    $stmtFotos = $conex->prepare($sqlFotos);
    $stmtFotos->execute([$id]);
    $fotos = $stmtFotos->fetchAll();

} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al obtener datos: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../../includes/head.php';
$title = 'Editar Trabajo | Nacional Tapizados';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            max-width: 900px;
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

        /* Estilos para formulario */
        .form-container {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
        }

        .form-label {
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            padding: 0.75rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(140, 74, 63, 0.25);
            color: var(--text-color);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        /* Estilos para botones */
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
            font-size: 1rem;
            gap: 0.5rem;
        }

        .btn i {
            font-size: 1rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-secondary:hover {
            background-color: rgba(108, 117, 125, 1);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: rgba(220, 53, 69, 1);
        }

        /* Estilos para alertas */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .alert-success {
            background-color: rgba(25, 135, 84, 0.2);
            border-left: 4px solid var(--success-color);
            color: white;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            border-left: 4px solid var(--danger-color);
            color: white;
        }

        /* Vista previa de imágenes */
        .img-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .img-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            position: relative;
        }

        .delete-img {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
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
        }
    </style>
</head>

<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-edit"></i> Editar Trabajo #<?= $trabajo['id_trabajos'] ?>
            </h1>
            <div class="d-flex gap-2">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if (!empty($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
                <div>
                    <i class="fas fa-<?=
                                        $_SESSION['tipo_mensaje'] === 'success' ? 'check-circle' : 'exclamation-triangle'
                                        ?> me-2"></i>
                    <?= $_SESSION['mensaje'] ?>
                </div>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
            </div>
            <?php
            $_SESSION['mensaje'] = '';
            $_SESSION['tipo_mensaje'] = '';
            ?>
        <?php endif; ?>

        <!-- Información del trabajo -->
        <div class="card mb-4" style="background-color: var(--bg-transparent-light); border-color: var(--border-color);">
            <div class="card-body">
                <h5 class="card-title" style="color: var(--text-color);">Información del trabajo</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Cliente:</strong> <?= htmlspecialchars($trabajo['nombre_cliente']) ?></p>
                        <p><strong>Vehículo:</strong> <?= htmlspecialchars($trabajo['marca_vehiculo']) ?> <?= htmlspecialchars($trabajo['modelo_vehiculo']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Placa:</strong> <?= htmlspecialchars($trabajo['placa_vehiculo']) ?></p>
                        <p><strong>Cotización:</strong> #<?= $trabajo['id_cotizacion'] ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="form-container">
            <form action="procesar.php" method="POST" enctype="multipart/form-data" id="formTrabajo">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id_trabajos" value="<?= $trabajo['id_trabajos'] ?>">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="fecha_inicio" class="form-label">Fecha de inicio *</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                               value="<?= htmlspecialchars($trabajo['fecha_inicio']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="fecha_fin" class="form-label">Fecha de finalización</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                               value="<?= htmlspecialchars($trabajo['fecha_fin'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="estado" class="form-label">Estado *</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="Pendiente" <?= $trabajo['estado'] == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="En progreso" <?= $trabajo['estado'] == 'En progreso' ? 'selected' : '' ?>>En progreso</option>
                            <option value="Completado" <?= $trabajo['estado'] == 'Completado' ? 'selected' : '' ?>>Completado</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="fotos" class="form-label">Agregar más fotos (opcional, máximo 5)</label>
                        <input type="file" class="form-control" id="fotos" name="fotos[]" multiple accept="image/*">
                        <small class="text-muted" style="color: var(--text-muted) !important;">Formatos permitidos: JPG, PNG, GIF. Máx. 5MB cada una</small>
                        <div id="previewFotos" class="img-preview-container mt-2"></div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="notas" class="form-label">Notas</label>
                    <textarea class="form-control" id="notas" name="notas" rows="3"><?= htmlspecialchars($trabajo['notas'] ?? '') ?></textarea>
                </div>
                
                <!-- Fotos existentes -->
                <?php if (!empty($fotos)): ?>
                <div class="mb-4">
                    <label class="form-label">Fotos existentes</label>
                    <div class="img-preview-container">
                        <?php foreach ($fotos as $foto): ?>
                        <div class="position-relative">
                            <img src="<?= htmlspecialchars($foto['ruta_foto']) ?>" class="img-preview" alt="Foto del trabajo">
                            <span class="delete-img" onclick="eliminarFoto(<?= $foto['id_foto'] ?>, <?= $trabajo['id_trabajos'] ?>)">
                                <i class="fas fa-times"></i>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Restablecer
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Vista previa de fotos seleccionadas
    document.getElementById('fotos').addEventListener('change', function(e) {
        const preview = document.getElementById('previewFotos');
        preview.innerHTML = '';
        const files = e.target.files;
        
        if (files.length > 5) {
            alert('Solo puedes subir un máximo de 5 fotos');
            this.value = '';
            return;
        }
        
        for (let i = 0; i < Math.min(files.length, 5); i++) {
            const file = files[i];
            if (!file.type.match('image.*')) continue;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-preview';
                preview.appendChild(img);
            }
            reader.readAsDataURL(file);
        }
    });

    // Validación antes de enviar
    document.getElementById('formTrabajo').addEventListener('submit', function(e) {
        const fotosInput = document.getElementById('fotos');
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (fotosInput.files) {
            // Validar cantidad de fotos
            if (fotosInput.files.length > 5) {
                alert('Solo puedes subir un máximo de 5 fotos');
                e.preventDefault();
                return;
            }
            
            // Validar tamaño de cada foto
            for (let i = 0; i < fotosInput.files.length; i++) {
                if (fotosInput.files[i].size > maxSize) {
                    alert('El archivo ' + fotosInput.files[i].name + ' excede el tamaño máximo de 5MB');
                    e.preventDefault();
                    return;
                }
            }
        }
    });

    // Función para eliminar foto
    function eliminarFoto(idFoto, idTrabajo) {
        if (confirm('¿Estás seguro de que deseas eliminar esta foto?')) {
            window.location.href = 'procesar.php?accion=eliminar_foto&id_foto=' + idFoto + '&id_trabajos=' + idTrabajo;
        }
    }
    </script>
</body>
</html>