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

// Verificar que el vehículo existe y está eliminado
try {
    $stmt = $conex->prepare("SELECT * FROM vehiculos WHERE id_vehiculo = ? AND activo = 0");
    $stmt->execute([$id_vehiculo]);
    $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$vehiculo) {
        $_SESSION['mensaje'] = 'Vehículo no encontrado en la papelera o ya fue restaurado.';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al verificar el vehículo: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Restaurar el vehículo
try {
    // Verificar si existe la columna fecha_eliminacion
    $column_check = $conex->query("SHOW COLUMNS FROM vehiculos LIKE 'fecha_eliminacion'");
    $has_fecha_eliminacion = $column_check->rowCount() > 0;
    
    // Establecer el usuario actual para el trigger
    $stmt = $conex->prepare("CALL SetUsuarioActual(?)");
    $stmt->execute([$id_usuario]);
    
    if ($has_fecha_eliminacion) {
        // Reactivar el vehículo con fecha_eliminacion
        $stmt = $conex->prepare("UPDATE vehiculos SET activo = 1, fecha_eliminacion = NULL WHERE id_vehiculo = ?");
    } else {
        // Reactivar el vehículo sin fecha_eliminacion
        $stmt = $conex->prepare("UPDATE vehiculos SET activo = 1 WHERE id_vehiculo = ?");
    }
    
    $stmt->execute([$id_vehiculo]);
    
    // Limpiar el usuario actual
    $stmt = $conex->prepare("CALL LimpiarUsuarioActual()");
    $stmt->execute();
    
    $_SESSION['mensaje'] = 'Vehículo "' . $vehiculo['marca_vehiculo'] . ' ' . $vehiculo['modelo_vehiculo'] . '" restaurado correctamente.';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al restaurar el vehículo: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>