<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

$usuario_id = getUserId();

// MOSTRAR MENSAJES DE ÉXITO O ERROR
if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_exito = $_SESSION['mensaje_exito'];
    unset($_SESSION['mensaje_exito']);
}

if (isset($_SESSION['errores_perfil'])) {
    $errores_perfil = $_SESSION['errores_perfil'];
    unset($_SESSION['errores_perfil']);
}

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

// Procesar búsqueda de cotizaciones si es una solicitud AJAX
if (isset($_GET['ajax_cotizaciones'])) {
    header('Content-Type: application/json');

    if (!isset($_GET['q']) || strlen($_GET['q']) < 2) {
        echo json_encode([]);
        exit;
    }

    $searchTerm = '%' . $_GET['q'] . '%';
    $stmt = $conex->prepare("
        SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo
        FROM cotizaciones c
        INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
        INNER JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
        WHERE c.id_usuario = ? AND c.activo = 1 
        AND (cl.nombre_cliente LIKE :search 
        OR v.marca_vehiculo LIKE :search 
        OR v.modelo_vehiculo LIKE :search
        OR v.placa_vehiculo LIKE :search
        OR c.estado_cotizacion LIKE :search)
        ORDER BY c.fecha_cotizacion DESC
        LIMIT 10
    ");
    $stmt->bindParam(':search', $searchTerm);
    $stmt->execute([$usuario_id]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// Procesar búsqueda de trabajos si es una solicitud AJAX
if (isset($_GET['ajax_trabajos'])) {
    header('Content-Type: application/json');

    if (!isset($_GET['q']) || strlen($_GET['q']) < 2) {
        echo json_encode([]);
        exit;
    }

    $searchTerm = '%' . $_GET['q'] . '%';
    $stmt = $conex->prepare("
        SELECT t.*, c.id_cotizacion, cl.nombre_cliente
        FROM trabajos t
        INNER JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
        INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
        WHERE c.id_usuario = ? AND t.activo = 1
        AND (cl.nombre_cliente LIKE :search 
        OR t.estado LIKE :search
        OR t.notas LIKE :search)
        ORDER BY t.fecha_inicio DESC
        LIMIT 10
    ");
    $stmt->bindParam(':search', $searchTerm);
    $stmt->execute([$usuario_id]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// Obtener todas las cotizaciones para mostrar inicialmente
$stmt_cotizaciones_all = $conex->prepare("
    SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo
    FROM cotizaciones c
    INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
    INNER JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    WHERE c.id_usuario = ? AND c.activo = 1
    ORDER BY c.fecha_cotizacion DESC
");
$stmt_cotizaciones_all->execute([$usuario_id]);
$todas_cotizaciones = $stmt_cotizaciones_all->fetchAll();

// Obtener todas los trabajos para mostrar inicialmente
$stmt_trabajos_all = $conex->prepare("
    SELECT t.*, c.id_cotizacion, cl.nombre_cliente
    FROM trabajos t
    INNER JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
    INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
    WHERE c.id_usuario = ? AND t.activo = 1
    ORDER BY t.fecha_inicio DESC
");
$stmt_trabajos_all->execute([$usuario_id]);
$todos_trabajos = $stmt_trabajos_all->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
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

        /* Estilos para el buscador */
        .search-container {
            position: relative;
            margin-bottom: 2rem;
        }

        .search-input {
            width: 100%;
            padding: 1rem;
            border-radius: 8px;
            border: none;
            background-color: var(--bg-input);
            color: var(--text-color);
            font-size: 1rem;
            backdrop-filter: blur(5px);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--primary-color);
            background-color: rgba(0, 0, 0, 0.7);
        }

        .search-input::placeholder {
            color: var(--text-muted);
        }

        .search-results {
            position: absolute;
            width: 100%;
            z-index: 1000;
            background-color: rgba(30, 30, 30, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 0 0 8px 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            border: 1px solid var(--border-color);
            border-top: none;
            max-height: 400px;
            overflow-y: auto;
            display: none;
        }

        .search-result-item {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .search-result-item:hover {
            background-color: var(--primary-color);
        }

        .search-client-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .search-client-info {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .search-client-date {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.5);
        }

        /* Estilos para la lista de clientes */
        .client-list {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .client-item {
            padding: 1.2rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .client-item:hover {
            background-color: rgba(140, 74, 63, 0.3);
        }

        .client-name {
            font-weight: 500;
            font-size: 1.1rem;
            color: var(--text-color);
        }

        .client-description {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 0.3rem;
        }

        .client-arrow {
            margin-left: 1rem;
            opacity: 0.7;
            transition: all 0.3s ease;
            color: var(--text-color);
        }

        .client-item:hover .client-arrow {
            opacity: 1;
            transform: translateX(3px);
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

        .btn-warning {
            background-color: var(--warning-color);
            color: black;
        }

        .btn-warning:hover {
            background-color: rgba(255, 193, 7, 1);
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

        /* Nueva tarjeta flotante para opciones */
        .options-floating-card {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            background-color: rgba(40, 40, 40, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 0;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            z-index: 1001;
            animation: fadeInUp 0.4s ease;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .options-card-header {
            background-color: var(--primary-color);
            padding: 1.2rem;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }

        .options-card-title {
            margin: 0;
            font-size: 1.2rem;
            color: white;
            font-weight: 500;
        }

        .options-card-body {
            padding: 1.5rem;
        }

        .option-item {
            display: flex;
            align-items: center;
            padding: 0.9rem 1rem;
            margin-bottom: 0.8rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
            color: var(--text-color);
            text-decoration: none;
            background-color: rgba(255, 255, 255, 0.08);
        }

        .option-item:last-child {
            margin-bottom: 0;
        }

        .option-item:hover {
            background-color: var(--primary-color);
            transform: translateX(5px);
        }

        .option-item i {
            margin-right: 0.8rem;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .option-close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.3rem;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .option-close:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Estilos para tarjetas flotantes */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate(-50%, -40%);
            }

            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
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

            .options-floating-card {
                width: 90%;
                max-width: 300px;
            }
        }
    </style>
</head>

<body>

    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-user"></i> Mi Perfil
            </h1>
            <div class="d-flex gap-2">
                <a href="../dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Inicio
                </a>
                <a href="cambiar_contrasena.php" class="btn btn-warning">
                    <i class="fas fa-key"></i> Cambiar Contraseña
                </a>
            </div>
        </div>

        <!-- MOSTRAR MENSAJES -->
        <?php if (isset($mensaje_exito)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($mensaje_exito); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($errores_perfil)): ?>
            <?php foreach ($errores_perfil as $error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Mis Cotizaciones
                    </h4>
                    <a href="exportar_cotizaciones_exel.php" class="btn btn-success">
                        <i class="fas fa-download me-1"></i> Exportar Excel
                    </a>
                </div>

                <!-- Sistema de Búsqueda -->
                <div class="search-container">
                    <input type="text" class="search-input" id="searchCotizaciones"
                        placeholder="Buscar cotizaciones por cliente, vehículo, placa o estado...">
                    <div class="search-results" id="searchResultsCotizaciones"></div>
                </div>

                <!-- Lista de Cotizaciones -->
                <div class="client-list" id="allCotizacionesList">
                    <?php if (empty($todas_cotizaciones)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice-dollar fa-3x mb-3" style="color: var(--text-muted);"></i>
                            <h4 style="color: var(--text-muted);">No hay cotizaciones</h4>
                            <p style="color: var(--text-muted);">No has realizado ninguna cotización aún.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($todas_cotizaciones as $cotizacion): ?>
                            <div class="client-item" onclick="showOptions('cotizacion', <?php echo $cotizacion['id_cotizacion']; ?>, '<?php echo htmlspecialchars(addslashes($cotizacion['nombre_cliente'])); ?>')">
                                <div class="client-info">
                                    <div class="client-name">
                                        <?php echo htmlspecialchars($cotizacion['nombre_cliente']); ?>
                                        <span class="badge <?php
                                                            switch ($cotizacion['estado_cotizacion']) {
                                                                case 'Pendiente':
                                                                    echo 'bg-warning';
                                                                    break;
                                                                case 'Aprobado':
                                                                    echo 'bg-success';
                                                                    break;
                                                                case 'Rechazada':
                                                                    echo 'bg-danger';
                                                                    break;
                                                                case 'Completada':
                                                                    echo 'bg-info';
                                                                    break;
                                                                default:
                                                                    echo 'bg-secondary';
                                                            }
                                                            ?> ms-2">
                                            <?php echo $cotizacion['estado_cotizacion']; ?>
                                        </span>
                                    </div>
                                    <div class="client-description">
                                        <i class="fas fa-car me-1"></i>
                                        <?php echo htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo']); ?>
                                        ·
                                        <i class="fas fa-tag me-1"></i>
                                        <?php echo htmlspecialchars($cotizacion['placa_vehiculo']); ?>
                                        ·
                                        <i class="fas fa-dollar-sign me-1"></i>
                                        $<?php echo number_format($cotizacion['total_cotizacion'], 2); ?>
                                        ·
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])); ?>
                                    </div>
                                </div>
                                <div class="client-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pestaña Mis Trabajos -->
            <div class="tab-pane fade" id="jobs" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">
                        <i class="fas fa-tools me-2"></i>Mis Trabajos
                    </h4>
                    <a href="exportar_trabajos_exel.php" class="btn btn-success">
                        <i class="fas fa-download me-1"></i> Exportar Excel
                    </a>
                </div>

                <!-- Sistema de Búsqueda -->
                <div class="search-container">
                    <input type="text" class="search-input" id="searchTrabajos"
                        placeholder="Buscar trabajos por cliente, estado o descripción...">
                    <div class="search-results" id="searchResultsTrabajos"></div>
                </div>

                <!-- Lista de Trabajos -->
                <div class="client-list" id="allTrabajosList">
                    <?php if (empty($todos_trabajos)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-tools fa-3x mb-3" style="color: var(--text-muted);"></i>
                            <h4 style="color: var(--text-muted);">No hay trabajos</h4>
                            <p style="color: var(--text-muted);">No tienes trabajos asignados.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($todos_trabajos as $trabajo): ?>
                            <div class="client-item" onclick="showOptions('trabajo', <?php echo $trabajo['id_trabajos']; ?>, '<?php echo htmlspecialchars(addslashes($trabajo['nombre_cliente'])); ?>')">
                                <div class="client-info">
                                    <div class="client-name">
                                        <?php echo htmlspecialchars($trabajo['nombre_cliente']); ?>
                                        <span class="badge <?php
                                                            switch ($trabajo['estado']) {
                                                                case 'Pendiente':
                                                                    echo 'bg-warning';
                                                                    break;
                                                                case 'En progreso':
                                                                    echo 'bg-primary';
                                                                    break;
                                                                case 'Entregado':
                                                                    echo 'bg-success';
                                                                    break;
                                                                case 'Cancelado':
                                                                    echo 'bg-danger';
                                                                    break;
                                                                default:
                                                                    echo 'bg-secondary';
                                                            }
                                                            ?> ms-2">
                                            <?php echo $trabajo['estado']; ?>
                                        </span>
                                    </div>
                                    <div class="client-description">
                                        <i class="fas fa-calendar me-1"></i>
                                        Inicio: <?php echo date('d/m/Y', strtotime($trabajo['fecha_inicio'])); ?>
                                        <?php if ($trabajo['fecha_fin'] && $trabajo['fecha_fin'] != '0000-00-00'): ?>
                                            · Fin: <?php echo date('d/m/Y', strtotime($trabajo['fecha_fin'])); ?>
                                        <?php endif; ?>
                                        <?php if (!empty($trabajo['notas'])): ?>
                                            · <i class="fas fa-clipboard me-1"></i>
                                            <?php echo htmlspecialchars(substr($trabajo['notas'], 0, 60)); ?>...
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="client-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay y Menú Flotante -->
    <div class="overlay" id="overlay"></div>
    <div class="options-floating-card" id="optionsCard">
        <button class="option-close" id="closeOptionsCard">
            <i class="fas fa-times"></i>
        </button>
        <div class="options-card-header">
            <h3 class="options-card-title" id="optionsCardTitle">Opciones</h3>
        </div>
        <div class="options-card-body" id="optionsCardBody">
            <!-- Las opciones se cargan dinámicamente aquí -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables globales
        let currentItemId = null;
        let currentItemType = null;

        // Mostrar tarjeta de opciones
        function showOptions(type, id, name) {
            currentItemId = id;
            currentItemType = type;

            // Actualizar título de la tarjeta
            document.getElementById('optionsCardTitle').textContent = name;

            // Actualizar opciones según el tipo
            let optionsHtml = '';
            if (type === 'cotizacion') {
                optionsHtml = `
                    <a href="ver_cotizacion.php?id=${id}" class="option-item">
                        <i class="fas fa-eye"></i> Ver Detalles
                    </a>
                    <a href="imprimir_cotizacion.php?id=${id}" class="option-item" target="_blank">
                        <i class="fas fa-print"></i> Imprimir
                    </a>
                `;
            } else if (type === 'trabajo') {
                optionsHtml = `
                    <a href="ver_trabajo.php?id=${id}" class="option-item">
                        <i class="fas fa-eye"></i> Ver Detalles
                    </a>
                `;
            }

            document.getElementById('optionsCardBody').innerHTML = optionsHtml;

            // Mostrar overlay y tarjeta
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('optionsCard').style.display = 'block';
        }

        // Cerrar tarjeta de opciones
        function closeOptionsCard() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('optionsCard').style.display = 'none';
            currentItemId = null;
            currentItemType = null;
        }

        // Event listeners para cerrar tarjeta
        document.getElementById('closeOptionsCard').addEventListener('click', closeOptionsCard);
        document.getElementById('overlay').addEventListener('click', closeOptionsCard);

        // Búsqueda en tiempo real para cotizaciones
        document.getElementById('searchCotizaciones').addEventListener('input', function() {
            const query = this.value.trim();
            const resultsContainer = document.getElementById('searchResultsCotizaciones');
            const allList = document.getElementById('allCotizacionesList');

            if (query.length < 2) {
                resultsContainer.style.display = 'none';
                allList.style.display = 'block';
                return;
            }

            allList.style.display = 'none';

            fetch(`?ajax_cotizaciones=1&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = '';

                    if (data.length === 0) {
                        resultsContainer.innerHTML = '<div class="search-result-item">No se encontraron cotizaciones</div>';
                        resultsContainer.style.display = 'block';
                        return;
                    }

                    data.forEach(cotizacion => {
                        const item = document.createElement('div');
                        item.className = 'search-result-item';
                        item.innerHTML = `
                            <div>
                                <div class="search-client-name">
                                    ${cotizacion.nombre_cliente}
                                    <span class="badge ${getBadgeClass(cotizacion.estado_cotizacion)} ms-2">
                                        ${cotizacion.estado_cotizacion}
                                    </span>
                                </div>
                                <div class="search-client-info">
                                    ${cotizacion.marca_vehiculo} ${cotizacion.modelo_vehiculo} - ${cotizacion.placa_vehiculo}
                                </div>
                                <div class="search-client-date">
                                    $${parseFloat(cotizacion.total_cotizacion).toFixed(2)} · ${new Date(cotizacion.fecha_cotizacion).toLocaleDateString()}
                                </div>
                            </div>
                            <div class="client-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        `;
                        item.addEventListener('click', () => {
                            showOptions('cotizacion', cotizacion.id_cotizacion, cotizacion.nombre_cliente);
                            resultsContainer.style.display = 'none';
                            document.getElementById('searchCotizaciones').value = '';
                            allList.style.display = 'block';
                        });
                        resultsContainer.appendChild(item);
                    });

                    resultsContainer.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error en la búsqueda:', error);
                    resultsContainer.innerHTML = '<div class="search-result-item">Error en la búsqueda</div>';
                    resultsContainer.style.display = 'block';
                });
        });

        // Búsqueda en tiempo real para trabajos
        document.getElementById('searchTrabajos').addEventListener('input', function() {
            const query = this.value.trim();
            const resultsContainer = document.getElementById('searchResultsTrabajos');
            const allList = document.getElementById('allTrabajosList');

            if (query.length < 2) {
                resultsContainer.style.display = 'none';
                allList.style.display = 'block';
                return;
            }

            allList.style.display = 'none';

            fetch(`?ajax_trabajos=1&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = '';

                    if (data.length === 0) {
                        resultsContainer.innerHTML = '<div class="search-result-item">No se encontraron trabajos</div>';
                        resultsContainer.style.display = 'block';
                        return;
                    }

                    data.forEach(trabajo => {
                        const item = document.createElement('div');
                        item.className = 'search-result-item';
                        item.innerHTML = `
                            <div>
                                <div class="search-client-name">
                                    ${trabajo.nombre_cliente}
                                    <span class="badge ${getBadgeClass(trabajo.estado)} ms-2">
                                        ${trabajo.estado}
                                    </span>
                                </div>
                                <div class="search-client-info">
                                    ${trabajo.notas ? trabajo.notas.substring(0, 60) + '...' : 'Sin descripción'}
                                </div>
                                <div class="search-client-date">
                                    ${new Date(trabajo.fecha_inicio).toLocaleDateString()}
                                </div>
                            </div>
                            <div class="client-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        `;
                        item.addEventListener('click', () => {
                            showOptions('trabajo', trabajo.id_trabajos, trabajo.nombre_cliente);
                            resultsContainer.style.display = 'none';
                            document.getElementById('searchTrabajos').value = '';
                            allList.style.display = 'block';
                        });
                        resultsContainer.appendChild(item);
                    });

                    resultsContainer.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error en la búsqueda:', error);
                    resultsContainer.innerHTML = '<div class="search-result-item">Error en la búsqueda</div>';
                    resultsContainer.style.display = 'block';
                });
        });

        // Función para obtener clase de badge según estado
        function getBadgeClass(estado) {
            switch (estado) {
                case 'Pendiente':
                    return 'bg-warning';
                case 'Aprobado':
                    return 'bg-success';
                case 'Rechazada':
                    return 'bg-danger';
                case 'Completada':
                    return 'bg-info';
                case 'En progreso':
                    return 'bg-primary';
                case 'Entregado':
                    return 'bg-success';
                case 'Cancelado':
                    return 'bg-danger';
                default:
                    return 'bg-secondary';
            }
        }

        // Cerrar resultados al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                document.getElementById('searchResultsCotizaciones').style.display = 'none';
                document.getElementById('searchResultsTrabajos').style.display = 'none';
            }
        });

        // Prevenir envío del formulario de búsqueda
        document.querySelectorAll('.search-container').forEach(container => {
            container.addEventListener('submit', function(e) {
                e.preventDefault();
            });
        });

        // Activar la pestaña guardada en localStorage
        document.addEventListener('DOMContentLoaded', function() {
            var activeTab = localStorage.getItem('activeProfileTab') || 'edit-tab';
            var triggerTab = document.querySelector('#' + activeTab);
            if (triggerTab) {
                bootstrap.Tab.getOrCreateInstance(triggerTab).show();
            }

            // Guardar la pestaña activa cuando cambie
            var tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabEls.forEach(function(tabEl) {
                tabEl.addEventListener('shown.bs.tab', function(event) {
                    localStorage.setItem('activeProfileTab', event.target.id);
                });
            });
        });

        // Validación del formulario - SIMPLIFICADA
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            // Validación básica de campos requeridos
            const nombre = document.getElementById('nombre_completo').value.trim();
            const correo = document.getElementById('correo_usuario').value.trim();
            const telefono = document.getElementById('telefono_usuario').value.trim();
            const username = document.getElementById('username_usuario').value.trim();

            if (!nombre || !correo || !telefono || !username) {
                e.preventDefault();
                mostrarError('Todos los campos obligatorios deben ser completados.');
                return;
            }

            // Validación básica de email
            if (!isValidEmail(correo)) {
                e.preventDefault();
                mostrarError('El formato del correo electrónico no es válido.');
                return;
            }
        });

        // Función para validar email
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Función para mostrar errores
        function mostrarError(mensaje) {
            const alerta = document.createElement('div');
            alerta.className = 'alert alert-danger alert-dismissible fade show';
            alerta.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

            // Insertar después del header
            const header = document.querySelector('.header-section');
            header.parentNode.insertBefore(alerta, header.nextSibling);

            // Hacer scroll a la alerta
            alerta.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        // Funciones para exportar (simuladas por ahora)
        function exportarCotizaciones() {
            alert('Función de exportación de cotizaciones en desarrollo');
        }

        function exportarTrabajos() {
            alert('Función de exportación de trabajos en desarrollo');
        }
    </script>
</body>

</html>