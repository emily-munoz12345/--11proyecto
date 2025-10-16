<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

// Verificar permisos (Admin y Vendedor)
if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Inicializar variables de sesión para mensajes
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
    $_SESSION['tipo_mensaje'] = '';
}

// Obtener ID del usuario actual
$usuario_id = getUserId();

// Procesar parámetros de búsqueda y filtros
$filtro_busqueda = $_GET['busqueda'] ?? '';
$filtro_estado = $_GET['estado'] ?? '';
$filtro_fecha = $_GET['fecha'] ?? '';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'marcar_leido':
                if (isset($_POST['id_mensaje'])) {
                    $id_mensaje = intval($_POST['id_mensaje']);
                    $stmt = $conex->prepare("UPDATE mensajes_contacto SET leido = 1 WHERE id_mensaje = ?");
                    $stmt->execute([$id_mensaje]);
                    
                    // Registrar en el log de auditoría
                    $stmt_log = $conex->prepare("CALL SetUsuarioActual(?)");
                    $stmt_log->execute([$usuario_id]);
                    
                    $stmt_audit = $conex->prepare("
                        INSERT INTO registro_eliminaciones (tabla, id_registro, eliminado_por, accion, datos) 
                        VALUES ('mensajes_contacto', ?, ?, 'MODIFICACION', ?)
                    ");
                    $stmt_audit->execute([
                        $id_mensaje, 
                        $usuario_id, 
                        "Mensaje marcado como leído por " . getUserName()
                    ]);
                    
                    $_SESSION['mensaje'] = "Mensaje marcado como leído";
                    $_SESSION['tipo_mensaje'] = 'success';
                    
                    header('Location: buzon.php?' . http_build_query($_GET));
                    exit;
                }
                break;
                
            case 'marcar_no_leido':
                if (isset($_POST['id_mensaje'])) {
                    $id_mensaje = intval($_POST['id_mensaje']);
                    $stmt = $conex->prepare("UPDATE mensajes_contacto SET leido = 0 WHERE id_mensaje = ?");
                    $stmt->execute([$id_mensaje]);
                    
                    $_SESSION['mensaje'] = "Mensaje marcado como no leído";
                    $_SESSION['tipo_mensaje'] = 'success';
                    
                    header('Location: buzon.php?' . http_build_query($_GET));
                    exit;
                }
                break;
                
            case 'eliminar_mensaje':
                if (isset($_POST['id_mensaje'])) {
                    $id_mensaje = intval($_POST['id_mensaje']);
                    
                    // Establecer usuario actual para el trigger
                    $stmt_user = $conex->prepare("CALL SetUsuarioActual(?)");
                    $stmt_user->execute([$usuario_id]);
                    
                    // Marcar como inactivo (eliminación lógica)
                    $stmt = $conex->prepare("UPDATE mensajes_contacto SET activo = 0 WHERE id_mensaje = ?");
                    $stmt->execute([$id_mensaje]);
                    
                    $_SESSION['mensaje'] = "Mensaje movido a la papelera";
                    $_SESSION['tipo_mensaje'] = 'success';
                    
                    header('Location: buzon.php?' . http_build_query($_GET));
                    exit;
                }
                break;
                
            case 'eliminar_multiples':
                if (isset($_POST['seleccionados']) && !empty($_POST['seleccionados'])) {
                    $seleccionados = $_POST['seleccionados'];
                    $successCount = 0;
                    
                    // Establecer usuario actual para el trigger
                    $stmt_user = $conex->prepare("CALL SetUsuarioActual(?)");
                    $stmt_user->execute([$usuario_id]);
                    
                    foreach ($seleccionados as $id_mensaje) {
                        $id_mensaje = intval($id_mensaje);
                        $stmt = $conex->prepare("UPDATE mensajes_contacto SET activo = 0 WHERE id_mensaje = ?");
                        if ($stmt->execute([$id_mensaje])) {
                            $successCount++;
                        }
                    }
                    
                    $_SESSION['mensaje'] = "$successCount mensaje(s) movido(s) a la papelera";
                    $_SESSION['tipo_mensaje'] = 'success';
                    
                    header('Location: buzon.php?' . http_build_query($_GET));
                    exit;
                }
                break;
                
            case 'marcar_leidos_multiples':
                if (isset($_POST['seleccionados']) && !empty($_POST['seleccionados'])) {
                    $seleccionados = $_POST['seleccionados'];
                    $successCount = 0;
                    
                    foreach ($seleccionados as $id_mensaje) {
                        $id_mensaje = intval($id_mensaje);
                        $stmt = $conex->prepare("UPDATE mensajes_contacto SET leido = 1 WHERE id_mensaje = ?");
                        if ($stmt->execute([$id_mensaje])) {
                            $successCount++;
                        }
                    }
                    
                    $_SESSION['mensaje'] = "$successCount mensaje(s) marcado(s) como leídos";
                    $_SESSION['tipo_mensaje'] = 'success';
                    
                    header('Location: buzon.php?' . http_build_query($_GET));
                    exit;
                }
                break;
                
            case 'marcar_no_leidos_multiples':
                if (isset($_POST['seleccionados']) && !empty($_POST['seleccionados'])) {
                    $seleccionados = $_POST['seleccionados'];
                    $successCount = 0;
                    
                    foreach ($seleccionados as $id_mensaje) {
                        $id_mensaje = intval($id_mensaje);
                        $stmt = $conex->prepare("UPDATE mensajes_contacto SET leido = 0 WHERE id_mensaje = ?");
                        if ($stmt->execute([$id_mensaje])) {
                            $successCount++;
                        }
                    }
                    
                    $_SESSION['mensaje'] = "$successCount mensaje(s) marcado(s) como no leídos";
                    $_SESSION['tipo_mensaje'] = 'success';
                    
                    header('Location: buzon.php?' . http_build_query($_GET));
                    exit;
                }
                break;
        }
    }
}

