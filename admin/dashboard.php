<?php
require_once __DIR__ . '/../php/auth.php';
require_once __DIR__ . '/../php/conexion.php';

requireAuth();

// Función auxiliar para obtener conteos con PDO
function getCount($conex, $table)
{
    try {
        $stmt = $conex->query("SELECT COUNT(*) as total FROM $table");
        return $stmt->fetch()['total'];
    } catch (PDOException $e) {
        error_log("Error al contar registros en $table: " . $e->getMessage());
        return 0;
    }
}

// Obtener el rol del usuario actual
$userRole = getUserRole();

// URLs de redirección (ajusta según tu estructura de archivos)
$urls = [
    'clientes' => 'formularios/clientes/listar_clientes.php',
    'vehiculos' => 'formularios/vehiculos/listar_vehiculos.php',
    'cotizaciones' => 'formularios/cotizaciones/listar_cotizaciones.php',
    'materiales' => 'formularios/materiales/listar_materiales.php',
    'servicios' => 'formularios/servicios/listar_servicios.php',
    'trabajos' => 'formularios/trabajos/listar_trabajos.php',
    'usuarios' => 'formularios/usuarios/listar_usuarios.php',
];

// Definir qué tarjetas puede ver cada rol
$allowedCards = [
    'Administrador' => ['clientes', 'vehiculos', 'cotizaciones', 'materiales', 'servicios', 'trabajos', 'usuarios', 'roles'],
    'Técnico' => ['clientes', 'vehiculos', 'trabajos', 'materiales'],
    'Vendedor' => ['clientes', 'vehiculos', 'cotizaciones', 'servicios']
];

