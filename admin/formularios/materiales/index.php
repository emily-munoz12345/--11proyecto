<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin()) {
    header('Location: ../dashboard.php');
    exit;
}

// Inicializar variables de sesión para mensajes
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
    $_SESSION['tipo_mensaje'] = '';
}

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    try {
        // Verificar si el material está asociado a algún trabajo
        $stmt = $conex->prepare("SELECT COUNT(*) FROM trabajo_materiales WHERE id_material = ?");
        $stmt->execute([$id]);
        $tieneTrabajos = $stmt->fetchColumn();
        
        if ($tieneTrabajos > 0) {
            $_SESSION['mensaje'] = 'No se puede eliminar: material está asociado a trabajos';
            $_SESSION['tipo_mensaje'] = 'danger';
        } else {
            $stmt = $conex->prepare("DELETE FROM materiales WHERE id_material = ?");
            if ($stmt->execute([$id])) {
                $_SESSION['mensaje'] = 'Material eliminado correctamente';
                $_SESSION['tipo_mensaje'] = 'success';
            }
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
    COUNT(*) as total_materiales,
    MAX(fecha_registro) as ultimo_registro,
    (SELECT COUNT(*) FROM materiales WHERE DATE(fecha_registro) = CURDATE()) as registros_hoy,
    SUM(stock_material) as stock_total,
    AVG(precio_metro) as precio_promedio
FROM materiales")->fetch(PDO::FETCH_ASSOC);

// Obtener los 4 materiales más recientes
$materialesRecientes = $conex->query("SELECT * FROM materiales ORDER BY id_material DESC LIMIT 4")->fetchAll();

// Obtener todos los materiales para las pestañas
$todosMateriales = $conex->query("SELECT * FROM materiales ORDER BY nombre_material ASC")->fetchAll();

// Procesar búsqueda si es una solicitud AJAX
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    if (!isset($_GET['q']) || strlen($_GET['q']) < 2) {
        echo json_encode([]);
        exit;
    }

    $searchTerm = '%' . $_GET['q'] . '%';
    $stmt = $conex->prepare("SELECT * FROM materiales 
                            WHERE (nombre_material LIKE :search 
                            OR categoria_material LIKE :search 
                            OR proveedor_material LIKE :search)
                            ORDER BY nombre_material ASC 
                            LIMIT 10");
    $stmt->bindParam(':search', $searchTerm);
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// Procesar solicitud para cargar detalles de material
if (isset($_GET['cargar_detalles']) && is_numeric($_GET['cargar_detalles'])) {
    $idMaterial = $_GET['cargar_detalles'];
    
    // Obtener información del material
    $stmt = $conex->prepare("SELECT * FROM materiales WHERE id_material = ?");
    $stmt->execute([$idMaterial]);
    $material = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($material) {
        echo '<div class="detail-item">';
        echo '<div class="detail-label">ID</div>';
        echo '<div class="detail-value">' . $material['id_material'] . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Nombre</div>';
        echo '<div class="detail-value">' . htmlspecialchars($material['nombre_material']) . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Precio por metro</div>';
        echo '<div class="detail-value">$' . number_format($material['precio_metro'], 0, ',', '.') . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Stock</div>';
        echo '<div class="detail-value">' . htmlspecialchars($material['stock_material']) . ' metros</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Categoría</div>';
        echo '<div class="detail-value">' . htmlspecialchars($material['categoria_material']) . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Proveedor</div>';
        echo '<div class="detail-value">' . htmlspecialchars($material['proveedor_material']) . '</div>';
        echo '</div>';
        
        if (!empty($material['descripcion_material'])) {
            echo '<div class="notes-section">';
            echo '<div class="detail-label">Descripción</div>';
            echo '<div class="detail-value">' . nl2br(htmlspecialchars($material['descripcion_material'])) . '</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Material no encontrado</div>';
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Materiales | Nacional Tapizados</title>
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

        .search-material-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .search-material-info {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .search-material-price {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.5);
        }

        /* Estilos para la lista de materiales */
        .material-list {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .material-item {
            padding: 1.2rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .material-item:hover {
            background-color: rgba(140, 74, 63, 0.3);
        }

        .material-name {
            font-weight: 500;
            font-size: 1.1rem;
            color: var(--text-color);
        }

        .material-description {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 0.3rem;
        }

        .material-arrow {
            margin-left: 1rem;
            opacity: 0.7;
            transition: all 0.3s ease;
            color: var(--text-color);
        }

        .material-item:hover .material-arrow {
            opacity: 1;
            transform: translateX(3px);
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

        /* Estilos para tarjetas de materiales */
        .material-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .material-card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
        }

        .material-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            background-color: rgba(140, 74, 63, 0.2);
        }

        .material-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .material-card-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .material-card-badge {
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .material-card-body {
            margin-bottom: 1.5rem;
        }

        .material-card-detail {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
            color: var(--text-color);
        }

        .material-card-detail i {
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }

        /* Flecha para tarjetas */
        .material-arrow-card {
            position: absolute;
            bottom: 15px;
            right: 15px;
            color: var(--primary-color);
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .material-arrow-card:hover {
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

            .material-cards {
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
                <i class="fas fa-box"></i> Gesti&oacute;n de Materiales
            </h1>
            <div class="d-flex gap-2">
                <a href="crear.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Material
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
                <h3>Total de Materiales</h3>
                <p><?= $stats['total_materiales'] ?></p>
            </div>
            <div class="summary-card">
                <h3>Stock Total</h3>
                <p><?= number_format($stats['stock_total'], 0, ',', '.') ?> m</p>
            </div>
            <div class="summary-card">
                <h3>Precio Promedio</h3>
                <p>$<?= number_format($stats['precio_promedio'], 0, ',', '.') ?></p>
            </div>
            <div class="summary-card">
                <h3>Último Registro</h3>
                <p><?= $stats['ultimo_registro'] ? date('d/m/Y', strtotime($stats['ultimo_registro'])) : 'N/A' ?></p>
            </div>
        </div>

        <!-- Pestañas de navegación -->
        <ul class="nav nav-tabs" id="materialTabs" role="tablist">
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
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="materialTabsContent">
            <!-- Pestaña de búsqueda -->
            <div class="tab-pane fade show active" id="search" role="tabpanel">
                <div class="search-container">
                    <input type="text" class="search-input" id="searchInput" placeholder="Buscar material por nombre, categoría o proveedor..." autocomplete="off">
                    <div class="search-results" id="searchResults"></div>
                </div>

                <!-- Resultados iniciales (todos los materiales) -->
                <div class="material-list" id="allMaterialsList">
                    <?php foreach ($todosMateriales as $material): ?>
                        <div class="material-item" data-material-id="<?= $material['id_material'] ?>" onclick="showOptionsCard(<?= $material['id_material'] ?>, '<?= htmlspecialchars(addslashes($material['nombre_material'])) ?>')">
                            <div class="material-info">
                                <div class="material-name"><?= htmlspecialchars($material['nombre_material']) ?></div>
                                <div class="material-description">
                                    <?= htmlspecialchars($material['categoria_material']) ?> · 
                                    $<?= number_format($material['precio_metro'], 0, ',', '.') ?> · 
                                    Stock: <?= htmlspecialchars($material['stock_material']) ?> m
                                </div>
                            </div>
                            <div class="material-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Pestaña de materiales recientes -->
            <div class="tab-pane fade" id="recent" role="tabpanel">
                <div class="material-cards">
                    <?php foreach ($materialesRecientes as $material): ?>
                        <div class="material-card" onclick="showOptionsCard(<?= $material['id_material'] ?>, '<?= htmlspecialchars(addslashes($material['nombre_material'])) ?>')">
                            <div class="material-card-header">
                                <h3 class="material-card-title"><?= htmlspecialchars($material['nombre_material']) ?></h3>
                                <span class="material-card-badge">ID: <?= $material['id_material'] ?></span>
                            </div>
                            <div class="material-card-body">
                                <div class="material-card-detail">
                                    <i class="fas fa-tag"></i>
                                    <span><?= htmlspecialchars($material['categoria_material']) ?></span>
                                </div>
                                <div class="material-card-detail">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span>$<?= number_format($material['precio_metro'], 0, ',', '.') ?> por metro</span>
                                </div>
                                <div class="material-card-detail">
                                    <i class="fas fa-boxes"></i>
                                    <span><?= htmlspecialchars($material['stock_material']) ?> metros en stock</span>
                                </div>
                                <div class="material-card-detail">
                                    <i class="fas fa-truck"></i>
                                    <span><?= htmlspecialchars($material['proveedor_material']) ?></span>
                                </div>
                            </div>
                            <div class="material-arrow-card">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjeta flotante para opciones -->
    <div class="overlay" id="overlay"></div>
    <div class="options-floating-card" id="optionsCard">
        <button class="option-close" id="closeOptionsCard">
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
                <i class="fas fa-trash-alt"></i> Eliminar
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables globales
        let currentMaterialId = null;
        let currentMaterialName = null;

        // Mostrar tarjeta de opciones
        function showOptionsCard(materialId, materialName) {
            currentMaterialId = materialId;
            currentMaterialName = materialName;
            
            // Actualizar título de la tarjeta
            document.getElementById('optionsCardTitle').textContent = materialName;
            
            // Actualizar enlaces
            document.getElementById('viewOption').href = `ver.php?id=${materialId}`;
            document.getElementById('editOption').href = `editar.php?id=${materialId}`;
            document.getElementById('deleteOption').href = `index.php?eliminar=${materialId}`;
            
            // Mostrar overlay y tarjeta
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('optionsCard').style.display = 'block';
        }

        // Cerrar tarjeta de opciones
        function closeOptionsCard() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('optionsCard').style.display = 'none';
            currentMaterialId = null;
            currentMaterialName = null;
        }

        // Event listeners para cerrar tarjeta
        document.getElementById('closeOptionsCard').addEventListener('click', closeOptionsCard);
        document.getElementById('overlay').addEventListener('click', closeOptionsCard);

        // Búsqueda en tiempo real
        document.getElementById('searchInput').addEventListener('input', function() {
            const query = this.value.trim();
            const resultsContainer = document.getElementById('searchResults');
            const allMaterialsList = document.getElementById('allMaterialsList');
            
            if (query.length < 2) {
                resultsContainer.style.display = 'none';
                allMaterialsList.style.display = 'block';
                return;
            }
            
            allMaterialsList.style.display = 'none';
            
            fetch(`?ajax=1&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = '';
                    
                    if (data.length === 0) {
                        resultsContainer.innerHTML = '<div class="search-result-item">No se encontraron materiales</div>';
                        resultsContainer.style.display = 'block';
                        return;
                    }
                    
                    data.forEach(material => {
                        const item = document.createElement('div');
                        item.className = 'search-result-item';
                        item.innerHTML = `
                            <div>
                                <div class="search-material-name">${material.nombre_material}</div>
                                <div class="search-material-info">${material.categoria_material} · Stock: ${material.stock_material} m</div>
                                <div class="search-material-price">$${new Intl.NumberFormat().format(material.precio_metro)} por metro</div>
                            </div>
                            <div class="material-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        `;
                        item.addEventListener('click', () => {
                            showOptionsCard(material.id_material, material.nombre_material);
                            resultsContainer.style.display = 'none';
                            document.getElementById('searchInput').value = '';
                            allMaterialsList.style.display = 'block';
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

        // Cerrar resultados al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                document.getElementById('searchResults').style.display = 'none';
            }
        });

        // Confirmación para eliminar
        document.getElementById('deleteOption').addEventListener('click', function(e) {
            if (!confirm(`¿Estás seguro de que deseas eliminar el material "${currentMaterialName}"?`)) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>