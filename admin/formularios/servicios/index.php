<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin() && !isSeller()) {
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
        // Verificar si el servicio está asociado a cotizaciones
        $stmt = $conex->prepare("SELECT COUNT(*) FROM cotizacion_servicios WHERE id_servicio = ?");
        $stmt->execute([$id]);
        $tieneCotizaciones = $stmt->fetchColumn();
        
        if ($tieneCotizaciones > 0) {
            $_SESSION['mensaje'] = 'No se puede eliminar: servicio está asociado a cotizaciones';
            $_SESSION['tipo_mensaje'] = 'danger';
        } else {
            $stmt = $conex->prepare("DELETE FROM servicios WHERE id_servicio = ?");
            if ($stmt->execute([$id])) {
                $_SESSION['mensaje'] = 'Servicio eliminado correctamente';
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

$sql = "SELECT * FROM servicios";
$params = [];
$where = '';

if (!empty($busqueda)) {
    $where = " WHERE nombre_servicio LIKE ? OR descripcion_servicio LIKE ? OR categoria_servicio LIKE ?";
    $params = ["%$busqueda%", "%$busqueda%", "%$busqueda%"];
}

// Contar total para paginación
$sqlCount = "SELECT COUNT(*) FROM servicios $where";
$stmt = $conex->prepare($sqlCount);
$stmt->execute($params);
$totalServicios = $stmt->fetchColumn();
$totalPaginas = ceil($totalServicios / $porPagina);

// Obtener servicios
$offset = ($pagina - 1) * $porPagina;
$sql .= " $where ORDER BY nombre_servicio ASC LIMIT $offset, $porPagina";

$stmt = $conex->prepare($sql);
if (!empty($busqueda)) {
    $stmt->execute($params);
} else {
    $stmt->execute();
}
$servicios = $stmt->fetchAll();

// Obtener servicios recientes (últimos 4)
$serviciosRecientes = $conex->query("SELECT * FROM servicios ORDER BY id_servicio DESC LIMIT 4")->fetchAll();

// Obtener todos los servicios para la pestaña de búsqueda
$todosServicios = $conex->query("SELECT * FROM servicios ORDER BY nombre_servicio ASC")->fetchAll();

// Procesar solicitud para cargar detalles de servicio
if (isset($_GET['cargar_detalles']) && is_numeric($_GET['cargar_detalles'])) {
    $idServicio = $_GET['cargar_detalles'];
    
    // Obtener información del servicio
    $stmt = $conex->prepare("SELECT * FROM servicios WHERE id_servicio = ?");
    $stmt->execute([$idServicio]);
    $servicio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($servicio) {
        echo '<div class="detail-item">';
        echo '<div class="detail-label">ID</div>';
        echo '<div class="detail-value">' . $servicio['id_servicio'] . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Nombre</div>';
        echo '<div class="detail-value">' . htmlspecialchars($servicio['nombre_servicio']) . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Precio</div>';
        echo '<div class="detail-value">$' . number_format($servicio['precio_servicio'], 0, ',', '.') . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Tiempo Estimado</div>';
        echo '<div class="detail-value">' . htmlspecialchars($servicio['tiempo_estimado']) . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Categoría</div>';
        echo '<div class="detail-value">' . htmlspecialchars($servicio['categoria_servicio']) . '</div>';
        echo '</div>';
        
        if (!empty($servicio['descripcion_servicio'])) {
            echo '<div class="notes-section">';
            echo '<div class="detail-label">Descripción</div>';
            echo '<div class="detail-value">' . nl2br(htmlspecialchars($servicio['descripcion_servicio'])) . '</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Servicio no encontrado</div>';
    }
    exit;
}

require_once __DIR__ . '/../../includes/head.php';
$title = 'Gestión de Servicios | Nacional Tapizados';
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

        /* Estilos para la lista de servicios */
        .service-list {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            background-color: rgba(255, 255, 255, 0.2);
        }

        .service-name {
            font-weight: 500;
            font-size: 1.1rem;
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
        }

        .service-item:hover .service-arrow {
            opacity: 1;
            transform: translateX(3px);
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
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
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

            .service-cards {
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
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="serviceTabsContent">
            <!-- Pestaña de búsqueda -->
            <div class="tab-pane fade show active" id="search" role="tabpanel">
                <!-- Buscador -->
                <div class="search-container">
                    <form class="row g-3">
                        <div class="col-md-8">
                            <input type="text" class="search-input" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" 
                                placeholder="Buscar por nombre, descripción o categoría" autocomplete="off">
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary w-100" type="submit">
                                <i class="fas fa-search me-1"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Resultados iniciales (todos los servicios) -->
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

            <!-- Pestaña de servicios recientes -->
            <div class="tab-pane fade" id="recent" role="tabpanel">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Vista de servicios recientemente agregados.
                </div>
                
                <div class="service-cards">
                    <?php foreach ($serviciosRecientes as $servicio): ?>
                        <div class="service-card" onclick="showOptionsCard(<?= $servicio['id_servicio'] ?>, '<?= htmlspecialchars(addslashes($servicio['nombre_servicio'])) ?>')">
                            <div class="service-card-header">
                                <h3 class="service-card-title"><?= htmlspecialchars($servicio['nombre_servicio']) ?></h3>
                                <span class="service-card-badge">ID: <?= $servicio['id_servicio'] ?></span>
                            </div>
                            <div class="service-card-body">
                                <div class="service-card-detail">
                                    <i class="fas fa-tag"></i>
                                    <span><?= htmlspecialchars($servicio['categoria_servicio']) ?></span>
                                </div>
                                <div class="service-card-detail">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span>$<?= number_format($servicio['precio_servicio'], 0, ',', '.') ?></span>
                                </div>
                                <div class="service-card-detail">
                                    <i class="fas fa-clock"></i>
                                    <span><?= htmlspecialchars($servicio['tiempo_estimado']) ?></span>
                                </div>
                            </div>
                            <div class="service-arrow-card">
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
            <h2 class="options-card-title" id="optionsServiceName">Opciones del Servicio</h2>
            <button class="option-close" onclick="hideOptionsCard()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="options-card-body" id="optionsCardContent">
            <!-- El contenido se cargará dinámicamente -->
        </div>
    </div>

    <!-- Tarjeta flotante para detalles del servicio -->
    <div class="overlay" id="detailOverlay" onclick="hideDetailCard()"></div>
    <div class="floating-card" id="detailCard">
        <div class="card-header">
            <h2 class="card-title" id="detailServiceName">Detalles del Servicio</h2>
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
        function showOptionsCard(serviceId, serviceName) {
            // Actualizar el nombre del servicio en la cabecera
            document.getElementById('optionsServiceName').textContent = serviceName;
            
            // Crear el contenido de las opciones
            const optionsContent = `
                <div class="option-item" onclick="showServiceDetails(${serviceId}, '${serviceName.replace(/'/g, "\\'")}')">
                    <i class="fas fa-eye"></i>
                    <span>Ver detalles</span>
                </div>
                <div class="option-item" onclick="window.location.href='editar.php?id=${serviceId}'">
                    <i class="fas fa-edit"></i>
                    <span>Editar servicio</span>
                </div>
                <div class="option-item" onclick="if(confirm('¿Estás seguro de eliminar este servicio?')) window.location.href='index.php?eliminar=${serviceId}'">
                    <i class="fas fa-trash"></i>
                    <span>Eliminar servicio</span>
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

        // Mostrar detalles del servicio
        function showServiceDetails(serviceId, serviceName) {
            // Ocultar la tarjeta de opciones primero
            hideOptionsCard();
            
            // Actualizar el nombre del servicio en la cabecera
            document.getElementById('detailServiceName').textContent = serviceName;
            
            // Mostrar loading
            document.getElementById('detailCardContent').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando detalles del servicio...</p>
                </div>
            `;
            
            // Mostrar overlay y tarjeta de detalles
            document.getElementById('detailOverlay').style.display = 'block';
            document.getElementById('detailCard').style.display = 'block';
            
            // Cargar detalles via AJAX
            fetch(`?cargar_detalles=${serviceId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('detailCardContent').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('detailCardContent').innerHTML = `
                        <div class="alert alert-danger"> 
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error al cargar los detalles del servicio.
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