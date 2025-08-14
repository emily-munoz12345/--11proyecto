<?php
require_once __DIR__ . '/../../php/auth.php';
require_once __DIR__ . '/../../php/conexion.php';
requireAuth();

$usuario_id = getUserId();

try {
    // Obtener información del usuario
    $stmt = $conex->prepare("SELECT u.*, r.nombre_rol FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol WHERE u.id_usuario = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        die("Usuario no encontrado");
    }

    // Obtener cotizaciones recientes del usuario (si es vendedor)
    $cotizaciones = [];
    if (isSeller() || isAdmin()) {
        $stmt = $conex->prepare("SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo 
                                FROM cotizaciones c 
                                JOIN clientes cl ON c.id_cliente = cl.id_cliente 
                                JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo 
                                WHERE c.id_usuario = ? 
                                ORDER BY c.fecha_cotizacion DESC 
                                LIMIT 5");
        $stmt->execute([$usuario_id]);
        $cotizaciones = $stmt->fetchAll();
    }

    // Obtener trabajos recientes asignados (si es técnico)
    $trabajos = [];
    if (isTechnician() || isAdmin()) {
        $stmt = $conex->prepare("SELECT t.*, c.id_cliente, cl.nombre_cliente 
                                FROM trabajos t 
                                JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion 
                                JOIN clientes cl ON c.id_cliente = cl.id_cliente 
                                WHERE t.estado IN ('En progreso', 'Pendiente') 
                                ORDER BY t.fecha_inicio DESC 
                                LIMIT 5");
        $stmt->execute();
        $trabajos = $stmt->fetchAll();
    }

} catch (PDOException $e) {
    die("Error al obtener datos del usuario: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario | Nacional Tapizados</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
        <?php include '../includes/head.php'; ?>
            <?php include __DIR__ . '../../includes/sidebar.php'; ?>
    <div class="main-container">
        <a href="javascript:history.back()" class="back-button">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        
        <h1><i class="fas fa-user"></i> Perfil de Usuario</h1>
        
        <section class="profile-header">
            <div class="profile-avatar">
                <span><?= strtoupper(substr($usuario['nombre_completo'], 0, 1)) ?></span>
            </div>
            <div class="profile-info">
                <h2 class="profile-name"><?= htmlspecialchars($usuario['nombre_completo']) ?></h2>
                <span class="profile-role"><?= $usuario['nombre_rol'] ?></span>
                
                <div class="profile-details">
                    <div class="profile-detail">
                        <i class="fas fa-user"></i>
                        <span><?= htmlspecialchars($usuario['username_usuario']) ?></span>
                    </div>
                    <div class="profile-detail">
                        <i class="fas fa-envelope"></i>
                        <span><?= htmlspecialchars($usuario['correo_usuario']) ?></span>
                    </div>
                    <div class="profile-detail">
                        <i class="fas fa-phone"></i>
                        <span><?= htmlspecialchars($usuario['telefono_usuario']) ?: 'No especificado' ?></span>
                    </div>
                </div>
                
                <div class="profile-actions">
                    <a href="editar_perfil.php" class="btn btn-primary">
                        <i class="fas fa-user-edit"></i> Editar Perfil
                    </a>
                    <a href="cambiar_password.php" class="btn btn-secondary">
                        <i class="fas fa-key"></i> Cambiar Contraseña
                    </a>
                </div>
            </div>
        </section>

        <?php if (isSeller() || isAdmin()): ?>
        <section class="activity-section">
            <h2 class="section-title">
                <i class="fas fa-file-invoice-dollar"></i> Cotizaciones Recientes
            </h2>
            <?php if (empty($cotizaciones)): ?>
                <p>No hay cotizaciones recientes.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Vehículo</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cotizaciones as $cotizacion): 
                                $estadoClass = strtolower(str_replace(' ', '-', $cotizacion['estado_cotizacion']));
                            ?>
                            <tr>
                                <td><?= $cotizacion['id_cotizacion'] ?></td>
                                <td><?= htmlspecialchars($cotizacion['nombre_cliente']) ?></td>
                                <td><?= htmlspecialchars($cotizacion['marca_vehiculo']) ?> <?= htmlspecialchars($cotizacion['modelo_vehiculo']) ?></td>
                                <td>$<?= number_format($cotizacion['total_cotizacion'], 2) ?></td>
                                <td>
                                    <span class="status-badge <?= $estadoClass ?>">
                                        <?= $cotizacion['estado_cotizacion'] ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="cotizaciones.php" class="btn btn-primary">
                    <i class="fas fa-list"></i> Ver todas las cotizaciones
                </a>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <?php if (isTechnician() || isAdmin()): ?>
        <section class="activity-section">
            <h2 class="section-title">
                <i class="fas fa-tools"></i> Trabajos Asignados
            </h2>
            <?php if (empty($trabajos)): ?>
                <p>No hay trabajos asignados.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($trabajos as $trabajo): 
                                $estadoClass = strtolower(str_replace(' ', '-', $trabajo['estado']));
                            ?>
                            <tr>
                                <td><?= $trabajo['id_trabajos'] ?></td>
                                <td><?= htmlspecialchars($trabajo['nombre_cliente']) ?></td>
                                <td><?= date('d/m/Y', strtotime($trabajo['fecha_inicio'])) ?></td>
                                <td><?= $trabajo['fecha_fin'] != '0000-00-00' ? date('d/m/Y', strtotime($trabajo['fecha_fin'])) : 'Pendiente' ?></td>
                                <td>
                                    <span class="status-badge <?= $estadoClass ?>">
                                        <?= $trabajo['estado'] ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="trabajos.php" class="btn btn-primary">
                    <i class="fas fa-list"></i> Ver todos los trabajos
                </a>
            <?php endif; ?>
        </section>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>