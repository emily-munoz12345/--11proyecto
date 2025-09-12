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
    MAX(fecha_cotizacion) as ultima_cotizacion,
    (SELECT COUNT(*) FROM cotizaciones WHERE DATE(fecha_cotizacion) = CURDATE() AND activo = 1) as cotizaciones_hoy,
    SUM(total_cotizacion) as valor_total
FROM cotizaciones WHERE activo = 1")->fetch(PDO::FETCH_ASSOC);

// Obtener las 4 cotizaciones más recientes (excluyendo eliminados - activos = 1)
$cotizacionesRecientes = $conex->query("
    SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo 
    FROM cotizaciones c
    LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
    LEFT JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    WHERE c.activo = 1 
    ORDER BY c.fecha_cotizacion DESC 
    LIMIT 4
")->fetchAll();

// Obtener todas las cotizaciones activas para las pestañas
$todasCotizaciones = $conex->query("
    SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo 
    FROM cotizaciones c
    LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
    LEFT JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    WHERE c.activo = 1 
    ORDER BY c.fecha_cotizacion DESC
")->fetchAll();

// Obtener cotizaciones en la papelera (eliminados - activos = 0)
$cotizacionesEliminadas = $conex->query("
    SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo 
    FROM cotizaciones c
    LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
    LEFT JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    WHERE c.activo = 0 
    ORDER BY c.fecha_cotizacion DESC
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
        SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo 
        FROM cotizaciones c
        LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
        LEFT JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
        WHERE c.activo = 1 
        AND (cl.nombre_cliente LIKE :search 
        OR v.marca_vehiculo LIKE :search 
        OR v.modelo_vehiculo LIKE :search
        OR c.id_cotizacion LIKE :search)
        ORDER BY c.fecha_cotizacion DESC 
        LIMIT 10
    ");
    $stmt->bindParam(':search', $searchTerm);
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// Procesar solicitud para cargar detalles
if (isset($_GET['cargar_detalles']) && is_numeric($_GET['cargar_detalles'])) {
    $idCotizacion = $_GET['cargar_detalles'];
    
    // Obtener información de la cotización
    $stmt = $conex->prepare("
        SELECT c.*, cl.nombre_cliente, cl.telefono_cliente, cl.correo_cliente,
               v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
               u.nombre_completo as vendedor
        FROM cotizaciones c
        LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
        LEFT JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
        LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario
        WHERE c.id_cotizacion = ?
    ");
    $stmt->execute([$idCotizacion]);
    $cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($cotizacion) {
        // Obtener servicios de la cotización
        $stmtServicios = $conex->prepare("
            SELECT s.nombre_servicio, cs.precio
            FROM cotizacion_servicios cs
            LEFT JOIN servicios s ON cs.id_servicio = s.id_servicio
            WHERE cs.id_cotizacion = ?
        ");
        $stmtServicios->execute([$idCotizacion]);
        $servicios = $stmtServicios->fetchAll();
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Número de Cotización</div>';
        echo '<div class="detail-value">#' . $cotizacion['id_cotizacion'] . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Cliente</div>';
        echo '<div class="detail-value">' . htmlspecialchars($cotizacion['nombre_cliente']) . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Vehículo</div>';
        echo '<div class="detail-value">' . htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo']) . ' (' . htmlspecialchars($cotizacion['placa_vehiculo']) . ')</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Vendedor</div>';
        echo '<div class="detail-value">' . htmlspecialchars($cotizacion['vendedor']) . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Estado</div>';
        echo '<div class="detail-value">';
        switch ($cotizacion['estado_cotizacion']) {
            case 'Pendiente':
                echo '<span class="badge bg-warning">Pendiente</span>';
                break;
            case 'Aprobado':
                echo '<span class="badge bg-success">Aprobado</span>';
                break;
            case 'Rechazada':
                echo '<span class="badge bg-danger">Rechazada</span>';
                break;
            case 'Completada':
                echo '<span class="badge bg-info">Completada</span>';
                break;
            default:
                echo htmlspecialchars($cotizacion['estado_cotizacion']);
        }
        echo '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Fecha</div>';
        echo '<div class="detail-value">' . date('d/m/Y H:i', strtotime($cotizacion['fecha_cotizacion'])) . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Servicios</div>';
        echo '<div class="detail-value">';
        if (!empty($servicios)) {
            echo '<ul class="list-unstyled">';
            foreach ($servicios as $servicio) {
                echo '<li>' . htmlspecialchars($servicio['nombre_servicio']) . ' - $' . number_format($servicio['precio'], 2) . '</li>';
            }
            echo '</ul>';
        } else {
            echo 'No hay servicios registrados';
        }
        echo '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Subtotal</div>';
        echo '<div class="detail-value">$' . number_format($cotizacion['subtotal_cotizacion'], 2) . '</div>';
        echo '</div>';
        
        if ($cotizacion['valor_adicional'] > 0) {
            echo '<div class="detail-item">';
            echo '<div class="detail-label">Valor Adicional</div>';
            echo '<div class="detail-value">$' . number_format($cotizacion['valor_adicional'], 2) . '</div>';
            echo '</div>';
        }
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">IVA (' . $cotizacion['iva'] . '%)</div>';
        echo '<div class="detail-value">$' . number_format(($cotizacion['subtotal_cotizacion'] + $cotizacion['valor_adicional']) * ($cotizacion['iva'] / 100), 2) . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Total</div>';
        echo '<div class="detail-value"><strong>$' . number_format($cotizacion['total_cotizacion'], 2) . '</strong></div>';
        echo '</div>';
        
        if (!empty($cotizacion['notas_cotizacion'])) {
            echo '<div class="notes-section">';
            echo '<div class="detail-label">Notas</div>';
            echo '<div class="detail-value">' . nl2br(htmlspecialchars($cotizacion['notas_cotizacion'])) . '</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Cotización no encontrada</div>';
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cotizaciones | Nacional Tapizados</title>
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
            position: relative;
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
            background-color: rgba(140, 74, 63, 0.6);
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

        /* Badges para estados */
        .badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.375rem;
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

            .options-floating-card {
                width: 90%;
                max-width: 300px;
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
                <i class="fas fa-file-invoice-dollar"></i> Gesti&oacute;n de Cotizaciones
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

        <!-- Mensajes -->
        <?php if (!empty($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?>">
                <span><?php echo $_SESSION['mensaje']; ?></span>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
            </div>
            <?php 
            // Limpiar mensaje después de mostrarlo
            $_SESSION['mensaje'] = '';
            $_SESSION['tipo_mensaje'] = '';
            ?>
        <?php endif; ?>

        <!-- Tarjetas de resumen -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total Cotizaciones</h3>
                <p><?php echo $stats['total_cotizaciones']; ?></p>
            </div>
            <div class="summary-card">
                <h3>Cotizaciones Hoy</h3>
                <p><?php echo $stats['cotizaciones_hoy']; ?></p>
            </div>
            <div class="summary-card">
                <h3>Última Cotización</h3>
                <p><?php echo $stats['ultima_cotizacion'] ? date('d/m/Y', strtotime($stats['ultima_cotizacion'])) : 'N/A'; ?></p>
            </div>
        </div>

        <!-- Pestañas -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="recent-tab" data-bs-toggle="tab" data-bs-target="#recent" type="button" role="tab" aria-controls="recent" aria-selected="true">
                    <i class="fas fa-clock"></i> Recientes
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="false">
                    <i class="fas fa-list"></i> Todas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="trash-tab" data-bs-toggle="tab" data-bs-target="#trash" type="button" role="tab" aria-controls="trash" aria-selected="false">
                    <i class="fas fa-trash"></i> Papelera
                </button>
            </li>
        </ul>

        <!-- Contenido de pestañas -->
        <div class="tab-content" id="myTabContent">
            <!-- Pestaña Recientes -->
            <div class="tab-pane fade show active" id="recent" role="tabpanel" aria-labelledby="recent-tab">
                <div class="search-container">
                    <input type="text" id="searchInput" class="search-input" placeholder="Buscar cotización por cliente, vehículo o ID...">
                    <div id="searchResults" class="search-results"></div>
                </div>

                <?php if (count($cotizacionesRecientes) > 0): ?>
                    <div class="client-cards">
                        <?php foreach ($cotizacionesRecientes as $cotizacion): ?>
                            <div class="client-card" onclick="showDetails(<?php echo $cotizacion['id_cotizacion']; ?>)">
                                <div class="client-card-header">
                                    <h3 class="client-card-title">Cotización #<?php echo $cotizacion['id_cotizacion']; ?></h3>
                                    <span class="client-card-badge">
                                        <?php 
                                        switch ($cotizacion['estado_cotizacion']) {
                                            case 'Pendiente': echo 'Pendiente'; break;
                                            case 'Aprobado': echo 'Aprobado'; break;
                                            case 'Rechazada': echo 'Rechazada'; break;
                                            case 'Completada': echo 'Completada'; break;
                                            default: echo $cotizacion['estado_cotizacion'];
                                        }
                                        ?>
                                    </span>
                                </div>
                                <div class="client-card-body">
                                    <div class="client-card-detail">
                                        <i class="fas fa-user"></i>
                                        <span><?php echo htmlspecialchars($cotizacion['nombre_cliente']); ?></span>
                                    </div>
                                    <div class="client-card-detail">
                                        <i class="fas fa-car"></i>
                                        <span><?php echo htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo']); ?></span>
                                    </div>
                                    <div class="client-card-detail">
                                        <i class="fas fa-calendar"></i>
                                        <span><?php echo date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])); ?></span>
                                    </div>
                                    <div class="client-card-detail">
                                        <i class="fas fa-dollar-sign"></i>
                                        <span>$<?php echo number_format($cotizacion['total_cotizacion'], 2); ?></span>
                                    </div>
                                </div>
                                <div class="edit-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No hay cotizaciones recientes.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pestaña Todas -->
            <div class="tab-pane fade" id="all" role="tabpanel" aria-labelledby="all-tab">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Vehículo</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($todasCotizaciones) > 0): ?>
                                <?php foreach ($todasCotizaciones as $cotizacion): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo $cotizacion['id_cotizacion']; ?></td>
                                        <td data-label="Cliente"><?php echo htmlspecialchars($cotizacion['nombre_cliente']); ?></td>
                                        <td data-label="Vehículo"><?php echo htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo']); ?></td>
                                        <td data-label="Fecha"><?php echo date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])); ?></td>
                                        <td data-label="Estado">
                                            <?php 
                                            switch ($cotizacion['estado_cotizacion']) {
                                                case 'Pendiente':
                                                    echo '<span class="badge bg-warning">Pendiente</span>';
                                                    break;
                                                case 'Aprobado':
                                                    echo '<span class="badge bg-success">Aprobado</span>';
                                                    break;
                                                case 'Rechazada':
                                                    echo '<span class="badge bg-danger">Rechazada</span>';
                                                    break;
                                                case 'Completada':
                                                    echo '<span class="badge bg-info">Completada</span>';
                                                    break;
                                                default:
                                                    echo htmlspecialchars($cotizacion['estado_cotizacion']);
                                            }
                                            ?>
                                        </td>
                                        <td data-label="Total">$<?php echo number_format($cotizacion['total_cotizacion'], 2); ?></td>
                                        <td data-label="Acciones">
                                            <button class="btn btn-sm btn-info" onclick="showDetails(<?php echo $cotizacion['id_cotizacion']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="editar.php?id=<?php echo $cotizacion['id_cotizacion']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $cotizacion['id_cotizacion']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No hay cotizaciones registradas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pestaña Papelera -->
            <div class="tab-pane fade" id="trash" role="tabpanel" aria-labelledby="trash-tab">
                <?php if (count($cotizacionesEliminadas) > 0): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Vehículo</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cotizacionesEliminadas as $cotizacion): ?>
                                    <tr class="deleted-item">
                                        <td data-label="ID"><?php echo $cotizacion['id_cotizacion']; ?></td>
                                        <td data-label="Cliente"><?php echo htmlspecialchars($cotizacion['nombre_cliente']); ?></td>
                                        <td data-label="Vehículo"><?php echo htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo']); ?></td>
                                        <td data-label="Fecha"><?php echo date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])); ?></td>
                                        <td data-label="Estado">
                                            <?php 
                                            switch ($cotizacion['estado_cotizacion']) {
                                                case 'Pendiente':
                                                    echo '<span class="badge bg-warning">Pendiente</span>';
                                                    break;
                                                case 'Aprobado':
                                                    echo '<span class="badge bg-success">Aprobado</span>';
                                                    break;
                                                case 'Rechazada':
                                                    echo '<span class="badge bg-danger">Rechazada</span>';
                                                    break;
                                                case 'Completada':
                                                    echo '<span class="badge bg-info">Completada</span>';
                                                    break;
                                                default:
                                                    echo htmlspecialchars($cotizacion['estado_cotizacion']);
                                            }
                                            ?>
                                            <span class="deleted-badge">Eliminado</span>
                                        </td>
                                        <td data-label="Total">$<?php echo number_format($cotizacion['total_cotizacion'], 2); ?></td>
                                        <td data-label="Acciones">
                                            <button class="btn btn-sm btn-success" onclick="restoreItem(<?php echo $cotizacion['id_cotizacion']; ?>)">
                                                <i class="fas fa-undo"></i> Restaurar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> La papelera está vacía.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tarjeta flotante para detalles -->
    <div class="overlay" id="overlay"></div>
    <div class="floating-card" id="detailsCard">
        <div class="card-header">
            <h2 class="card-title">Detalles de Cotización</h2>
            <button class="close-card" onclick="closeDetails()">&times;</button>
        </div>
        <div class="card-content" id="detailsContent">
            <!-- Los detalles se cargarán aquí mediante AJAX -->
        </div>
    </div>

    <!-- Tarjeta flotante para opciones -->
    <div class="options-floating-card" id="optionsCard">
        <div class="options-card-header">
            <h3 class="options-card-title">Opciones de Cotización</h3>
            <button class="option-close" onclick="closeOptions()">&times;</button>
        </div>
        <div class="options-card-body">
            <a href="crear.php" class="option-item">
                <i class="fas fa-plus"></i> Nueva Cotización
            </a>
            <a href="../../dashboard.php" class="option-item">
                <i class="fas fa-home"></i> Volver al Dashboard
            </a>
            <a href="../clientes/index.php" class="option-item">
                <i class="fas fa-users"></i> Gestión de Clientes
            </a>
            <a href="../vehiculos/index.php" class="option-item">
                <i class="fas fa-car"></i> Gestión de Vehículos
            </a>
            <a href="../servicios/index.php" class="option-item">
                <i class="fas fa-tools"></i> Gestión de Servicios
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Búsqueda en tiempo real
        document.getElementById('searchInput').addEventListener('input', function() {
            const query = this.value.trim();
            const resultsContainer = document.getElementById('searchResults');
            
            if (query.length < 2) {
                resultsContainer.style.display = 'none';
                return;
            }
            
            fetch(`index.php?ajax=1&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = '';
                    
                    if (data.length === 0) {
                        resultsContainer.style.display = 'none';
                        return;
                    }
                    
                    data.forEach(cotizacion => {
                        const item = document.createElement('div');
                        item.className = 'search-result-item';
                        item.innerHTML = `
                            <div>
                                <div class="search-client-name">Cotización #${cotizacion.id_cotizacion} - ${cotizacion.nombre_cliente}</div>
                                <div class="search-client-info">${cotizacion.marca_vehiculo} ${cotizacion.modelo_vehiculo}</div>
                                <div class="search-client-date">${new Date(cotizacion.fecha_cotizacion).toLocaleDateString()}</div>
                            </div>
                            <div>
                                $${parseFloat(cotizacion.total_cotizacion).toFixed(2)}
                            </div>
                        `;
                        item.addEventListener('click', () => {
                            showDetails(cotizacion.id_cotizacion);
                            resultsContainer.style.display = 'none';
                            document.getElementById('searchInput').value = '';
                        });
                        resultsContainer.appendChild(item);
                    });
                    
                    resultsContainer.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error en la búsqueda:', error);
                    resultsContainer.style.display = 'none';
                });
        });

        // Cerrar resultados al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                document.getElementById('searchResults').style.display = 'none';
            }
        });

        // Mostrar detalles de la cotización
        function showDetails(id) {
            const overlay = document.getElementById('overlay');
            const detailsCard = document.getElementById('detailsCard');
            const detailsContent = document.getElementById('detailsContent');
            
            // Mostrar overlay y tarjeta
            overlay.style.display = 'block';
            detailsCard.style.display = 'block';
            
            // Cargar detalles mediante AJAX
            fetch(`index.php?cargar_detalles=${id}`)
                .then(response => response.text())
                .then(data => {
                    detailsContent.innerHTML = data;
                })
                .catch(error => {
                    console.error('Error al cargar detalles:', error);
                    detailsContent.innerHTML = '<div class="alert alert-danger">Error al cargar los detalles</div>';
                });
        }

        // Cerrar detalles
        function closeDetails() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('detailsCard').style.display = 'none';
        }

        // Confirmar eliminación
        function confirmDelete(id) {
            if (confirm('¿Estás seguro de que deseas mover esta cotización a la papelera?')) {
                window.location.href = `eliminar.php?id=${id}`;
            }
        }

        // Restaurar elemento
        function restoreItem(id) {
            if (confirm('¿Estás seguro de que deseas restaurar esta cotización?')) {
                window.location.href = `restaurar.php?id=${id}`;
            }
        }

        // Mostrar opciones
        function showOptions() {
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('optionsCard').style.display = 'block';
        }

        // Cerrar opciones
        function closeOptions() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('optionsCard').style.display = 'none';
        }
    </script>
</body>

</html>