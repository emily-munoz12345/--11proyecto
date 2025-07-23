





























<?php
require_once __DIR__ . '/../php/auth.php';
require_once __DIR__ . '/../php/conexion.php';

requireAuth();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
    <title>Dashboard - Sistema de Tapicería</title>
    <style>
        .admin-body {
            background-color: #f8f9fa;
        }
        .content-wrapper {
            margin-left: 250px;
            transition: all 0.3s;
            min-height: 100vh;
        }
        .sidebar-collapsed .content-wrapper {
            margin-left: 80px;
        }

        /* ===== ESTILOS DEL DASHBOARD ===== */
        .admin-body {
            background-color: var(--neutral-light);
            color: var(--text-dark);
        }

        /* Contenedor principal */
        .content-wrapper {
            margin-left: var(--sidebar-width);
            transition: var(--transition-normal);
            min-height: 100vh;
            background-color: var(--neutral-light);
        }

        .content-wrapper.sidebar-collapsed {
            margin-left: 80px;
        }

        /* Cabecera del dashboard */
        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--space-md) var(--space-lg);
            background-color: var(--text-light);
            box-shadow: var(--shadow-sm);
            height: var(--header-height);
            position: sticky;
            top: 0;
            z-index: var(--z-header);
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--primary-dark);
            font-size: 1.25rem;
            cursor: pointer;
            transition: var(--transition-fast);
        }

        .sidebar-toggle:hover {
            color: var(--accent-color);
        }

        .dashboard-title {
            color: var(--primary-dark);
            font-weight: 600;
            margin: 0;
        }

        /* Información de usuario */
        .user-info {
            display: flex;
            align-items: center;
            gap: var(--space-md);
        }

        .user-greeting {
            color: var(--text-dark);
            font-size: 0.95rem;
        }

        .user-greeting strong {
            color: var(--primary-dark);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--accent-color);
            color: var(--text-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        /* Contenido principal */
        .main-content {
            padding: var(--space-lg);
        }

        .dashboard-content {
            margin-top: var(--space-lg);
        }

        /* Tarjetas principales */
        .dashboard-card {
            background-color: var(--text-light);
            border-radius: 8px;
            box-shadow: var(--shadow-sm);
            border: var(--border-soft);
            margin-bottom: var(--space-lg);
            transition: var(--transition-normal);
        }

        .dashboard-card:hover {
            box-shadow: var(--shadow-md);
        }

        .card-body {
            padding: var(--space-lg);
        }

        .card-title {
            color: var(--primary-dark);
            font-weight: 600;
            margin-bottom: var(--space-md);
        }

        .user-role {
            background-color: var(--accent-color);
            color: var(--text-light);
            font-weight: 500;
            padding: var(--space-xs) var(--space-sm);
            border-radius: 20px;
        }

        /* Tarjetas de resumen */
        .summary-card {
            background-color: var(--text-light);
            border-radius: 8px;
            box-shadow: var(--shadow-sm);
            border: var(--border-soft);
            height: 100%;
            transition: var(--transition-normal);
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .summary-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--space-md);
        }

        .summary-title {
            color: var(--secondary-dark);
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: var(--space-xs);
        }

        .summary-value {
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 1.75rem;
            margin: 0;
        }

        .summary-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--neutral-medium);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        /* Efectos específicos para cada tarjeta */
        .summary-card:nth-child(1) .summary-icon {
            background-color: rgba(94, 48, 35, 0.1);
            color: var(--primary-dark);
        }

        .summary-card:nth-child(2) .summary-icon {
            background-color: rgba(140, 74, 63, 0.1);
            color: var(--secondary-dark);
        }

        .summary-card:nth-child(3) .summary-icon {
            background-color: rgba(181, 113, 87, 0.1);
            color: var(--accent-color);
        }

        .summary-card:nth-child(4) .summary-icon {
            background-color: rgba(212, 163, 115, 0.1);
            color: var(--accent-light);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .content-wrapper {
                margin-left: 0;
            }
            
            .content-wrapper.sidebar-collapsed {
                margin-left: 0;
            }
            
            .dashboard-header {
                padding: var(--space-md);
            }
            
            .user-greeting {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: var(--space-md);
            }
            
            .card-body {
                padding: var(--space-md);
            }
            
            .summary-value {
                font-size: 1.5rem;
            }
            
            .summary-icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
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
                                <p>Rol actual: <span class="badge user-role"><?= htmlspecialchars(getUserRole()) ?></span></p>
                                
                                <div class="row mt-4">
                                    <div class="col-md-3 mb-3">
                                        <div class="summary-card">
                                            <div class="card-body">
                                                <div class="summary-content">
                                                    <div>
                                                        <h6 class="summary-title">Clientes</h6>
                                                        <h3 class="summary-value">3</h3>
                                                    </div>
                                                    <div class="summary-icon">
                                                        <i class="fas fa-users"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <div class="summary-card">
                                            <div class="card-body">
                                                <div class="summary-content">
                                                    <div>
                                                        <h6 class="summary-title">Vehículos</h6>
                                                        <h3 class="summary-value">3</h3>
                                                    </div>
                                                    <div class="summary-icon">
                                                        <i class="fas fa-car"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <div class="summary-card">
                                            <div class="card-body">
                                                <div class="summary-content">
                                                    <div>
                                                        <h6 class="summary-title">Cotizaciones</h6>
                                                        <h3 class="summary-value">3</h3>
                                                    </div>
                                                    <div class="summary-icon">
                                                        <i class="fas fa-file-invoice-dollar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <div class="summary-card">
                                            <div class="card-body">
                                                <div class="summary-content">
                                                    <div>
                                                        <h6 class="summary-title">Materiales</h6>
                                                        <h3 class="summary-value">3</h3>
                                                    </div>
                                                    <div class="summary-icon">
                                                        <i class="fas fa-archive"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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