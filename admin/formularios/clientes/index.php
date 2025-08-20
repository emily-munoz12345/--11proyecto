<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/conexion.php';
require_once ROOT_PATH . '/php/auth.php';

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

// Obtener estadísticas generales (excluyendo eliminados)
$stats = $conex->query("SELECT 
    COUNT(*) as total_clientes,
    MAX(fecha_registro) as ultimo_registro,
    (SELECT COUNT(*) FROM clientes WHERE DATE(fecha_registro) = CURDATE() AND eliminado = 0) as registros_hoy
FROM clientes WHERE eliminado = 0")->fetch(PDO::FETCH_ASSOC);

// Obtener los 4 clientes más recientes (excluyendo eliminados)
$clientesRecientes = $conex->query("SELECT * FROM clientes WHERE eliminado = 0 ORDER BY fecha_registro DESC LIMIT 4")->fetchAll();

// Obtener todos los clientes activos para las pestañas
$clientesEditar = $conex->query("SELECT * FROM clientes WHERE eliminado = 0 ORDER BY nombre_cliente ASC")->fetchAll();

// Obtener clientes en la papelera (eliminados)
$clientesEliminar = $conex->query("SELECT * FROM clientes WHERE eliminado = 1 ORDER BY fecha_eliminacion DESC")->fetchAll();

// Obtener todos los clientes activos para vista general
$todosClientes = $conex->query("SELECT * FROM clientes WHERE eliminado = 0 ORDER BY nombre_cliente ASC")->fetchAll();

// Obtener clientes editados con información de edición (excluyendo eliminados)
$clientesEditados = $conex->query("
    SELECT DISTINCT c.*, 
           (SELECT COUNT(*) FROM clientes_ediciones ce WHERE ce.id_cliente = c.id_cliente) as total_ediciones
    FROM clientes c
    INNER JOIN clientes_ediciones ce ON c.id_cliente = ce.id_cliente
    WHERE c.eliminado = 0
    ORDER BY (SELECT MAX(fecha_edicion) FROM clientes_ediciones WHERE id_cliente = c.id_cliente) DESC
")->fetchAll();

// Obtener historial de ediciones para un cliente específico si se solicita
$historialEdiciones = [];
if (isset($_GET['ver_ediciones']) && is_numeric($_GET['ver_ediciones'])) {
    $idCliente = $_GET['ver_ediciones'];
    $stmt = $conex->prepare("
        SELECT ce.*, u.nombre_completo as editor 
        FROM clientes_ediciones ce 
        LEFT JOIN usuarios u ON ce.editado_por = u.id_usuario 
        WHERE ce.id_cliente = ? 
        ORDER BY ce.fecha_edicion DESC
    ");
    $stmt->execute([$idCliente]);
    $historialEdiciones = $stmt->fetchAll();
}

// Procesar búsqueda si es una solicitud AJAX
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    if (!isset($_GET['q']) || strlen($_GET['q']) < 2) {
        echo json_encode([]);
        exit;
    }

    $searchTerm = '%' . $_GET['q'] . '%';
    $stmt = $conex->prepare("SELECT * FROM clientes 
                            WHERE eliminado = 0 
                            AND (nombre_cliente LIKE :search 
                            OR telefono_cliente LIKE :search 
                            OR correo_cliente LIKE :search)
                            ORDER BY nombre_cliente ASC 
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
    <title>Gestión de Clientes | Nacional Tapizados</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.8);
            --primary-hover: rgba(140, 74, 63, 1);
            --secondary-color: rgba(108, 117, 125, 0.8);
            --text-color: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --bg-transparent: rgba(255, 255, 255, 0.1);
            --bg-transparent-light: rgba(255, 255, 255, 0.15);
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
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
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
            background-color: var(--bg-transparent-light);
            color: var(--text-color);
            font-size: 1rem;
            backdrop-filter: blur(5px);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--primary-color);
            background-color: rgba(255, 255, 255, 0.2);
        }

        .search-input::placeholder {
            color: var(--text-muted);
        }

        .search-results {
            position: absolute;
            width: 100%;
            z-index: 1000;
            background-color: rgba(50, 50, 50, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 0 0 8px 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
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

        .search-loading {
            padding: 1rem;
            text-align: center;
            color: var(--text-muted);
            display: none;
        }

        .search-loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Estilos para tarjetas de clientes */
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
        }

        .client-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
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
        }

        .client-card-detail i {
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }

        .client-card-footer {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        /* Estilos para la lista de clientes */
        .client-list {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
        }

        .client-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .client-name {
            font-weight: 500;
            font-size: 1.1rem;
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
        }

        .client-item:hover .client-arrow {
            opacity: 1;
            transform: translateX(3px);
        }

        /* Estilos para tablas */
        .table-container {
            overflow-x: auto;
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            background-color: rgba(255, 255, 255, 0.1);
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
            background-color: rgba(140, 74, 63, 0.5);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-5px);
        }

        .summary-card h3 {
            margin-top: 0;
            font-size: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 0.5rem;
        }

        .summary-card p {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0;
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

        /* Estilos para modal de historial de ediciones */
        .modal-content {
            background-color: var(--bg-transparent);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            color: var(--text-color);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
        }

        .edit-history-item {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--info-color);
        }
        
        .edit-field {
            font-weight: bold;
            color: var(--info-color);
        }
        
        .edit-old-value, .edit-new-value {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            margin: 0.25rem 0;
            display: inline-block;
        }
        
        .edit-old-value {
            background-color: rgba(220, 53, 69, 0.2);
            text-decoration: line-through;
        }
        
        .edit-new-value {
            background-color: rgba(25, 135, 84, 0.2);
        }
        
        .edit-meta {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .edit-count-badge {
            background-color: var(--info-color);
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-left: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .summary-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

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

            .client-cards {
                grid-template-columns: 1fr;
            }

            .summary-cards {
                grid-template-columns: 1fr;
            }

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                margin-bottom: 1rem;
                border: 1px solid var(--border-color);
                border-radius: 8px;
                padding: 0.5rem;
            }

            td {
                border: none;
                position: relative;
                padding-left: 50%;
                text-align: right;
            }

            td::before {
                content: attr(data-label);
                position: absolute;
                left: 1rem;
                width: calc(50% - 1rem);
                padding-right: 10px;
                text-align: left;
                font-weight: 600;
                color: var(--text-muted);
            }
            
            .edit-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }

        @media (max-width: 576px) {
            .client-card-footer {
                flex-direction: column;
                gap: 0.5rem;
            }

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
                <i class="fas fa-users"></i> Gesti&oacute;n de Clientes
            </h1>
            <div class="d-flex gap-2">
                <a href="crear.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Cliente
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
                <h3>Total de Clientes</h3>
                <p><?= $stats['total_clientes'] ?></p>
            </div>
            <div class="summary-card">
                <h3>Último Registro</h3>
                <p><?= $stats['ultimo_registro'] ? date('d/m/Y', strtotime($stats['ultimo_registro'])) : 'N/A' ?></p>
            </div>
            <div class="summary-card">
                <h3>Registros Hoy</h3>
                <p><?= $stats['registros_hoy'] ?></p>
            </div>
        </div>

        <!-- Pestañas de navegación -->
        <ul class="nav nav-tabs" id="clientTabs" role="tablist">
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
                <button class="nav-link" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit" type="button" role="tab">
                    <i class="fas fa-edit"></i> Editados
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="delete-tab" data-bs-toggle="tab" data-bs-target="#delete" type="button" role="tab">
                    <i class="fas fa-trash-alt"></i> Papelera
                </button>
            </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="clientTabsContent">
            <!-- Pestaña de búsqueda -->
            <div class="tab-pane fade show active" id="search" role="tabpanel">
                <div class="search-container">
                    <input type="text" class="search-input" id="searchInput" placeholder="Buscar cliente por nombre, teléfono o correo..." autocomplete="off">
                    <div class="search-loading" id="searchLoading">
                        <i class="fas fa-spinner"></i> Buscando...
                    </div>
                    <div class="search-results" id="searchResults"></div>
                </div>

                <!-- Resultados iniciales (todos los clientes activos) -->
                <div class="client-list" id="allClientsList">
                    <?php foreach ($todosClientes as $cliente): ?>
                        <div class="client-item" data-client-id="<?= $cliente['id_cliente'] ?>"
                            onclick="window.location.href='ver.php?id=<?= $cliente['id_cliente'] ?>'">
                            <div class="client-info">
                                <div class="client-name"><?= htmlspecialchars($cliente['nombre_cliente']) ?></div>
                                <div class="client-description">
                                    <?= htmlspecialchars($cliente['telefono_cliente']) ?> ·
                                    <?= htmlspecialchars($cliente['correo_cliente']) ?>
                                </div>
                            </div>
                            <div class="client-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Pestaña de clientes recientes -->
            <div class="tab-pane fade" id="recent" role="tabpanel">
                <div class="client-cards">
                    <?php foreach ($clientesRecientes as $cliente): ?>
                        <div class="client-card">
                            <div class="client-card-header">
                                <h3 class="client-card-title"><?= htmlspecialchars($cliente['nombre_cliente']) ?></h3>
                                <span class="client-card-badge">ID: <?= $cliente['id_cliente'] ?></span>
                            </div>
                            <div class="client-card-body">
                                <div class="client-card-detail">
                                    <i class="fas fa-envelope"></i>
                                    <span><?= htmlspecialchars($cliente['correo_cliente']) ?></span>
                                </div>
                                <div class="client-card-detail">
                                    <i class="fas fa-phone"></i>
                                    <span><?= htmlspecialchars($cliente['telefono_cliente']) ?></span>
                                </div>
                                <div class="client-card-detail">
                                    <i class="fas fa-calendar"></i>
                                    <span>Registrado: <?= date('d/m/Y', strtotime($cliente['fecha_registro'])) ?></span>
                                </div>
                            </div>
                            <div class="client-card-footer">
                                <a href="ver.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <a href="editar.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Pestaña de edición -->
            <div class="tab-pane fade" id="edit" role="tabpanel">
                <?php if (!empty($clientesEditados)): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Contacto</th>
                                    <th>Ediciones</th>
                                    <th>Última Edición</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clientesEditados as $cliente): ?>
                                    <?php 
                                    // Obtener información de la última edición
                                    $stmt = $conex->prepare("
                                        SELECT ce.fecha_edicion, u.nombre_completo as editor 
                                        FROM clientes_ediciones ce 
                                        LEFT JOIN usuarios u ON ce.editado_por = u.id_usuario 
                                        WHERE ce.id_cliente = ? 
                                        ORDER BY ce.fecha_edicion DESC 
                                        LIMIT 1
                                    ");
                                    $stmt->execute([$cliente['id_cliente']]);
                                    $ultimaEdicion = $stmt->fetch();
                                    ?>
                                    <tr>
                                        <td data-label="ID"><?= $cliente['id_cliente'] ?></td>
                                        <td data-label="Cliente">
                                            <strong><?= htmlspecialchars($cliente['nombre_cliente']) ?></strong>
                                        </td>
                                        <td data-label="Contacto">
                                            <i class="fas fa-phone me-1"></i> <?= htmlspecialchars($cliente['telefono_cliente']) ?><br>
                                            <i class="fas fa-envelope me-1"></i> <?= htmlspecialchars($cliente['correo_cliente']) ?>
                                        </td>
                                        <td data-label="Ediciones">
                                            <span class="edit-count-badge"><?= $cliente['total_ediciones'] ?> ediciones</span>
                                        </td>
                                        <td data-label="Última Edición">
                                            <?php if ($ultimaEdicion): ?>
                                                <?= date('d/m/Y', strtotime($ultimaEdicion['fecha_edicion'])) ?><br>
                                                <small class="text-muted">Por: <?= htmlspecialchars($ultimaEdicion['editor'] ?? 'Sistema') ?></small>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Acciones" class="text-nowrap">
                                            <button class="btn btn-info btn-sm view-history-btn" data-client-id="<?= $cliente['id_cliente'] ?>" data-client-name="<?= htmlspecialchars($cliente['nombre_cliente']) ?>">
                                                <i class="fas fa-history"></i> Ver Historial
                                            </button>
                                            <a href="editar.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No hay clientes con historial de ediciones.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pestaña de eliminación -->
            <div class="tab-pane fade" id="delete" role="tabpanel">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Clientes eliminados. Estos registros se pueden restaurar dentro de los próximos 30 días.
                </div>
                
                <?php if (!empty($clientesEliminar)): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Teléfono</th>
                                    <th>Eliminado el</th>
                                    <th>Eliminado por</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clientesEliminar as $cliente): ?>
                                    <?php
                                    // Obtener información de quién eliminó el cliente
                                    $eliminadoPor = 'Sistema';
                                    if ($cliente['eliminado_por']) {
                                        $stmt = $conex->prepare("SELECT nombre_completo FROM usuarios WHERE id_usuario = ?");
                                        $stmt->execute([$cliente['eliminado_por']]);
                                        $usuario = $stmt->fetch();
                                        $eliminadoPor = $usuario ? $usuario['nombre_completo'] : 'Usuario eliminado';
                                    }
                                    ?>
                                    <tr class="deleted-item">
                                        <td data-label="ID"><?= $cliente['id_cliente'] ?></td>
                                        <td data-label="Cliente">
                                            <?= htmlspecialchars($cliente['nombre_cliente']) ?>
                                            <span class="deleted-badge">Eliminado</span>
                                        </td>
                                        <td data-label="Teléfono"><?= htmlspecialchars($cliente['telefono_cliente']) ?></td>
                                        <td data-label="Eliminado el">
                                            <?= $cliente['fecha_eliminacion'] ? date('d/m/Y H:i', strtotime($cliente['fecha_eliminacion'])) : 'N/A' ?>
                                        </td>
                                        <td data-label="Eliminado por"><?= htmlspecialchars($eliminadoPor) ?></td>
                                        <td data-label="Acciones">
                                            <a href="restaurar.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-success btn-sm" onclick="return confirm('¿Restaurar a <?= htmlspecialchars(addslashes($cliente['nombre_cliente'])) ?>?')">
                                                <i class="fas fa-undo"></i> Restaurar
                                            </a>
                                            <a href="eliminar_permanentemente.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar permanentemente a <?= htmlspecialchars(addslashes($cliente['nombre_cliente'])) ?>? Esta acción no se puede deshacer.')">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No hay clientes en la papelera.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal para historial de ediciones -->
    <div class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-history me-2"></i>Historial de Ediciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="historyModalBody">
                    <!-- El contenido se cargará dinámicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Búsqueda en tiempo real con AJAX
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const searchLoading = document.getElementById('searchLoading');
        const allClientsList = document.getElementById('allClientsList');
        let searchTimeout;

        // Función para filtrar en el DOM
        function filterDomClients(query) {
            const clientItems = allClientsList.querySelectorAll('.client-item');
            const lowerQuery = query.toLowerCase();

            clientItems.forEach(item => {
                const clientText = item.textContent.toLowerCase();
                item.style.display = clientText.includes(lowerQuery) ? 'flex' : 'none';
            });
        }

        // Evento de búsqueda
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();

            // Mostrar/ocultar lista completa
            if (query.length === 0) {
                searchResults.style.display = 'none';
                allClientsList.style.display = 'block';
                return;
            } else {
                allClientsList.style.display = 'none';
            }

            // Filtrado instantáneo en el DOM
            filterDomClients(query);

            // Búsqueda AJAX para resultados más precisos
            clearTimeout(searchTimeout);

            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            searchLoading.style.display = 'block';
            searchResults.style.display = 'none';

            searchTimeout = setTimeout(() => {
                fetch(`?ajax=1&q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchLoading.style.display = 'none';

                        if (data.length > 0) {
                            searchResults.innerHTML = '';
                            data.forEach(client => {
                                const item = document.createElement('div');
                                item.className = 'search-result-item';
                                item.innerHTML = `
                                    <div>
                                        <div class="search-client-name">${client.nombre_cliente}</div>
                                        <div class="search-client-info">
                                            <i class="fas fa-phone"></i> ${client.telefono_cliente} · 
                                            <i class="fas fa-envelope"></i> ${client.correo_cliente}
                                        </div>
                                    </div>
                                    <div class="search-client-date">
                                        ${client.fecha_registro ? new Date(client.fecha_registro).toLocaleDateString('es-ES') : ''}
                                    </div>
                                `;
                                item.addEventListener('click', function() {
                                    window.location.href = `ver.php?id=${client.id_cliente}`;
                                });
                                searchResults.appendChild(item);
                            });
                            searchResults.style.display = 'block';
                        } else {
                            searchResults.innerHTML = '<div class="search-result-item">No se encontraron resultados</div>';
                            searchResults.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        searchLoading.style.display = 'none';
                        console.error('Error:', error);
                        searchResults.innerHTML = '<div class="search-result-item">Error en la búsqueda</div>';
                        searchResults.style.display = 'block';
                    });
            }, 300);
        });

        // Ocultar resultados al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });

        // Manejo de teclado
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && searchResults.style.display === 'block') {
                const firstResult = searchResults.querySelector('.search-result-item');
                if (firstResult) firstResult.click();
            }

            if (e.key === 'Escape') {
                searchResults.style.display = 'none';
                searchInput.value = '';
                allClientsList.style.display = 'block';
                const clientItems = allClientsList.querySelectorAll('.client-item');
                clientItems.forEach(item => item.style.display = 'flex');
            }
        });

        // Inicializar pestañas de Bootstrap
        const tabElms = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabElms.forEach(tabEl => {
            tabEl.addEventListener('click', function(event) {
                event.preventDefault();
                const tab = new bootstrap.Tab(this);
                tab.show();

                // Restablecer búsqueda al cambiar de pestaña
                if (this.id !== 'search-tab') {
                    searchInput.value = '';
                    searchResults.style.display = 'none';
                    allClientsList.style.display = 'block';
                    const clientItems = allClientsList.querySelectorAll('.client-item');
                    clientItems.forEach(item => item.style.display = 'flex');
                }
            });
        });

        // Mostrar tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Cargar historial de ediciones en modal
        document.querySelectorAll('.view-history-btn').forEach(button => {
            button.addEventListener('click', function() {
                const clientId = this.getAttribute('data-client-id');
                const clientName = this.getAttribute('data-client-name');
                
                // Mostrar loading en el modal
                document.getElementById('historyModalBody').innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando historial de ediciones...</p>
                    </div>
                `;
                
                // Mostrar el modal
                const historyModal = new bootstrap.Modal(document.getElementById('historyModal'));
                historyModal.show();
                
                // Actualizar título del modal
                document.querySelector('#historyModal .modal-title').innerHTML = 
                    `<i class="fas fa-history me-2"></i>Historial de Ediciones: ${clientName}`;
                
                // Cargar el historial via AJAX
                fetch(`cargar_historial.php?id=${clientId}`)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('historyModalBody').innerHTML = data;
                    })
                    .catch(error => {
                        document.getElementById('historyModalBody').innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Error al cargar el historial de ediciones.
                            </div>
                        `;
                    });
            });
        });

        // Inicializar todos los tooltips
        const bootstrapTooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        bootstrapTooltips.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>

</html>