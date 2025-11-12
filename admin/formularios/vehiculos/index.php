<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Inicializar variables de sesión para mensajes si no existen
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
    $_SESSION['tipo_mensaje'] = '';
}

// Procesar mensajes de otras operaciones (crear, editar, eliminar)
if (isset($_SESSION['mensaje']) && !empty($_SESSION['mensaje'])) {
    // El mensaje ya está en sesión, se mostrará en la interfaz
}

// Obtener estadísticas generales - SOLO VEHÍCULOS ACTIVOS
$stats = $conex->query("SELECT 
    COUNT(*) as total_vehiculos,
    (SELECT COUNT(*) FROM vehiculos WHERE DATE(fecha_registro) = CURDATE() AND activo = 1) as registros_hoy,
    (SELECT DATE(fecha_registro) 
     FROM vehiculos 
     WHERE activo = 1 
     ORDER BY id_vehiculo DESC 
     LIMIT 1) as ultima_fecha,
    (SELECT COUNT(*) FROM vehiculos WHERE activo = 0) as en_papelera
FROM vehiculos WHERE activo = 1")->fetch(PDO::FETCH_ASSOC);

// Formatear la última fecha
if (!empty($stats['ultima_fecha'])) {
    $stats['ultima_fecha'] = date('d/m/Y', strtotime($stats['ultima_fecha']));
} else {
    $stats['ultima_fecha'] = 'N/A';
}

// Obtener los 4 vehículos más recientes - SOLO ACTIVOS
$vehiculosRecientes = $conex->query("SELECT * FROM vehiculos WHERE activo = 1 ORDER BY id_vehiculo DESC LIMIT 4")->fetchAll();

// Obtener todos los vehículos para la pestaña de buscar - SOLO ACTIVOS
$todosVehiculos = $conex->query("SELECT * FROM vehiculos WHERE activo = 1 ORDER BY marca_vehiculo ASC")->fetchAll();

// Obtener vehículos eliminados (papelera)
$vehiculosEliminados = $conex->query("SELECT * FROM vehiculos WHERE activo = 0 ORDER BY fecha_eliminacion DESC")->fetchAll();

// Procesar búsqueda si es una solicitud AJAX
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    if (!isset($_GET['q']) || strlen($_GET['q']) < 2) {
        echo json_encode([]);
        exit;
    }

    $searchTerm = '%' . $_GET['q'] . '%';
    $stmt = $conex->prepare("
        SELECT * FROM vehiculos 
        WHERE (marca_vehiculo LIKE :search 
        OR modelo_vehiculo LIKE :search 
        OR placa_vehiculo LIKE :search)
        AND activo = 1
        ORDER BY marca_vehiculo ASC 
        LIMIT 10
    ");
    $stmt->bindParam(':search', $searchTerm);
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

require_once __DIR__ . '/../../includes/head.php';
$title = 'Gestión de Vehículos';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .search-vehicle-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .search-vehicle-info {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        /* Estilos para la lista de vehículos */
        .vehicle-list {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .vehicle-item {
            padding: 1.2rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .vehicle-item:hover {
            background-color: rgba(140, 74, 63, 0.3);
        }

        .vehicle-name {
            font-weight: 500;
            font-size: 1.1rem;
            color: var(--text-color);
        }

        .vehicle-description {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 0.3rem;
        }

        .vehicle-arrow {
            margin-left: 1rem;
            opacity: 0.7;
            transition: all 0.3s ease;
            color: var(--text-color);
        }

        .vehicle-item:hover .vehicle-arrow {
            opacity: 1;
            transform: translateX(3px);
        }

        /* Estilos para elementos eliminados */
        .deleted-item {
            opacity: 0.8;
            background-color: rgba(220, 53, 69, 0.1);
        }
        
        .deleted-item:hover {
            background-color: rgba(220, 53, 69, 0.2);
        }
        
        .deleted-badge {
            background-color: var(--danger-color);
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-left: 0.5rem;
        }
        
        .days-left {
            font-size: 0.8rem;
            color: var(--warning-color);
            margin-top: 0.25rem;
        }

        /* Estilos para tarjetas de resumen */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            background-color: var(--bg-transparent-light);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .summary-card:hover {
            transform: translateY(-5px);
            background-color: rgba(140, 74, 63, 0.3);
        }

        .summary-card h3 {
            margin-top: 0;
            font-size: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .summary-card p {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0;
            color: var(--text-color);
        }

        /* Estilos para botones */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            gap: 0.5rem;
        }

        .btn i {
            font-size: 0.9rem;
        }

        .btn-sm {
            padding: 0.35rem 0.5rem;
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

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: rgba(220, 53, 69, 1);
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: rgba(25, 135, 84, 1);
        }

        /* Estilos para alertas */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .alert-success {
            background-color: rgba(25, 135, 84, 0.2);
            border-left: 4px solid var(--success-color);
            color: white;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            border-left: 4px solid var(--danger-color);
            color: white;
        }

        .alert-warning {
            background-color: rgba(255, 193, 7, 0.2);
            border-left: 4px solid var(--warning-color);
            color: white;
        }

        .alert-info {
            background-color: rgba(13, 202, 240, 0.2);
            border-left: 4px solid var(--info-color);
            color: white;
        }

        /* Estilos para tarjetas de vehículos */
        .vehicle-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .vehicle-card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
        }

        .vehicle-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            background-color: rgba(140, 74, 63, 0.2);
        }

        .vehicle-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .vehicle-card-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .vehicle-card-badge {
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .vehicle-card-body {
            margin-bottom: 1.5rem;
        }

        .vehicle-card-detail {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
            color: var(--text-color);
        }

        .vehicle-card-detail i {
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }

        /* Flecha para tarjetas */
        .edit-arrow {
            position: absolute;
            bottom: 15px;
            right: 15px;
            color: var(--primary-color);
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .edit-arrow:hover {
            transform: translateX(3px);
            color: var(--text-color);
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

            .summary-cards {
                grid-template-columns: 1fr;
            }

            .vehicle-cards {
                grid-template-columns: 1fr;
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
                <i class="fas fa-car"></i> Gesti&oacute;n de Veh&iacute;culos
            </h1>
            <div class="d-flex gap-2">
                <a href="crear.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Vehículo
                </a>
                <a href="../../dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if (!empty($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
                <div>
                    <i class="fas fa-<?=
                                        $_SESSION['tipo_mensaje'] === 'success' ? 'check-circle' : ($_SESSION['tipo_mensaje'] === 'danger' ? 'times-circle' : ($_SESSION['tipo_mensaje'] === 'warning' ? 'exclamation-triangle' : 'info-circle'))
                                        ?> me-2"></i>
                    <?= $_SESSION['mensaje'] ?>
                </div>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
            </div>
            <?php
            $_SESSION['mensaje'] = '';
            $_SESSION['tipo_mensaje'] = '';
            ?>
        <?php endif; ?>

        <!-- Mensajes adicionales -->
        <?php if (isset($_SESSION['mensaje_adicional'])): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= $_SESSION['mensaje_adicional'] ?>
                <?php if (isset($_SESSION['errores']) && !empty($_SESSION['errores'])): ?>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($_SESSION['errores'] as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <?php
            unset($_SESSION['mensaje_adicional']);
            unset($_SESSION['errores']);
            ?>
        <?php endif; ?>

        <!-- Tarjetas de resumen -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total de Vehículos</h3>
                <p><?= $stats['total_vehiculos'] ?></p>
            </div>
            <div class="summary-card">
                <h3>Último Registro</h3>
                <p><?= $stats['ultima_fecha'] ?></p>
            </div>
            <div class="summary-card">
                <h3>Registros Hoy</h3>
                <p><?= $stats['registros_hoy'] ?></p>
            </div>
            <div class="summary-card">
                <h3>En Papelera</h3>
                <p><?= $stats['en_papelera'] ?></p>
            </div>
        </div>

        <!-- Pestañas de navegación -->
        <ul class="nav nav-tabs" id="vehicleTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="search-tab" data-bs-toggle="tab" data-bs-target="#search" type="button" role="tab">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="recent-tab" data-bs-toggle="tab" data-bs-target="#recent" type="button" role="tab">
                    <i class="fas fa-clock"></i> Recientes
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="delete-tab" data-bs-toggle="tab" data-bs-target="#delete" type="button" role="tab">
                    <i class="fas fa-trash-alt"></i> Papelera
                </button>
            </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="vehicleTabsContent">
            <!-- Pestaña de búsqueda -->
            <div class="tab-pane fade show active" id="search" role="tabpanel">
                <!-- Buscador -->
                <div class="search-container">
                    <input type="text" id="searchInput" class="search-input" placeholder="Buscar vehículos por marca, modelo o placa...">
                    <div id="searchResults" class="search-results"></div>
                </div>

                <!-- Resultados iniciales (todos los vehículos) -->
                <div class="vehicle-list" id="allVehiclesList">
                    <?php foreach ($todosVehiculos as $vehiculo): ?>
                        <div class="vehicle-item" onclick="showOptions(<?= $vehiculo['id_vehiculo'] ?>, '<?= htmlspecialchars(addslashes($vehiculo['marca_vehiculo'] . ' ' . $vehiculo['modelo_vehiculo'])) ?>')">
                            <div class="vehicle-info">
                                <div class="vehicle-name"><?= htmlspecialchars($vehiculo['marca_vehiculo'] . ' ' . $vehiculo['modelo_vehiculo']) ?></div>
                                <div class="vehicle-description">
                                    <?= htmlspecialchars($vehiculo['placa_vehiculo']) ?> · 
                                    <?= htmlspecialchars($vehiculo['anio_vehiculo'] ?? 'N/A') ?>
                                </div>
                            </div>
                            <div class="vehicle-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Pestaña de vehículos recientes -->
            <div class="tab-pane fade" id="recent" role="tabpanel">
                
                <div class="vehicle-cards">
                    <?php foreach ($vehiculosRecientes as $vehiculo): ?>
                        <div class="vehicle-card" onclick="showOptions(<?= $vehiculo['id_vehiculo'] ?>, '<?= htmlspecialchars(addslashes($vehiculo['marca_vehiculo'] . ' ' . $vehiculo['modelo_vehiculo'])) ?>')">
                            <div class="vehicle-card-header">
                                <h3 class="vehicle-card-title"><?= htmlspecialchars($vehiculo['marca_vehiculo']) ?></h3>
                                <span class="vehicle-card-badge">ID: <?= $vehiculo['id_vehiculo'] ?></span>
                            </div>
                            <div class="vehicle-card-body">
                                <div class="vehicle-card-detail">
                                    <i class="fas fa-car"></i>
                                    <span><?= htmlspecialchars($vehiculo['modelo_vehiculo']) ?></span>
                                </div>
                                <div class="vehicle-card-detail">
                                    <i class="fas fa-tag"></i>
                                    <span><?= htmlspecialchars($vehiculo['placa_vehiculo']) ?></span>
                                </div>
                                <div class="vehicle-card-detail">
                                    <i class="fas fa-calendar"></i>
                                    <span>Año: <?= htmlspecialchars($vehiculo['anio_vehiculo'] ?? 'N/A') ?></span>
                                </div>
                                <?php if (!empty($vehiculo['color_vehiculo'])): ?>
                                <div class="vehicle-card-detail">
                                    <i class="fas fa-palette"></i>
                                    <span>Color: <?= htmlspecialchars($vehiculo['color_vehiculo']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="edit-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Pestaña de papelera -->
            <div class="tab-pane fade" id="delete" role="tabpanel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <?php if (isAdmin() && !empty($vehiculosEliminados)): ?>
            <button type="button" class="btn btn-outline-warning btn-sm" onclick="vaciarPapelera()">
                <i class="fas fa-broom me-1"></i> Vaciar papelera
            </button>
        <?php endif; ?>
    </div>
                
                <?php if (count($vehiculosEliminados) > 0): ?>
                    <div class="vehicle-list">
                        <?php foreach ($vehiculosEliminados as $vehiculo): ?>
                            <div class="vehicle-item deleted-item">
                                <div class="vehicle-info">
                                    <div class="vehicle-name"><?= htmlspecialchars($vehiculo['marca_vehiculo'] . ' ' . $vehiculo['modelo_vehiculo']) ?></div>
                                    <div class="vehicle-description">
                                        <?= htmlspecialchars($vehiculo['placa_vehiculo']) ?> · 
                                        <?= htmlspecialchars($vehiculo['anio_vehiculo'] ?? 'N/A') ?>
                                        <br>
                                        <small>Eliminado: <?= $vehiculo['fecha_eliminacion'] ? date('d/m/Y H:i', strtotime($vehiculo['fecha_eliminacion'])) : 'Fecha no disponible' ?></small>
                                    </div>
                                </div>
                                <div class="vehicle-actions">
                                    <a href="restaurar.php?id=<?= $vehiculo['id_vehiculo'] ?>" class="btn btn-success btn-sm" onclick="return confirm('¿Restaurar este vehículo?')">
                                        <i class="fas fa-undo"></i> Restaurar
                                    </a>
                                    <?php if (isAdmin()): ?>
                                    <a href="eliminar_permanentemente.php?id=<?= $vehiculo['id_vehiculo'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar permanentemente? Esta acción no se puede deshacer.')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-trash-alt fa-3x mb-3" style="color: var(--text-muted);"></i>
            <h4 style="color: var(--text-muted);">La papelera está vacía</h4>
            <p style="color: var(--text-muted);">No hay Vehículos eliminados</p>
        </div>
    <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Overlay -->
    <div class="overlay" id="overlay" onclick="closeOptions()"></div>

    <!-- Tarjeta flotante para opciones -->
    <div class="options-floating-card" id="optionsCard">
        <div class="options-card-header">
            <h3 class="options-card-title" id="optionsVehicleName">Opciones de Vehículo</h3>
            <button class="option-close" onclick="closeOptions()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="options-card-body" id="optionsCardContent">
            <!-- El contenido se cargará dinámicamente -->
        </div>
    </div>
<?php include '../../includes/bot.php'; ?>
    <script>setHelpModule('Vehiculos');</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables globales
        let currentVehicleId = null;

        // Función para mostrar opciones de un vehículo
        function showOptions(id, vehicleName) {
            currentVehicleId = id;
            
            // Actualizar el nombre del vehículo en la cabecera
            document.getElementById('optionsVehicleName').textContent = 'Opciones: ' + vehicleName;
            
            // Crear el contenido de las opciones
            const optionsContent = `
                <div class="option-item" onclick="window.location.href='ver.php?id=${id}'">
                    <i class="fas fa-eye"></i>
                    <span>Ver detalles</span>
                </div>
                <div class="option-item" onclick="window.location.href='editar.php?id=${id}'">
                    <i class="fas fa-edit"></i>
                    <span>Editar</span>
                </div>
                <div class="option-item" onclick="if(confirm('¿Estás seguro de mover este vehículo a la papelera?')) window.location.href='eliminar.php?id=${id}'">
                    <i class="fas fa-trash"></i>
                    <span>Mover a papelera</span>
                </div>
            `;
            
            document.getElementById('optionsCardContent').innerHTML = optionsContent;
            
            // Mostrar overlay y tarjeta de opciones
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('optionsCard').style.display = 'block';
        }

        // Función para cerrar opciones
        function closeOptions() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('optionsCard').style.display = 'none';
            currentVehicleId = null;
        }

        // Función para vaciar papelera
        function vaciarPapelera() {
            if (confirm('¿Estás seguro de que deseas vaciar la papelera?\n\nSe eliminarán permanentemente todos los vehículos en la papelera.\n\n⚠️ Esta acción NO se puede deshacer.')) {
                window.location.href = 'vaciar_papelera.php';
            }
        }

        // Búsqueda en tiempo real
        document.getElementById('searchInput').addEventListener('input', function() {
            const query = this.value.trim();
            const resultsContainer = document.getElementById('searchResults');
            const allVehiclesList = document.getElementById('allVehiclesList');
            
            if (query.length === 0) {
                resultsContainer.style.display = 'none';
                allVehiclesList.style.display = 'block';
                return;
            } else {
                allVehiclesList.style.display = 'none';
            }
            
            if (query.length < 2) {
                resultsContainer.style.display = 'none';
                return;
            }
            
            fetch(`?ajax=1&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = '';
                    
                    if (data.length === 0) {
                        resultsContainer.innerHTML = '<div class="search-result-item">No se encontraron resultados</div>';
                        resultsContainer.style.display = 'block';
                        return;
                    }
                    
                    data.forEach(vehicle => {
                        const item = document.createElement('div');
                        item.className = 'search-result-item';
                        item.innerHTML = `
                            <div onclick="showOptions(${vehicle.id_vehiculo}, '${(vehicle.marca_vehiculo + ' ' + vehicle.modelo_vehiculo).replace(/'/g, "\\'")}')">
                                <div class="search-vehicle-name">${vehicle.marca_vehiculo} ${vehicle.modelo_vehiculo}</div>
                                <div class="search-vehicle-info">${vehicle.placa_vehiculo} · ${vehicle.anio_vehiculo || 'N/A'}</div>
                            </div>
                            <div class="vehicle-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        `;
                        resultsContainer.appendChild(item);
                    });
                    
                    resultsContainer.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultsContainer.style.display = 'none';
                });
        });

        // Cerrar resultados de búsqueda al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                document.getElementById('searchResults').style.display = 'none';
            }
        });

        // Cerrar tarjeta de opciones con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeOptions();
            }
        });
    </script>
</body>
</html>