// Obtener solo los conteos necesarios según el rol
$counts = [];
foreach ($allowedCards[$userRole] as $card) {
    $counts[$card] = getCount($conex, $card);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
    <title>Panel de control - Nacional Tapizados</title>
    <style>
        body {
            background-image: url('https://pfst.cf2.poecdn.net/base/image/fe72e5f0bf336b4faca086bc6a42c20a45e904d165e796b52eca655a143283b8?w=1024&h=768&pmaid=426747789');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            color: #333;
        }

        .content {
            padding: 2rem;
            margin: 1rem;
            border-radius: 12px;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            min-height: calc(100vh - 4rem);
        }

        .dashboard-head {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .dashboard-title {
            color: white;
            font-size: 1.8rem;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .user-role {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            color: white;
            font-weight: 500;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .dashboard-content {
            margin-top: 2rem;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .summary-card {
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            cursor: pointer;
            backdrop-filter: blur(8px);
            overflow: hidden;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            background-color: rgba(255, 255, 255, 0.25);
        }

        .summary-card a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 1.5rem;
            height: 100%;
        }

        .summary-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
        }

        .summary-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-left: 1rem;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .summary-title {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 0.5rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .summary-value {
            font-size: 2rem;
            font-weight: 600;
            color: white;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Colores para tarjetas */
        .card-clientes .summary-icon {
            background-color: rgba(140, 74, 63, 0.3);
        }

        .card-vehiculos .summary-icon {
            background-color: rgba(140, 74, 63, 0.3);
        }

        .card-cotizaciones .summary-icon {
            background-color: rgba(140, 74, 63, 0.3);
        }

        .card-materiales .summary-icon {
            background-color: rgba(140, 74, 63, 0.3);
        }

        .card-servicios .summary-icon {
            background-color: rgba(140, 74, 63, 0.3);
        }

        .card-trabajos .summary-icon {
            background-color: rgba(140, 74, 63, 0.3);
        }

        .card-usuarios .summary-icon {
            background-color: rgba(140, 74, 63, 0.3);
        }

        .card-roles .summary-icon {
            background-color: rgba(140, 74, 63, 0.3);
        }

        .dashboard-card {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 2rem;
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .card-title {
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Estilos para el header derecho */
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logo-container {
            height: 50px;
        }

        .logo-container img {
            height: 100%;
            max-width: 100%;
            object-fit: contain;
        }

        .profile-button {
            display: flex;
            align-items: center;
            gap: 8px;
            background-color: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
            padding: 8px 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
        }

        .profile-button:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .profile-button i {
            font-size: 1.2rem;
            color: white;
        }

        .user-name {
            font-weight: 600;
            color: white;
        }

        @media (max-width: 768px) {
            .dashboard-head {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .header-right {
                width: 100%;
                justify-content: space-between;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body class="admin-body">
    <!-- Sidebar -->
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="dashboard-card text-center">
                <div class="dashboard-">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2 class="dashboard-title">Resumen del sistema</h2>
                            <span class="user-role-badge"><?= htmlspecialchars($userRole) ?></span>
                        </div>

                        <div class="dashboard-header-right">
                            <!-- Botón de perfil -->
                            <a href="perfil_usuario.php" class="dashboard-profile-btn">
                                <i class="fas fa-user-circle"></i>
                                <span class="dashboard-username"><?= htmlspecialchars(getUserName()) ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="dashboard-card text-center">
                    <h2 class="card-title mb-4">Contraciones</h2>

                    <div class="summary-grid">
                        <?php if (in_array('clientes', $allowedCards[$userRole])): ?>
                            <!-- Clientes -->
                            <div class="summary-card card-clientes" onclick="window.location.href='<?= $urls['clientes'] ?>'">
                                <div class="summary-content">
                                    <div>
                                        <h3 class="summary-title">Clientes</h3>
                                        <p class="summary-value"><?= $counts['clientes'] ?></p>
                                    </div>
                                    <div class="summary-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('vehiculos', $allowedCards[$userRole])): ?>
                            <!-- Vehículos -->
                            <div class="summary-card card-vehiculos" onclick="window.location.href='<?= $urls['vehiculos'] ?>'">
                                <div class="summary-content">
                                    <div>
                                        <h3 class="summary-title">Vehículos</h3>
                                        <p class="summary-value"><?= $counts['vehiculos'] ?></p>
                                    </div>
                                    <div class="summary-icon">
                                        <i class="fas fa-car"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('cotizaciones', $allowedCards[$userRole])): ?>
                            <!-- Cotizaciones -->
                            <div class="summary-card card-cotizaciones" onclick="window.location.href='<?= $urls['cotizaciones'] ?>'">
                                <div class="summary-content">
                                    <div>
                                        <h3 class="summary-title">Cotizaciones</h3>
                                        <p class="summary-value"><?= $counts['cotizaciones'] ?></p>
                                    </div>
                                    <div class="summary-icon">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('materiales', $allowedCards[$userRole])): ?>
                            <!-- Materiales -->
                            <div class="summary-card card-materiales" onclick="window.location.href='<?= $urls['materiales'] ?>'">
                                <div class="summary-content">
                                    <div>
                                        <h3 class="summary-title">Materiales</h3>
                                        <p class="summary-value"><?= $counts['materiales'] ?></p>
                                    </div>
                                    <div class="summary-icon">
                                        <i class="fas fa-archive"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('servicios', $allowedCards[$userRole])): ?>
                            <!-- Servicios -->
                            <div class="summary-card card-servicios" onclick="window.location.href='<?= $urls['servicios'] ?>'">
                                <div class="summary-content">
                                    <div>
                                        <h3 class="summary-title">Servicios</h3>
                                        <p class="summary-value"><?= $counts['servicios'] ?></p>
                                    </div>
                                    <div class="summary-icon">
                                        <i class="fas fa-concierge-bell"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('trabajos', $allowedCards[$userRole])): ?>
                            <!-- Trabajos -->
                            <div class="summary-card card-trabajos" onclick="window.location.href='<?= $urls['trabajos'] ?>'">
                                <div class="summary-content">
                                    <div>
                                        <h3 class="summary-title">Trabajos</h3>
                                        <p class="summary-value"><?= $counts['trabajos'] ?></p>
                                    </div>
                                    <div class="summary-icon">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('usuarios', $allowedCards[$userRole])): ?>
                            <!-- Usuarios -->
                            <div class="summary-card card-usuarios" onclick="window.location.href='<?= $urls['usuarios'] ?>'">
                                <div class="summary-content">
                                    <div>
                                        <h3 class="summary-title">Usuarios</h3>
                                        <p class="summary-value"><?= $counts['usuarios'] ?></p>
                                    </div>
                                    <div class="summary-icon">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>

    <script>
        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.admin-sidebar').classList.toggle('collapsed');
            document.querySelector('.content-wrapper').classList.toggle('sidebar-collapsed');
        });
    </script>
</body>

</html>