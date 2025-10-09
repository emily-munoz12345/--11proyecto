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
    $_SESSION['mensaje'] = 'ID de vehículo no válido.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

$id_vehiculo = $_GET['id'];
$id_usuario = $_SESSION['usuario_id'];

// Obtener datos del vehículo antes de eliminar
try {
    $stmt = $conex->prepare("SELECT * FROM vehiculos WHERE id_vehiculo = ? AND activo = 1");
    $stmt->execute([$id_vehiculo]);
    $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$vehiculo) {
        $_SESSION['mensaje'] = 'Vehículo no encontrado o ya eliminado.';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al obtener datos del vehículo: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// VERIFICAR SI EL VEHÍCULO TIENE COTIZACIONES ACTIVAS ANTES DE ELIMINAR
try {
    $stmt = $conex->prepare("SELECT COUNT(*) FROM cotizaciones WHERE id_vehiculo = ? AND activo = 1");
    $stmt->execute([$id_vehiculo]);
    $cotizaciones_activas = $stmt->fetchColumn();
    
    if ($cotizaciones_activas > 0) {
        $_SESSION['mensaje'] = 'No se puede eliminar el vehículo porque tiene cotizaciones activas relacionadas.';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al verificar cotizaciones: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Realizar eliminación lógica (soft delete)
try {
    // Verificar si existe la columna fecha_eliminacion
    $column_check = $conex->query("SHOW COLUMNS FROM vehiculos LIKE 'fecha_eliminacion'");
    $has_fecha_eliminacion = $column_check->rowCount() > 0;
    
    if ($has_fecha_eliminacion) {
        // Usar el trigger existente con fecha_eliminacion
        $stmt = $conex->prepare("CALL SetUsuarioActual(?)");
        $stmt->execute([$id_usuario]);
        
        $stmt = $conex->prepare("UPDATE vehiculos SET activo = 0, fecha_eliminacion = NOW() WHERE id_vehiculo = ?");
        $stmt->execute([$id_vehiculo]);
        
        $stmt = $conex->prepare("CALL LimpiarUsuarioActual()");
        $stmt->execute();
    } else {
        // Alternativa sin fecha_eliminacion
        $stmt = $conex->prepare("CALL SetUsuarioActual(?)");
        $stmt->execute([$id_usuario]);
        
        $stmt = $conex->prepare("UPDATE vehiculos SET activo = 0 WHERE id_vehiculo = ?");
        $stmt->execute([$id_vehiculo]);
        
        $stmt = $conex->prepare("CALL LimpiarUsuarioActual()");
        $stmt->execute();
    }
    
    $_SESSION['mensaje'] = 'Vehículo "' . $vehiculo['marca_vehiculo'] . ' ' . $vehiculo['modelo_vehiculo'] . '" movido a la papelera correctamente.';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al eliminar el vehículo: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>