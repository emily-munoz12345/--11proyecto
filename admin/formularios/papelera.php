<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

// Verificar permisos (solo Admin)
if (!isAdmin()) {
    header('Location: ../dashboard.php');
    exit;
}

// Inicializar mensajes
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
    $_SESSION['tipo_mensaje'] = '';
}

// Parámetros de búsqueda
$tipoFiltro = $_GET['tipo'] ?? 'all';
$buscarTexto = trim($_GET['buscar'] ?? '');

// Configuración de tablas y columnas
$tablasConfig = [
    'clientes' => [
        'id_col' => 'id_cliente', 
        'nombre_col' => 'nombre_cliente'
    ],
    'vehiculos' => [
        'id_col' => 'id_vehiculo', 
        'nombre_col' => 'CONCAT(marca_vehiculo, " ", modelo_vehiculo)'
    ],
    'materiales' => [
        'id_col' => 'id_material', 
        'nombre_col' => 'nombre_material'
    ],
    'servicios' => [
        'id_col' => 'id_servicio', 
        'nombre_col' => 'nombre_servicio'
    ],
    'cotizaciones' => [
        'id_col' => 'id_cotizacion', 
        'nombre_col' => 'CONCAT("Cotización #", id_cotizacion)'
    ],
    'trabajos' => [
        'id_col' => 'id_trabajos', 
        'nombre_col' => 'CONCAT("Trabajo #", id_trabajos)'
    ],
    'mensajes_contacto' => [
        'id_col' => 'id_mensaje', 
        'nombre_col' => 'CONCAT("Mensaje: ", asunto)'
    ]
];

// Obtener estadísticas basadas en elementos actualmente en papelera
$statsQuery = "
    SELECT 
        COUNT(DISTINCT CONCAT(tabla, '-', id_registro)) as total_elementos,
        MAX(fecha_eliminacion) as ultima_eliminacion,
        COUNT(DISTINCT tabla) as tipos_diferentes
    FROM registro_eliminaciones 
    WHERE accion = 'ELIMINACION'
    AND EXISTS (
        SELECT 1 FROM (
            SELECT 'clientes' as tabla_name, id_cliente as id FROM clientes WHERE activo = 0
            UNION ALL SELECT 'vehiculos', id_vehiculo FROM vehiculos WHERE activo = 0
            UNION ALL SELECT 'materiales', id_material FROM materiales WHERE activo = 0
            UNION ALL SELECT 'servicios', id_servicio FROM servicios WHERE activo = 0
            UNION ALL SELECT 'cotizaciones', id_cotizacion FROM cotizaciones WHERE activo = 0
            UNION ALL SELECT 'trabajos', id_trabajos FROM trabajos WHERE activo = 0
            UNION ALL SELECT 'mensajes_contacto', id_mensaje FROM mensajes_contacto WHERE activo = 0
        ) AS elementos_papelera
        WHERE elementos_papelera.tabla_name = registro_eliminaciones.tabla 
        AND elementos_papelera.id = registro_eliminaciones.id_registro
    )
";

$stats = $conex->query($statsQuery)->fetch(PDO::FETCH_ASSOC);

// Construir consulta principal para obtener elementos únicos en papelera
$query = "
    SELECT 
        re.*,
        u.nombre_completo as usuario_responsable,
        CASE re.tabla
";

foreach ($tablasConfig as $tabla => $config) {
    $query .= " WHEN '$tabla' THEN (SELECT {$config['nombre_col']} FROM $tabla WHERE {$config['id_col']} = re.id_registro) \n";
}

$query .= " ELSE 'Elemento' END as nombre_elemento
    FROM registro_eliminaciones re
    LEFT JOIN usuarios u ON re.eliminado_por = u.id_usuario
    WHERE re.accion = 'ELIMINACION'
    AND (re.tabla, re.id_registro, re.fecha_eliminacion) IN (
        SELECT tabla, id_registro, MAX(fecha_eliminacion)
        FROM registro_eliminaciones 
        WHERE accion = 'ELIMINACION'
        GROUP BY tabla, id_registro
    )
    AND EXISTS (
        SELECT 1 FROM (
            SELECT 'clientes' as tabla_name, id_cliente as id FROM clientes WHERE activo = 0
            UNION ALL SELECT 'vehiculos', id_vehiculo FROM vehiculos WHERE activo = 0
            UNION ALL SELECT 'materiales', id_material FROM materiales WHERE activo = 0
            UNION ALL SELECT 'servicios', id_servicio FROM servicios WHERE activo = 0
            UNION ALL SELECT 'cotizaciones', id_cotizacion FROM cotizaciones WHERE activo = 0
            UNION ALL SELECT 'trabajos', id_trabajos FROM trabajos WHERE activo = 0
            UNION ALL SELECT 'mensajes_contacto', id_mensaje FROM mensajes_contacto WHERE activo = 0
        ) AS elementos_papelera
        WHERE elementos_papelera.tabla_name = re.tabla 
        AND elementos_papelera.id = re.id_registro
    )
