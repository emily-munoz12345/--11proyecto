<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos (solo Admin y Vendedor)
if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Verificar que se haya proporcionado un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje'] = 'ID de material no válido';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

$id_material = $_GET['id'];

// Obtener información del material
$stmt = $conex->prepare("SELECT * FROM materiales WHERE id_material = ?");
$stmt->execute([$id_material]);
$material = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si el material existe
if (!$material) {
    $_SESSION['mensaje'] = 'Material no encontrado';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Verificar si existe la tabla registro_eliminaciones antes de usarla
$tablaExiste = false;
try {
    $result = $conex->query("SELECT 1 FROM registro_eliminaciones LIMIT 1");
    $tablaExiste = true;
} catch (Exception $e) {
    $tablaExiste = false;
}

// Obtener historial de ediciones si la tabla existe
$historialEdiciones = [];
if ($tablaExiste) {
    $stmt = $conex->prepare("
        SELECT re.*, u.nombre_completo as editor 
        FROM registro_eliminaciones re 
        LEFT JOIN usuarios u ON re.eliminado_por = u.id_usuario 
        WHERE re.tabla = 'materiales' AND re.id_registro = ? AND re.accion = 'MODIFICACION'
        ORDER BY re.fecha_eliminacion DESC
    ");
    $stmt->execute([$id_material]);
    $historialEdiciones = $stmt->fetchAll();
}

// Inicializar variables de sesión para mensajes
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
    $_SESSION['tipo_mensaje'] = '';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Material</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.8);
            --primary-hover: rgba(140, 74, 63, 1);
            --secondary-color: rgba(108, 117, 125, 0.8);
            --text-color: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --bg-transparent: rgba(0, 0, 0, 0.5);
            --bg-transparent-light: rgba(0, 0, 0, 0.4);
            --bg-input: rgba(0, 0, 0, 0.6);
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
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
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
            color: var(--text-color);
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
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
            color: var(--text-color);
        }

        .info-row {
            display: flex;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .info-label {
            font-weight: 600;
            color: var(--text-color);
            width: 40%;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        .info-value {
            color: var(--text-color);
            width: 60%;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
            font-weight: 500;
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

        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-secondary:hover {
            background-color: rgba(108, 117, 125, 1);
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

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: rgba(25, 135, 84, 1);
        }

        .btn-warning {
            background-color: var(--warning-color);
            color: black;
        }

        .btn-warning:hover {
            background-color: rgba(255, 193, 7, 1);
        }

        .btn-info {
            background-color: var(--info-color);
            color: white;
        }

        .btn-info:hover {
            background-color: rgba(13, 202, 240, 1);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: rgba(220, 53, 69, 1);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(5px);
            border-left: 4px solid var(--info-color);
            background-color: rgba(13, 202, 240, 0.2);
            color: var(--text-color);
        }

        .alert strong {
            color: var(--text-color);
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

            .info-row {
                flex-direction: column;
            }

            .info-label, .info-value {
                width: 100%;
            }

            .info-label {
                margin-bottom: 0.5rem;
            }

            .btn-group {
                width: 100%;
                justify-content: center;
                margin-top: 1rem;
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
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-box"></i> Detalles del Material
            </h1>
            <div class="d-flex gap-2 flex-wrap button-group">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
                <a href="editar.php?id=<?= $material['id_material'] ?>" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if (!empty($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
                <div>
                    <i class="fas fa-<?=
                                        $_SESSION['tipo_mensaje'] === 'success' ? 'check-circle' : ($_SESSION['tipo_mensaje'] === 'danger' ? 'times-circle' : ($_SESSION['tipo_mensaje'] === 'warning' ? 'exclamation-triangle' : 'info-circle'))
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

        <!-- Información del material -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Material</h5>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-label">ID del Material:</div>
                    <div class="info-value">#<?= $material['id_material'] ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Nombre:</div>
                    <div class="info-value"><?= htmlspecialchars($material['nombre_material']) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Descripción:</div>
                    <div class="info-value"><?= htmlspecialchars($material['descripcion_material']) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Precio por Metro:</div>
                    <div class="info-value">$<?= number_format($material['precio_metro'], 2) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Stock Disponible:</div>
                    <div class="info-value"><?= $material['stock_material'] ?> metros</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Categoría:</div>
                    <div class="info-value"><?= htmlspecialchars($material['categoria_material']) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Proveedor:</div>
                    <div class="info-value"><?= htmlspecialchars($material['proveedor_material']) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Fecha de Registro:</div>
                    <div class="info-value"><?= date('d/m/Y H:i', strtotime($material['fecha_registro'])) ?></div>
                </div>
                <?php if (!empty($material['fecha_actualizacion']) && $material['fecha_actualizacion'] != $material['fecha_registro']): ?>
                <div class="info-row">
                    <div class="info-label">Última Actualización:</div>
                    <div class="info-value"><?= date('d/m/Y H:i', strtotime($material['fecha_actualizacion'])) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($material['fecha_eliminacion'])): ?>
                <div class="info-row">
                    <div class="info-label">Fecha de Eliminación:</div>
                    <div class="info-value"><?= date('d/m/Y H:i', strtotime($material['fecha_eliminacion'])) ?></div>
                </div>
                <?php endif; ?>
                <div class="info-row">
                    <div class="info-label">Estado:</div>
                    <div class="info-value">
                        <span class="badge bg-<?= $material['activo'] == 1 ? 'success' : 'danger' ?>">
                            <?= $material['activo'] == 1 ? 'Activo' : 'Eliminado' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de Ediciones -->
        <?php if (!empty($historialEdiciones)): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historial de Ediciones</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Editor</th>
                                <th>Cambios</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historialEdiciones as $edicion): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($edicion['fecha_eliminacion'])) ?></td>
                                <td><?= htmlspecialchars($edicion['editor'] ?? 'Sistema') ?></td>
                                <td>
                                    <?php if (!empty($edicion['datos_anteriores']) && !empty($edicion['datos_nuevos'])): ?>
                                        <small>Se realizaron modificaciones en los datos del material</small>
                                    <?php else: ?>
                                        <small>Modificación registrada</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php include '../../includes/bot.php'; ?>
    <script>setHelpModule('Materiales');</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>