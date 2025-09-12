<?php
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

// Búsqueda y paginación
$busqueda = $_GET['busqueda'] ?? '';
$pagina = max(1, intval($_GET['pagina'] ?? 1));
$porPagina = 10;

$sql = "SELECT * FROM materiales";
$params = [];
$where = '';

if (!empty($busqueda)) {
    $where = " WHERE nombre_material LIKE ? OR categoria_material LIKE ? OR proveedor_material LIKE ?";
    $params = ["%$busqueda%", "%$busqueda%", "%$busqueda%"];
}

// Contar total para paginación
$sqlCount = "SELECT COUNT(*) FROM materiales $where";
$stmt = $conex->prepare($sqlCount);
$stmt->execute($params);
$totalMateriales = $stmt->fetchColumn();
$totalPaginas = ceil($totalMateriales / $porPagina);

// Obtener materiales
$offset = ($pagina - 1) * $porPagina;
$sql .= " $where ORDER BY nombre_material ASC LIMIT $offset, $porPagina";

$stmt = $conex->prepare($sql);
if (!empty($busqueda)) {
    $stmt->execute($params);
} else {
    $stmt->execute();
}
$materiales = $stmt->fetchAll();

// Obtener materiales recientes (últimos 4)
$materialesRecientes = $conex->query("SELECT * FROM materiales ORDER BY id_material DESC LIMIT 4")->fetchAll();

// Obtener todos los materiales para la pestaña de búsqueda
$todosMateriales = $conex->query("SELECT * FROM materiales ORDER BY nombre_material ASC")->fetchAll();

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

