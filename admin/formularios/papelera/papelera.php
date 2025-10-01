<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos (solo Admin)
if (!isAdmin()) {
    header('Location: ../dashboard.php');
    exit;
}

// Inicializar variables de sesión para mensajes
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
    $_SESSION['tipo_mensaje'] = '';
}

// Procesar parámetros de búsqueda
$tipoFiltro = $_GET['tipo'] ?? 'all';
$fechaFiltro = $_GET['fecha'] ?? 'all';
$buscarTexto = $_GET['buscar'] ?? '';

// Construir consulta base con filtros
$query = "
    SELECT 
        re.*,
        u.nombre_completo as usuario_responsable,
        CASE 
            WHEN re.tabla = 'clientes' THEN (SELECT nombre_cliente FROM clientes WHERE id_cliente = re.id_registro)
            WHEN re.tabla = 'vehiculos' THEN (SELECT CONCAT(marca_vehiculo, ' ', modelo_vehiculo) FROM vehiculos WHERE id_vehiculo = re.id_registro)
            WHEN re.tabla = 'materiales' THEN (SELECT nombre_material FROM materiales WHERE id_material = re.id_registro)
            WHEN re.tabla = 'servicios' THEN (SELECT nombre_servicio FROM servicios WHERE id_servicio = re.id_registro)
            WHEN re.tabla = 'usuarios' THEN (SELECT nombre_completo FROM usuarios WHERE id_usuario = re.id_registro)
            WHEN re.tabla = 'cotizaciones' THEN (SELECT CONCAT('Cotización #', id_cotizacion) FROM cotizaciones WHERE id_cotizacion = re.id_registro)
            WHEN re.tabla = 'trabajos' THEN (SELECT CONCAT('Trabajo #', id_trabajos) FROM trabajos WHERE id_trabajos = re.id_registro)
            WHEN re.tabla = 'mensajes_contacto' THEN (SELECT CONCAT('Mensaje: ', asunto) FROM mensajes_contacto WHERE id_mensaje = re.id_registro)
            ELSE 'Elemento'
        END as nombre_elemento
    FROM registro_eliminaciones re
    LEFT JOIN usuarios u ON re.eliminado_por = u.id_usuario
    WHERE re.accion = 'ELIMINACION'
";

// Aplicar filtros
$params = [];

// Filtro por tipo
if ($tipoFiltro !== 'all') {
    $query .= " AND re.tabla = ?";
    $params[] = $tipoFiltro;
}

// Filtro por fecha
if ($fechaFiltro !== 'all') {
    switch ($fechaFiltro) {
        case 'today':
            $query .= " AND DATE(re.fecha_eliminacion) = CURDATE()";
            break;
        case 'week':
            $query .= " AND re.fecha_eliminacion >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $query .= " AND re.fecha_eliminacion >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
    }
}

// Filtro por texto de búsqueda
if (!empty($buscarTexto)) {
    $query .= " AND (
        re.datos LIKE ? OR 
        u.nombre_completo LIKE ? OR
        re.id_registro LIKE ?
    )";
    $searchTerm = "%$buscarTexto%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// Ordenar resultados
$query .= " ORDER BY re.fecha_eliminacion DESC";

