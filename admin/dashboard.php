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

// URLs de redirección (ajusta según tu estructura de archivos)
$urls = [
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
    <title>Panel de control - Nacional Tapizados</title>
</head>

<body class="admin-body">
    <!-- Sidebar -->
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="row justify-content-center">

        <div class="col-12 col-lg-10">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h2 class="dashboard-title">Resumen del sistema</h2>
                    <span class="user-role-badge"><?= htmlspecialchars($userRole) ?></span>
                </div>
                <div class="dashboard-header-right">
                    <!-- Menú de usuario estilo ASOS -->
                    <div class="user-dropdown">
                        <button class="user-dropdown-toggle">
                            <i class="fas fa-user-circle"></i>
                            <span class="username"><?= htmlspecialchars(getUserName()) ?></span>
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
    <div class="dashboard-content">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="dashboard-card text-center">
                    <h2 class="card-title mb-4">Controles</h2>

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
            document.querySelector('.user-dropdown-menu').classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.querySelector('.user-dropdown');
            if (!dropdown.contains(event.target)) {
                document.querySelector('.user-dropdown-menu').classList.remove('show');
            }
        });
    </script>
</body>

</html>