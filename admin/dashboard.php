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
                            <a href="mis_pedidos.php" class="dropdown-item">Mis pedidos</a>
                            <a href="lista_deseos.php" class="dropdown-item">Lista de deseos</a>
                            <a href="perfil/configuracion.php" class="dropdown-item">Configuración</a>
                            <div class="dropdown-divider"></div>
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