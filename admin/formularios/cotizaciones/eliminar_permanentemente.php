<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/conexion.php';
require_once ROOT_PATH . '/php/auth.php';

// Verificar permisos (solo Admin)
if (!isAdmin()) {
    $_SESSION['mensaje'] = 'No tienes permisos para eliminar permanentemente cotizaciones.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Verificar si se proporcionó un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje'] = 'ID de cotización no válido.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

$id_cotizacion = $_GET['id'];
$id_usuario = $_SESSION['usuario_id'];

// Verificar que la cotización existe y está en la papelera
try {
    $stmt = $conex->prepare("SELECT * FROM cotizaciones WHERE id_cotizacion = ? AND activo = 0");
    $stmt->execute([$id_cotizacion]);
    $cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$cotizacion) {
        $_SESSION['mensaje'] = 'Cotización no encontrada en la papelera.';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al verificar la cotización: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Eliminar permanentemente la cotización
try {
    $conex->beginTransaction();
    
    // 1. Guardar registro manual de eliminación permanente
    $stmt = $conex->prepare("
        INSERT INTO registro_eliminaciones 
        (tabla, id_registro, eliminado_por, accion, datos, datos_anteriores) 
        VALUES ('cotizaciones', ?, ?, 'ELIMINACION_PERMANENTE', ?, ?)
    ");
    
    $datos_eliminados = "Cotización eliminada permanentemente: #" . $cotizacion['id_cotizacion'] . " - Total: $" . $cotizacion['total_cotizacion'];
    $datos_completos = json_encode([
        'id_cotizacion' => $cotizacion['id_cotizacion'],
        'id_usuario' => $cotizacion['id_usuario'],
        'id_cliente' => $cotizacion['id_cliente'],
        'id_vehiculo' => $cotizacion['id_vehiculo'],
        'fecha_cotizacion' => $cotizacion['fecha_cotizacion'],
        'subtotal_cotizacion' => $cotizacion['subtotal_cotizacion'],
        'valor_adicional' => $cotizacion['valor_adicional'],
        'iva' => $cotizacion['iva'],
        'total_cotizacion' => $cotizacion['total_cotizacion'],
        'estado_cotizacion' => $cotizacion['estado_cotizacion'],
        'notas_cotizacion' => $cotizacion['notas_cotizacion']
    ]);
    
    $stmt->execute([$id_cotizacion, $id_usuario, $datos_eliminados, $datos_completos]);
    
    // 2. Eliminar relaciones en cotizacion_servicios
    $stmt = $conex->prepare("DELETE FROM cotizacion_servicios WHERE id_cotizacion = ?");
    $stmt->execute([$id_cotizacion]);
    
    // 3. Verificar si hay trabajos relacionados
    $stmt = $conex->prepare("SELECT * FROM trabajos WHERE id_cotizacion = ? AND activo = 1");
    $stmt->execute([$id_cotizacion]);
    $trabajos_relacionados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($trabajos_relacionados) {
        $_SESSION['mensaje'] = 'No se puede eliminar la cotización porque tiene trabajos relacionados. Elimine primero los trabajos.';
        $_SESSION['tipo_mensaje'] = 'danger';
        $conex->rollBack();
        header('Location: index.php');
        exit;
    }
    
    // 4. Eliminar físicamente el registro de la cotización
    $stmt = $conex->prepare("DELETE FROM cotizaciones WHERE id_cotizacion = ?");
    $stmt->execute([$id_cotizacion]);
    
    $conex->commit();
    
    $_SESSION['mensaje'] = 'Cotización #' . $cotizacion['id_cotizacion'] . ' eliminada permanentemente del sistema.';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (PDOException $e) {
    $conex->rollBack();
    $_SESSION['mensaje'] = 'Error al eliminar permanentemente la cotización: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>