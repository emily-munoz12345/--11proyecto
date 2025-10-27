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
    $_SESSION['error'] = "ID de cotización no válido";
    header("Location: perfil.php");
    exit;
}

$cotizacion_id = intval($_GET['id']);

// Obtener datos de la cotización
$stmt = $conex->prepare("
    SELECT c.*, 
           cl.nombre_cliente, cl.correo_cliente, cl.telefono_cliente, cl.direccion_cliente,
           v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo, v.notas_vehiculo,
           u.nombre_completo as vendedor_nombre
    FROM cotizaciones c
    INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
    INNER JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
    WHERE c.id_cotizacion = ? AND c.activo = 1
");

$stmt->execute([$cotizacion_id]);
$cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cotizacion) {
    $_SESSION['error'] = "Cotización no encontrada";
    header("Location: perfil.php");
    exit;
}

// Verificar que la cotización pertenece al usuario actual (a menos que sea admin)
if (!isAdmin() && $cotizacion['id_usuario'] != $usuario_id) {
    $_SESSION['error'] = "No tienes permisos para ver esta cotización";
    header("Location: perfil.php");
    exit;
}

// Obtener servicios de la cotización
$stmt_servicios = $conex->prepare("
    SELECT cs.*, s.nombre_servicio, s.descripcion_servicio, s.categoria_servicio
    FROM cotizacion_servicios cs
    INNER JOIN servicios s ON cs.id_servicio = s.id_servicio
    WHERE cs.id_cotizacion = ? AND cs.activo = 1
");
$stmt_servicios->execute([$cotizacion_id]);
$servicios = $stmt_servicios->fetchAll();

// Calcular totales
$subtotal_servicios = 0;
foreach ($servicios as $servicio) {
    $subtotal_servicios += $servicio['precio'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización #<?php echo $cotizacion_id; ?> - Sistema de Tapicería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .invoice-header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        .service-item {
            border-left: 4px solid #667eea;
            padding-left: 1rem;
            margin-bottom: 1rem;
        }
        .totals-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
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
                    <i class="fas fa-file-invoice-dollar me-2"></i>Cotización #<?php echo $cotizacion_id; ?>
                </h1>
                <p class="text-muted mb-0">Detalles completos de la cotización</p>
            </div>
            <div class="text-end">
                <a href="perfil.php#quotations" class="btn btn-outline-primary me-2">
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
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Información de la Cotización
                        </h5>
                        <span class="badge status-badge 
                            <?php 
                            echo [
                                'Pendiente' => 'bg-warning',
                                'Aprobado' => 'bg-success',
                                'Rechazada' => 'bg-danger',
                                'Completada' => 'bg-info'
                            ][$cotizacion['estado_cotizacion']] ?? 'bg-secondary';
                            ?>">
                            <?php echo $cotizacion['estado_cotizacion']; ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($cotizacion['fecha_cotizacion'])); ?></p>
                                <p><strong>Vendedor:</strong> <?php echo htmlspecialchars($cotizacion['vendedor_nombre']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Subtotal:</strong> $<?php echo number_format($cotizacion['subtotal_cotizacion'], 2); ?></p>
                                <p><strong>Valor Adicional:</strong> $<?php echo number_format($cotizacion['valor_adicional'], 2); ?></p>
                            </div>
                        </div>
                        
                        <?php if (!empty($cotizacion['notas_cotizacion'])): ?>
                        <div class="mt-3">
                            <strong>Notas:</strong>
                            <p class="mb-0 p-2 bg-light rounded"><?php echo nl2br(htmlspecialchars($cotizacion['notas_cotizacion'])); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Servicios -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-concierge-bell me-2"></i>Servicios Cotizados
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($servicios) > 0): ?>
                            <?php foreach ($servicios as $servicio): ?>
                            <div class="service-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($servicio['nombre_servicio']); ?></h6>
                                        <p class="text-muted mb-1 small"><?php echo htmlspecialchars($servicio['descripcion_servicio']); ?></p>
                                        <span class="badge bg-light text-dark"><?php echo $servicio['categoria_servicio']; ?></span>
                                    </div>
                                    <div class="text-end">
                                        <strong>$<?php echo number_format($servicio['precio'], 2); ?></strong>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center py-3">No hay servicios en esta cotización</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Información Lateral -->
            <div class="col-md-4">
                <!-- Cliente -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>Información del Cliente
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong><br><?php echo htmlspecialchars($cotizacion['nombre_cliente']); ?></p>
                        <p><strong>Correo:</strong><br><?php echo htmlspecialchars($cotizacion['correo_cliente']); ?></p>
                        <p><strong>Teléfono:</strong><br><?php echo htmlspecialchars($cotizacion['telefono_cliente']); ?></p>
                        <p><strong>Dirección:</strong><br><?php echo nl2br(htmlspecialchars($cotizacion['direccion_cliente'])); ?></p>
                    </div>
                </div>

                <!-- Vehículo -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-car me-2"></i>Vehículo
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Marca:</strong> <?php echo htmlspecialchars($cotizacion['marca_vehiculo']); ?></p>
                        <p><strong>Modelo:</strong> <?php echo htmlspecialchars($cotizacion['modelo_vehiculo']); ?></p>
                        <p><strong>Placa:</strong> <?php echo htmlspecialchars($cotizacion['placa_vehiculo']); ?></p>
                        <?php if (!empty($cotizacion['notas_vehiculo'])): ?>
                            <p><strong>Notas:</strong><br><?php echo nl2br(htmlspecialchars($cotizacion['notas_vehiculo'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Totales -->
                <div class="card totals-section">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Resumen de Costos</h5>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal Servicios:</span>
                            <span>$<?php echo number_format($cotizacion['subtotal_cotizacion'], 2); ?></span>
                        </div>
                        
                        <?php if ($cotizacion['valor_adicional'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Valor Adicional:</span>
                            <span class="text-success">+ $<?php echo number_format($cotizacion['valor_adicional'], 2); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>IVA (19%):</span>
                            <span>$<?php echo number_format($cotizacion['iva'], 2); ?></span>
                        </div>
                        
                        <hr>
                        <div class="d-flex justify-content-between mb-0">
                            <strong>Total:</strong>
                            <strong class="h5 text-primary">$<?php echo number_format($cotizacion['total_cotizacion'], 2); ?></strong>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <?php if (isAdmin() || $cotizacion['id_usuario'] == $usuario_id): ?>
                <div class="card mt-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cogs me-2"></i>Acciones
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="editar_cotizacion.php?id=<?php echo $cotizacion_id; ?>" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Editar Cotización
                            </a>
                            <?php if (isAdmin()): ?>
                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#eliminarModal">
                                <i class="fas fa-trash me-2"></i>Eliminar Cotización
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
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
                    <p>¿Estás seguro de que deseas eliminar la cotización #<?php echo $cotizacion_id; ?>?</p>
                    <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="eliminar_cotizacion.php" method="POST" class="d-inline">
                        <input type="hidden" name="id_cotizacion" value="<?php echo $cotizacion_id; ?>">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>