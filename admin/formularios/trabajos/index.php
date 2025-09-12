<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin() && !isTechnician()) {
    header('Location: ../dashboard.php');
    exit;
}

// Inicializar variables de sesión para mensajes
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
    $_SESSION['tipo_mensaje'] = '';
}

// Obtener parámetros de filtrado
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filtro_cliente = isset($_GET['cliente']) ? $_GET['cliente'] : '';

// Construir consulta base
$sql = "SELECT t.*, c.id_cotizacion, cl.nombre_cliente, 
        v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
        (SELECT COUNT(*) FROM fotos_trabajos ft WHERE ft.id_trabajos = t.id_trabajos) as num_fotos
        FROM trabajos t
        JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
        JOIN clientes cl ON c.id_cliente = cl.id_cliente
        JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo";

$params = [];
$conditions = [];

// Aplicar filtros
if (!empty($filtro_estado)) {
    $conditions[] = "t.estado = ?";
    $params[] = $filtro_estado;
}

if (!empty($filtro_cliente)) {
    $conditions[] = "cl.nombre_cliente LIKE ?";
    $params[] = "%$filtro_cliente%";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY t.fecha_inicio DESC, t.id_trabajos DESC";

try {
    $stmt = $conex->prepare($sql);
    $stmt->execute($params);
    $trabajos = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al obtener trabajos: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    $trabajos = [];
}

// Obtener trabajos recientes (últimos 4)
$trabajosRecientes = [];
try {
    $sqlRecientes = "SELECT t.*, c.id_cotizacion, cl.nombre_cliente, 
                    v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
                    (SELECT COUNT(*) FROM fotos_trabajos ft WHERE ft.id_trabajos = t.id_trabajos) as num_fotos
                    FROM trabajos t
                    JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
                    JOIN clientes cl ON c.id_cliente = cl.id_cliente
                    JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
                    ORDER BY t.fecha_inicio DESC LIMIT 4";
    $stmtRecientes = $conex->query($sqlRecientes);
    $trabajosRecientes = $stmtRecientes->fetchAll();
} catch (PDOException $e) {
    // Error silencioso para trabajos recientes
}

// Procesar solicitud para cargar detalles de trabajo
if (isset($_GET['cargar_detalles']) && is_numeric($_GET['cargar_detalles'])) {
    $idTrabajo = $_GET['cargar_detalles'];
    
    // Obtener información del trabajo
    $sqlDetalles = "SELECT t.*, c.id_cotizacion, cl.nombre_cliente, cl.telefono_cliente, cl.email_cliente,
                   v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo, v.anio_vehiculo, v.color_vehiculo,
                   (SELECT COUNT(*) FROM fotos_trabajos ft WHERE ft.id_trabajos = t.id_trabajos) as num_fotos
                   FROM trabajos t
                   JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
                   JOIN clientes cl ON c.id_cliente = cl.id_cliente
                   JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
                   WHERE t.id_trabajos = ?";
    
    $stmt = $conex->prepare($sqlDetalles);
    $stmt->execute([$idTrabajo]);
    $trabajo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($trabajo) {
        echo '<div class="detail-item">';
        echo '<div class="detail-label">ID</div>';
        echo '<div class="detail-value">' . $trabajo['id_trabajos'] . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Cliente</div>';
        echo '<div class="detail-value">' . htmlspecialchars($trabajo['nombre_cliente']) . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Contacto</div>';
        echo '<div class="detail-value">' . htmlspecialchars($trabajo['telefono_cliente']) . '<br>' . htmlspecialchars($trabajo['email_cliente'] ?? 'N/A') . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Vehículo</div>';
        echo '<div class="detail-value">' . htmlspecialchars($trabajo['marca_vehiculo'] . ' ' . $trabajo['modelo_vehiculo']) . 
             ' (' . htmlspecialchars($trabajo['placa_vehiculo']) . ')<br>' .
             'Año: ' . htmlspecialchars($trabajo['anio_vehiculo'] ?? 'N/A') . ', Color: ' . htmlspecialchars($trabajo['color_vehiculo'] ?? 'N/A') . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Fecha Inicio</div>';
        echo '<div class="detail-value">' . date('d/m/Y', strtotime($trabajo['fecha_inicio'])) . '</div>';
        echo '</div>';
        
        if (!empty($trabajo['fecha_fin'])) {
            echo '<div class="detail-item">';
            echo '<div class="detail-label">Fecha Fin</div>';
            echo '<div class="detail-value">' . date('d/m/Y', strtotime($trabajo['fecha_fin'])) . '</div>';
            echo '</div>';
        }
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Estado</div>';
        echo '<div class="detail-value"><span class="badge badge-' . str_replace(' ', '-', strtolower($trabajo['estado'])) . '">' . $trabajo['estado'] . '</span></div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Fotos</div>';
        echo '<div class="detail-value">' . $trabajo['num_fotos'] . ' fotos</div>';
        echo '</div>';
        
        if (!empty($trabajo['descripcion'])) {
            echo '<div class="notes-section">';
            echo '<div class="detail-label">Descripción</div>';
            echo '<div class="detail-value">' . nl2br(htmlspecialchars($trabajo['descripcion'])) . '</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Trabajo no encontrado</div>';
    }
    exit;
}

require_once __DIR__ . '/../../includes/head.php';
$title = 'Gestión de Trabajos | Nacional Tapizados';
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

        /* Estilos para la lista de trabajos */
        .work-list {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .work-item {
            padding: 1.2rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .work-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .work-name {
            font-weight: 500;
            font-size: 1.1rem;
        }

        .work-description {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 0.3rem;
        }

        .work-arrow {
            margin-left: 1rem;
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .work-item:hover .work-arrow {
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

        .btn-outline-secondary {
            background-color: transparent;
            border: 1px solid var(--secondary-color);
            color: var(--text-color);
        }

        .btn-outline-secondary:hover {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-outline-danger {
            background-color: transparent;
            border: 1px solid var(--danger-color);
            color: var(--text-color);
        }

        .btn-outline-danger:hover {
            background-color: var(--danger-color);
            color: white;
        }

        /* Estilos para badges */
        .badge {
            padding: 0.35rem 0.65rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-pendiente {
            background-color: var(--warning-color);
            color: #000;
        }

        .badge-en-proceso {
            background-color: var(--info-color);
            color: #000;
        }

        .badge-completado {
            background-color: var(--success-color);
            color: white;
        }

        .badge-cancelado {
            background-color: var(--danger-color);
            color: white;
        }

        /* Estilos para tarjetas flotantes */
        .floating-card {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            padding: 2rem;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            border: 1px solid var(--border-color);
            display: none;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .card-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .close-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close-btn:hover {
            color: var(--text-color);
        }

        .detail-item {
            display: flex;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .detail-label {
            width: 120px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .detail-value {
            flex: 1;
        }

        .notes-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        /* Overlay para tarjetas flotantes */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 999;
            display: none;
        }

        /* Estilos para mensajes */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: none;
            backdrop-filter: blur(5px);
        }

        .alert-success {
            background-color: rgba(25, 135, 84, 0.8);
            color: white;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.8);
            color: white;
        }

        .alert-warning {
            background-color: rgba(255, 193, 7, 0.8);
            color: black;
        }

        /* Estilos para filtros */
        .filter-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .filter-select, .filter-input {
            padding: 0.5rem;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            background-color: var(--bg-transparent-light);
            color: var(--text-color);
            min-width: 180px;
        }

        .filter-input::placeholder {
            color: var(--text-muted);
        }

        /* Estilos para trabajos recientes */
        .recent-works {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .recent-card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .recent-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .recent-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .recent-card-title {
            font-size: 1.1rem;
            font-weight: 500;
            margin: 0;
        }

        .recent-card-body {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .recent-card-footer {
            margin-top: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            
            .nav-tabs .nav-link {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
            
            .filter-container {
                flex-direction: column;
            }
            
            .filter-select, .filter-input {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    
    <div class="main-container">
        <div class="header-section">
            <h1 class="page-title"><i class="fas fa-tools"></i> GESTIÓN DE TRABAJOS</h1>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Trabajo
            </a>
        </div>

        <?php if (!empty($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
                <?= $_SESSION['mensaje'] ?>
            </div>
            <?php 
            $_SESSION['mensaje'] = '';
            $_SESSION['tipo_mensaje'] = '';
            ?>
        <?php endif; ?>

        <!-- Pestañas de navegación -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="search-tab" data-bs-toggle="tab" data-bs-target="#search" type="button" role="tab" aria-controls="search" aria-selected="true">
                    <i class="fas fa-search me-1"></i> Buscar
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="recent-tab" data-bs-toggle="tab" data-bs-target="#recent" type="button" role="tab" aria-controls="recent" aria-selected="false">
                    <i class="fas fa-history me-1"></i> Recientes
                </button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Pestaña de búsqueda -->
            <div class="tab-pane fade show active" id="search" role="tabpanel" aria-labelledby="search-tab">
                <div class="filter-container">
                    <select class="filter-select" id="filtro-estado">
                        <option value="">Todos los estados</option>
                        <option value="Pendiente" <?= $filtro_estado == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="En Proceso" <?= $filtro_estado == 'En Proceso' ? 'selected' : '' ?>>En Proceso</option>
                        <option value="Completado" <?= $filtro_estado == 'Completado' ? 'selected' : '' ?>>Completado</option>
                        <option value="Cancelado" <?= $filtro_estado == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                    
                    <input type="text" class="filter-input" id="filtro-cliente" placeholder="Filtrar por cliente" value="<?= htmlspecialchars($filtro_cliente) ?>">
                    
                    <button class="btn btn-secondary" id="aplicar-filtros">
                        <i class="fas fa-filter"></i> Aplicar Filtros
                    </button>
                    
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Vehículo</th>
                                <th>Fecha Inicio</th>
                                <th>Estado</th>
                                <th>Fotos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($trabajos) > 0): ?>
                                <?php foreach ($trabajos as $trabajo): ?>
                                    <tr>
                                        <td><?= $trabajo['id_trabajos'] ?></td>
                                        <td><?= htmlspecialchars($trabajo['nombre_cliente']) ?></td>
                                        <td><?= htmlspecialchars($trabajo['marca_vehiculo'] . ' ' . $trabajo['modelo_vehiculo'] . ' (' . $trabajo['placa_vehiculo'] . ')') ?></td>
                                        <td><?= date('d/m/Y', strtotime($trabajo['fecha_inicio'])) ?></td>
                                        <td>
                                            <span class="badge badge-<?= str_replace(' ', '-', strtolower($trabajo['estado'])) ?>">
                                                <?= $trabajo['estado'] ?>
                                            </span>
                                        </td>
                                        <td><?= $trabajo['num_fotos'] ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary view-details" data-id="<?= $trabajo['id_trabajos'] ?>">
                                                <i class="fas fa-eye"></i> Ver
                                            </button>
                                            <a href="edit.php?id=<?= $trabajo['id_trabajos'] ?>" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <a href="delete.php?id=<?= $trabajo['id_trabajos'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este trabajo?');">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">No se encontraron trabajos</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pestaña de trabajos recientes -->
            <div class="tab-pane fade" id="recent" role="tabpanel" aria-labelledby="recent-tab">
                <h3 class="mb-4">Trabajos Recientes</h3>
                
                <?php if (count($trabajosRecientes) > 0): ?>
                    <div class="recent-works">
                        <?php foreach ($trabajosRecientes as $trabajo): ?>
                            <div class="recent-card" data-id="<?= $trabajo['id_trabajos'] ?>">
                                <div class="recent-card-header">
                                    <h4 class="recent-card-title"><?= htmlspecialchars($trabajo['nombre_cliente']) ?></h4>
                                    <span class="badge badge-<?= str_replace(' ', '-', strtolower($trabajo['estado'])) ?>">
                                        <?= $trabajo['estado'] ?>
                                    </span>
                                </div>
                                <div class="recent-card-body">
                                    <p><strong>Vehículo:</strong> <?= htmlspecialchars($trabajo['marca_vehiculo'] . ' ' . $trabajo['modelo_vehiculo']) ?></p>
                                    <p><strong>Placa:</strong> <?= htmlspecialchars($trabajo['placa_vehiculo']) ?></p>
                                    <p><strong>Inicio:</strong> <?= date('d/m/Y', strtotime($trabajo['fecha_inicio'])) ?></p>
                                </div>
                                <div class="recent-card-footer">
                                    <span><?= $trabajo['num_fotos'] ?> fotos</span>
                                    <button class="btn btn-sm btn-primary view-details" data-id="<?= $trabajo['id_trabajos'] ?>">
                                        <i class="fas fa-eye"></i> Detalles
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        No hay trabajos recientes para mostrar.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tarjeta flotante para detalles -->
    <div class="overlay" id="overlay"></div>
    <div class="floating-card" id="details-card">
        <div class="card-header">
            <h3 class="card-title">Detalles del Trabajo</h3>
            <button class="close-btn" id="close-details">&times;</button>
        </div>
        <div class="card-body" id="details-content">
            <!-- Los detalles se cargarán aquí mediante AJAX -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('overlay');
            const detailsCard = document.getElementById('details-card');
            const detailsContent = document.getElementById('details-content');
            const closeDetailsBtn = document.getElementById('close-details');
            
            // Función para mostrar detalles
            function showDetails(id) {
                fetch(`?cargar_detalles=${id}`)
                    .then(response => response.text())
                    .then(data => {
                        detailsContent.innerHTML = data;
                        detailsCard.style.display = 'block';
                        overlay.style.display = 'block';
                        document.body.style.overflow = 'hidden';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        detailsContent.innerHTML = '<div class="alert alert-danger">Error al cargar los detalles</div>';
                    });
            }
            
            // Event listeners para botones de ver detalles
            document.querySelectorAll('.view-details').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    showDetails(id);
                });
            });
            
            // Event listeners para tarjetas recientes
            document.querySelectorAll('.recent-card').forEach(card => {
                card.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    showDetails(id);
                });
            });
            
            // Cerrar tarjeta de detalles
            closeDetailsBtn.addEventListener('click', function() {
                detailsCard.style.display = 'none';
                overlay.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
            
            overlay.addEventListener('click', function() {
                detailsCard.style.display = 'none';
                overlay.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
            
            // Aplicar filtros
            document.getElementById('aplicar-filtros').addEventListener('click', function() {
                const estado = document.getElementById('filtro-estado').value;
                const cliente = document.getElementById('filtro-cliente').value;
                
                let url = 'index.php?';
                if (estado) url += `estado=${estado}&`;
                if (cliente) url += `cliente=${encodeURIComponent(cliente)}`;
                
                window.location.href = url;
            });
        });
    </script>
</body>
</html>