// Obtener estadísticas de mensajes
$statsQuery = "
    SELECT 
        COUNT(*) as total_mensajes,
        SUM(CASE WHEN leido = 0 THEN 1 ELSE 0 END) as no_leidos,
        SUM(CASE WHEN leido = 1 AND DATE(fecha_envio) = CURDATE() THEN 1 ELSE 0 END) as leidos_hoy,
        MAX(fecha_envio) as ultimo_mensaje
    FROM mensajes_contacto 
    WHERE activo = 1
";

$stats = $conex->query($statsQuery)->fetch(PDO::FETCH_ASSOC);

// Construir consulta base para mensajes
$query = "SELECT m.*, 
                 COALESCE(u.nombre_completo, 'Sistema') as visto_por,
                 u.id_usuario as id_usuario_vio
          FROM mensajes_contacto m 
          LEFT JOIN registro_eliminaciones re ON m.id_mensaje = re.id_registro 
            AND re.tabla = 'mensajes_contacto' 
            AND re.accion = 'MODIFICACION'
            AND re.datos LIKE '%marcado como leído%'
          LEFT JOIN usuarios u ON re.eliminado_por = u.id_usuario
          WHERE m.activo = 1";

$params = [];

// Aplicar filtros
if (!empty($filtro_busqueda)) {
    $query .= " AND (m.nombre_completo LIKE ? OR m.correo_electronico LIKE ? OR m.asunto LIKE ? OR m.mensaje LIKE ?)";
    $search_term = "%$filtro_busqueda%";
    $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
}

if (!empty($filtro_estado)) {
    if ($filtro_estado === 'leidos') {
        $query .= " AND m.leido = 1";
    } elseif ($filtro_estado === 'no_leidos') {
        $query .= " AND m.leido = 0";
    }
}

