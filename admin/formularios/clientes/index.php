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
    COUNT(*) as total_clientes,
    MAX(fecha_registro) as ultimo_registro,
    (SELECT COUNT(*) FROM clientes WHERE DATE(fecha_registro) = CURDATE() AND activo = 1) as registros_hoy
FROM clientes WHERE activo = 1")->fetch(PDO::FETCH_ASSOC);

// Obtener los 4 clientes más recientes (excluyendo eliminados - activos = 1)
$clientesRecientes = $conex->query("SELECT * FROM clientes WHERE activo = 1 ORDER BY fecha_registro DESC LIMIT 4")->fetchAll();

// Obtener todos los clientes activos para las pestañas
$clientesEditar = $conex->query("SELECT * FROM clientes WHERE activo = 1 ORDER BY nombre_cliente ASC")->fetchAll();

// Obtener clientes en la papelera (eliminados - activos = 0)
$clientesEliminar = $conex->query("SELECT * FROM clientes WHERE activo = 0 ORDER BY fecha_registro DESC")->fetchAll();

// Obtener todos los clientes activos para vista general
$todosClientes = $conex->query("SELECT * FROM clientes WHERE activo = 1 ORDER BY nombre_cliente ASC")->fetchAll();

// Verificar si existe la tabla registro_eliminaciones antes de usarla
$tablaExiste = false;
try {
    $result = $conex->query("SELECT 1 FROM registro_eliminaciones LIMIT 1");
    $tablaExiste = true;
} catch (Exception $e) {
    $tablaExiste = false;
}

