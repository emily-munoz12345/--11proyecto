<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

$usuario_id = getUserId();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID de trabajo no válido";
    header("Location: perfil.php");
    exit;
}

$trabajo_id = intval($_GET['id']);

// Obtener datos del trabajo
$stmt = $conex->prepare("
    SELECT t.*, 
           c.id_cotizacion, c.total_cotizacion, c.notas_cotizacion,
           cl.nombre_cliente, cl.correo_cliente, cl.telefono_cliente,
           v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
           u.nombre_completo as tecnico_nombre
    FROM trabajos t
    INNER JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
    INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
    INNER JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario
    WHERE t.id_trabajos = ? AND t.activo = 1
");

$stmt->execute([$trabajo_id]);
$trabajo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trabajo) {
    $_SESSION['error'] = "Trabajo no encontrado";
    header("Location: perfil.php");
    exit;
}

// Verificar que el trabajo pertenece al usuario actual (a menos que sea admin)
if (!isAdmin() && $trabajo['id_usuario'] != $usuario_id) {
    $_SESSION['error'] = "No tienes permisos para ver este trabajo";
    header("Location: perfil.php");
    exit;
}

// Obtener servicios de la cotización relacionada
$stmt_servicios = $conex->prepare("
    SELECT cs.*, s.nombre_servicio, s.descripcion_servicio
    FROM cotizacion_servicios cs
    INNER JOIN servicios s ON cs.id_servicio = s.id_servicio
    WHERE cs.id_cotizacion = ? AND cs.activo = 1
");
$stmt_servicios->execute([$trabajo['id_cotizacion']]);
$servicios = $stmt_servicios->fetchAll();

// Calcular días transcurridos
$fecha_inicio = new DateTime($trabajo['fecha_inicio']);
$fecha_actual = new DateTime();
$dias_transcurridos = $fecha_inicio->diff($fecha_actual)->days;

// Determinar si está atrasado
$atrasado = false;
if ($trabajo['fecha_fin'] != '0000-00-00') {
    $fecha_fin = new DateTime($trabajo['fecha_fin']);
    $atrasado = $fecha_actual > $fecha_fin && $trabajo['estado'] != 'Entregado';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trabajo #<?php echo $trabajo_id; ?> - Sistema de Tapicería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .work-header {
            border-bottom: 3px solid #28a745;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        .progress-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 1.5rem;
        }
        .service-badge {
            font-size: 0.8rem;
        }
        .photo-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .photo-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .photo-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .photo-item:hover img {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <?php include '../includes/head.php'; ?>

    <div class="container mt-4">
        <!-- Encabezado -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="fas fa-tools me-2"></i>Trabajo #<?php echo $trabajo_id; ?>
                </h1>
                <p class="text-muted mb-0">Seguimiento y detalles del trabajo</p>
            </div>
            <div class="text-end">
                <a href="perfil.php#jobs" class="btn btn-outline-primary me-2">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
                <button onclick="window.print()" class="btn btn-outline-secondary">
                    <i class="fas fa-print me-2"></i>Imprimir
                </button>
            </div>
        </div>

        <!-- Alertas -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Información Principal -->
            <div class="col-md-8">
                <!-- Estado y Progreso -->
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tasks me-2"></i>Estado del Trabajo
                        </h5>
                        <div>
                            <?php if ($atrasado): ?>
                                <span class="badge bg-danger me-2">Atrasado</span>
                            <?php endif; ?>
                            <span class="badge 
                                <?php 
                                echo [
                                    'Pendiente' => 'bg-warning',
                                    'En progreso' => 'bg-primary',
                                    'Entregado' => 'bg-success',
                                    'Cancelado' => 'bg-danger'
                                ][$trabajo['estado']] ?? 'bg-secondary';
                                ?>">
                                <?php echo $trabajo['estado']; ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="progress-section">
                            <div class="row text-center">
                                <div class="col-md-4 mb-3">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-play-circle fa-2x text-primary mb-2"></i>
                                        <h6>Inicio</h6>
                                        <p class="mb-0"><?php echo date('d/m/Y', strtotime($trabajo['fecha_inicio'])); ?></p>
                                        <small class="text-muted"><?php echo $dias_transcurridos; ?> días</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-flag-checkered fa-2x text-success mb-2"></i>
                                        <h6>Fin Estimado</h6>
                                        <p class="mb-0">
                                            <?php 
                                            if ($trabajo['fecha_fin'] != '0000-00-00') {
                                                echo date('d/m/Y', strtotime($trabajo['fecha_fin']));
                                            } else {
                                                echo '<span class="text-muted">Por definir</span>';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="border rounded p-3">
                                        <i class="fas fa-dollar-sign fa-2x text-info mb-2"></i>
                                        <h6>Valor</h6>
                                        <p class="mb-0">$<?php echo number_format($trabajo['total_cotizacion'], 2); ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Barra de progreso visual -->
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>Inicio</small>
                                    <small>En progreso</small>
                                    <small>Completado</small>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <?php
                                    $progress_width = [
                                        'Pendiente' => '0%',
                                        'En progreso' => '50%',
                                        'Entregado' => '100%',
                                        'Cancelado' => '0%'
                                    ][$trabajo['estado']] ?? '0%';
                                    
                                    $progress_class = [
                                        'Pendiente' => 'bg-warning',
                                        'En progreso' => 'bg-primary',
                                        'Entregado' => 'bg-success',
                                        'Cancelado' => 'bg-danger'
                                    ][$trabajo['estado']] ?? 'bg-secondary';
                                    ?>
                                    <div class="progress-bar <?php echo $progress_class; ?>" 
                                         style="width: <?php echo $progress_width; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Servicios a Realizar -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-concierge-bell me-2"></i>Servicios a Realizar
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($servicios) > 0): ?>
                            <div class="row">
                                <?php foreach ($servicios as $servicio): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="mb-2"><?php echo htmlspecialchars($servicio['nombre_servicio']); ?></h6>
                                        <p class="small text-muted mb-2"><?php echo htmlspecialchars($servicio['descripcion_servicio']); ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge service-badge bg-light text-dark">
                                                $<?php echo number_format($servicio['precio'], 2); ?>
                                            </span>
                                            <span class="badge service-badge bg-primary">
                                                <i class="fas fa-check-circle me-1"></i>Incluido
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center py-3">No hay servicios asignados a este trabajo</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Notas y Observaciones -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-sticky-note me-2"></i>Notas y Observaciones
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($trabajo['notas'])): ?>
                            <div class="bg-light p-3 rounded">
                                <?php echo nl2br(htmlspecialchars($trabajo['notas'])); ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center py-3">No hay notas registradas para este trabajo</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Fotos del Trabajo -->
                <?php if (!empty($trabajo['fotos'])): ?>
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-camera me-2"></i>Fotos del Trabajo
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="photo-gallery">
                            <?php
                            $fotos = explode(',', $trabajo['fotos']);
                            foreach ($fotos as $foto):
                                if (!empty(trim($foto))):
                            ?>
                            <div class="photo-item">
                                <img src="<?php echo htmlspecialchars(trim($foto)); ?>" 
                                     alt="Foto del trabajo #<?php echo $trabajo_id; ?>"
                                     class="img-fluid"
                                     onclick="openModal('<?php echo htmlspecialchars(trim($foto)); ?>')"
                                     style="cursor: pointer;">
                            </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Información Lateral -->
            <div class="col-md-4">
                <!-- Información del Cliente -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>Cliente
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong><br><?php echo htmlspecialchars($trabajo['nombre_cliente']); ?></p>
                        <p><strong>Contacto:</strong><br>
                            <?php echo htmlspecialchars($trabajo['correo_cliente']); ?><br>
                            <?php echo htmlspecialchars($trabajo['telefono_cliente']); ?>
                        </p>
                    </div>
                </div>

                <!-- Información del Vehículo -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-car me-2"></i>Vehículo
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Marca/Modelo:</strong><br><?php echo htmlspecialchars($trabajo['marca_vehiculo'] . ' ' . $trabajo['modelo_vehiculo']); ?></p>
                        <p><strong>Placa:</strong> <?php echo htmlspecialchars($trabajo['placa_vehiculo']); ?></p>
                    </div>
                </div>

                <!-- Información de la Cotización -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-invoice me-2"></i>Cotización Relacionada
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Número:</strong> #<?php echo $trabajo['id_cotizacion']; ?></p>
                        <p><strong>Valor Total:</strong> $<?php echo number_format($trabajo['total_cotizacion'], 2); ?></p>
                        <a href="ver_cotizacion.php?id=<?php echo $trabajo['id_cotizacion']; ?>" class="btn btn-sm btn-outline-primary w-100">
                            Ver Cotización
                        </a>
                    </div>
                </div>

                <!-- Acciones -->
                <?php if (isAdmin() || isTechnician()): ?>
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cogs me-2"></i>Acciones
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="editar_trabajo.php?id=<?php echo $trabajo_id; ?>" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Actualizar Trabajo
                            </a>
                            <?php if (isAdmin()): ?>
                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#eliminarModal">
                                <i class="fas fa-trash me-2"></i>Eliminar Trabajo
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal para ver imagen en grande -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto del Trabajo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Eliminación -->
    <?php if (isAdmin()): ?>
    <div class="modal fade" id="eliminarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar el trabajo #<?php echo $trabajo_id; ?>?</p>
                    <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="eliminar_trabajo.php" method="POST" class="d-inline">
                        <input type="hidden" name="id_trabajo" value="<?php echo $trabajo_id; ?>">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
            imageModal.show();
        }
    </script>
</body>
</html>