require_once __DIR__ . '/../../includes/head.php';
$title = 'Gestión de Materiales | Nacional Tapizados';
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

        /* Estilos para la lista de materiales */
        .material-list {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            background-color: rgba(255, 255, 255, 0.2);
        }

        .material-name {
            font-weight: 500;
            font-size: 1.1rem;
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
        }

        .material-item:hover .material-arrow {
            opacity: 1;
            transform: translateX(3px);
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
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
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

        .floating-card {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 700px;
            max-height: 90vh;
            background-color: rgba(50, 50, 50, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            z-index: 1001;
            animation: fadeInUp 0.4s ease;
            overflow-y: auto;
            border: 1px solid var(--border-color);
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

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
            position: relative;
        }

        .card-title {
            margin: 0;
            font-size: 1.8rem;
            color: #fff;
        }

        .close-card {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            transition: all 0.3s ease;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-card:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .card-content {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .detail-item {
            margin-bottom: 1rem;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 8px;
        }

        .detail-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 1.1rem;
            word-break: break-word;
            color: #fff;
        }

        .notes-section {
            grid-column: 1 / -1;
            background-color: rgba(0, 0, 0, 0.2);
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1rem;
        }

        /* Nueva tarjeta flotante para opciones */
        .options-floating-card {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            background-color: rgba(50, 50, 50, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 0;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
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

            .material-cards {
                grid-template-columns: 1fr;
            }

            .options-floating-card {
                width: 90%;
                max-width: 300px;
            }
        }

        @media (max-width: 576px) {
            .btn {
                width: 100%;
                justify-content: center;
            }

            .floating-card {
                width: 95%;
                padding: 1.5rem;
            }

            .card-content {
                grid-template-columns: 1fr;
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
                <!-- Buscador -->
                <div class="search-container">
                    <form class="row g-3">
                        <div class="col-md-8">
                            <input type="text" class="search-input" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" 
                                placeholder="Buscar por nombre, categoría o proveedor" autocomplete="off">
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary w-100" type="submit">
                                <i class="fas fa-search me-1"></i> Buscar
                            </button>
                        </div>
                    </form>
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

                <!-- Paginación -->
                <?php if ($totalPaginas > 1): ?>
                    <nav aria-label="Paginación" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($pagina > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?pagina=<?= $pagina-1 ?>&busqueda=<?= urlencode($busqueda) ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                    <a class="page-link" href="?pagina=<?= $i ?>&busqueda=<?= urlencode($busqueda) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagina < $totalPaginas): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?pagina=<?= $pagina+1 ?>&busqueda=<?= urlencode($busqueda) ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>

            <!-- Pestaña de materiales recientes -->
            <div class="tab-pane fade" id="recent" role="tabpanel">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Vista de materiales recientemente agregados.
                </div>
                
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
    <div class="overlay" id="optionsOverlay" onclick="hideOptionsCard()"></div>
    <div class="options-floating-card" id="optionsCard">
        <div class="options-card-header">
            <h2 class="options-card-title" id="optionsMaterialName">Opciones del Material</h2>
            <button class="option-close" onclick="hideOptionsCard()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="options-card-body" id="optionsCardContent">
            <!-- El contenido se cargará dinámicamente -->
        </div>
    </div>

    <!-- Tarjeta flotante para detalles del material -->
    <div class="overlay" id="detailOverlay" onclick="hideDetailCard()"></div>
    <div class="floating-card" id="detailCard">
        <div class="card-header">
            <h2 class="card-title" id="detailMaterialName">Detalles del Material</h2>
            <button class="close-card" onclick="hideDetailCard()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="card-content" id="detailCardContent">
            <!-- El contenido se cargará dinámicamente -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar tarjeta de opciones
        function showOptionsCard(materialId, materialName) {
            // Actualizar el nombre del material en la cabecera
            document.getElementById('optionsMaterialName').textContent = materialName;
            
            // Crear el contenido de las opciones
            const optionsContent = `
                <div class="option-item" onclick="showMaterialDetails(${materialId}, '${materialName.replace(/'/g, "\\'")}')">
                    <i class="fas fa-eye"></i>
                    <span>Ver detalles</span>
                </div>
                <div class="option-item" onclick="window.location.href='editar.php?id=${materialId}'">
                    <i class="fas fa-edit"></i>
                    <span>Editar material</span>
                </div>
                <div class="option-item" onclick="if(confirm('¿Estás seguro de eliminar este material?')) window.location.href='index.php?eliminar=${materialId}'">
                    <i class="fas fa-trash"></i>
                    <span>Eliminar material</span>
                </div>
            `;
            
            document.getElementById('optionsCardContent').innerHTML = optionsContent;
            
            // Mostrar overlay y tarjeta de opciones
            document.getElementById('optionsOverlay').style.display = 'block';
            document.getElementById('optionsCard').style.display = 'block';
        }

        // Ocultar tarjeta de opciones
        function hideOptionsCard() {
            document.getElementById('optionsOverlay').style.display = 'none';
            document.getElementById('optionsCard').style.display = 'none';
        }

        // Mostrar detalles del material
        function showMaterialDetails(materialId, materialName) {
            // Ocultar la tarjeta de opciones primero
            hideOptionsCard();
            
            // Actualizar el nombre del material en la cabecera
            document.getElementById('detailMaterialName').textContent = materialName;
            
            // Mostrar loading
            document.getElementById('detailCardContent').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando detalles del material...</p>
                </div>
            `;
            
            // Mostrar overlay y tarjeta de detalles
            document.getElementById('detailOverlay').style.display = 'block';
            document.getElementById('detailCard').style.display = 'block';
            
            // Cargar detalles via AJAX
            fetch(`?cargar_detalles=${materialId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('detailCardContent').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('detailCardContent').innerHTML = `
                        <div class="alert alert-danger"> 
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error al cargar los detalles del material.
                        </div>
                    `;
                });
        }

        // Ocultar tarjeta de detalles
        function hideDetailCard() {
            document.getElementById('detailOverlay').style.display = 'none';
            document.getElementById('detailCard').style.display = 'none';
        }

        // Cerrar con tecla ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideOptionsCard();
                hideDetailCard();
            }
        });

        // Inicializar pestañas de Bootstrap
        const tabElms = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabElms.forEach(tabEl => {
            tabEl.addEventListener('click', function(event) {
                event.preventDefault();
                const tab = new bootstrap.Tab(this);
                tab.show();
            });
        });
    </script>
</body>
</html>