// Obtener clientes editados con información de edición (solo si la tabla existe)
$clientesEditados = [];
if ($tablaExiste) {
    $clientesEditados = $conex->query("
        SELECT c.*, 
               COUNT(re.id) as total_ediciones,
               MAX(re.fecha_eliminacion) as ultima_edicion
        FROM clientes c
        LEFT JOIN registro_eliminaciones re ON re.tabla = 'clientes' AND re.id_registro = c.id_cliente AND re.accion = 'MODIFICACION'
        WHERE c.activo = 1
        GROUP BY c.id_cliente
        HAVING total_ediciones > 0
        ORDER BY ultima_edicion DESC
    ")->fetchAll();
} else {
    // Alternativa: obtener clientes modificados recientemente (últimos 30 días)
    $clientesEditados = $conex->query("
        SELECT * FROM clientes 
        WHERE activo = 1 AND fecha_actualizacion >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ORDER BY fecha_actualizacion DESC
    ")->fetchAll();
}

// Obtener historial de ediciones para un cliente específico si se solicita
$historialEdiciones = [];
if (isset($_GET['ver_ediciones']) && is_numeric($_GET['ver_ediciones']) && $tablaExiste) {
    $idCliente = $_GET['ver_ediciones'];
    $stmt = $conex->prepare("
        SELECT re.*, u.nombre_completo as editor 
        FROM registro_eliminaciones re 
        LEFT JOIN usuarios u ON re.eliminado_por = u.id_usuario 
        WHERE re.tabla = 'clientes' AND re.id_registro = ? AND re.accion = 'MODIFICACION'
        ORDER BY re.fecha_eliminacion DESC
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
                            WHERE activo = 1 
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

// Procesar solicitud para cargar detalles de edición
if (isset($_GET['cargar_detalles']) && is_numeric($_GET['cargar_detalles'])) {
    $idCliente = $_GET['cargar_detalles'];
    
    // Obtener información del cliente
    $stmt = $conex->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$idCliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($cliente) {
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Nombre</div>';
        echo '<div class="detail-value" data-client-name="' . htmlspecialchars($cliente['nombre_cliente']) . '">' . htmlspecialchars($cliente['nombre_cliente']) . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Teléfono</div>';
        echo '<div class="detail-value">' . htmlspecialchars($cliente['telefono_cliente']) . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Correo</div>';
        echo '<div class="detail-value">' . htmlspecialchars($cliente['correo_cliente']) . '</div>';
        echo '</div>';
        
        echo '<div class="detail-item">';
        echo '<div class="detail-label">Fecha de Registro</div>';
        echo '<div class="detail-value">' . date('d/m/Y H:i', strtotime($cliente['fecha_registro'])) . '</div>';
        echo '</div>';
        
        if (!empty($cliente['fecha_actualizacion']) && $cliente['fecha_actualizacion'] != $cliente['fecha_registro']) {
            echo '<div class="detail-item">';
            echo '<div class="detail-label">Última Actualización</div>';
            echo '<div class="detail-value">' . date('d/m/Y H:i', strtotime($cliente['fecha_actualizacion'])) . '</div>';
            echo '</div>';
        }
        
        if (!empty($cliente['notas_cliente'])) {
            echo '<div class="notes-section">';
            echo '<div class="detail-label">Notas</div>';
            echo '<div class="detail-value">' . nl2br(htmlspecialchars($cliente['notas_cliente'])) . '</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Cliente no encontrado</div>';
    }
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

        /* Flecha para tarjetas de editados */
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
                        <div class="client-item" data-client-id="<?= $cliente['id_cliente'] ?>" onclick="showOptionsCard(<?= $cliente['id_cliente'] ?>, '<?= htmlspecialchars(addslashes($cliente['nombre_cliente'])) ?>')">
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
                        <div class="client-card" onclick="showOptionsCard(<?= $cliente['id_cliente'] ?>, '<?= htmlspecialchars(addslashes($cliente['nombre_cliente'])) ?>')">
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
                            <div class="edit-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Pestaña de eliminación -->
            <div class="tab-pane fade" id="delete" role="tabpanel">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Clientes eliminados. Estos registros se pueden restaurar.
                </div>
                
                <?php if (!empty($clientesEliminar)): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Teléfono</th>
                                    <th>Correo</th>
                                    <th>Fecha Eliminación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clientesEliminar as $cliente): ?>
                                    <tr class="deleted-item">
                                        <td data-label="ID"><?= $cliente['id_cliente'] ?></td>
                                        <td data-label="Cliente">
                                            <?= htmlspecialchars($cliente['nombre_cliente']) ?>
                                        </td>
                                        <td data-label="Teléfono"><?= htmlspecialchars($cliente['telefono_cliente']) ?></td>
                                        <td data-label="Correo"><?= htmlspecialchars($cliente['correo_cliente']) ?></td>
                                        <td data-label="Fecha Eliminación">
                                            <?= !empty($cliente['fecha_eliminacion']) ? date('d/m/Y H:i', strtotime($cliente['fecha_eliminacion'])) : 'N/A' ?>
                                        </td>
                                        <td data-label="Acciones">
                                            <a href="restaurar.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-success btn-sm" onclick="return confirm('¿Restaurar a <?= htmlspecialchars(addslashes($cliente['nombre_cliente'])) ?>?')">
                                                <i class="fas fa-undo"></i> Restaurar
                                            </a>
                                            <?php if (isAdmin()): ?>
                                            <a href="eliminar_permanentemente.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿ESTÁS SEGURO? Esta acción eliminará permanentemente a <?= htmlspecialchars(addslashes($cliente['nombre_cliente'])) ?> y no se podrá recuperar.')">
                                                <i class="fas fa-trash"></i> Eliminar Permanentemente
                                            </a>
                                            <?php endif; ?>
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

    <!-- Tarjeta flotante para detalles de edición -->
    <div class="overlay" id="editOverlay" onclick="hideEditDetails()"></div>
    <div class="floating-card" id="editDetailCard">
        <div class="card-header">
            <h2 class="card-title" id="editClientName">Detalles del Cliente</h2>
            <button class="close-detail close-card" onclick="hideEditDetails()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="card-content" id="editDetailContent">
            <!-- El contenido se cargará dinámicamente -->
        </div>
    </div>

    <!-- Nueva tarjeta flotante para opciones -->
    <div class="overlay" id="optionsOverlay" onclick="hideOptionsCard()"></div>
    <div class="options-floating-card" id="optionsCard">
        <div class="options-card-header">
            <h2 class="options-card-title" id="optionsClientName">Opciones del Cliente</h2>
            <button class="option-close" onclick="hideOptionsCard()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="options-card-body" id="optionsCardContent">
            <!-- El contenido se cargará dinámicamente -->
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
                                item.setAttribute('data-client-id', client.id_cliente);
                                item.innerHTML = `
                                    <div onclick="showOptionsCard(${client.id_cliente}, '${client.nombre_cliente.replace(/'/g, "\\'")}')">
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

        // Mostrar tarjeta de opciones
        function showOptionsCard(clientId, clientName) {
            // Actualizar el nombre del cliente en la cabecera
            document.getElementById('optionsClientName').textContent = clientName;
            
            // Crear el contenido de las opciones
            const optionsContent = `
                <div class="option-item" onclick="window.location.href='ver.php?id=${clientId}'">
                    <i class="fas fa-eye"></i>
                    <span>Ver detalles</span>
                </div>
                <div class="option-item" onclick="window.location.href='editar.php?id=${clientId}'">
                    <i class="fas fa-edit"></i>
                    <span>Editar cliente</span>
                </div>
                <div class="option-item" onclick="if(confirm('¿Estás seguro de eliminar a ${clientName}?')) window.location.href='eliminar.php?id=${clientId}'">
                    <i class="fas fa-trash"></i>
                    <span>Eliminar cliente</span>
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

        // Mostrar detalles de edición
        function showEditDetails(clientId, clientName) {
            // Mostrar loading
            document.getElementById('editClientName').textContent = 'Historial de ' + clientName;
            document.getElementById('editDetailContent').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando historial de ediciones...</p>
                </div>
            `;
            
            // Mostrar overlay y tarjeta flotante
            document.getElementById('editOverlay').style.display = 'block';
            document.getElementById('editDetailCard').style.display = 'block';
            
            // Cargar historial via AJAX
            fetch(`cargar_historial.php?id=${clientId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('editDetailContent').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('editDetailContent').innerHTML = `
                        <div class="alert alert-danger"> 
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error al cargar el historial de ediciones.
                        </div>
                    `;
                });
        }

        // Ocultar detalles de edición
        function hideEditDetails() {
            document.getElementById('editOverlay').style.display = 'none';
            document.getElementById('editDetailCard').style.display = 'none';
        }

        // Cerrar con tecla ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideOptionsCard();
                hideEditDetails();
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
    </script>
</body>

</html>