if (!empty($filtro_fecha)) {
    switch ($filtro_fecha) {
        case 'hoy':
            $query .= " AND DATE(m.fecha_envio) = CURDATE()";
            break;
        case 'semana':
            $query .= " AND m.fecha_envio >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'mes':
            $query .= " AND m.fecha_envio >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
    }
}

$query .= " ORDER BY m.leido ASC, m.fecha_envio DESC";

// Ejecutar consulta
$mensajes = [];
try {
    $stmt = $conex->prepare($query);
    $stmt->execute($params);
    $mensajes = $stmt->fetchAll();
} catch (Exception $e) {
    $_SESSION['mensaje'] = "Error al cargar los mensajes: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buzón de Mensajes - Sistema de Tapicería</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.9);
            --primary-hover: rgba(140, 74, 63, 1);
            --secondary-color: rgba(108, 117, 125, 0.9);
            --text-color: #ffffff;
            --text-dark: #2c3e50;
            --text-muted: rgba(255, 255, 255, 0.8);
            --bg-transparent: rgba(0, 0, 0, 0.7);
            --bg-transparent-light: rgba(0, 0, 0, 0.5);
            --bg-input: rgba(0, 0, 0, 0.6);
            --border-color: rgba(255, 255, 255, 0.3);
            --success-color: rgba(25, 135, 84, 0.9);
            --danger-color: rgba(220, 53, 69, 0.9);
            --warning-color: rgba(255, 193, 7, 0.9);
            --info-color: rgba(13, 202, 240, 0.9);
            --card-bg: rgba(255, 255, 255, 0.95);
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
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
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
            font-size: 2.2rem;
            font-weight: 700;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
            color: var(--text-color);
        }

        .page-title i {
            margin-right: 12px;
            color: var(--primary-color);
        }

        /* Estadísticas */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.2rem;
            margin-bottom: 2.5rem;
        }

        .summary-card {
            background: linear-gradient(135deg, var(--bg-transparent-light), rgba(140, 74, 63, 0.3));
            border-radius: 12px;
            padding: 1.8rem 1.5rem;
            text-align: center;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            backdrop-filter: blur(10px);
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        .summary-card h3 {
            margin-top: 0;
            font-size: 0.95rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 0.8rem;
        }

        .summary-card p {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0;
            color: var(--text-color);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Estilos para mensajes */
        .mensaje-item {
            background: linear-gradient(135deg, var(--bg-transparent-light), rgba(0, 0, 0, 0.4));
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.2rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            cursor: pointer;
            backdrop-filter: blur(8px);
        }

        .mensaje-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
            border-color: var(--primary-color);
        }

        .mensaje-no-leido {
            border-left: 6px solid var(--info-color);
            background: linear-gradient(135deg, rgba(13, 202, 240, 0.15), var(--bg-transparent-light));
        }

        .mensaje-leido {
            border-left: 6px solid var(--success-color);
        }

        .badge-nuevo {
            background: linear-gradient(135deg, var(--info-color), #0dcaf0);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .mensaje-preview {
            color: var(--text-color);
            font-size: 0.95rem;
            line-height: 1.5;
            margin-top: 0.5rem;
        }

        .mensaje-header {
            display: flex;
            justify-content: between;
            align-items: flex-start;
            margin-bottom: 0.8rem;
        }

        .mensaje-info {
            flex-grow: 1;
        }

        .mensaje-nombre {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--text-color);
            margin-bottom: 0.3rem;
        }

        .mensaje-meta {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        /* Checkbox para selección múltiple */
        .select-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            margin-right: 1rem;
        }

        .bulk-actions {
            background: linear-gradient(135deg, var(--warning-color), #ffc107);
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: none;
            align-items: center;
            gap: 1rem;
            color: var(--text-dark);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .bulk-actions.show {
            display: flex;
        }

        .bulk-buttons {
            display: flex;
            gap: 0.5rem;
            margin-left: auto;
        }

        /* Estilos para botones */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            gap: 0.5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .btn i {
            font-size: 0.9rem;
        }

        .btn-sm {
            padding: 0.4rem 0.7rem;
            font-size: 0.85rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-hover), #8c4a3f);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(140, 74, 63, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary-color), #6c757d);
            color: white;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #dc3545);
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #dc3545, #c82333);
            transform: translateY(-2px);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #198754);
            color: white;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #198754, #157347);
            transform: translateY(-2px);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color), #ffc107);
            color: var(--text-dark);
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            transform: translateY(-2px);
        }

        .btn-info {
            background: linear-gradient(135deg, var(--info-color), #0dcaf0);
            color: white;
        }

        .btn-info:hover {
            background: linear-gradient(135deg, #0dcaf0, #0bb5d4);
            transform: translateY(-2px);
        }

        /* Estilos para alertas */
        .alert {
            padding: 1.2rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(8px);
            border-left: 5px solid var(--info-color);
            background: linear-gradient(135deg, rgba(13, 202, 240, 0.25), rgba(13, 202, 240, 0.1));
            color: var(--text-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(25, 135, 84, 0.25), rgba(25, 135, 84, 0.1));
            border-left: 5px solid var(--success-color);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.25), rgba(220, 53, 69, 0.1));
            border-left: 5px solid var(--danger-color);
        }

        .alert-warning {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.25), rgba(255, 193, 7, 0.1));
            border-left: 5px solid var(--warning-color);
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(13, 202, 240, 0.25), rgba(13, 202, 240, 0.1));
            border-left: 5px solid var(--info-color);
        }

        /* Formularios */
        .form-control, .form-select {
            background-color: var(--bg-input);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            backdrop-filter: blur(5px);
            border-radius: 8px;
            padding: 0.75rem;
        }

        .form-control:focus, .form-select:focus {
            background-color: rgba(0, 0, 0, 0.7);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.3rem rgba(140, 74, 63, 0.25);
            color: var(--text-color);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        /* Modal de mensaje rápido */
        .modal-mensaje-rapido {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1050;
            backdrop-filter: blur(5px);
        }

        .modal-mensaje-rapido.show {
            display: flex;
        }

        .modal-mensaje-content {
            background: linear-gradient(135deg, var(--bg-transparent), var(--bg-transparent-light));
            border-radius: 15px;
            padding: 2rem;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(15px);
        }

        .modal-mensaje-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-mensaje-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-color);
            margin: 0;
        }

        .modal-mensaje-body {
            color: var(--text-color);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .modal-mensaje-footer {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
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
                font-size: 1.8rem;
            }
            
            .btn-sm {
                padding: 0.35rem 0.6rem;
                font-size: 0.8rem;
            }

            .summary-cards {
                grid-template-columns: 1fr;
            }

            .bulk-actions {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.8rem;
            }
            
            .bulk-buttons {
                margin-left: 0;
                width: 100%;
                justify-content: space-between;
            }
            
            .mensaje-item {
                padding: 1.2rem;
            }
            
            .modal-mensaje-content {
                padding: 1.5rem;
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-inbox"></i> Buzón de Mensajes
                <?php if ($stats['no_leidos'] > 0): ?>
                    <span class="badge-nuevo ms-2"><?php echo $stats['no_leidos']; ?> nuevos</span>
                <?php endif; ?>
            </h1>
            <div class="d-flex gap-2 align-items-center">
                <a href="papelera.php" class="btn btn-warning">
                    <i class="fas fa-trash"></i> Papelera
                </a>
                <a href="../dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if (!empty($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
                <div>
                    <i class="fas fa-<?=
                                        $_SESSION['tipo_mensaje'] === 'success' ? 'check-circle' : 
                                        ($_SESSION['tipo_mensaje'] === 'danger' ? 'times-circle' : 
                                        ($_SESSION['tipo_mensaje'] === 'warning' ? 'exclamation-triangle' : 'info-circle'))
                                        ?> me-2"></i>
                    <?= $_SESSION['mensaje'] ?>
                </div>
                <button type="button" class="btn-close btn-close-white" onclick="this.parentElement.style.display='none'"></button>
            </div>
            <?php
            $_SESSION['mensaje'] = '';
            $_SESSION['tipo_mensaje'] = '';
            ?>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total Mensajes</h3>
                <p><?= $stats['total_mensajes'] ?? 0 ?></p>
            </div>
            <div class="summary-card">
                <h3>Por Leer</h3>
                <p><?= $stats['no_leidos'] ?? 0 ?></p>
            </div>
            <div class="summary-card">
                <h3>Leídos Hoy</h3>
                <p><?= $stats['leidos_hoy'] ?? 0 ?></p>
            </div>
            <div class="summary-card">
                <h3>Último Mensaje</h3>
                <p><?= $stats['ultimo_mensaje'] ? date('d/m/Y', strtotime($stats['ultimo_mensaje'])) : 'N/A' ?></p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4" style="background: linear-gradient(135deg, var(--bg-transparent-light), rgba(140, 74, 63, 0.2)); border: 1px solid var(--border-color);">
            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-hover)); border-bottom: 1px solid var(--border-color);">
                <h5 class="m-0"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</h5>
            </div>
            <div class="card-body">
                <form method="get" id="filterForm" class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Búsqueda Rápida</label>
                        <input type="text" class="form-control" name="busqueda" id="busquedaFilter" 
                               placeholder="Buscar en nombre, email, asunto..." 
                               value="<?= htmlspecialchars($filtro_busqueda) ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado" id="estadoFilter">
                            <option value="">Todos los mensajes</option>
                            <option value="no_leidos" <?= $filtro_estado === 'no_leidos' ? 'selected' : '' ?>>No leídos</option>
                            <option value="leidos" <?= $filtro_estado === 'leidos' ? 'selected' : '' ?>>Leídos</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Fecha</label>
                        <select class="form-select" name="fecha" id="fechaFilter">
                            <option value="">Todas las fechas</option>
                            <option value="hoy" <?= $filtro_fecha === 'hoy' ? 'selected' : '' ?>>Hoy</option>
                            <option value="semana" <?= $filtro_fecha === 'semana' ? 'selected' : '' ?>>Esta semana</option>
                            <option value="mes" <?= $filtro_fecha === 'mes' ? 'selected' : '' ?>>Este mes</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <div class="w-100">
                            <?php if ($filtro_busqueda !== '' || $filtro_estado !== '' || $filtro_fecha !== ''): ?>
                                <a href="buzon.php" class="btn btn-secondary w-100">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Mensajes -->
        <div class="card" style="background: linear-gradient(135deg, var(--bg-transparent-light), rgba(0, 0, 0, 0.4)); border: 1px solid var(--border-color);">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-hover)); border-bottom: 1px solid var(--border-color);">
                <h5 class="m-0"><i class="fas fa-envelope me-2"></i>Mensajes de Contacto</h5>
                <div>
                    <?php if (!empty($mensajes)): ?>
                        <span class="text-light me-3">
                            Mostrando <?= count($mensajes) ?> mensaje(s)
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($mensajes)): ?>
                    <!-- Acciones en lote -->
                    <div class="bulk-actions" id="bulkActions">
                        <span id="selectedCount">0 mensajes seleccionados</span>
                        <div class="bulk-buttons">
                            <button type="button" class="btn btn-success btn-sm" onclick="marcarLeidosSeleccionados()">
                                <i class="fas fa-envelope-open"></i> Marcar Leídos
                            </button>
                            <button type="button" class="btn btn-info btn-sm" onclick="marcarNoLeidosSeleccionados()">
                                <i class="fas fa-envelope"></i> Marcar No Leídos
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarSeleccionados()">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="deseleccionarTodos()">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                    </div>

                    <div id="listaMensajes">
                        <?php foreach ($mensajes as $mensaje): ?>
                            <div class="mensaje-item <?= $mensaje['leido'] ? 'mensaje-leido' : 'mensaje-no-leido' ?>" 
                                 data-id="<?= $mensaje['id_mensaje'] ?>"
                                 onclick="abrirMensajeRapido(<?= $mensaje['id_mensaje'] ?>, '<?= htmlspecialchars(addslashes($mensaje['nombre_completo'])) ?>', '<?= htmlspecialchars(addslashes($mensaje['mensaje'])) ?>', <?= $mensaje['leido'] ?>)">
                                <div class="row align-items-center">
                                    <div class="col-md-1">
                                        <input type="checkbox" class="select-checkbox item-checkbox" 
                                               value="<?= $mensaje['id_mensaje'] ?>" onclick="event.stopPropagation(); updateSelection()">
                                    </div>
                                    <div class="col-md-9">
                                        <div class="mensaje-header">
                                            <div class="mensaje-info">
                                                <div class="mensaje-nombre">
                                                    <?= htmlspecialchars($mensaje['nombre_completo']) ?>
                                                    <?php if (!$mensaje['leido']): ?>
                                                        <span class="badge-nuevo ms-2">NUEVO</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="mensaje-meta">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    <?= htmlspecialchars($mensaje['correo_electronico']) ?>
                                                    <?php if ($mensaje['telefono']): ?>
                                                        <span class="ms-2">
                                                            <i class="fas fa-phone me-1"></i><?= htmlspecialchars($mensaje['telefono']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <span class="ms-2">
                                                        <i class="fas fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($mensaje['fecha_envio'])) ?>
                                                    </span>
                                                    <?php if ($mensaje['leido'] && $mensaje['visto_por']): ?>
                                                        <span class="ms-2">
                                                            <i class="fas fa-eye me-1"></i>Visto por: <?= htmlspecialchars($mensaje['visto_por']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mensaje-preview">
                                            <strong>Asunto:</strong> <?= htmlspecialchars($mensaje['asunto']) ?>
                                            <br>
                                            <?= nl2br(htmlspecialchars(substr($mensaje['mensaje'], 0, 150))) ?><?= strlen($mensaje['mensaje']) > 150 ? '...' : '' ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <div class="btn-group" role="group" onclick="event.stopPropagation();">
                                            <?php if ($mensaje['leido']): ?>
                                                <button class="btn btn-info btn-sm" onclick="event.stopPropagation(); marcarNoLeido(<?= $mensaje['id_mensaje'] ?>)">
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-success btn-sm" onclick="event.stopPropagation(); marcarLeido(<?= $mensaje['id_mensaje'] ?>)">
                                                    <i class="fas fa-envelope-open"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-danger btn-sm" onclick="event.stopPropagation(); eliminarMensaje(<?= $mensaje['id_mensaje'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x mb-3" style="color: var(--text-muted);"></i>
                        <h4 style="color: var(--text-color);">No hay mensajes</h4>
                        <p style="color: var(--text-muted);">No se encontraron mensajes con los filtros aplicados.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal para vista rápida de mensaje -->
    <div class="modal-mensaje-rapido" id="modalMensaje">
        <div class="modal-mensaje-content">
            <div class="modal-mensaje-header">
                <h3 class="modal-mensaje-title" id="modalMensajeTitulo">Mensaje</h3>
                <button type="button" class="btn-close btn-close-white" onclick="cerrarModalMensaje()"></button>
            </div>
            <div class="modal-mensaje-body">
                <div class="mb-4">
                    <h5 id="modalMensajeNombre" style="color: var(--text-color);"></h5>
                    <p id="modalMensajeContenido" style="color: var(--text-color);"></p>
                </div>
            </div>
            <div class="modal-mensaje-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarModalMensaje()">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <a href="#" class="btn btn-primary" id="btnVerDetalles">
                    <i class="fas fa-eye"></i> Ver Detalles
                </a>
            </div>
        </div>
    </div>

    <!-- Formulario para acciones -->
    <form id="actionForm" method="post" style="display: none;">
        <input type="hidden" name="accion" id="accion">
        <input type="hidden" name="id_mensaje" id="id_mensaje">
        <input type="hidden" name="seleccionados" id="seleccionados">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para abrir mensaje rápido
        function abrirMensajeRapido(id, nombre, mensaje, leido) {
            document.getElementById('modalMensajeNombre').textContent = 'De: ' + nombre;
            document.getElementById('modalMensajeContenido').textContent = mensaje;
            document.getElementById('btnVerDetalles').href = 'ver.php?id=' + id;
            
            // Mostrar modal
            document.getElementById('modalMensaje').classList.add('show');
            
            // Si no estaba leído, marcarlo como leído
            if (!leido) {
                marcarLeido(id);
            }
        }

        // Función para cerrar modal
        function cerrarModalMensaje() {
            document.getElementById('modalMensaje').classList.remove('show');
        }

        // Función para marcar mensaje como leído
        function marcarLeido(id) {
            document.getElementById('accion').value = 'marcar_leido';
            document.getElementById('id_mensaje').value = id;
            document.getElementById('actionForm').submit();
        }

        // Función para marcar mensaje como no leído
        function marcarNoLeido(id) {
            document.getElementById('accion').value = 'marcar_no_leido';
            document.getElementById('id_mensaje').value = id;
            document.getElementById('actionForm').submit();
        }

        // Función para eliminar mensaje
        function eliminarMensaje(id) {
            if (confirm('¿Está seguro de que desea mover este mensaje a la papelera?')) {
                document.getElementById('accion').value = 'eliminar_mensaje';
                document.getElementById('id_mensaje').value = id;
                document.getElementById('actionForm').submit();
            }
        }

        // Funciones para selección múltiple
        function updateSelection() {
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');
            
            selectedCount.textContent = checkboxes.length + ' mensaje(s) seleccionado(s)';
            
            if (checkboxes.length > 0) {
                bulkActions.classList.add('show');
            } else {
                bulkActions.classList.remove('show');
            }
        }

        function deseleccionarTodos() {
            document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelection();
        }

        function marcarLeidosSeleccionados() {
            const seleccionados = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
            if (seleccionados.length > 0) {
                document.getElementById('accion').value = 'marcar_leidos_multiples';
                document.getElementById('seleccionados').value = JSON.stringify(seleccionados);
                document.getElementById('actionForm').submit();
            }
        }

        function marcarNoLeidosSeleccionados() {
            const seleccionados = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
            if (seleccionados.length > 0) {
                document.getElementById('accion').value = 'marcar_no_leidos_multiples';
                document.getElementById('seleccionados').value = JSON.stringify(seleccionados);
                document.getElementById('actionForm').submit();
            }
        }

        function eliminarSeleccionados() {
            const seleccionados = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
            if (seleccionados.length > 0) {
                if (confirm('¿Está seguro de que desea mover ' + seleccionados.length + ' mensaje(s) a la papelera?')) {
                    document.getElementById('accion').value = 'eliminar_multiples';
                    document.getElementById('seleccionados').value = JSON.stringify(seleccionados);
                    document.getElementById('actionForm').submit();
                }
            }
        }

        // Cerrar modal con ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                cerrarModalMensaje();
            }
        });

        // Cerrar modal al hacer clic fuera
        document.getElementById('modalMensaje').addEventListener('click', function(event) {
            if (event.target === this) {
                cerrarModalMensaje();
            }
        });

        // Aplicar filtros automáticamente
        document.getElementById('busquedaFilter').addEventListener('input', function() {
            document.getElementById('filterForm').submit();
        });

        document.getElementById('estadoFilter').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        document.getElementById('fechaFilter').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    </script>
</body>
</html>