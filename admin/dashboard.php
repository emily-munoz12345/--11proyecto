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
    'clientes' => 'formularios/clientes/listar_clientes.php',
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
        body {
            background-image: url('https://pfst.cf2.poecdn.net/base/image/fe72e5f0bf336b4faca086bc6a42c20a45e904d165e796b52eca655a143283b8?w=1024&h=768&pmaid=426747789');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
        }
        
        .content {
            padding: 2rem;
            margin: 1rem;
            border-radius: 12px;
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            min-height: calc(100vh - 4rem);
        }
        
        .dashboard-head {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(140, 74, 63, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .dashboard-content {
            margin-top: 2rem;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .summary-card {
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(140, 74, 63, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            cursor: pointer;
            backdrop-filter: blur(6px);
            overflow: hidden;
            position: relative;
        }
        
        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            z-index: -1;
            filter: blur(10px);
            margin: -10px;
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            background-color: rgba(255, 255, 255, 0.85);
        }
        
        .summary-card a {
            display: block;
            color: inherit;
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
        }
        
        .summary-title {
            font-size: 1rem;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .summary-value {
            font-size: 2rem;
            font-weight: 600;
            color: #8c4a3f;
            margin: 0;
        }
        
        /* Colores para tarjetas */
        .card-clientes {
            background-color: rgba(140, 74, 63, 0.15);
        }
        
        .card-vehiculos {
            background-color: rgba(101, 115, 126, 0.15);
        }
        
        .card-cotizaciones {
            background-color: rgba(76, 145, 65, 0.15);
        }
        
        .card-materiales {
            background-color: rgba(169, 126, 60, 0.15);
        }
        
        .card-servicios {
            background-color: rgba(60, 110, 169, 0.15);
        }
        
        .card-trabajos {
            background-color: rgba(169, 60, 101, 0.15);
        }
        
        .card-usuarios {
            background-color: rgba(110, 60, 169, 0.15);
        }
        
        .card-roles {
            background-color: rgba(60, 169, 157, 0.15);
        }
        
        .dashboard-card {
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 12px;
            padding: 2rem;
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .card-title {
            color: #8c4a3f;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .user-role {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background-color: rgba(140, 74, 63, 0.2);
            border-radius: 20px;
            color: #8c4a3f;
            font-weight: 600;
        }
        
        /* Nuevos estilos para el logo y perfil */
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
            background-color: rgba(140, 74, 63, 0.1);
            border: 1px solid rgba(140, 74, 63, 0.2);
            border-radius: 25px;
            padding: 8px 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #333;
        }
        
        .profile-button:hover {
            background-color: rgba(140, 74, 63, 0.2);
            transform: translateY(-2px);
        }
        
        .profile-button i {
            font-size: 1.2rem;
            color: #8c4a3f;
        }
        
        .user-name {
            font-weight: 600;
            color: #8c4a3f;
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
        }
    </style>
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="content">
        <div class="dashboard-head">
            <div>
                <h2 class="dashboard-title">Panel de control</h2>
                <p class="mb-0">Rol actual: <span class="user-role"><?= htmlspecialchars($userRole) ?></span></p>
            </div>
            
            <div class="header-right">
                <!-- Logo en la esquina superior derecha -->
                <div class="logo-container">
                    <img src="https://via.placeholder.com/150x50?text=Logo+Empresa" alt="Logo de la empresa">
                </div>
                
                <!-- Botón de perfil -->
                <a href="perfil_usuario.php" class="profile-button">
                    <i class="fas fa-user-circle"></i>
                    <span class="user-name"><?= htmlspecialchars(getUserName()) ?></span>
                </a>
            </div>
        </div>
        
        <div class="dashboard-content">
            <div class="row">
                <div class="col-12">
                    <div class="dashboard-card">
                        <h5 class="card-title">Resumen del Sistema</h5>
                        
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