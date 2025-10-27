<?php
require_once 'auth.php';
require_once 'conexion.php';
requireAuth();

$usuario_id = getUserId();

// Obtener datos del usuario
$stmt = $conex->prepare("SELECT nombre_completo FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener todos los trabajos del usuario
$stmt_trabajos = $conex->prepare("
    SELECT t.*, c.id_cotizacion, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo
    FROM trabajos t
    INNER JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
    INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
    INNER JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    WHERE c.id_usuario = ? AND t.activo = 1
    ORDER BY t.fecha_inicio DESC
");
$stmt_trabajos->execute([$usuario_id]);
$trabajos = $stmt_trabajos->fetchAll();

$total_trabajos = count($trabajos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Trabajos - Sistema de Tapicería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                <i class="fas fa-tools me-2"></i>Mis Trabajos
            </h1>
            <a href="perfil.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Perfil
            </a>
        </div>

        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Todos mis trabajos</h5>
                <span class="badge bg-success"><?php echo $total_trabajos; ?> trabajos</span>
            </div>
            <div class="card-body">
                <?php if ($total_trabajos > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Cotización</th>
                                <th>Cliente</th>
                                <th>Vehículo</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Estado</th>
                                <th>Notas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($trabajos as $trabajo): 
                                $badge_class = [
                                    'Pendiente' => 'bg-warning',
                                    'En progreso' => 'bg-primary',
                                    'Entregado' => 'bg-success',
                                    'Cancelado' => 'bg-danger'
                                ][$trabajo['estado']] ?? 'bg-secondary';
                            ?>
                            <tr>
                                <td>#<?php echo $trabajo['id_trabajos']; ?></td>
                                <td>#<?php echo $trabajo['id_cotizacion']; ?></td>
                                <td><?php echo htmlspecialchars($trabajo['nombre_cliente']); ?></td>
                                <td><?php echo htmlspecialchars($trabajo['marca_vehiculo'] . ' ' . $trabajo['modelo_vehiculo']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($trabajo['fecha_inicio'])); ?></td>
                                <td>
                                    <?php 
                                    if ($trabajo['fecha_fin'] != '0000-00-00') {
                                        echo date('d/m/Y', strtotime($trabajo['fecha_fin']));
                                    } else {
                                        echo '<span class="text-muted">-</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo $trabajo['estado']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    if (!empty($trabajo['notas'])) {
                                        echo '<span title="' . htmlspecialchars($trabajo['notas']) . '">';
                                        echo strlen($trabajo['notas']) > 50 ? 
                                            htmlspecialchars(substr($trabajo['notas'], 0, 50)) . '...' : 
                                            htmlspecialchars($trabajo['notas']);
                                        echo '</span>';
                                    } else {
                                        echo '<span class="text-muted">Sin notas</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="ver_trabajo.php?id=<?php echo $trabajo['id_trabajos']; ?>" 
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
                    <i class="fas fa-tools fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No tienes trabajos asignados</h4>
                    <p class="text-muted">Tus trabajos aparecerán aquí cuando te sean asignados.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>