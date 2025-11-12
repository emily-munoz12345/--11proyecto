<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos (solo Admin y Vendedor)
if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Inicializar variables de sesión para mensajes
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
    $_SESSION['tipo_mensaje'] = '';
}

// Procesar eliminación a papelera (NO verifica asociaciones)
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    try {
        // Eliminación lógica (soft delete) - SIN verificar cotizaciones
        $stmt = $conex->prepare("UPDATE servicios SET activo = 0, fecha_eliminacion = NOW() WHERE id_servicio = ?");
        if ($stmt->execute([$id])) {
            $_SESSION['mensaje'] = 'Servicio movido a la papelera correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al mover a la papelera';
            $_SESSION['tipo_mensaje'] = 'danger';
        }
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'Error al eliminar: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    header('Location: index.php');
    exit;
}

// Obtener estadísticas generales
$stats = $conex->query("SELECT 
    COUNT(*) as total_servicios,
    MAX(fecha_registro) as ultimo_registro,
    (SELECT COUNT(*) FROM servicios WHERE DATE(fecha_registro) = CURDATE() AND activo = 1) as registros_hoy,
    (SELECT COUNT(*) FROM servicios WHERE activo = 0) as en_papelera
FROM servicios WHERE activo = 1")->fetch(PDO::FETCH_ASSOC);

// Búsqueda y paginación
$busqueda = $_GET['busqueda'] ?? '';
$pagina = max(1, intval($_GET['pagina'] ?? 1));
$porPagina = 10;

$sql = "SELECT * FROM servicios WHERE activo = 1";
$params = [];
$where = '';

if (!empty($busqueda)) {
    $where = " AND (nombre_servicio LIKE ? OR descripcion_servicio LIKE ? OR categoria_servicio LIKE ?)";
    $params = ["%$busqueda%", "%$busqueda%", "%$busqueda%"];
}

// Contar total para paginación
$sqlCount = "SELECT COUNT(*) FROM servicios WHERE activo = 1 $where";
$stmt = $conex->prepare($sqlCount);
$stmt->execute($params);
$totalServicios = $stmt->fetchColumn();
$totalPaginas = ceil($totalServicios / $porPagina);

// Obtener servicios activos
$offset = ($pagina - 1) * $porPagina;
$sql .= " $where ORDER BY nombre_servicio ASC LIMIT $offset, $porPagina";

$stmt = $conex->prepare($sql);
if (!empty($busqueda)) {
    $stmt->execute($params);
} else {
    $stmt->execute();
}
$servicios = $stmt->fetchAll();

// Obtener servicios recientes (últimos 4 activos)
$serviciosRecientes = $conex->query("SELECT * FROM servicios WHERE activo = 1 ORDER BY id_servicio DESC LIMIT 4")->fetchAll();

// Obtener todos los servicios activos para la pestaña de búsqueda
$todosServicios = $conex->query("SELECT * FROM servicios WHERE activo = 1 ORDER BY nombre_servicio ASC")->fetchAll();

// Obtener servicios en la papelera (eliminados)
$serviciosPapelera = $conex->query("SELECT * FROM servicios WHERE activo = 0 ORDER BY fecha_eliminacion DESC")->fetchAll();

// Procesar búsqueda si es una solicitud AJAX
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    if (!isset($_GET['q']) || strlen($_GET['q']) < 2) {
        echo json_encode([]);
        exit;
    }

    $searchTerm = '%' . $_GET['q'] . '%';
    $stmt = $conex->prepare("SELECT * FROM servicios 
                            WHERE activo = 1 
                            AND (nombre_servicio LIKE :search 
                            OR descripcion_servicio LIKE :search 
                            OR categoria_servicio LIKE :search)
                            ORDER BY nombre_servicio ASC 
                            LIMIT 10");
    $stmt->bindParam(':search', $searchTerm);
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Servicios</title>
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

        .search-service-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .search-service-info {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .search-service-date {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.5);
        }

        /* Estilos para la lista de servicios */
        .service-list {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .service-item {
            padding: 1.2rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .service-item:hover {
            background-color: rgba(140, 74, 63, 0.3);
        }

        .service-name {
            font-weight: 500;
            font-size: 1.1rem;
            color: var(--text-color);
        }

        .service-description {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 0.3rem;
        }

        .service-arrow {
            margin-left: 1rem;
            opacity: 0.7;
            transition: all 0.3s ease;
            color: var(--text-color);
        }

        .service-item:hover .service-arrow {
            opacity: 1;
            transform: translateX(3px);
        }

        /* Estilos para acciones en la lista */
        .service-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
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

        .btn-info {
            background-color: var(--info-color);
            color: white;
        }

        .btn-info:hover {
            background-color: rgba(13, 202, 240, 1);
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: rgba(25, 135, 84, 1);
        }

        .btn-warning {
            background-color: var(--warning-color);
            color: black;
        }

        .btn-warning:hover {
            background-color: rgba(255, 193, 7, 1);
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

        /* Estilos para alertas */
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

        /* Estilos para tarjetas de servicios */
        .service-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .service-card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            background-color: rgba(140, 74, 63, 0.2);
        }

        .service-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .service-card-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .service-card-badge {
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .service-card-body {
            margin-bottom: 1.5rem;
        }

        .service-card-detail {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
            color: var(--text-color);
        }

        .service-card-detail i {
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }

        /* Flecha para tarjetas */
        .service-arrow-card {
            position: absolute;
            bottom: 15px;
            right: 15px;
            color: var(--primary-color);
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .service-arrow-card:hover {
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
                margin: 1rem;
                padding: 1.5rem;
            }

            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-tabs {
                flex-wrap: wrap;
            }

            .nav-link {
                border-radius: 8px;
                margin-bottom: 0.5rem;
            }

            .service-cards {
                grid-template-columns: 1fr;
            }

            .options-floating-card {
                width: 90%;
                max-width: 300px;
            }

            .summary-cards {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-concierge-bell"></i> Gesti&oacute;n de Servicios
            </h1>
            <div class="d-flex gap-2">
                <a href="crear.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Servicio
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

        <!-- Estadísticas rápidas -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total de Servicios</h3>
                <p><?= $stats['total_servicios'] ?></p>
            </div>
            <div class="summary-card">
                <h3>Último Registro</h3>
                <p><?= $stats['ultimo_registro'] ? date('d/m/Y', strtotime($stats['ultimo_registro'])) : 'N/A' ?></p>
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
        <ul class="nav nav-tabs" id="serviceTabs" role="tablist">
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
        <div class="tab-content" id="serviceTabsContent">
            <!-- Pestaña de búsqueda -->
            <div class="tab-pane fade show active" id="search" role="tabpanel">
                <!-- Buscador -->
                <div class="search-container">
                    <input type="text" class="search-input" id="searchInput" placeholder="Buscar servicio por nombre, descripción o categoría..." autocomplete="off">
                    <div class="search-results" id="searchResults"></div>
                </div>

                <!-- Resultados iniciales (todos los servicios activos) -->
                <div class="service-list" id="allServicesList">
                    <?php foreach ($todosServicios as $servicio): ?>
                        <div class="service-item" data-service-id="<?= $servicio['id_servicio'] ?>" onclick="showOptionsCard(<?= $servicio['id_servicio'] ?>, '<?= htmlspecialchars(addslashes($servicio['nombre_servicio'])) ?>')">
                            <div class="service-info">
                                <div class="service-name"><?= htmlspecialchars($servicio['nombre_servicio']) ?></div>
                                <div class="service-description">
                                    <?= htmlspecialchars($servicio['categoria_servicio']) ?> · 
                                    $<?= number_format($servicio['precio_servicio'], 0, ',', '.') ?> · 
                                    <?= htmlspecialchars($servicio['tiempo_estimado']) ?>
                                </div>
                            </div>
                            <div class="service-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Paginación -->
                <?php if ($totalPaginas > 1): ?>
                    <nav aria-label="Paginación" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($pagina > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?pagina=<?= $pagina-1 ?>&busqueda=<?= urlencode($busqueda) ?>">Anterior</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                    <a class="page-link" href="?pagina=<?= $i ?>&busqueda=<?= urlencode($busqueda) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($pagina < $totalPaginas): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?pagina=<?= $pagina+1 ?>&busqueda=<?= urlencode($busqueda) ?>">Siguiente</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>

            <!-- Pestaña de servicios recientes -->
            <div class="tab-pane fade" id="recent" role="tabpanel">
                <div class="service-cards">
                    <?php foreach ($serviciosRecientes as $servicio): ?>
                        <div class="service-card" onclick="showOptionsCard(<?= $servicio['id_servicio'] ?>, '<?= htmlspecialchars(addslashes($servicio['nombre_servicio'])) ?>')">
                            <div class="service-card-header">
                                <h3 class="service-card-title"><?= htmlspecialchars($servicio['nombre_servicio']) ?></h3>
                                <span class="service-card-badge"><?= htmlspecialchars($servicio['categoria_servicio']) ?></span>
                            </div>
                            <div class="service-card-body">
                                <div class="service-card-detail">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span>$<?= number_format($servicio['precio_servicio'], 0, ',', '.') ?></span>
                                </div>
                                <div class="service-card-detail">
                                    <i class="fas fa-clock"></i>
                                    <span><?= htmlspecialchars($servicio['tiempo_estimado']) ?></span>
                                </div>
                                <?php if (!empty($servicio['descripcion_servicio'])): ?>
                                    <div class="service-card-detail">
                                        <i class="fas fa-file-alt"></i>
                                        <span><?= htmlspecialchars(substr($servicio['descripcion_servicio'], 0, 100)) . (strlen($servicio['descripcion_servicio']) > 100 ? '...' : '') ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="service-arrow-card">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Pestaña de papelera -->
            <div class="tab-pane fade" id="delete" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <?php if (isAdmin() && !empty($serviciosPapelera)): ?>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="vaciarPapelera()">
                            <i class="fas fa-broom me-1"></i> Vaciar papelera
                        </button>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($serviciosPapelera)): ?>
                    <div class="service-list">
                        <?php foreach ($serviciosPapelera as $servicio): ?>
                            <div class="service-item deleted-item">
                                <div class="service-info">
                                    <div class="service-name"><?= htmlspecialchars($servicio['nombre_servicio']) ?></div>
                                    <div class="service-description">
                                        <?= htmlspecialchars($servicio['categoria_servicio']) ?> · 
                                        $<?= number_format($servicio['precio_servicio'], 0, ',', '.') ?> · 
                                        <?= htmlspecialchars($servicio['tiempo_estimado']) ?>
                                        <br>
                                        <small>Eliminado: <?= $servicio['fecha_eliminacion'] ? date('d/m/Y H:i', strtotime($servicio['fecha_eliminacion'])) : 'Fecha no disponible' ?></small>
                                    </div>
                                </div>
                                <div class="service-actions">
                                    <a href="restaurar.php?id=<?= $servicio['id_servicio'] ?>" class="btn btn-success btn-sm" onclick="return confirm('¿Restaurar servicio <?= htmlspecialchars(addslashes($servicio['nombre_servicio'])) ?>?')">
                                        <i class="fas fa-undo"></i> Restaurar
                                    </a>
                                    <?php if (isAdmin()): ?>
                                    <a href="eliminar_permanentemente.php?id=<?= $servicio['id_servicio'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿ESTÁS SEGURO? Esta acción eliminará permanentemente el servicio <?= htmlspecialchars(addslashes($servicio['nombre_servicio'])) ?> y no se podrá recuperar.')">
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
                        <p style="color: var(--text-muted);">No hay servicios eliminados</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php include '../../includes/bot.php'; ?>
    <script>setHelpModule('Servicios');</script>

    <!-- Overlay para tarjetas flotantes -->
    <div class="overlay" id="overlay" onclick="hideOptionsCard()"></div>

    <!-- Tarjeta flotante para opciones -->
    <div class="options-floating-card" id="optionsCard">
        <button class="option-close" onclick="hideOptionsCard()">
            <i class="fas fa-times"></i>
        </button>
        <div class="options-card-header">
            <h3 class="options-card-title" id="optionsCardTitle">Opciones</h3>
        </div>
        <div class="options-card-body">
            <a href="#" class="option-item" id="viewOption">
                <i class="fas fa-eye"></i> Ver Detalles
            </a>
            <a href="#" class="option-item" id="editOption">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="#" class="option-item" id="deleteOption">
                <i class="fas fa-trash-alt"></i> Mover a Papelera
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables globales
        let currentServiceId = null;

        // Mostrar tarjeta de opciones
        function showOptionsCard(serviceId, serviceName) {
            currentServiceId = serviceId;
            
            // Actualizar título
            document.getElementById('optionsCardTitle').textContent = serviceName;
            
            // Actualizar enlaces
            document.getElementById('viewOption').href = `ver.php?id=${serviceId}`;
            document.getElementById('editOption').href = `editar.php?id=${serviceId}`;
            document.getElementById('deleteOption').href = `index.php?eliminar=${serviceId}`;
            
            // Mostrar tarjeta y overlay
            document.getElementById('optionsCard').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        // Ocultar tarjeta de opciones
        function hideOptionsCard() {
            document.getElementById('optionsCard').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
            currentServiceId = null;
        }

        // Búsqueda en tiempo real - CORREGIDA
        document.getElementById('searchInput').addEventListener('input', function() {
            const query = this.value.trim();
            const resultsContainer = document.getElementById('searchResults');
            const allServicesList = document.getElementById('allServicesList');
            
            if (query.length < 2) {
                resultsContainer.style.display = 'none';
                allServicesList.style.display = 'block';
                return;
            }
            
            // Ocultar lista completa mientras se busca
            allServicesList.style.display = 'none';
            
            fetch(`index.php?ajax=1&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = '';
                    
                    if (data.length === 0) {
                        resultsContainer.style.display = 'none';
                        allServicesList.style.display = 'block';
                        return;
                    }
                    
                    data.forEach(service => {
                        const resultItem = document.createElement('div');
                        resultItem.className = 'search-result-item';
                        resultItem.innerHTML = `
                            <div>
                                <div class="search-service-name">${escapeHtml(service.nombre_servicio)}</div>
                                <div class="search-service-info">
                                    ${escapeHtml(service.categoria_servicio)} · 
                                    $${formatPrice(service.precio_servicio)} · 
                                    ${escapeHtml(service.tiempo_estimado)}
                                </div>
                            </div>
                            <div class="search-service-date">
                                ${formatDate(service.fecha_registro)}
                            </div>
                        `;
                        resultItem.addEventListener('click', () => {
                            showOptionsCard(service.id_servicio, service.nombre_servicio);
                            resultsContainer.style.display = 'none';
                            document.getElementById('searchInput').value = '';
                            allServicesList.style.display = 'block';
                        });
                        resultsContainer.appendChild(resultItem);
                    });
                    
                    resultsContainer.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error en la búsqueda:', error);
                    resultsContainer.style.display = 'none';
                    allServicesList.style.display = 'block';
                });
        });

        // Ocultar resultados al hacer clic fuera y restaurar lista
        document.addEventListener('click', function(e) {
            const searchContainer = document.querySelector('.search-container');
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            const allServicesList = document.getElementById('allServicesList');
            
            if (!searchContainer.contains(e.target)) {
                searchResults.style.display = 'none';
                if (searchInput.value.trim() === '') {
                    allServicesList.style.display = 'block';
                }
            }
        });

        // Restaurar lista cuando se borra la búsqueda
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            if (this.value.trim() === '') {
                document.getElementById('searchResults').style.display = 'none';
                document.getElementById('allServicesList').style.display = 'block';
            }
        });

        // Función para vaciar la papelera (solo admin)
        function vaciarPapelera() {
            if (confirm('¿ESTÁS SEGURO DE QUE QUIERES VACIAR LA PAPELERA? Esta acción eliminará permanentemente todos los servicios en la papelera y no se podrán recuperar.')) {
                window.location.href = 'vaciar_papelera.php';
            }
        }

        // Funciones auxiliares
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatPrice(price) {
            return parseFloat(price).toLocaleString('es-CO');
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-CO');
        }

        // Cerrar con tecla ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideOptionsCard();
            }
        });
    </script>
</body>
</html>