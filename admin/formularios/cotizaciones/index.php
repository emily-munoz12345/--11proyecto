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

// Obtener estadísticas generales (excluyendo eliminados - activos = 1)
$stats = $conex->query("SELECT 
    COUNT(*) as total_cotizaciones,
    (SELECT CONCAT(cl.nombre_cliente, ' - ', v.marca_vehiculo, ' ', v.modelo_vehiculo) 
     FROM cotizaciones c2 
     LEFT JOIN clientes cl ON c2.id_cliente = cl.id_cliente
     LEFT JOIN vehiculos v ON c2.id_vehiculo = v.id_vehiculo
     WHERE c2.activo = 1 
     ORDER BY c2.fecha_cotizacion DESC LIMIT 1) as ultima_cotizacion,
    (SELECT COUNT(*) FROM cotizaciones WHERE DATE(fecha_cotizacion) = CURDATE() AND activo = 1) as cotizaciones_hoy,
    (SELECT COUNT(*) FROM cotizaciones WHERE activo = 0) as en_papelera
FROM cotizaciones WHERE activo = 1")->fetch(PDO::FETCH_ASSOC);

// Obtener las 4 cotizaciones más recientes (excluyendo eliminados - activos = 1)
$cotizacionesRecientes = $conex->query("
    SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo
    FROM cotizaciones c
    LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
    LEFT JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    WHERE c.activo = 1 
    ORDER BY c.fecha_cotizacion DESC LIMIT 4
")->fetchAll();

// Obtener todas las cotizaciones activas para la pestaña de buscar
$todasCotizaciones = $conex->query("
    SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo
    FROM cotizaciones c
    LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
    LEFT JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    WHERE c.activo = 1 
    ORDER BY c.fecha_cotizacion DESC
")->fetchAll();

// Obtener cotizaciones en la papelera (eliminados - activos = 0)
$cotizacionesEliminar = $conex->query("
    SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo
    FROM cotizaciones c
    LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
    LEFT JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    WHERE c.activo = 0 
    ORDER BY c.fecha_eliminacion DESC
")->fetchAll();

// Procesar búsqueda si es una solicitud AJAX
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    if (!isset($_GET['q']) || strlen($_GET['q']) < 2) {
        echo json_encode([]);
        exit;
    }

    $searchTerm = '%' . $_GET['q'] . '%';
    $stmt = $conex->prepare("
        SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo
        FROM cotizaciones c
        LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
        LEFT JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
        WHERE c.activo = 1 
        AND (cl.nombre_cliente LIKE :search 
        OR v.placa_vehiculo LIKE :search 
        OR c.id_cotizacion LIKE :search)
        ORDER BY c.fecha_cotizacion DESC 
        LIMIT 10
    ");
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
    <title>Gestión de Cotizaciones</title>
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

        /* Estilos para la lista de cotizaciones */
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

        /* Estilos para acciones en la lista */
        .client-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        /* Estilos para tablas */
        .table-container {
            overflow-x: auto;
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        th,
        td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-color);
        }

        th {
            background-color: var(--primary-color);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        tr:hover {
            background-color: rgba(140, 74, 63, 0.2);
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

        .summary-card .summary-description {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
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

        .days-left {
            font-size: 0.8rem;
            color: var(--warning-color);
            margin-top: 0.25rem;
        }

        /* Estilos para tarjetas de cotizaciones */
        .client-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .client-card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
        }

        .client-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            background-color: rgba(140, 74, 63, 0.2);
        }

        .client-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .client-card-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .client-card-badge {
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .client-card-body {
            margin-bottom: 1.5rem;
        }

        .client-card-detail {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
            color: var(--text-color);
        }

        .client-card-detail i {
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
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }

            .client-cards {
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
                <i class="fas fa-file-invoice-dollar"></i>Gestión de Cotizaciones
            </h1>
            <div class="d-flex gap-2">
                <a href="crear.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Cotización
                </a>
                <a href="../../dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <!-- Mensajes de alerta -->
        <?php if ($_SESSION['mensaje']): ?>
            <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?>">
                <span><?php echo $_SESSION['mensaje']; ?></span>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php
            // Limpiar mensaje después de mostrarlo
            $_SESSION['mensaje'] = '';
            $_SESSION['tipo_mensaje'] = '';
            ?>
        <?php endif; ?>

        <!-- Tarjetas de resumen (ACTUALIZADAS) -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total de Cotizaciones</h3>
                <p><?php echo $stats['total_cotizaciones']; ?></p>
            </div>
            <div class="summary-card">
                <h3>Última Cotización</h3>
                <p><?php echo $stats['ultima_cotizacion'] ?: 'No hay cotizaciones'; ?></p>
                <div class="summary-description">Cliente - Vehículo</div>
            </div>
            <div class="summary-card">
                <h3>Cotizaciones de Hoy</h3>
                <p><?php echo $stats['cotizaciones_hoy']; ?></p>
            </div>
            <div class="summary-card">
                <h3>En Papelera</h3>
                <p><?php echo $stats['en_papelera']; ?></p>
            </div>
        </div>

        <!-- Pestañas de navegación -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="buscar-tab" data-bs-toggle="tab" data-bs-target="#buscar" type="button" role="tab" aria-controls="buscar" aria-selected="true">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="recientes-tab" data-bs-toggle="tab" data-bs-target="#recientes" type="button" role="tab" aria-controls="recientes" aria-selected="false">
                    <i class="fas fa-clock"></i> Recientes
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="eliminar-tab" data-bs-toggle="tab" data-bs-target="#eliminar" type="button" role="tab" aria-controls="eliminar" aria-selected="false">
                    <i class="fas fa-trash"></i> Papelera
                </button>
            </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="myTabContent">

            <!-- Pestaña Buscar (activa por defecto) -->
            <div class="tab-pane fade show active" id="buscar" role="tabpanel" aria-labelledby="buscar-tab">
                <div class="search-container">
                    <input type="text" id="searchInput" class="search-input" placeholder="Buscar cotizaciones por cliente, placa o ID...">
                    <div id="searchResults" class="search-results"></div>
                </div>

                <div class="client-list" id="allCotizacionesList">
                    <?php foreach ($todasCotizaciones as $cotizacion): ?>
                        <div class="client-item" onclick="showOptions(<?php echo $cotizacion['id_cotizacion']; ?>, '<?php echo htmlspecialchars(addslashes($cotizacion['nombre_cliente'])); ?>')">
                            <div>
                                <div class="client-name"><?php echo htmlspecialchars($cotizacion['nombre_cliente']); ?></div>
                                <div class="client-description">
                                    <?php echo htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo']); ?> -
                                    <?php echo htmlspecialchars($cotizacion['placa_vehiculo']); ?> -
                                    $<?php echo number_format($cotizacion['total_cotizacion'], 2); ?>
                                </div>
                            </div>
                            <div class="client-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Pestaña Recientes -->
            <div class="tab-pane fade" id="recientes" role="tabpanel" aria-labelledby="recientes-tab">
                <div class="client-cards">
                    <?php foreach ($cotizacionesRecientes as $cotizacion): ?>
                        <div class="client-card" onclick="showOptions(<?php echo $cotizacion['id_cotizacion']; ?>, '<?php echo htmlspecialchars(addslashes($cotizacion['nombre_cliente'])); ?>')">
                            <div class="client-card-header">
                                <h3 class="client-card-title"><?php echo htmlspecialchars($cotizacion['nombre_cliente']); ?></h3>
                                <span class="client-card-badge"><?php echo htmlspecialchars($cotizacion['estado_cotizacion']); ?></span>
                            </div>
                            <div class="client-card-body">
                                <div class="client-card-detail">
                                    <i class="fas fa-car"></i>
                                    <span><?php echo htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo']); ?></span>
                                </div>
                                <div class="client-card-detail">
                                    <i class="fas fa-tag"></i>
                                    <span><?php echo htmlspecialchars($cotizacion['placa_vehiculo']); ?></span>
                                </div>
                                <div class="client-card-detail">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span>$<?php echo number_format($cotizacion['total_cotizacion'], 2); ?></span>
                                </div>
                                <div class="client-card-detail">
                                    <i class="fas fa-calendar"></i>
                                    <span><?php echo date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])); ?></span>
                                </div>
                            </div>
                            <div class="edit-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Pestaña Papelera (ACTUALIZADA) -->
            <div class="tab-pane fade" id="eliminar" role="tabpanel" aria-labelledby="eliminar-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <?php if (isAdmin() && count($cotizacionesEliminar) > 0): ?>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="vaciarPapelera()">
                            <i class="fas fa-broom me-1"></i> Vaciar papelera
                        </button>
                    <?php endif; ?>
                </div>

                <?php if (count($cotizacionesEliminar) > 0): ?>
                    <div class="client-list">
                        <?php foreach ($cotizacionesEliminar as $cotizacion): ?>
                            <div class="client-item deleted-item">
                                <div class="client-info">
                                    <div class="client-name"><?php echo htmlspecialchars($cotizacion['nombre_cliente']); ?></div>
                                    <div class="client-description">
                                        <?php echo htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo']); ?> ·
                                        <?php echo htmlspecialchars($cotizacion['placa_vehiculo']); ?> ·
                                        $<?php echo number_format($cotizacion['total_cotizacion'], 2); ?>
                                        <br>
                                        <small>Eliminado: <?php echo $cotizacion['fecha_eliminacion'] ? date('d/m/Y H:i', strtotime($cotizacion['fecha_eliminacion'])) : 'Fecha no disponible'; ?></small>
                                    </div>
                                </div>
                                <div class="client-actions">
                                    <a href="restaurar.php?id=<?= $cotizacion['id_cotizacion'] ?>" class="btn btn-success btn-sm" onclick="return confirm('¿Restaurar cotización de <?= htmlspecialchars(addslashes($cotizacion['nombre_cliente'])) ?>?')">
                                        <i class="fas fa-undo"></i> Restaurar
                                    </a>
                                    <?php if (isAdmin()): ?>
                                        <a href="eliminar_permanentemente.php?id=<?= $cotizacion['id_cotizacion'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿ESTÁS SEGURO? Esta acción eliminará permanentemente la cotización de <?= htmlspecialchars(addslashes($cotizacion['nombre_cliente'])) ?> y no se podrá recuperar.')">
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
                        <p style="color: var(--text-muted);">No hay cotizaciones eliminadas</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include '../../includes/bot.php'; ?>
    <script>setHelpModule('Cotizaciones');</script>

    <!-- Tarjeta flotante para opciones -->
    <div class="overlay" id="overlay" onclick="hideOptions()"></div>
    <div class="options-floating-card" id="optionsCard">
        <button class="option-close" onclick="hideOptions()">
            <i class="fas fa-times"></i>
        </button>
        <div class="options-card-header">
            <h3 class="options-card-title" id="optionsTitle">Opciones</h3>
        </div>
        <div class="options-card-body">
            <a href="#" class="option-item" id="viewOption">
                <i class="fas fa-eye"></i> Ver Detalles
            </a>
            <a href="#" class="option-item" id="editOption">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="#" class="option-item" id="deleteOption">
                <i class="fas fa-trash"></i> Mover a Papelera
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables globales
        let currentCotizacionId = null;
        let currentCotizacionName = '';

        // Función para mostrar opciones
        function showOptions(id, name) {
            currentCotizacionId = id;
            currentCotizacionName = name;

            // Actualizar título
            document.getElementById('optionsTitle').textContent = name;

            // Actualizar enlaces
            document.getElementById('viewOption').href = `ver.php?id=${id}`;
            document.getElementById('editOption').href = `editar.php?id=${id}`;
            document.getElementById('deleteOption').href = `eliminar.php?id=${id}`;

            // Mostrar overlay y tarjeta
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('optionsCard').style.display = 'block';
        }

        // Función para ocultar opciones
        function hideOptions() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('optionsCard').style.display = 'none';
            currentCotizacionId = null;
            currentCotizacionName = '';
        }

        // Función para vaciar papelera
        function vaciarPapelera() {
            if (confirm('¿ESTÁS SEGURO? Esta acción eliminará permanentemente todas las cotizaciones de la papelera y no se podrán recuperar.')) {
                window.location.href = 'vaciar_papelera.php';
            }
        }

        // Búsqueda en tiempo real
        document.getElementById('searchInput').addEventListener('input', function() {
            const query = this.value.trim();
            const resultsContainer = document.getElementById('searchResults');

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
                    } else {
                        data.forEach(cotizacion => {
                            const item = document.createElement('div');
                            item.className = 'search-result-item';
                            item.innerHTML = `
                                <div>
                                    <div class="search-client-name">${cotizacion.nombre_cliente}</div>
                                    <div class="search-client-info">
                                        ${cotizacion.marca_vehiculo} ${cotizacion.modelo_vehiculo} - ${cotizacion.placa_vehiculo}
                                    </div>
                                </div>
                                <div class="search-client-date">
                                    ${new Date(cotizacion.fecha_cotizacion).toLocaleDateString()}
                                </div>
                            `;
                            item.onclick = () => {
                                showOptions(cotizacion.id_cotizacion, cotizacion.nombre_cliente);
                                resultsContainer.style.display = 'none';
                                document.getElementById('searchInput').value = '';
                            };
                            resultsContainer.appendChild(item);
                        });
                    }

                    resultsContainer.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error en la búsqueda:', error);
                    resultsContainer.innerHTML = '<div class="search-result-item">Error en la búsqueda</div>';
                    resultsContainer.style.display = 'block';
                });
        });

        // Ocultar resultados al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                document.getElementById('searchResults').style.display = 'none';
            }
        });

        // Cerrar tarjeta de opciones con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideOptions();
            }
        });
    </script>
</body>

</html>