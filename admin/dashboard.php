<?php
require_once __DIR__ . '/../php/auth.php';
require_once __DIR__ . '/../php/conexion.php';

requireAuth();

// Función auxiliar para obtener conteos con PDO
function getCount($conex, $table)
{
    try {
        $stmt = $conex->query("SELECT COUNT(*) as total FROM $table WHERE activo = 1");
        return $stmt->fetch()['total'];
    } catch (PDOException $e) {
        error_log("Error al contar registros en $table: " . $e->getMessage());
        return 0;
    }
}

// Obtener el rol del usuario actual
$userRole = getUserRole();
$userName = getUserName();

// URLs de redirección (ajusta según tu estructura de archivos)
$urls = [
    'inicio' => 'inicio.php',
    'clientes' => 'formularios/clientes/listar_clientes.php',
    'vehiculos' => 'formularios/vehiculos/listar_vehiculos.php',
    'cotizaciones' => 'formularios/cotizaciones/listar_cotizaciones.php',
    'materiales' => 'formularios/materiales/listar_materiales.php',
    'servicios' => 'formularios/servicios/listar_servicios.php',
    'trabajos' => 'formularios/trabajos/listar_trabajos.php',
    'usuarios' => 'formularios/usuarios/listar_usuarios.php',
    'roles' => 'formularios/roles/listar_roles.php',
];

// Definir qué tarjetas puede ver cada rol (usando nombres consistentes con auth.php)
$allowedCards = [
    'Administrador' => ['clientes', 'vehiculos', 'cotizaciones', 'materiales', 'servicios', 'trabajos', 'usuarios', 'roles'],
    'Tecnico' => ['clientes', 'vehiculos', 'trabajos', 'materiales'],
    'Vendedor' => ['clientes', 'vehiculos', 'cotizaciones', 'servicios']
];

// Asegurar que el rol existe en el array, si no usar array vacío
$userCards = $allowedCards[$userRole] ?? [];

