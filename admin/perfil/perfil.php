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
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/perfil.css">
    <style>
        /* Estilos específicos para el perfil */
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--space-lg);
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: var(--space-xl);
            margin-bottom: var(--space-xl);
            background-color: var(--neutral-light);
            padding: var(--space-lg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: var(--border-soft);
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: var(--gradient-gold);
            color: var(--primary-dark);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            flex-shrink: 0;
            box-shadow: var(--shadow-md);
        }

        .profile-info {
            flex: 1;
        }

        .profile-name {
            font-size: 1.8rem;
            color: var(--primary-dark);
            margin-bottom: var(--space-sm);
            font-family: 'Roboto Condensed', sans-serif;
        }

        .profile-role {
            display: inline-block;
            padding: var(--space-xs) var(--space-sm);
            background: var(--gradient-gold);
            color: var(--primary-dark);
            font-weight: 600;
            border-radius: 20px;
            margin-bottom: var(--space-md);
            font-size: 0.9rem;
        }

        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: var(--space-md);
            margin-top: var(--space-md);
        }

        .profile-detail {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .profile-detail i {
            color: var(--accent-color);
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }

        .profile-actions {
            display: flex;
            gap: var(--space-md);
            margin-top: var(--space-lg);
        }

        .btn {
            padding: var(--space-sm) var(--space-lg);
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: var(--transition-fast);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .btn-primary {
            background: var(--gradient-gold);
            color: var(--primary-dark);
            border: 1px solid var(--gold-dark);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--gold-pastel), var(--gold-cream));
            box-shadow: var(--shadow-sm);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--neutral-light);
            color: var(--primary-dark);
            border: 1px solid var(--neutral-dark);
        }

        .btn-secondary:hover {
            background-color: var(--neutral-medium);
            box-shadow: var(--shadow-sm);
            transform: translateY(-2px);
        }

        /* Secciones de actividad */
        .activity-section {
            background-color: var(--neutral-light);
            border-radius: var(--border-radius);
            padding: var(--space-lg);
            margin-bottom: var(--space-xl);
            box-shadow: var(--shadow-sm);
            border: var(--border-soft);
        }

        .section-title {
            font-size: 1.4rem;
            color: var(--primary-dark);
            margin-bottom: var(--space-lg);
            padding-bottom: var(--space-sm);
            border-bottom: 2px solid var(--gold-cream);
            font-family: 'Roboto Condensed', sans-serif;
        }

        /* Tablas */
        .table-responsive {
            overflow-x: auto;
            margin-bottom: var(--space-lg);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--neutral-light);
        }

        th, td {
            padding: var(--space-md) var(--space-sm);
            text-align: left;
            border-bottom: 1px solid var(--neutral-medium);
        }

        th {
            background-color: var(--primary-dark);
            color: var(--text-light);
            font-weight: 500;
        }

        tr:hover {
            background-color: var(--neutral-medium);
        }

        .status-badge {
            display: inline-block;
            padding: var(--space-xs) var(--space-sm);
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-badge.pendiente {
            background-color: rgba(230, 200, 140, 0.2);
            color: var(--gold-accent);
            border: 1px solid var(--gold-pastel);
        }

        .status-badge.completado {
            background-color: rgba(138, 155, 110, 0.2);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }

        .status-badge.en-progreso {
            background-color: rgba(214, 163, 77, 0.2);
            color: var(--gold-dark);
            border: 1px solid var(--gold-dark);
        }

        .status-badge.cancelado {
            background-color: rgba(196, 90, 77, 0.2);
            color: var(--error-color);
            border: 1px solid var(--error-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .profile-avatar {
                margin-bottom: var(--space-md);
            }

            .profile-actions {
                justify-content: center;
            }

            .profile-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="admin-body">
    <?php include '../includes/head.php'; ?>

    <div class="content-wrapper">
        <main class="profile-container">
            <section class="profile-header">
                <div class="profile-avatar">
                    <span><?= strtoupper(substr($usuario['nombre_completo'], 0, 1)) ?></span>
                </div>
                <div class="profile-info">
                    <h1 class="profile-name"><?= htmlspecialchars($usuario['nombre_completo']) ?></h1>
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
                                <?php foreach ($cotizaciones as $cotizacion): ?>
                                <tr>
                                    <td><?= $cotizacion['id_cotizacion'] ?></td>
                                    <td><?= htmlspecialchars($cotizacion['nombre_cliente']) ?></td>
                                    <td><?= htmlspecialchars($cotizacion['marca_vehiculo']) ?> <?= htmlspecialchars($cotizacion['modelo_vehiculo']) ?></td>
                                    <td>$<?= number_format($cotizacion['total_cotizacion'], 2) ?></td>
                                    <td>
                                        <span class="status-badge <?= strtolower(str_replace(' ', '-', $cotizacion['estado_cotizacion'])) ?>">
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
                                <?php foreach ($trabajos as $trabajo): ?>
                                <tr>
                                    <td><?= $trabajo['id_trabajos'] ?></td>
                                    <td><?= htmlspecialchars($trabajo['nombre_cliente']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($trabajo['fecha_inicio'])) ?></td>
                                    <td><?= $trabajo['fecha_fin'] != '0000-00-00' ? date('d/m/Y', strtotime($trabajo['fecha_fin'])) : 'Pendiente' ?></td>
                                    <td>
                                        <span class="status-badge <?= strtolower(str_replace(' ', '-', $trabajo['estado'])) ?>">
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
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>