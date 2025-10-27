<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

$usuario_id = getUserId();

// Obtener datos del usuario
$stmt = $conex->prepare("SELECT u.*, r.nombre_rol 
                        FROM usuarios u 
                        INNER JOIN roles r ON u.id_rol = r.id_rol 
                        WHERE u.id_usuario = ? AND u.activo = 1");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Usuario no encontrado");
}

// Obtener estadísticas
$stmt_cotizaciones = $conex->prepare("SELECT COUNT(*) as total FROM cotizaciones WHERE id_usuario = ? AND activo = 1");
$stmt_cotizaciones->execute([$usuario_id]);
$total_cotizaciones = $stmt_cotizaciones->fetchColumn();

$stmt_trabajos = $conex->prepare("SELECT COUNT(*) as total 
                                FROM trabajos t 
                                INNER JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion 
                                WHERE c.id_usuario = ? AND t.activo = 1");
$stmt_trabajos->execute([$usuario_id]);
$total_trabajos = $stmt_trabajos->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Sistema de Tapicería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        .main-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--bg-transparent);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            color: var(--text-color);
        }

        .page-title i {
            margin-right: 12px;
            color: var(--primary-color);
        }

        /* Estilos para pestañas */
        .nav-tabs {
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 2rem;
        }

        .nav-link {
            color: var(--text-muted);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--text-color);
            background-color: var(--bg-transparent-light);
        }

        .nav-link.active {
            color: white;
            background-color: var(--primary-color);
            border-radius: 8px 8px 0 0;
        }

        .tab-content {
            padding: 1.5rem 0;
        }

        /* Estilos para tarjetas */
        .profile-card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header {
            background-color: var(--primary-color);
            border-bottom: 1px solid var(--border-color);
            padding: 1.2rem 1.5rem;
        }

        .card-title {
            margin: 0;
            color: white;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Estilos para estadísticas */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background-color: var(--bg-transparent-light);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background-color: rgba(140, 74, 63, 0.3);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        .stat-label {
            font-size: 1rem;
            color: var(--text-muted);
        }

        /* Estilos para formularios */
        .form-label {
            color: var(--text-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label i {
            color: var(--primary-color);
            width: 16px;
        }

        .form-control {
            background-color: var(--bg-input);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: rgba(0, 0, 0, 0.7);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px var(--primary-color);
            color: var(--text-color);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        /* Grupo de contraseña con botón de visibilidad */
        .password-group {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 5px;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--text-color);
        }

        /* Estilos para tablas */
        .table {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 0;
        }

        .table th {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 600;
        }

        .table td {
            background-color: transparent;
            color: var(--text-color);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem;
            vertical-align: middle;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(140, 74, 63, 0.2);
        }

        /* Estilos para botones */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            gap: 0.5rem;
        }

        .btn i {
            font-size: 0.9rem;
        }

        .btn-sm {
            padding: 0.4rem 0.75rem;
            font-size: 0.8rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-secondary:hover {
            background-color: rgba(108, 117, 125, 1);
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: rgba(25, 135, 84, 1);
        }

        .btn-info {
            background-color: var(--info-color);
            color: white;
        }

        .btn-info:hover {
            background-color: rgba(13, 202, 240, 1);
        }

        .btn-outline-primary {
            background-color: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-outline-info {
            background-color: transparent;
            border: 1px solid var(--info-color);
            color: var(--info-color);
        }

        .btn-outline-info:hover {
            background-color: var(--info-color);
            color: white;
        }

        /* Estilos para badges */
        .badge {
            padding: 0.35rem 0.65rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 6px;
        }

        .bg-warning {
            background-color: var(--warning-color) !important;
        }

        .bg-success {
            background-color: var(--success-color) !important;
        }

        .bg-danger {
            background-color: var(--danger-color) !important;
        }

        .bg-info {
            background-color: var(--info-color) !important;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        /* Header del perfil */
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, rgba(118, 75, 162, 0.8) 100%);
            color: white;
            padding: 2rem 0;
            margin: -2rem -2rem 2rem -2rem;
            border-radius: 16px 16px 0 0;
            backdrop-filter: blur(10px);
        }

        .profile-avatar {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            width: 100px;
            height: 100px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .profile-avatar i {
            color: var(--primary-color);
            font-size: 2.5rem;
        }

        /* Sección de cambio de contraseña */
        .password-section {
            background-color: rgba(140, 74, 63, 0.1);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
            border-left: 4px solid var(--primary-color);
        }

        .section-title {
            color: var(--text-color);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Alertas personalizadas */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(5px);
            border-left: 4px solid var(--info-color);
            background-color: rgba(13, 202, 240, 0.2);
            color: var(--text-color);
        }

        .alert-success {
            background-color: rgba(25, 135, 84, 0.2);
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            border-left: 4px solid var(--danger-color);
        }

        .alert-warning {
            background-color: rgba(255, 193, 7, 0.2);
            border-left: 4px solid var(--warning-color);
        }

        .alert-info {
            background-color: rgba(13, 202, 240, 0.2);
            border-left: 4px solid var(--info-color);
        }

        .alert-warning-custom {
            background-color: rgba(255, 193, 7, 0.2);
            border: 1px solid var(--warning-color);
            border-left: 4px solid var(--warning-color);
        }

        /* Form sections */
        .form-section {
            margin-bottom: 2rem;
        }

        .form-section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-section-title i {
            color: var(--primary-color);
        }

        .required-field::after {
            content: " *";
            color: var(--danger-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
                margin: 1rem;
            }

            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .nav-tabs .nav-link {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .stat-cards {
                grid-template-columns: 1fr;
            }

            .profile-header {
                margin: -1rem -1rem 1rem -1rem;
                padding: 1.5rem 0;
            }

            .btn-group {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
            }

            .btn-group .btn {
                margin-bottom: 0.25rem;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/head.php'; ?>

    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-user"></i> Mi Perfil
            </h1>
            <div class="d-flex gap-2">
                <a href="../../dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>

        <!-- Header del perfil -->
        <div class="profile-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <h1 class="display-5"><?php echo htmlspecialchars($usuario['nombre_completo']); ?></h1>
                        <p class="lead mb-1">
                            <i class="fas fa-briefcase me-2"></i><?php echo htmlspecialchars($usuario['nombre_rol']); ?>
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($usuario['correo_usuario']); ?>
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($usuario['telefono_usuario']); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_cotizaciones; ?></div>
                <div class="stat-label">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Cotizaciones Realizadas
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_trabajos; ?></div>
                <div class="stat-label">
                    <i class="fas fa-tools me-2"></i>Trabajos Asignados
                </div>
            </div>
        </div>

        <!-- Navegación -->
        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit" type="button" role="tab">
                    <i class="fas fa-edit me-2"></i>Editar Perfil
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="quotations-tab" data-bs-toggle="tab" data-bs-target="#quotations" type="button" role="tab">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Mis Cotizaciones
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="jobs-tab" data-bs-toggle="tab" data-bs-target="#jobs" type="button" role="tab">
                    <i class="fas fa-tools me-2"></i>Mis Trabajos
                </button>
            </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="profileTabsContent">
            <!-- Pestaña Editar Perfil -->
            <div class="tab-pane fade show active" id="edit" role="tabpanel">
                <div class="profile-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-edit me-2"></i>Editar Información Personal
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="profileForm" action="actualizar_perfil.php" method="POST">
                            <div class="form-section">
                                <h3 class="form-section-title">
                                    <i class="fas fa-user"></i>
                                    Información Personal
                                </h3>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre_completo" class="form-label required-field">
                                            <i class="fas fa-user"></i>
                                            Nombre Completo
                                        </label>
                                        <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" 
                                               value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="correo_usuario" class="form-label required-field">
                                            <i class="fas fa-envelope"></i>
                                            Correo Electrónico
                                        </label>
                                        <input type="email" class="form-control" id="correo_usuario" name="correo_usuario" 
                                               value="<?php echo htmlspecialchars($usuario['correo_usuario']); ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="telefono_usuario" class="form-label required-field">
                                            <i class="fas fa-phone"></i>
                                            Teléfono
                                        </label>
                                        <input type="text" class="form-control" id="telefono_usuario" name="telefono_usuario" 
                                               value="<?php echo htmlspecialchars($usuario['telefono_usuario']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="username_usuario" class="form-label required-field">
                                            <i class="fas fa-user-circle"></i>
                                            Nombre de Usuario
                                        </label>
                                        <input type="text" class="form-control" id="username_usuario" name="username_usuario" 
                                               value="<?php echo htmlspecialchars($usuario['username_usuario']); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección de Cambio de Contraseña Mejorada -->
                            <div class="form-section">
                                <h3 class="form-section-title">
                                    <i class="fas fa-lock"></i>
                                    Cambiar Contraseña
                                </h3>
                                
                                <div class="password-section">
                                    <div class="alert alert-warning-custom">
                                        <small>
                                            <i class="fas fa-info-circle me-2"></i>
                                            Solo complete los campos de contraseña si desea cambiar la contraseña actual.
                                        </small>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="contrasena_actual" class="form-label">
                                                <i class="fas fa-key"></i>
                                                Contraseña Actual
                                            </label>
                                            <div class="password-group">
                                                <input type="password" class="form-control" id="contrasena_actual" name="contrasena_actual" 
                                                       placeholder="Ingrese su contraseña actual">
                                                <button type="button" class="password-toggle" onclick="togglePassword('contrasena_actual')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="nueva_contrasena" class="form-label">
                                                <i class="fas fa-lock"></i>
                                                Nueva Contraseña
                                            </label>
                                            <div class="password-group">
                                                <input type="password" class="form-control" id="nueva_contrasena" name="nueva_contrasena" 
                                                       placeholder="Ingrese nueva contraseña">
                                                <button type="button" class="password-toggle" onclick="togglePassword('nueva_contrasena')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted">Mínimo 8 caracteres</small>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="confirmar_contrasena" class="form-label">
                                                <i class="fas fa-lock"></i>
                                                Confirmar Nueva Contraseña
                                            </label>
                                            <div class="password-group">
                                                <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" 
                                                       placeholder="Confirme la nueva contraseña">
                                                <button type="button" class="password-toggle" onclick="togglePassword('confirmar_contrasena')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3 d-flex align-items-end">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="mostrar_contrasenas">
                                                <label class="form-check-label" for="mostrar_contrasenas">
                                                    Mostrar todas las contraseñas
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">* Campos obligatorios</small>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Pestaña Mis Cotizaciones -->
            <div class="tab-pane fade" id="quotations" role="tabpanel">
                <div class="profile-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-invoice-dollar me-2"></i>Mis Cotizaciones
                        </h5>
                        <div>
                            <span class="badge bg-primary me-2"><?php echo $total_cotizaciones; ?> cotizaciones</span>
                            <a href="exportar_cotizaciones_excel.php?usuario_id=<?php echo $usuario_id; ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-download me-1"></i> Exportar Excel
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Cliente</th>
                                        <th>Vehículo</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt_cotizaciones = $conex->prepare("
                                        SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo
                                        FROM cotizaciones c
                                        INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
                                        INNER JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
                                        WHERE c.id_usuario = ? AND c.activo = 1
                                        ORDER BY c.fecha_cotizacion DESC
                                        LIMIT 10
                                    ");
                                    $stmt_cotizaciones->execute([$usuario_id]);
                                    $cotizaciones = $stmt_cotizaciones->fetchAll();

                                    if (count($cotizaciones) > 0) {
                                        foreach ($cotizaciones as $cotizacion) {
                                            $badge_class = [
                                                'Pendiente' => 'bg-warning',
                                                'Aprobado' => 'bg-success',
                                                'Rechazada' => 'bg-danger',
                                                'Completada' => 'bg-info'
                                            ][$cotizacion['estado_cotizacion']] ?? 'bg-secondary';
                                    ?>
                                    <tr>
                                        <td>#<?php echo $cotizacion['id_cotizacion']; ?></td>
                                        <td><?php echo htmlspecialchars($cotizacion['nombre_cliente']); ?></td>
                                        <td><?php echo htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo']); ?></td>
                                        <td>$<?php echo number_format($cotizacion['total_cotizacion'], 2); ?></td>
                                        <td>
                                            <span class="badge <?php echo $badge_class; ?>">
                                                <?php echo $cotizacion['estado_cotizacion']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="ver_cotizacion.php?id=<?php echo $cotizacion['id_cotizacion']; ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="editar_cotizacion.php?id=<?php echo $cotizacion['id_cotizacion']; ?>" 
                                                   class="btn btn-sm btn-outline-info" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-file-invoice-dollar fa-3x mb-3"></i>
                                            <p>No has realizado ninguna cotización aún.</p>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($total_cotizaciones > 10) { ?>
                        <div class="text-center mt-3">
                            <a href="mis_cotizaciones.php" class="btn btn-outline-primary">
                                Ver todas las cotizaciones
                            </a>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Pestaña Mis Trabajos -->
            <div class="tab-pane fade" id="jobs" role="tabpanel">
                <div class="profile-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tools me-2"></i>Mis Trabajos
                        </h5>
                        <div>
                            <span class="badge bg-success me-2"><?php echo $total_trabajos; ?> trabajos</span>
                            <a href="exportar_trabajos_excel.php?usuario_id=<?php echo $usuario_id; ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-download me-1"></i> Exportar Excel
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Cotización</th>
                                        <th>Cliente</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt_trabajos = $conex->prepare("
                                        SELECT t.*, c.id_cotizacion, cl.nombre_cliente
                                        FROM trabajos t
                                        INNER JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
                                        INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
                                        WHERE c.id_usuario = ? AND t.activo = 1
                                        ORDER BY t.fecha_inicio DESC
                                        LIMIT 10
                                    ");
                                    $stmt_trabajos->execute([$usuario_id]);
                                    $trabajos = $stmt_trabajos->fetchAll();

                                    if (count($trabajos) > 0) {
                                        foreach ($trabajos as $trabajo) {
                                            $badge_class = [
                                                'Pendiente' => 'bg-warning',
                                                'En progreso' => 'bg-primary',
                                                'Entregado' => 'bg-success',
                                                'Cancelado' => 'bg-danger'
                                            ][$trabajo['estado']] ?? 'bg-secondary';
                                    ?>
                                    <tr>
                                        <td>#<?php echo $trabajo['id_trabajos']; ?></td>
                                        <td>#<?php echo $trabajo['id_cotizacion']; ?></td>
                                        <td><?php echo htmlspecialchars($trabajo['nombre_cliente']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($trabajo['fecha_inicio'])); ?></td>
                                        <td>
                                            <?php 
                                            if ($trabajo['fecha_fin'] != '0000-00-00') {
                                                echo date('d/m/Y', strtotime($trabajo['fecha_fin']));
                                            } else {
                                                echo '<span class="text-muted">-</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $badge_class; ?>">
                                                <?php echo $trabajo['estado']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="ver_trabajo.php?id=<?php echo $trabajo['id_trabajos']; ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="editar_trabajo.php?id=<?php echo $trabajo['id_trabajos']; ?>" 
                                                   class="btn btn-sm btn-outline-info" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-tools fa-3x mb-3"></i>
                                            <p>No tienes trabajos asignados.</p>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($total_trabajos > 10) { ?>
                        <div class="text-center mt-3">
                            <a href="mis_trabajos.php" class="btn btn-outline-primary">
                                Ver todos los trabajos
                            </a>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Activar la pestaña guardada en localStorage o la primera por defecto
        document.addEventListener('DOMContentLoaded', function() {
            var activeTab = localStorage.getItem('activeProfileTab') || 'edit-tab';
            var triggerTab = document.querySelector('#' + activeTab);
            if (triggerTab) {
                bootstrap.Tab.getOrCreateInstance(triggerTab).show();
            }

            // Guardar la pestaña activa cuando cambie
            var tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabEls.forEach(function(tabEl) {
                tabEl.addEventListener('shown.bs.tab', function (event) {
                    localStorage.setItem('activeProfileTab', event.target.id);
                });
            });

            // Configurar el checkbox para mostrar/ocultar todas las contraseñas
            document.getElementById('mostrar_contrasenas').addEventListener('change', function() {
                const show = this.checked;
                const passwordFields = ['contrasena_actual', 'nueva_contrasena', 'confirmar_contrasena'];
                
                passwordFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    const toggleBtn = field.parentNode.querySelector('.password-toggle i');
                    
                    if (field) {
                        field.type = show ? 'text' : 'password';
                        toggleBtn.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
                    }
                });
            });
        });

        // Función para mostrar/ocultar contraseña individual
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const toggleBtn = field.parentNode.querySelector('.password-toggle i');
            
            if (field.type === 'password') {
                field.type = 'text';
                toggleBtn.className = 'fas fa-eye-slash';
            } else {
                field.type = 'password';
                toggleBtn.className = 'fas fa-eye';
            }
        }

        // Validación del formulario
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const nuevaContrasena = document.getElementById('nueva_contrasena').value;
            const confirmarContrasena = document.getElementById('confirmar_contrasena').value;
            const contrasenaActual = document.getElementById('contrasena_actual').value;

            // Si se llena alguna contraseña, validar que se llenen todas
            if (nuevaContrasena || confirmarContrasena || contrasenaActual) {
                if (!contrasenaActual) {
                    e.preventDefault();
                    alert('Para cambiar la contraseña, debe ingresar la contraseña actual.');
                    return;
                }

                if (!nuevaContrasena) {
                    e.preventDefault();
                    alert('Debe ingresar una nueva contraseña.');
                    return;
                }

                if (nuevaContrasena.length < 8) {
                    e.preventDefault();
                    alert('La nueva contraseña debe tener al menos 8 caracteres.');
                    return;
                }

                if (nuevaContrasena !== confirmarContrasena) {
                    e.preventDefault();
                    alert('Las contraseñas nuevas no coinciden.');
                    return;
                }
            }
        });

        // Efectos visuales para los campos
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    </script>
</body>
</html>