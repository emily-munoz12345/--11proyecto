<?php
require_once 'auth.php';
require_once 'conexion.php';
requireAuth();

$usuario_id = getUserId();

// Obtener datos del usuario
$stmt = $conex->prepare("SELECT nombre_completo FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener todas las cotizaciones del usuario
$stmt_cotizaciones = $conex->prepare("
    SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo
    FROM cotizaciones c
    INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
    INNER JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    WHERE c.id_usuario = ? AND c.activo = 1
    ORDER BY c.fecha_cotizacion DESC
");
$stmt_cotizaciones->execute([$usuario_id]);
$cotizaciones = $stmt_cotizaciones->fetchAll();

$total_cotizaciones = count($cotizaciones);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Cotizaciones - Sistema de Tapicería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                <i class="fas fa-file-invoice-dollar me-2"></i>Mis Cotizaciones
            </h1>
            <a href="perfil.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Perfil
            </a>
        </div>

        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Todas mis cotizaciones</h5>
                <span class="badge bg-primary"><?php echo $total_cotizaciones; ?> cotizaciones</span>
            </div>
            <div class="card-body">
                <?php if ($total_cotizaciones > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Vehículo</th>
                                <th>Subtotal</th>
                                <th>Adicional</th>
                                <th>IVA</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cotizaciones as $cotizacion): 
                                $badge_class = [
                                    'Pendiente' => 'bg-warning',
                                    'Aprobado' => 'bg-success',
                                    'Rechazada' => 'bg-danger',
                                    'Completada' => 'bg-info'
                                ][$cotizacion['estado_cotizacion']] ?? 'bg-secondary';
                            ?>
                            <tr>
                                <td>#<?php echo $cotizacion['id_cotizacion']; ?></td>
                                <td><?php echo htmlspecialchars($cotizacion['nombre_cliente']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo']); ?>
                                    <br><small class="text-muted"><?php echo $cotizacion['placa_vehiculo']; ?></small>
                                </td>
                                <td>$<?php echo number_format($cotizacion['subtotal_cotizacion'], 2); ?></td>
                                <td>$<?php echo number_format($cotizacion['valor_adicional'], 2); ?></td>
                                <td>$<?php echo number_format($cotizacion['iva'], 2); ?></td>
                                <td><strong>$<?php echo number_format($cotizacion['total_cotizacion'], 2); ?></strong></td>
                                <td>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo $cotizacion['estado_cotizacion']; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($cotizacion['fecha_cotizacion'])); ?></td>
                                <td>
                                    <a href="ver_cotizacion.php?id=<?php echo $cotizacion['id_cotizacion']; ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice-dollar fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No has realizado ninguna cotización</h4>
                    <p class="text-muted">Tus cotizaciones aparecerán aquí cuando las crees.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>