// Obtener registros filtrados
$registrosEliminaciones = [];
try {
    $stmt = $conex->prepare($query);
    $stmt->execute($params);
    $registrosEliminaciones = $stmt->fetchAll();
} catch (Exception $e) {
    $_SESSION['mensaje'] = "Error al cargar la papelera: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

// Procesar restauración de elementos
if (isset($_POST['restaurar']) && is_numeric($_POST['id']) && !empty($_POST['tabla'])) {
    $id = $_POST['id'];
    $tabla = $_POST['tabla'];
    
    try {
        // Verificar que el elemento existe y está eliminado
        $checkStmt = $conex->prepare("SELECT activo FROM $tabla WHERE id_" . substr($tabla, 0, -1) . " = ?");
        $checkStmt->execute([$id]);
        $elemento = $checkStmt->fetch();
        
        if ($elemento && $elemento['activo'] == 0) {
            // Establecer usuario actual para el trigger
            $conex->query("CALL SetUsuarioActual(" . $_SESSION['user_id'] . ")");
            
            // Restaurar elemento
            $id_column = 'id_' . substr($tabla, 0, -1);
            $sql = "UPDATE $tabla SET activo = 1 WHERE $id_column = ?";
            $stmt = $conex->prepare($sql);
            $stmt->execute([$id]);
            
            // Limpiar usuario actual
            $conex->query("CALL LimpiarUsuarioActual()");
            
            $_SESSION['mensaje'] = "Elemento restaurado correctamente";
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = "El elemento no existe o ya fue restaurado";
            $_SESSION['tipo_mensaje'] = 'warning';
        }
        
        header('Location: papelera.php?' . http_build_query($_GET));
        exit;
    } catch (Exception $e) {
        $_SESSION['mensaje'] = "Error al restaurar elemento: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
}

// Procesar eliminación permanente
if (isset($_POST['eliminar_permanentemente']) && is_numeric($_POST['id']) && !empty($_POST['tabla'])) {
    $id = $_POST['id'];
    $tabla = $_POST['tabla'];
    $id_column = 'id_' . substr($tabla, 0, -1);
    
    try {
        // Eliminar permanentemente según la tabla
        switch($tabla) {
            case 'clientes':
                // Verificar si hay relaciones antes de eliminar
                $checkRelations = $conex->prepare("
                    SELECT COUNT(*) as count FROM cotizaciones WHERE id_cliente = ?
                    UNION ALL
                    SELECT COUNT(*) FROM cliente_vehiculo WHERE id_cliente = ?
                ");
                $checkRelations->execute([$id, $id]);
                $relations = $checkRelations->fetchAll();
                
                if ($relations[0]['count'] > 0 || $relations[1]['count'] > 0) {
                    $_SESSION['mensaje'] = "No se puede eliminar el cliente porque tiene cotizaciones o vehículos asociados";
                    $_SESSION['tipo_mensaje'] = 'warning';
                } else {
                    $sql = "DELETE FROM $tabla WHERE $id_column = ?";
                    $stmt = $conex->prepare($sql);
                    $stmt->execute([$id]);
                    $_SESSION['mensaje'] = "Cliente eliminado permanentemente";
                    $_SESSION['tipo_mensaje'] = 'success';
                }
                break;
                
            case 'vehiculos':
                $checkRelations = $conex->prepare("
                    SELECT COUNT(*) as count FROM cotizaciones WHERE id_vehiculo = ?
                    UNION ALL
                    SELECT COUNT(*) FROM cliente_vehiculo WHERE id_vehiculo = ?
                ");
                $checkRelations->execute([$id, $id]);
                $relations = $checkRelations->fetchAll();
                
                if ($relations[0]['count'] > 0 || $relations[1]['count'] > 0) {
                    $_SESSION['mensaje'] = "No se puede eliminar el vehículo porque tiene cotizaciones o clientes asociados";
                    $_SESSION['tipo_mensaje'] = 'warning';
                } else {
                    $sql = "DELETE FROM $tabla WHERE $id_column = ?";
                    $stmt = $conex->prepare($sql);
                    $stmt->execute([$id]);
                    $_SESSION['mensaje'] = "Vehículo eliminado permanentemente";
                    $_SESSION['tipo_mensaje'] = 'success';
                }
                break;
                
            default:
                $sql = "DELETE FROM $tabla WHERE $id_column = ?";
                $stmt = $conex->prepare($sql);
                $stmt->execute([$id]);
                $_SESSION['mensaje'] = "Elemento eliminado permanentemente";
                $_SESSION['tipo_mensaje'] = 'success';
        }
        
        // Eliminar el registro de eliminaciones si se eliminó correctamente
        if ($_SESSION['tipo_mensaje'] === 'success') {
            $deleteRegistro = "DELETE FROM registro_eliminaciones WHERE tabla = ? AND id_registro = ? AND accion = 'ELIMINACION'";
            $stmt2 = $conex->prepare($deleteRegistro);
            $stmt2->execute([$tabla, $id]);
        }
        
        header('Location: papelera.php?' . http_build_query($_GET));
        exit;
    } catch (Exception $e) {
        $_SESSION['mensaje'] = "Error al eliminar elemento: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
}

// Procesar vaciar papelera
if (isset($_POST['vaciar_papelera'])) {
    try {
        $successCount = 0;
        $errorCount = 0;
        
        // Eliminar permanentemente todos los elementos en papelera
        $tablas = ['clientes', 'vehiculos', 'materiales', 'servicios', 'usuarios', 'cotizaciones', 'trabajos', 'mensajes_contacto'];
        
        foreach ($tablas as $tabla) {
            try {
                $id_column = 'id_' . substr($tabla, 0, -1);
                
                // Para clientes y vehículos, verificar relaciones primero
                if ($tabla === 'clientes' || $tabla === 'vehiculos') {
                    $elements = $conex->query("SELECT $id_column FROM $tabla WHERE activo = 0")->fetchAll();
                    
                    foreach ($elements as $element) {
                        $elementId = $element[$id_column];
                        
                        if ($tabla === 'clientes') {
                            $check = $conex->prepare("SELECT COUNT(*) as count FROM cotizaciones WHERE id_cliente = ?");
                            $check->execute([$elementId]);
                            $hasRelations = $check->fetch()['count'] > 0;
                        } else {
                            $check = $conex->prepare("SELECT COUNT(*) as count FROM cotizaciones WHERE id_vehiculo = ?");
                            $check->execute([$elementId]);
                            $hasRelations = $check->fetch()['count'] > 0;
                        }
                        
                        if (!$hasRelations) {
                            $deleteStmt = $conex->prepare("DELETE FROM $tabla WHERE $id_column = ?");
                            $deleteStmt->execute([$elementId]);
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                    }
                } else {
                    $deleteStmt = $conex->prepare("DELETE FROM $tabla WHERE activo = 0");
                    $deleteStmt->execute();
                    $successCount += $deleteStmt->rowCount();
                }
            } catch (Exception $e) {
                $errorCount++;
            }
        }
        
        // Limpiar todos los registros de eliminaciones
        $conex->exec("DELETE FROM registro_eliminaciones WHERE accion = 'ELIMINACION'");
        
        if ($errorCount > 0) {
            $_SESSION['mensaje'] = "Papelera vaciada parcialmente. $successCount elementos eliminados, $errorCount no se pudieron eliminar por relaciones existentes.";
            $_SESSION['tipo_mensaje'] = 'warning';
        } else {
            $_SESSION['mensaje'] = "Papelera vaciada correctamente. $successCount elementos eliminados.";
            $_SESSION['tipo_mensaje'] = 'success';
        }
        
        header('Location: papelera.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['mensaje'] = "Error al vaciar papelera: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Papelera | Nacional Tapizados</title>
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
            min-width: 1000px;
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

        /* Badges para tipos de elementos */
        .badge {
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-cliente {
            background-color: var(--primary-color);
        }

        .badge-vehiculo {
            background-color: var(--info-color);
        }

        .badge-material {
            background-color: var(--warning-color);
            color: #000;
        }

        .badge-servicio {
            background-color: var(--success-color);
        }

        .badge-usuario {
            background-color: var(--secondary-color);
        }

        .badge-cotizacion {
            background-color: #6f42c1;
        }

        .badge-trabajo {
            background-color: #fd7e14;
        }

        .badge-mensaje {
            background-color: #20c997;
        }

        /* Formularios */
        .form-control, .form-select {
            background-color: var(--bg-input);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            backdrop-filter: blur(5px);
        }

        .form-control:focus, .form-select:focus {
            background-color: rgba(0, 0, 0, 0.7);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(140, 74, 63, 0.25);
            color: var(--text-color);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        /* Contador de resultados */
        .results-count {
            background-color: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
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
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-trash"></i> Sistema de Papelera
            </h1>
            <div class="d-flex gap-2 align-items-center">
                <?php if (!empty($registrosEliminaciones)): ?>
                    <span class="results-count">
                        <i class="fas fa-list"></i> 
                        <?= count($registrosEliminaciones) ?> resultado(s)
                    </span>
                <?php endif; ?>
                <a href="../../dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
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

        <!-- Filtros -->
        <div class="card mb-4" style="background-color: var(--bg-transparent-light); border: 1px solid var(--border-color);">
            <div class="card-header" style="background-color: var(--primary-color); border-bottom: 1px solid var(--border-color);">
                <h5 class="m-0"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</h5>
            </div>
            <div class="card-body">
                <form method="get" class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tipo de Elemento</label>
                        <select class="form-select" name="tipo">
                            <option value="all">Todos los tipos</option>
                            <option value="clientes" <?= $tipoFiltro == 'clientes' ? 'selected' : '' ?>>Clientes</option>
                            <option value="vehiculos" <?= $tipoFiltro == 'vehiculos' ? 'selected' : '' ?>>Vehículos</option>
                            <option value="materiales" <?= $tipoFiltro == 'materiales' ? 'selected' : '' ?>>Materiales</option>
                            <option value="servicios" <?= $tipoFiltro == 'servicios' ? 'selected' : '' ?>>Servicios</option>
                            <option value="usuarios" <?= $tipoFiltro == 'usuarios' ? 'selected' : '' ?>>Usuarios</option>
                            <option value="cotizaciones" <?= $tipoFiltro == 'cotizaciones' ? 'selected' : '' ?>>Cotizaciones</option>
                            <option value="trabajos" <?= $tipoFiltro == 'trabajos' ? 'selected' : '' ?>>Trabajos</option>
                            <option value="mensajes_contacto" <?= $tipoFiltro == 'mensajes_contacto' ? 'selected' : '' ?>>Mensajes</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Fecha de Eliminación</label>
                        <select class="form-select" name="fecha">
                            <option value="all">Todas las fechas</option>
                            <option value="today" <?= $fechaFiltro == 'today' ? 'selected' : '' ?>>Hoy</option>
                            <option value="week" <?= $fechaFiltro == 'week' ? 'selected' : '' ?>>Esta semana</option>
                            <option value="month" <?= $fechaFiltro == 'month' ? 'selected' : '' ?>>Este mes</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Buscar en contenido</label>
                        <input type="text" class="form-control" name="buscar" placeholder="Buscar en datos, usuario o ID..." value="<?= htmlspecialchars($buscarTexto) ?>">
                        <div class="form-text text-light">Busca en datos del elemento, nombre del usuario o ID</div>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <div class="w-100">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <?php if ($tipoFiltro !== 'all' || $fechaFiltro !== 'all' || !empty($buscarTexto)): ?>
                                <a href="papelera.php" class="btn btn-secondary w-100 mt-2">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Elementos en Papelera -->
        <div class="card" style="background-color: var(--bg-transparent-light); border: 1px solid var(--border-color);">
            <div class="card-header d-flex justify-content-between align-items-center" style="background-color: var(--primary-color); border-bottom: 1px solid var(--border-color);">
                <h5 class="m-0"><i class="fas fa-trash-alt me-2"></i>Registros de Eliminación</h5>
                <div>
                    <?php if (!empty($registrosEliminaciones)): ?>
                    <form method="post" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea vaciar la papelera? Esta acción eliminará permanentemente todos los elementos y no se puede deshacer.')">
                        <button type="submit" name="vaciar_papelera" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Vaciar Papelera
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($registrosEliminaciones)): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Elemento</th>
                                    <th>Tipo</th>
                                    <th>Información</th>
                                    <th>Eliminado por</th>
                                    <th>Fecha de Eliminación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registrosEliminaciones as $registro): 
                                    // Determinar clase del badge según la tabla
                                    $badgeClass = 'badge-' . strtolower($registro['tabla']);
                                    $tipoTexto = ucfirst($registro['tabla']);
                                ?>
                                    <tr class="deleted-item">
                                        <td>
                                            <?= htmlspecialchars($registro['nombre_elemento'] ?? 'Elemento #' . $registro['id_registro']) ?>
                                        </td>
                                        <td>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= $tipoTexto ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($registro['datos'] ?? 'Sin información adicional') ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($registro['usuario_responsable'] ?? 'Sistema') ?>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($registro['fecha_eliminacion'])) ?></td>
                                        <td>
                                            <form method="post" class="d-inline" onsubmit="return confirm('¿Restaurar este elemento?')">
                                                <input type="hidden" name="id" value="<?= $registro['id_registro'] ?>">
                                                <input type="hidden" name="tabla" value="<?= $registro['tabla'] ?>">
                                                <button type="submit" name="restaurar" class="btn btn-success btn-sm">
                                                    <i class="fas fa-undo"></i> Restaurar
                                                </button>
                                            </form>
                                            <form method="post" class="d-inline" onsubmit="return confirm('¿ESTÁ SEGURO? Esta acción eliminará permanentemente este elemento y no se podrá recuperar.')">
                                                <input type="hidden" name="id" value="<?= $registro['id_registro'] ?>">
                                                <input type="hidden" name="tabla" value="<?= $registro['tabla'] ?>">
                                                <button type="submit" name="eliminar_permanentemente" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <?php if ($tipoFiltro !== 'all' || $fechaFiltro !== 'all' || !empty($buscarTexto)): ?>
                            No se encontraron resultados con los filtros aplicados.
                            <a href="papelera.php" class="alert-link text-light">Ver todos los registros</a>
                        <?php else: ?>
                            No hay registros de eliminación en la papelera.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-submit del formulario cuando cambian los selects
        document.addEventListener('DOMContentLoaded', function() {
            const tipoSelect = document.querySelector('select[name="tipo"]');
            const fechaSelect = document.querySelector('select[name="fecha"]');
            
            if (tipoSelect) {
                tipoSelect.addEventListener('change', function() {
                    this.form.submit();
                });
            }
            
            if (fechaSelect) {
                fechaSelect.addEventListener('change', function() {
                    this.form.submit();
                });
            }
        });
    </script>
</body>
</html>