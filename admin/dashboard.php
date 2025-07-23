<?php
require_once __DIR__ . '/../php/auth.php';
require_once __DIR__ . '/../php/conexion.php';

requireAuth();

// Función auxiliar para obtener conteos con PDO
function getCount($conex, $table) {
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
    'clientes' => 'clientes/listar_clientes.php',
    'vehiculos' => 'vehiculos/listar_vehiculos.php',
    'cotizaciones' => 'cotizaciones/listar_cotizaciones.php',
    'materiales' => 'materiales/listar_materiales.php',
    'servicios' => 'servicios/listar_servicios.php',
    'trabajos' => 'trabajos/listar_trabajos.php',
    'usuarios' => 'usuarios/listar_usuarios.php',
    'roles' => 'roles/listar_roles.php'
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
    <title>Panel de control - Sistema de Tapicería</title>
    <style>
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .summary-card {
            background-color: var(--text-light);
            border-radius: 8px;
            box-shadow: var(--shadow-sm);
            border: var(--border-soft);
            transition: var(--transition-normal);
            height: 100%;
            cursor: pointer;
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            background-color: #f8f9fa;
        }
        
        .summary-card a {
            display: block;
            color: inherit;
            text-decoration: none;
            padding: 1rem;
        }
        
        /* Colores para tarjetas */
        .card-clientes .summary-icon {
            background-color: rgba(94, 48, 35, 0.1);
            color: var(--primary-dark);
        }
        
        .card-vehiculos .summary-icon {
            background-color: rgba(140, 74, 63, 0.1);
            color: var(--secondary-dark);
        }
        
        .card-cotizaciones .summary-icon {
            background-color: rgba(181, 113, 87, 0.1);
            color: var(--accent-color);
        }
        
        .card-materiales .summary-icon {
            background-color: rgba(212, 163, 115, 0.1);
            color: var(--accent-light);
        }
        
        .card-servicios .summary-icon {
            background-color: rgba(94, 114, 228, 0.1);
            color: #5e72e4;
        }
        
        .card-trabajos .summary-icon {
            background-color: rgba(245, 54, 92, 0.1);
            color: #f5365c;
        }
        
        .card-usuarios .summary-icon {
            background-color: rgba(45, 206, 137, 0.1);
            color: #2dce89;
        }
        
        .card-roles .summary-icon {
            background-color: rgba(255, 159, 67, 0.1);
            color: #ff9f43;
        }
    </style>
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="content-wrapper">
        <main class="main-content">
            <div class="dashboard-header">
                <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 class="dashboard-title">Dashboard</h2>
                <div class="user-info">
                    <span class="user-greeting">Bienvenido, <strong><?= htmlspecialchars(getUserName()) ?></strong></span>
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-content">
                <div class="row">
                    <div class="col-12">
                        <div class="dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Resumen del Sistema</h5>
                                <p>Rol actual: <span class="badge user-role"><?= htmlspecialchars($userRole) ?></span></p>
                                
                                <div class="summary-grid">
                                    <?php if (in_array('clientes', $allowedCards[$userRole])): ?>
                                    <!-- Clientes -->
                                    <div class="summary-card card-clientes" onclick="window.location.href='<?= $urls['clientes'] ?>'">
                                        <div class="summary-content">
                                            <div>
                                                <h6 class="summary-title">Clientes</h6>
                                                <h3 class="summary-value"><?= $counts['clientes'] ?></h3>
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
                                                <h6 class="summary-title">Vehículos</h6>
                                                <h3 class="summary-value"><?= $counts['vehiculos'] ?></h3>
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
                                                <h6 class="summary-title">Cotizaciones</h6>
                                                <h3 class="summary-value"><?= $counts['cotizaciones'] ?></h3>
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
                                                <h6 class="summary-title">Materiales</h6>
                                                <h3 class="summary-value"><?= $counts['materiales'] ?></h3>
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
                                                <h6 class="summary-title">Servicios</h6>
                                                <h3 class="summary-value"><?= $counts['servicios'] ?></h3>
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
                                                <h6 class="summary-title">Trabajos</h6>
                                                <h3 class="summary-value"><?= $counts['trabajos'] ?></h3>
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
                                                <h6 class="summary-title">Usuarios</h6>
                                                <h3 class="summary-value"><?= $counts['usuarios'] ?></h3>
                                            </div>
                                            <div class="summary-icon">
                                                <i class="fas fa-user-shield"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (in_array('roles', $allowedCards[$userRole])): ?>
                                    <!-- Roles -->
                                    <div class="summary-card card-roles" onclick="window.location.href='<?= $urls['roles'] ?>'">
                                        <div class="summary-content">
                                            <div>
                                                <h6 class="summary-title">Roles</h6>
                                                <h3 class="summary-value"><?= $counts['roles'] ?></h3>
                                            </div>
                                            <div class="summary-icon">
                                                <i class="fas fa-user-tag"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.admin-sidebar').classList.toggle('collapsed');
            document.querySelector('.content-wrapper').classList.toggle('sidebar-collapsed');
        });
    </script>
</body>
</html>