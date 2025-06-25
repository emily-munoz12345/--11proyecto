<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../php/conexion.php';
require_once __DIR__ . '/../php/auth.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Nacional Tapizados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #212529;
            --primary-color: #0d6efd;
        }
        
        body {
            overflow-x: hidden;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            color: white;
            position: fixed;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            margin-bottom: 5px;
            border-radius: 5px;
            padding: 10px 15px;
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        
        .sidebar .nav-link.active {
            color: white;
            background-color: var(--primary-color);
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 20px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <div class="text-center mb-4">
            <h4>Nacional Tapizados</h4>
            <small>Sistema de Gestión</small>
            <button class="btn btn-sm btn-outline-light d-md-none mt-2" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            
            <?php if (isAdmin() || isSeller()): ?>
            <li class="nav-item">
                <a class="nav-link" href="../admin/clientes/index.php">
                    <i class="fas fa-users"></i> Clientes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../admin/vehiculos/index.php">
                    <i class="fas fa-car"></i> Vehículos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../admin/cotizaciones/index.php">
                    <i class="fas fa-file-invoice-dollar"></i> Cotizaciones
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (isAdmin() || isTechnician()): ?>
            <li class="nav-item">
                <a class="nav-link" href="../admin/trabajos/index.php">
                    <i class="fas fa-tools"></i> Trabajos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../admin/materiales/index.php">
                    <i class="fas fa-archive"></i> Materiales
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (isAdmin()): ?>
            <li class="nav-item">
                <a class="nav-link" href="../admin/servicios/index.php">
                    <i class="fas fa-concierge-bell"></i> Servicios
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../admin/usuarios/index.php">
                    <i class="fas fa-user-cog"></i> Usuarios
                </a>
            </li>
            <?php endif; ?>
            
            <li class="nav-item mt-4">
                <a class="nav-link" href="../php/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Dashboard</h2>
            <div class="user-info d-flex align-items-center">
                <span class="me-2">Bienvenido, <strong><?= htmlspecialchars(getUserName()) ?></strong></span>
                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Resumen del Sistema</h5>
                        <p>Rol actual: <span class="badge bg-primary"><?= htmlspecialchars(getUserRole()) ?></span></p>
                        
                        <div class="row mt-4">
                            <div class="col-md-3 mb-3">
                                <div class="card bg-white border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="text-muted">Clientes</h6>
                                                <h3 class="mb-0">3</h3>
                                            </div>
                                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                                <i class="fas fa-users text-primary"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <div class="card bg-white border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="text-muted">Vehículos</h6>
                                                <h3 class="mb-0">3</h3>
                                            </div>
                                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                                <i class="fas fa-car text-primary"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <div class="card bg-white border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="text-muted">Cotizaciones</h6>
                                                <h3 class="mb-0">3</h3>
                                            </div>
                                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                                <i class="fas fa-file-invoice-dollar text-primary"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <div class="card bg-white border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="text-muted">Materiales</h6>
                                                <h3 class="mb-0">3</h3>
                                            </div>
                                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                                <i class="fas fa-archive text-primary"></i>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar en móviles
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>