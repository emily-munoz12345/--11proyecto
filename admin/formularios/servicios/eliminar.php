<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/conexion.php';
require_once ROOT_PATH . '/php/auth.php';

// Verificar permisos (solo Admin y Vendedor)
if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Verificar si se proporcionó un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje'] = 'ID de servicio no válido.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

$id_servicio = $_GET['id'];
$id_usuario = $_SESSION['usuario_id'];

// Obtener datos del servicio antes de eliminar
try {
    $stmt = $conex->prepare("SELECT * FROM servicios WHERE id_servicio = ? AND activo = 1");
    $stmt->execute([$id_servicio]);
    $servicio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$servicio) {
        $_SESSION['mensaje'] = 'Servicio no encontrado o ya eliminado.';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al obtener datos del servicio: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Verificar si el servicio está siendo usado en alguna cotización
try {
    $stmt = $conex->prepare("SELECT COUNT(*) FROM cotizacion_servicios WHERE id_servicio = ?");
    $stmt->execute([$id_servicio]);
    $usoEnCotizaciones = $stmt->fetchColumn();
    
    if ($usoEnCotizaciones > 0) {
        $_SESSION['mensaje'] = 'No se puede eliminar el servicio porque está siendo usado en ' . $usoEnCotizaciones . ' cotización(es)';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al verificar uso del servicio: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Realizar eliminación lógica (soft delete)
try {
    // Verificar si existe la columna fecha_eliminacion
    $column_check = $conex->query("SHOW COLUMNS FROM servicios LIKE 'fecha_eliminacion'");
    $has_fecha_eliminacion = $column_check->rowCount() > 0;
    
    if ($has_fecha_eliminacion) {
        // Usar el trigger existente con fecha_eliminacion
        $stmt = $conex->prepare("CALL SetUsuarioActual(?)");
        $stmt->execute([$id_usuario]);
        
        $stmt = $conex->prepare("UPDATE servicios SET activo = 0, fecha_eliminacion = NOW() WHERE id_servicio = ?");
        $stmt->execute([$id_servicio]);
        
        $stmt = $conex->prepare("CALL LimpiarUsuarioActual()");
        $stmt->execute();
    } else {
        // Alternativa sin fecha_eliminacion
        $stmt = $conex->prepare("CALL SetUsuarioActual(?)");
        $stmt->execute([$id_usuario]);
        
        $stmt = $conex->prepare("UPDATE servicios SET activo = 0 WHERE id_servicio = ?");
        $stmt->execute([$id_servicio]);
        
        $stmt = $conex->prepare("CALL LimpiarUsuarioActual()");
        $stmt->execute();
    }
    
    $_SESSION['mensaje'] = 'Servicio "' . $servicio['nombre_servicio'] . '" movido a la papelera correctamente.';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al eliminar el servicio: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>