// Obtener solo los conteos necesarios según el rol
$counts = [];
foreach ($userCards as $card) {
    $counts[$card] = getCount($conex, $card);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
    <title>MSkingCars - Inicio</title>
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

        .admin-body {
            background-color: transparent;
        }

        .dashboard-content {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--bg-transparent);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
        }

        .dashboard-title {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            color: var(--text-color);
        }

        .user-role-badge {
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 0.5rem;
            display: inline-block;
        }

        .dashboard-header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Estilos para el menú de usuario */
        .user-dropdown {
            position: relative;
        }

        .user-dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background-color: var(--bg-transparent-light);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-dropdown-toggle:hover {
            background-color: rgba(140, 74, 63, 0.3);
        }

        .username {
            font-weight: 500;
        }

        .dropdown-arrow {
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }

        .user-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            width: 200px;
            background-color: rgba(40, 40, 40, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            border: 1px solid var(--border-color);
            z-index: 1000;
            display: none;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .user-dropdown-menu.show {
            display: block;
            animation: fadeInUp 0.3s ease;
        }

        .dropdown-item {
            display: block;
            padding: 0.75rem 1rem;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s ease;
            border-bottom: 1px solid var(--border-color);
        }

        .dropdown-item:hover {
            background-color: var(--primary-color);
        }

        .dropdown-item.logout {
            color: var(--danger-color);
        }

        .dropdown-item.logout:hover {
            background-color: var(--danger-color);
            color: white;
        }

        /* Estilos para el contenido de bienvenida */
        .welcome-section {
            text-align: center;
            padding: 3rem 2rem;
        }

        .brand-title {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            color: var(--text-color);
            letter-spacing: 2px;
        }

        .welcome-message {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            color: var(--text-muted);
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        .user-welcome {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 600px;
            border: 1px solid var(--border-color);
        }

        .welcome-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        .welcome-role {
            font-size: 1.1rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .welcome-text {
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Estilos para las tarjetas del dashboard */
        .dashboard-card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            margin-top: 2rem;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .summary-card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            background-color: rgba(140, 74, 63, 0.2);
        }

        .summary-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-title {
            font-size: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .summary-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0;
            color: var(--text-color);
        }

        .summary-icon {
            font-size: 2.5rem;
            opacity: 0.8;
            color: var(--primary-color);
        }

        /* Colores específicos para cada tarjeta */
        .card-clientes:hover .summary-icon {
            color: #3498db;
        }

        .card-vehiculos:hover .summary-icon {
            color: #e74c3c;
        }

        .card-cotizaciones:hover .summary-icon {
            color: #2ecc71;
        }

        .card-materiales:hover .summary-icon {
            color: #f39c12;
        }

        .card-servicios:hover .summary-icon {
            color: #9b59b6;
        }

        .card-trabajos:hover .summary-icon {
            color: #1abc9c;
        }

        .card-usuarios:hover .summary-icon {
            color: #34495e;
        }

        /* Animación para el dropdown */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-content {
                padding: 1rem;
                margin: 1rem;
            }

            .dashboard-title {
                font-size: 1.5rem;
            }

            .brand-title {
                font-size: 2.5rem;
            }

            .welcome-message {
                font-size: 1.2rem;
                padding: 0 1rem;
            }

            .user-welcome {
                padding: 1.5rem;
                margin: 1rem;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .summary-card {
                padding: 1rem;
            }

            .summary-value {
                font-size: 1.25rem;
            }

            .summary-icon {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body class="admin-body">
    <!-- Sidebar -->
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="dashboard-content">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="dashboard-title">MSkingCars</h2>
                        <span class="user-role-badge">Panel Administrativo</span>
                    </div>
                    <div class="dashboard-header-right">
                        <!-- Menú de usuario estilo ASOS -->
                        <div class="user-dropdown">
                            <button class="user-dropdown-toggle">
                                <i class="fas fa-user-circle"></i>
                                <span class="username"><?= htmlspecialchars($userName) ?></span>
                                <i class="fas fa-chevron-down dropdown-arrow"></i>
                            </button>
                            <div class="user-dropdown-menu">
                                <a href="perfil/perfil.php" class="dropdown-item">Mi perfil</a>
                                <a href="../php/logout.php" class="dropdown-item logout">Cerrar sesión</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sección de Bienvenida -->
        <div class="row justify-content-center mt-4">
            <div class="col-12 col-lg-10">
                <div class="welcome-section">
                    <h1 class="brand-title">MSkingCars</h1>
                    <p class="welcome-message">
                        Sistema integral de gestión para tapicería automotriz. 
                        Administra clientes, vehículos, cotizaciones y trabajos de manera eficiente 
                        desde una única plataforma.
                    </p>
                    
                    <div class="user-welcome">
                        <h3 class="welcome-title">¡Bienvenido, <?= htmlspecialchars($userName) ?>!</h3>
                        <p class="welcome-role"> <?= htmlspecialchars($userRole) ?></p>
                        <p class="welcome-text">
                            Estás accediendo al sistema administrativo de MSkingCars. 
                            Desde aquí podrás gestionar toda la información relacionada con los servicios 
                            de tapicería automotriz, clientes, inventario y más.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Resumen del Sistema -->
        <div class="row justify-content-center mt-4">
            <div class="col-12 col-lg-10">
                <div class="dashboard-card text-center">
                    <h3 class="card-title">Resumen del Sistema</h3>
                    <div class="summary-grid">
                        <?php if (in_array('clientes', $userCards)): ?>
                            <!-- Clientes -->
                            <div class="summary-card card-clientes" onclick="window.location.href='<?= $urls['clientes'] ?>'">
                                <div class="summary-content">
                                    <div>
                                        <h3 class="summary-title">Clientes</h3>
                                        <p class="summary-value"><?= $counts['clientes'] ?? 0 ?></p>
                                    </div>
                                    <div class="summary-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('vehiculos', $userCards)): ?>
                            <!-- Vehículos -->
                            <div class="summary-card card-vehiculos" onclick="window.location.href='<?= $urls['vehiculos'] ?>'">
                                <div class="summary-content">
                                    <div>
                                        <h3 class="summary-title">Vehículos</h3>
                                        <p class="summary-value"><?= $counts['vehiculos'] ?? 0 ?></p>
                                    </div>
                                    <div class="summary-icon">
                                        <i class="fas fa-car"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('cotizaciones', $userCards)): ?>
                            <!-- Cotizaciones -->
                            <div class="summary-card card-cotizaciones" onclick="window.location.href='<?= $urls['cotizaciones'] ?>'">
                                <div class="summary-content">
                                    <div>
                                        <h3 class="summary-title">Cotizaciones</h3>
                                        <p class="summary-value"><?= $counts['cotizaciones'] ?? 0 ?></p>
                                    </div>
                                    <div class="summary-icon">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('materiales', $userCards)): ?>
                            <!-- Materiales -->
                            <div class="summary-card card-materiales" onclick="window.location.href='<?= $urls['materiales'] ?>'">
                                <div class="summary-content">
                                    <div>
                                        <h3 class="summary-title">Materiales</h3>
                                        <p class="summary-value"><?= $counts['materiales'] ?? 0 ?></p>
                                    </div>
                                    <div class="summary-icon">
                                        <i class="fas fa-archive"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('servicios', $userCards)): ?>
                            <!-- Servicios -->
                            <div class="summary-card card-servicios" onclick="window.location.href='<?= $urls['servicios'] ?>'">
                                <div class="summary-content">
                                    <div>
                                        <h3 class="summary-title">Servicios</h3>
                                        <p class="summary-value"><?= $counts['servicios'] ?? 0 ?></p>
                                    </div>
                                    <div class="summary-icon">
                                        <i class="fas fa-concierge-bell"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('trabajos', $userCards)): ?>
                            <!-- Trabajos -->
                            <div class="summary-card card-trabajos" onclick="window.location.href='<?= $urls['trabajos'] ?>'">
                                <div class="summary-content">
                                    <div>
                                        <h3 class="summary-title">Trabajos</h3>
                                        <p class="summary-value"><?= $counts['trabajos'] ?? 0 ?></p>
                                    </div>
                                    <div class="summary-icon">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('usuarios', $userCards)): ?>
                            <!-- Usuarios -->
                            <div class="summary-card card-usuarios" onclick="window.location.href='<?= $urls['usuarios'] ?>'">
                                <div class="summary-content">
                                    <div>
                                        <h3 class="summary-title">Usuarios</h3>
                                        <p class="summary-value"><?= $counts['usuarios'] ?? 0 ?></p>
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

        // User dropdown functionality
        document.querySelector('.user-dropdown-toggle').addEventListener('click', function() {
            const dropdownMenu = document.querySelector('.user-dropdown-menu');
            const arrow = document.querySelector('.dropdown-arrow');
            
            dropdownMenu.classList.toggle('show');
            
            if (dropdownMenu.classList.contains('show')) {
                arrow.style.transform = 'rotate(180deg)';
            } else {
                arrow.style.transform = 'rotate(0deg)';
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.querySelector('.user-dropdown');
            const dropdownMenu = document.querySelector('.user-dropdown-menu');
            const arrow = document.querySelector('.dropdown-arrow');
            
            if (!dropdown.contains(event.target)) {
                dropdownMenu.classList.remove('show');
                arrow.style.transform = 'rotate(0deg)';
            }
        });
    </script>
</body>

</html>