";

// Aplicar filtros
$params = [];
if ($tipoFiltro !== 'all' && isset($tablasConfig[$tipoFiltro])) {
    $query .= " AND re.tabla = ?";
    $params[] = $tipoFiltro;
}

if (!empty($buscarTexto)) {
    $query .= " AND (
        re.datos LIKE ? OR 
        u.nombre_completo LIKE ? OR
        re.id_registro LIKE ? OR
        re.tabla LIKE ?
    )";
    $searchTerm = "%$buscarTexto%";
    array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
}

$query .= " ORDER BY re.fecha_eliminacion DESC";

// Obtener registros
$registrosEliminaciones = [];
try {
    $stmt = $conex->prepare($query);
    $stmt->execute($params);
    $registrosEliminaciones = $stmt->fetchAll();
} catch (Exception $e) {
    $_SESSION['mensaje'] = "Error al cargar la papelera: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conex->beginTransaction();
        
        if (isset($_POST['restaurar']) && isset($_POST['id']) && isset($_POST['tabla'])) {
            $id = $_POST['id'];
            $tabla = $_POST['tabla'];
            
            if (!is_numeric($id) || !isset($tablasConfig[$tabla])) {
                throw new Exception("Datos inválidos para restaurar");
            }
            
            $id_col = $tablasConfig[$tabla]['id_col'];
            
            // Verificar que el elemento existe y está en papelera
            $checkStmt = $conex->prepare("SELECT COUNT(*) as count FROM $tabla WHERE $id_col = ? AND activo = 0");
            $checkStmt->execute([$id]);
            $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] == 0) {
                throw new Exception("El elemento no existe en la papelera o ya fue restaurado");
            }
            
            // Configurar usuario actual para triggers - CORREGIDO EL TYP0
            $user_id = $_SESSION['user_id'] ?? 0; // Usar valor por defecto si no existe
            $conex->query("CALL SetUsuarioActual(" . intval($user_id) . ")");
            
            // Restaurar elemento
            $updateStmt = $conex->prepare("UPDATE $tabla SET activo = 1 WHERE $id_col = ?");
            $updateStmt->execute([$id]);
            
            // Limpiar usuario actual
            $conex->query("CALL LimpiarUsuarioActual()");
            
            $_SESSION['mensaje'] = "Elemento restaurado correctamente de " . ucfirst(str_replace('_', ' ', $tabla));
            $_SESSION['tipo_mensaje'] = 'success';
            
        } elseif (isset($_POST['eliminar_permanentemente']) && isset($_POST['id']) && isset($_POST['tabla'])) {
            $id = $_POST['id'];
            $tabla = $_POST['tabla'];
            
            if (!is_numeric($id) || !isset($tablasConfig[$tabla])) {
                throw new Exception("Datos inválidos para eliminar");
            }
            
            $id_col = $tablasConfig[$tabla]['id_col'];
            
            // Verificar que el elemento existe
            $checkStmt = $conex->prepare("SELECT COUNT(*) as count FROM $tabla WHERE $id_col = ?");
            $checkStmt->execute([$id]);
            $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] == 0) {
                throw new Exception("El elemento no existe");
            }
            
            // Eliminar elemento permanentemente
            $deleteStmt = $conex->prepare("DELETE FROM $tabla WHERE $id_col = ?");
            $deleteStmt->execute([$id]);
            
            // Eliminar registros de eliminación
            $deleteRegistroStmt = $conex->prepare("DELETE FROM registro_eliminaciones WHERE tabla = ? AND id_registro = ? AND accion = 'ELIMINACION'");
            $deleteRegistroStmt->execute([$tabla, $id]);
            
            $_SESSION['mensaje'] = "Elemento eliminado permanentemente de " . ucfirst(str_replace('_', ' ', $tabla));
            $_SESSION['tipo_mensaje'] = 'success';
            
        } elseif (isset($_POST['vaciar_papelera'])) {
            $totalEliminados = 0;
            
            foreach ($tablasConfig as $tabla => $config) {
                $id_col = $config['id_col'];
                
                // Contar elementos a eliminar
                $countStmt = $conex->prepare("SELECT COUNT(*) as count FROM $tabla WHERE activo = 0");
                $countStmt->execute();
                $count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
                $totalEliminados += $count;
                
                // Eliminar elementos inactivos
                $deleteStmt = $conex->prepare("DELETE FROM $tabla WHERE activo = 0");
                $deleteStmt->execute();
            }
            
            // Limpiar registros de eliminaciones
            $conex->exec("DELETE FROM registro_eliminaciones WHERE accion = 'ELIMINACION'");
            
            $_SESSION['mensaje'] = "Papelera vaciada correctamente. Se eliminaron $totalEliminados elementos permanentemente.";
            $_SESSION['tipo_mensaje'] = 'success';
        }
        
        $conex->commit();
        
        // Redirigir para evitar reenvío del formulario
        header('Location: papelera.php?' . http_build_query($_GET));
        exit;
        
    } catch (Exception $e) {
        $conex->rollBack();
        $_SESSION['mensaje'] = "Error en la operación: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Papelera</title>
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
        }

        .page-title i {
            margin-right: 12px;
            color: var(--primary-color);
        }

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
        }

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
            min-width: 800px;
        }

        th, td {
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
        }

        tr:hover {
            background-color: rgba(140, 74, 63, 0.2);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.35rem 0.5rem;
            font-size: 0.8rem;
        }

        .btn-primary { background-color: var(--primary-color); }
        .btn-secondary { background-color: var(--secondary-color); }
        .btn-danger { background-color: var(--danger-color); }
        .btn-success { background-color: var(--success-color); }

        .btn:hover {
            opacity: 0.9;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(5px);
            border-left: 4px solid var(--info-color);
        }

        .alert-success { background-color: rgba(25, 135, 84, 0.2); border-left-color: var(--success-color); }
        .alert-danger { background-color: rgba(220, 53, 69, 0.2); border-left-color: var(--danger-color); }

        .deleted-item {
            opacity: 0.8;
            background-color: rgba(220, 53, 69, 0.1);
        }
        
        .deleted-item:hover {
            background-color: rgba(220, 53, 69, 0.2);
        }

        .badge {
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-cliente { background-color: var(--primary-color); }
        .badge-vehiculo { background-color: var(--info-color); }
        .badge-material { background-color: var(--warning-color); color: #000; }
        .badge-servicio { background-color: var(--success-color); }
        .badge-cotizacion { background-color: #6f42c1; }
        .badge-trabajo { background-color: #fd7e14; }
        .badge-mensaje { background-color: #20c997; }

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
            table {
                min-width: 600px;
            }
            .summary-cards {
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
                <i class="fas fa-trash"></i> Sistema de Papelera
            </h1>
            <a href="../dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Inicio
            </a>
        </div>

        <!-- Mensajes -->
        <?php if (!empty($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
                <div>
                    <i class="fas fa-<?= $_SESSION['tipo_mensaje'] === 'success' ? 'check-circle' : 'times-circle' ?> me-2"></i>
                    <?= $_SESSION['mensaje'] ?>
                </div>
                <button type="button" class="btn-close btn-close-white" onclick="this.parentElement.style.display='none'"></button>
            </div>
            <?php $_SESSION['mensaje'] = $_SESSION['tipo_mensaje'] = ''; ?>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total en Papelera</h3>
                <p><?= $stats['total_elementos'] ?? 0 ?></p>
            </div>
            <div class="summary-card">
                <h3>Tipos Diferentes</h3>
                <p><?= $stats['tipos_diferentes'] ?? 0 ?></p>
            </div>
            <div class="summary-card">
                <h3>Última Eliminación</h3>
                <p><?= $stats['ultima_eliminacion'] ? date('d/m/Y H:i', strtotime($stats['ultima_eliminacion'])) : 'N/A' ?></p>
            </div>
            <div class="summary-card">
                <h3>Mostrando</h3>
                <p><?= count($registrosEliminaciones) ?></p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4" style="background-color: var(--bg-transparent-light); border: 1px solid var(--border-color);">
            <div class="card-header" style="background-color: var(--primary-color); border-bottom: 1px solid var(--border-color);">
                <h5 class="m-0"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</h5>
            </div>
            <div class="card-body">
                <form method="get" id="filterForm" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tipo de Elemento</label>
                        <select class="form-select" name="tipo" onchange="this.form.submit()">
                            <option value="all">Todos los tipos</option>
                            <?php foreach ($tablasConfig as $tabla => $config): ?>
                                <option value="<?= $tabla ?>" <?= $tipoFiltro == $tabla ? 'selected' : '' ?>>
                                    <?= ucfirst(str_replace('_', ' ', $tabla)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Buscar en contenido</label>
                        <input type="text" class="form-control" name="buscar" value="<?= htmlspecialchars($buscarTexto) ?>"
                               placeholder="Buscar en datos, usuario o ID...">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <?php if ($tipoFiltro !== 'all' || !empty($buscarTexto)): ?>
                            <a href="papelera.php" class="btn btn-secondary w-100">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Elementos en Papelera -->
        <div class="card" style="background-color: var(--bg-transparent-light); border: 1px solid var(--border-color);">
            <div class="card-header d-flex justify-content-between align-items-center" style="background-color: var(--primary-color); border-bottom: 1px solid var(--border-color);">
                <h5 class="m-0"><i class="fas fa-trash-alt me-2"></i>Registros de Eliminación</h5>
                <?php if (!empty($registrosEliminaciones)): ?>
                    <form method="post" onsubmit="return confirm('¿Está seguro de que desea vaciar la papelera? Esta acción eliminará permanentemente todos los elementos y no se puede deshacer.')">
                        <button type="submit" name="vaciar_papelera" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Vaciar Papelera
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (!empty($registrosEliminaciones)): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Elemento</th>
                                    <th>Tipo</th>
                                    <th>Eliminado por</th>
                                    <th>Fecha de Eliminación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registrosEliminaciones as $registro): ?>
                                    <tr class="deleted-item">
                                        <td>
                                            <?= htmlspecialchars($registro['nombre_elemento'] ?? 'Elemento #' . $registro['id_registro']) ?>
                                            <?php if (!empty($registro['datos'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($registro['datos']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= str_replace('_', '', $registro['tabla']) ?>">
                                                <?= ucfirst(str_replace('_', ' ', $registro['tabla'])) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($registro['usuario_responsable'] ?? 'Sistema') ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($registro['fecha_eliminacion'])) ?></td>
                                        <td>
                                            <form method="post" class="d-inline" onsubmit="return confirm('¿Restaurar este elemento de <?= ucfirst(str_replace('_', ' ', $registro['tabla'])) ?>?')">
                                                <input type="hidden" name="id" value="<?= $registro['id_registro'] ?>">
                                                <input type="hidden" name="tabla" value="<?= $registro['tabla'] ?>">
                                                <button type="submit" name="restaurar" class="btn btn-success btn-sm">
                                                    <i class="fas fa-undo"></i> Restaurar
                                                </button>
                                            </form>
                                            <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar PERMANENTEMENTE este elemento de <?= ucfirst(str_replace('_', ' ', $registro['tabla'])) ?>? Esta acción no se puede deshacer.')">
                                                <input type="hidden" name="id" value="<?= $registro['id_registro'] ?>">
                                                <input type="hidden" name="tabla" value="<?= $registro['tabla'] ?>">
                                                <button type="submit" name="eliminar_permanentemente" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-times"></i> Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-trash fa-4x mb-3" style="color: var(--text-muted);"></i>
                        <h4 style="color: var(--text-muted);">La papelera está vacía</h4>
                        <p style="color: var(--text-muted);">No hay elementos eliminados que mostrar.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-ocultar mensajes después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(() => {
                    if (alert.style.display !== 'none') {
                        alert.style.opacity = '0';
                        setTimeout(() => alert.style.display = 'none', 300);
                    }
                }, 5000);
            });
        });
    </script>
</body>
</html>