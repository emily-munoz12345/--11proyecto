<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../php/conexion.php';
require_once __DIR__ . '/../php/auth.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <!-- Main Content Wrapper -->
    <div class="content-wrapper">
        <!-- Main Content -->
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
                            
                            <!-- Repetir estructura para los otros 3 cards -->
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

    <?php include __DIR__ . '/includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/tu-codigo.js" crossorigin="anonymous"></script>
    <script>
        // Toggle sidebar mejorado
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            const contentWrapper = document.querySelector('.content-wrapper');
            
            sidebar.classList.toggle('active');
            contentWrapper.classList.toggle('sidebar-collapsed');
            
            // Opcional: Guardar estado
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('active'));
        });
        
        // Cargar estado al inicio (opcional)
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            document.querySelector('.sidebar').classList.add('active');
            document.querySelector('.content-wrapper').classList.add('sidebar-collapsed');
        }
    </script>
</body>
</html>