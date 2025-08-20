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

require_once __DIR__ . '/../../includes/head.php';
$title = 'Editar Cliente | Nacional Tapizados';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../../includes/head.php'; ?>
    <title>Editar Cliente | Nacional Tapizados</title>
    <style>
        body {
            background-image: url('https://pfst.cf2.poecdn.net/base/image/fe72e5f0bf336b4faca086bc6a42c20a45e904d165e796b52eca655a143283b8?w=1024&h=768&pmaid=426747789');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Roboto', sans-serif;
            color: #333;
        }

        .main-content {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin: 1rem;
            padding: 2rem;
            min-height: calc(100vh - 2rem);
        }

        .card {
            background-color: white;
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            padding: 1.25rem 1.5rem;
            border-radius: 12px 12px 0 0 !important;
        }

        .btn-primary {
            background-color: #8c4a3f;
            border-color: #8c4a3f;
        }

        .btn-primary:hover {
            background-color: #7a4036;
            border-color: #7a4036;
        }

        h1, h2, h3, h4, h5, h6 {
            color: #8c4a3f;
        }

        .user-role-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background-color: #e9ecef;
            border-radius: 20px;
            color: #495057;
            font-weight: 500;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

            <!-- Contenido principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="main-content">
                    <!-- Barra superior -->
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">
                            <i class="fas fa-user-edit me-2"></i>Editar Cliente
                        </h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <div class="btn-group me-2">
                                <a href="index.php" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Volver
                                </a>
                            </div>
                            <div class="text-muted small">
                                Rol actual: <span class="user-role-badge"><?= isAdmin() ? 'Administrador' : 'Vendedor' ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Información del Cliente</h5>
                        </div>
                        <div class="card-body">
                            <form action="procesar.php" method="POST">
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
                                
                                <div class="mb-4">
                                    <label for="notas" class="form-label">Notas Adicionales</label>
                                    <textarea class="form-control" id="notas" name="notas" rows="3"><?= 
                                        htmlspecialchars($cliente['notas_cliente']) 
                                    ?></textarea>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="index.php" class="btn btn-outline-secondary me-md-2">
                                        <i class="fas fa-times me-1"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Actualizar Cliente
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>