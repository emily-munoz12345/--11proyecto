<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos (solo Admin y Técnico)
if (!isAdmin() && !isTechnician()) {
    header('Location: ../dashboard.php');
    exit;
}

// Verificar que se haya proporcionado un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje'] = 'ID de trabajo no válido';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

$id_trabajo = $_GET['id'];

try {
    // Obtener información del trabajo
    $sql = "SELECT t.*, c.id_cotizacion, cl.nombre_cliente, cl.telefono_cliente, 
            v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
            u.nombre_completo as nombre_tecnico
            FROM trabajos t
            JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
            JOIN clientes cl ON c.id_cliente = cl.id_cliente
            JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
            JOIN usuarios u ON c.id_usuario = u.id_usuario
            WHERE t.id_trabajos = ?";
    
    $stmt = $conex->prepare($sql);
    $stmt->execute([$id_trabajo]);
    $trabajo = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el trabajo existe
    if (!$trabajo) {
        $_SESSION['mensaje'] = 'Trabajo no encontrado';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: index.php');
        exit;
    }

    // Obtener servicios de la cotización asociada
    $sqlServicios = "SELECT s.nombre_servicio, cs.precio 
                    FROM cotizacion_servicios cs
                    JOIN servicios s ON cs.id_servicio = s.id_servicio
                    WHERE cs.id_cotizacion = ?";
    $stmtServicios = $conex->prepare($sqlServicios);
    $stmtServicios->execute([$trabajo['id_cotizacion']]);
    $servicios = $stmtServicios->fetchAll();

    // Calcular total
    $total = 0;
    foreach ($servicios as $servicio) {
        $total += $servicio['precio'];
    }

    // Obtener fotos del trabajo
    $fotos = [];
    if (!empty($trabajo['fotos'])) {
        // Si las fotos están en formato JSON o array serializado
        if (is_string($trabajo['fotos']) && strpos($trabajo['fotos'], '[') !== false) {
            $fotos = json_decode($trabajo['fotos'], true);
        } else {
            // Si están separadas por comas
            $fotos = explode(',', $trabajo['fotos']);
        }
        $fotos = array_filter($fotos); // Eliminar elementos vacíos
    }

} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al obtener datos del trabajo: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
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
    <title>Detalles del Trabajo | Nacional Tapizados</title>
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
            max-width: 1200px;
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

        .btn-warning {
            background-color: var(--warning-color);
            color: black;
        }

        .btn-warning:hover {
            background-color: rgba(255, 193, 7, 1);
        }

        .table {
            color: var(--text-color);
            border-color: var(--border-color);
        }

        .table th {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--border-color);
        }

        .table td {
            background-color: var(--bg-transparent-light);
            border-color: var(--border-color);
        }

        .img-thumbnail {
            background-color: var(--bg-transparent);
            border: 1px solid var(--border-color);
            border-radius: 8px;
        }

        .total-row {
            background-color: var(--primary-color) !important;
            font-weight: bold;
            font-size: 1.1em;
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

        .photo-card {
            transition: transform 0.3s ease;
        }

        .photo-card:hover {
            transform: translateY(-5px);
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
                padding: 1rem;
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
                <i class="fas fa-tools"></i> Trabajo #<?= $trabajo['id_trabajos'] ?>
            </h1>
            <div class="d-flex gap-2 flex-wrap button-group">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
                <a href="editar.php?id=<?= $trabajo['id_trabajos'] ?>" class="btn btn-warning">
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

        <!-- Información General -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-label">ID del Trabajo:</div>
                            <div class="info-value">#<?= $trabajo['id_trabajos'] ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Cliente:</div>
                            <div class="info-value"><?= htmlspecialchars($trabajo['nombre_cliente']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Teléfono:</div>
                            <div class="info-value"><?= htmlspecialchars($trabajo['telefono_cliente']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Vehículo:</div>
                            <div class="info-value"><?= htmlspecialchars($trabajo['marca_vehiculo']) ?> <?= htmlspecialchars($trabajo['modelo_vehiculo']) ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-label">Placa:</div>
                            <div class="info-value"><?= htmlspecialchars($trabajo['placa_vehiculo']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Cotización:</div>
                            <div class="info-value">#<?= $trabajo['id_cotizacion'] ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Técnico:</div>
                            <div class="info-value"><?= htmlspecialchars($trabajo['nombre_tecnico']) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Fecha inicio:</div>
                            <div class="info-value"><?= date('d/m/Y', strtotime($trabajo['fecha_inicio'])) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Fecha fin:</div>
                            <div class="info-value"><?= $trabajo['fecha_fin'] ? date('d/m/Y', strtotime($trabajo['fecha_fin'])) : '--' ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Estado:</div>
                            <div class="info-value">
                                <span class="badge bg-<?= 
                                    $trabajo['estado'] == 'Entregado' ? 'success' : 
                                    ($trabajo['estado'] == 'Cancelado' ? 'danger' : 
                                    ($trabajo['estado'] == 'En progreso' ? 'primary' : 'warning')) 
                                ?>">
                                    <?= $trabajo['estado'] ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servicios -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Servicios</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th class="text-end">Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicios as $servicio): ?>
                            <tr>
                                <td><?= htmlspecialchars($servicio['nombre_servicio']) ?></td>
                                <td class="text-end">$ <?= number_format($servicio['precio'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (count($servicios) > 0): ?>
                            <tr class="total-row">
                                <td><strong>TOTAL</strong></td>
                                <td class="text-end"><strong>$ <?= number_format($total, 0, ',', '.') ?></strong></td>
                            </tr>
                            <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center">No hay servicios registrados</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if (!empty($trabajo['notas'])): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notas</h5>
            </div>
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($trabajo['notas'])) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($fotos)): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Fotos del Trabajo (<?= count($fotos) ?>)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($fotos as $index => $foto): ?>
                        <?php if (!empty($foto)): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 photo-card">
                                <img src="<?= htmlspecialchars(trim($foto)) ?>" 
                                     class="card-img-top img-thumbnail" 
                                     alt="Foto del trabajo <?= $index + 1 ?>"
                                     style="height: 200px; object-fit: cover;"
                                     onerror="this.src='https://via.placeholder.com/300x200/333333/ffffff?text=Imagen+no+disponible'">
                                <div class="card-body text-center">
                                    <a href="<?= htmlspecialchars(trim($foto)) ?>" 
                                       target="_blank" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-expand me-1"></i> Ampliar
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Fotos del Trabajo</h5>
            </div>
            <div class="card-body text-center">
                <p class="text-muted mb-0">No hay fotos registradas para este trabajo</p>
            </div>
        </div>
        <?php endif; ?>
    </div>

<?php include '../../includes/bot.php'; ?>
    <script>setHelpModule('Trabajos');</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>