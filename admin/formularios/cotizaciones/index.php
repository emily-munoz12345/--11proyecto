<?php
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

// Verificar si la tabla cotizaciones tiene la columna activo
$tablaInfo = $conex->query("SHOW COLUMNS FROM cotizaciones LIKE 'activo'")->fetch();
$tieneColumnaActivo = !empty($tablaInfo);

// Verificar si la tabla cotizaciones tiene la columna fecha_eliminacion
$tablaInfoEliminacion = $conex->query("SHOW COLUMNS FROM cotizaciones LIKE 'fecha_eliminacion'")->fetch();
$tieneColumnaFechaEliminacion = !empty($tablaInfoEliminacion);

// Procesar cambio de estado
if (isset($_GET['cambiar_estado'])) {
    $id = intval($_GET['id']);
    $nuevo_estado = $_GET['estado'];

    try {
        $stmt = $conex->prepare("UPDATE cotizaciones SET estado_cotizacion = ? WHERE id_cotizacion = ?");
        if ($stmt->execute([$nuevo_estado, $id])) {
            $_SESSION['mensaje'] = 'Estado de cotización actualizado';
            $_SESSION['tipo_mensaje'] = 'success';
        }
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'Error al actualizar: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
}

// Procesar eliminación (mover a papelera)
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    
    try {
        if ($tieneColumnaActivo) {
            // Si tiene columna activo, marcamos como inactivo
            $stmt = $conex->prepare("UPDATE cotizaciones SET activo = 0" . 
                                   ($tieneColumnaFechaEliminacion ? ", fecha_eliminacion = NOW()" : "") . 
                                   " WHERE id_cotizacion = ?");
        } else {
            // Si no tiene columna activo, eliminamos permanentemente
            $stmt = $conex->prepare("DELETE FROM cotizaciones WHERE id_cotizacion = ?");
        }
        
        if ($stmt->execute([$id])) {
            $_SESSION['mensaje'] = $tieneColumnaActivo ? 
                'Cotización movida a la papelera' : 'Cotización eliminada permanentemente';
            $_SESSION['tipo_mensaje'] = 'success';
        }
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'Error al eliminar: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    header('Location: index.php');
    exit;
}

// Procesar restauración desde papelera
if (isset($_GET['restaurar'])) {
    $id = intval($_GET['restaurar']);
    
    try {
        if ($tieneColumnaActivo) {
            $stmt = $conex->prepare("UPDATE cotizaciones SET activo = 1" . 
                                   ($tieneColumnaFechaEliminacion ? ", fecha_eliminacion = NULL" : "") . 
                                   " WHERE id_cotizacion = ?");
            if ($stmt->execute([$id])) {
                $_SESSION['mensaje'] = 'Cotización restaurada correctamente';
                $_SESSION['tipo_mensaje'] = 'success';
            }
        }
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'Error al restaurar: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    header('Location: index.php');
    exit;
}

// Procesar eliminación permanente
if (isset($_GET['eliminar_permanentemente'])) {
    $id = intval($_GET['eliminar_permanentemente']);
    
    try {
        $stmt = $conex->prepare("DELETE FROM cotizaciones WHERE id_cotizacion = ?");
        if ($stmt->execute([$id])) {
            $_SESSION['mensaje'] = 'Cotización eliminada permanentemente';
            $_SESSION['tipo_mensaje'] = 'success';
        }
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'Error al eliminar: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    header('Location: index.php');
    exit;
}

// Obtener estadísticas generales
$whereActivo = $tieneColumnaActivo ? " WHERE c.activo = 1" : "";
$stats = $conex->query("SELECT 
    COUNT(*) as total_cotizaciones,
    SUM(CASE WHEN c.estado_cotizacion = 'Aprobado' THEN 1 ELSE 0 END) as aprobadas,
    SUM(CASE WHEN c.estado_cotizacion = 'Rechazada' THEN 1 ELSE 0 END) as rechazadas,
    SUM(CASE WHEN c.estado_cotizacion = 'Completada' THEN 1 ELSE 0 END) as completadas,
    MAX(c.fecha_cotizacion) as ultima_cotizacion,
    (SELECT COUNT(*) FROM cotizaciones WHERE DATE(fecha_cotizacion) = CURDATE()" . 
    ($tieneColumnaActivo ? " AND activo = 1" : "") . ") as cotizaciones_hoy
FROM cotizaciones c" . $whereActivo)->fetch(PDO::FETCH_ASSOC);

// Obtener las 4 cotizaciones más recientes
$whereActivoRecent = $tieneColumnaActivo ? " WHERE c.activo = 1" : "";
$orderByRecent = $tieneColumnaActivo ? "c.fecha_cotizacion" : "c.fecha_cotizacion";
$cotizacionesRecientes = $conex->query("SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
        u.nombre_completo as nombre_usuario
        FROM cotizaciones c
        JOIN clientes cl ON c.id_cliente = cl.id_cliente
        JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
        JOIN usuarios u ON c.id_usuario = u.id_usuario
        " . $whereActivoRecent . " ORDER BY " . $orderByRecent . " DESC LIMIT 4")->fetchAll();

// Obtener todas las cotizaciones activas para la pestaña de búsqueda
$whereActivoAll = $tieneColumnaActivo ? " WHERE c.activo = 1" : "";
$todasCotizaciones = $conex->query("SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
        u.nombre_completo as nombre_usuario
        FROM cotizaciones c
        JOIN clientes cl ON c.id_cliente = cl.id_cliente
        JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
        JOIN usuarios u ON c.id_usuario = u.id_usuario
        " . $whereActivoAll . " ORDER BY cl.nombre_cliente ASC")->fetchAll();

// Obtener cotizaciones en la papelera (solo si existe la columna activo)
if ($tieneColumnaActivo) {
    $orderByPapelera = $tieneColumnaFechaEliminacion ? "c.fecha_eliminacion" : "c.fecha_cotizacion";
    $cotizacionesPapelera = $conex->query("SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
            u.nombre_completo as nombre_usuario
            FROM cotizaciones c
            JOIN clientes cl ON c.id_cliente = cl.id_cliente
            JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
            JOIN usuarios u ON c.id_usuario = u.id_usuario
            WHERE c.activo = 0 ORDER BY " . $orderByPapelera . " DESC")->fetchAll();
} else {
    $cotizacionesPapelera = [];
}

// Procesar búsqueda si es una solicitud AJAX
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    if (!isset($_GET['q']) || strlen($_GET['q']) < 2) {
        echo json_encode([]);
        exit;
    }

    $searchTerm = '%' . $_GET['q'] . '%';
    $whereActivoSearch = $tieneColumnaActivo ? " AND c.activo = 1" : "";
    $stmt = $conex->prepare("SELECT c.*, cl.nombre_cliente, v.placa_vehiculo 
                            FROM cotizaciones c
                            JOIN clientes cl ON c.id_cliente = cl.id_cliente
                            JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
                            WHERE (cl.nombre_cliente LIKE :search 
                            OR v.placa_vehiculo LIKE :search 
                            OR c.estado_cotizacion LIKE :search)
                            " . $whereActivoSearch . "
                            ORDER BY cl.nombre_cliente ASC 
                            LIMIT 10");
    $stmt->bindParam(':search', $searchTerm);
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

require_once __DIR__ . '/../../includes/head.php';
$title = 'Gestión de Cotizaciones | Nacional Tapizados';
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
            box-shadow: 极速4px 12px rgba(0, 0, 0, 0.1);
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
            transition: all 极速.3s ease;
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

        .极速btn-info {
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 极速.1);
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

        /* Estilos para overlay y tarjeta flotante de opciones */
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
                grid-template-columns: 1极速fr;
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
                <h3>Total de Cotizaciones</h3>
                <p><?= $stats['total_cotizaciones'] ?></p>
            </div>
            <div class="summary-card">
                <h3>Aprobadas</h3>
                <p><?= $stats['aprobadas'] ?></p>
            </div>
            <div class="summary-card">
                <h3>Rechazadas</h3>
                <p><?= $stats['rechazadas'] ?></p>
            </div>
            <div class="summary-card">
                <h3>Completadas</h3>
                <p><?= $stats['completadas'] ?></p>
            </div>
            <div class="summary-card">
                <h3>Cotizaciones Hoy</h3>
                <p><?= $stats['cotizaciones_hoy'] ?></p>
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
                <button class="nav-link"极速 id="recent-tab" data-bs-toggle="tab" data-bs-target="#recent" type="button" role="tab">
                    <i class="fas fa-clock"></i> Recientes
                </button>
            </li>
            <?php if ($tieneColumnaActivo): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="delete-tab" data-bs-toggle="tab" data-bs-target="#delete" type="button" role="tab">
                    <i class="fas fa-trash-alt"></i> Papelera
                </button>
            </li>
            <?php endif; ?>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="clientTabsContent">
            <!-- Pestaña de búsqueda -->
            <div class="tab-pane fade show active" id="search" role="tabpanel">
                <div class="search-container">
                    <input type="text" class="search-input" id="searchInput" placeholder="Buscar cotización por cliente, placa o estado..." autocomplete="off">
                    <div class="search-loading" id="searchLoading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Buscando...
                    </div>
                    <div class="search-results" id="searchResults"></div>
                </div>

                <!-- Resultados iniciales (todas las cotizaciones activas) -->
                <div class="client-list" id="allClientsList">
                    <?php foreach ($todasCotizaciones as $cotizacion): ?>
                        <div class="client-item" data-client-id="<?= $cotizacion['id_cotizacion'] ?>" onclick="showOptionsCard(<?= $cotizacion['id_cotizacion'] ?>, '<?= htmlspecialchars($cotizacion['nombre_cliente']) ?>', '<?= htmlspecialchars($cotizacion['placa_vehiculo']) ?>', '<?= htmlspecialchars($cotizacion['estado_cotizacion']) ?>')">
                            <div>
                                <div class="client-name"><?= htmlspecialchars($cotizacion['nombre_cliente']) ?></div>
                                <div class="client-description">
                                    <span class="badge bg-<?= 
                                        $cotizacion['estado_cotizacion'] === 'Aprobado' ? 'success' : 
                                        ($cotizacion['estado_cotizacion'] === 'Rechazada' ? 'danger' : 
                                        ($cotizacion['estado_cotizacion'] === 'Completada' ? 'primary' : 'warning')) 
                                    ?>"><?= $cotizacion['estado_cotizacion'] ?></span>
                                    | <?= htmlspecialchars($cotizacion['marca_vehiculo']) ?> <?= htmlspecialchars($cotizacion['modelo_vehiculo']) ?> | <?= htmlspecialchars($cotizacion['placa_vehiculo']) ?>
                                </div>
                            </div>
                            <div class="client-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Pestaña de recientes -->
            <div class="tab-pane fade" id="recent" role="tabpanel">
                <div class="client-cards">
                    <?php foreach ($cotizacionesRecientes as $cotizacion): ?>
                        <div class="client-card" onclick="showOptionsCard(<?= $cotizacion['id_cotizacion'] ?>, '<?= htmlspecialchars($cotizacion['nombre_cliente']) ?>', '<?= htmlspecialchars($cotizacion['placa_vehiculo']) ?>', '<?= htmlspecialchars($cotizacion['estado_cotizacion']) ?>')">
                            <div class="client-card-header">
                                <h3 class="client-card-title"><?= htmlspecialchars($cotizacion['nombre_cliente']) ?></h3>
                                <span class="client-card-badge"><?= $cotizacion['estado_cotizacion'] ?></span>
                            </div>
                            <div class="client-card-body">
                                <div class="client-card-detail">
                                    <i class="fas fa-car"></i>
                                    <span><?= htmlspecialchars($cotizacion['marca_vehiculo']) ?> <?= htmlspecialchars($cotizacion['modelo_vehiculo']) ?></span>
                                </div>
                                <div class="client-card-detail">
                                    <i class="fas fa-tag"></i>
                                    <span><?= htmlspecialchars($cotizacion['placa_vehiculo']) ?></span>
                                </div>
                                <div class="client-card-detail">
                                    <i class="fas fa-user"></i>
                                    <span><?= htmlspecialchars($cotizacion['nombre_usuario']) ?></span>
                                </div>
                                <div class="client-card-detail">
                                    <i class="fas fa-calendar"></i>
                                    <span><?= date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])) ?></span>
                                </div>
                            </div>
                            <div class="edit-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Pestaña de papelera -->
            <?php if ($tieneColumnaActivo): ?>
            <div class="tab-pane fade" id="delete" role="tabpanel">
                <?php if (count($cotizacionesPapelera) > 0): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Vehículo</th>
                                    <th>Placa</th>
                                    <th>Estado</th>
                                    <th>Fecha Eliminación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cotizacionesPapelera as $cotizacion): ?>
                                    <tr class="deleted-item">
                                        <td data-label="Cliente"><?= htmlspecialchars($cotizacion['nombre_cliente']) ?></td>
                                        <td data-label="Vehículo"><?= htmlspecialchars($cotizacion['marca_vehiculo']) ?> <?= htmlspecialchars($cotizacion['modelo_vehiculo']) ?></td>
                                        <td data-label="Placa"><?= htmlspecialchars($cotizacion['placa_vehiculo']) ?></td>
                                        <td data-label="Estado">
                                            <?= $cotizacion['estado_cotizacion'] ?>
                                            <span class="deleted-badge">Eliminado</span>
                                        </td>
                                        <td data-label="Fecha Eliminación">
                                            <?= $tieneColumnaFechaEliminacion && $cotizacion['fecha_eliminacion'] ? 
                                                date('d/m/Y H:i', strtotime($cotizacion['fecha_eliminacion'])) : 'N/A' ?>
                                        </td>
                                        <td data-label="Acciones">
                                            <a href="index.php?restaurar=<?= $cotizacion['id_cotizacion'] ?>" class="btn btn-success btn-sm" title="Restaurar">
                                                <i class="fas fa-undo"></i>
                                            </a>
                                            <a href="index.php?eliminar_permanentemente=<?= $cotizacion['id_cotizacion'] ?>" class="btn btn-danger btn-sm" title="Eliminar permanentemente" onclick="return confirm('¿Estás seguro de eliminar permanentemente esta cotización? Esta acción no se puede deshacer.')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> No hay cotizaciones en la papelera.
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Overlay y tarjeta flotante de opciones -->
    <div class="overlay" id="overlay" onclick="hideOptionsCard()"></div>
    <div class="options-floating-card" id="optionsCard">
        <button class="option-close" onclick="hideOptionsCard()">
            <i class="fas fa-times"></i>
        </button>
        <div class="options-card-header">
            <h3 class="options-card-title" id="optionsCardTitle">Opciones</h3>
        </div>
        <div class="options-card-body">
            <a href="#" class="option-item" id="optionView">
                <i class="fas fa-eye"></i> Ver detalles
            </a>
            <a href="#" class="option-item" id="optionEdit">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="#" class="option-item" id="optionChangeStatus">
                <i class="fas fa-exchange-alt"></i> Cambiar estado
            </a>
            <a href="#" class="option-item text-danger" id="optionDelete">
                <i class="fas fa-trash"></i> Eliminar
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables globales
        let currentClientId = null;
        let currentClientName = null;
        let currentClientPlate = null;
        let currentClientStatus = null;

        // Mostrar tarjeta de opciones
        function showOptionsCard(id, name, plate, status) {
            currentClientId = id;
            currentClientName = name;
            currentClientPlate = plate;
            currentClientStatus = status;
            
            // Actualizar título de la tarjeta
            document.getElementById('optionsCardTitle').textContent = name;
            
            // Actualizar enlaces
            document.getElementById('optionView').href = `ver.php?id=${id}`;
            document.getElementById('optionEdit').href = `editar.php?id=${id}`;
            document.getElementById('optionChangeStatus').href = `cambiar_estado.php?id=${id}`;
            document.getElementById('optionDelete').href = `index.php?eliminar=${id}`;
            
            // Mostrar overlay y tarjeta
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('optionsCard').style.display = 'block';
            
            // Prevenir que el clic en la tarjeta cierre el overlay
            event.stopPropagation();
        }

        // Ocultar tarjeta de opciones
        function hideOptionsCard() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('optionsCard').style.display = 'none';
        }

        // Búsqueda en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            const allClientsList = document.getElementById('allClientsList');
            const searchLoading = document.getElementById('searchLoading');

            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                if (query.length < 2) {
                    searchResults.style.display = 'none';
                    allClientsList.style.display = 'block';
                    return;
                }

                searchLoading.style.display = 'block';
                allClientsList.style.display = 'none';
                
                fetch(`index.php?ajax=1&q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchLoading.style.display = 'none';
                        
                        if (data.length === 0) {
                            searchResults.innerHTML = '<div class="search-result-item">No se encontraron resultados</div>';
                            searchResults.style.display = 'block';
                            return;
                        }
                        
                        let html = '';
                        data.forEach(item => {
                            html += `
                                <div class="search-result-item" onclick="showOptionsCard(${item.id_cotizacion}, '${item.nombre_cliente.replace(/'/g, "\\'")}', '${item.placa_vehiculo}', '${item.estado_cotizacion}')">
                                    <div>
                                        <div class="search-client-name">${item.nombre_cliente}</div>
                                        <div class="search-client-info">${item.placa_vehiculo} | ${item.estado_cotizacion}</div>
                                    </div>
                                    <div class="search-client-date">${new Date(item.fecha_cotizacion).toLocaleDateString()}</div>
                                </div>
                            `;
                        });
                        
                        searchResults.innerHTML = html;
                        searchResults.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        searchLoading.style.display = 'none';
                    });
            });

            // Ocultar resultados al hacer clic fuera
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.style.display = 'none';
                    if (searchInput.value.trim().length < 2) {
                        allClientsList.style.display = 'block';
                    }
                }
            });
        });

        // Prevenir que el clic en la tarjeta cierre el overlay
        document.getElementById('optionsCard').addEventListener('click', function(event) {
            event.stopPropagation();
        });
    </script>
</body>

</html>