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

// Verificar que el servicio existe y está eliminado
try {
    $stmt = $conex->prepare("SELECT * FROM servicios WHERE id_servicio = ? AND activo = 0");
    $stmt->execute([$id_servicio]);
    $servicio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$servicio) {
        $_SESSION['mensaje'] = 'Servicio no encontrado en la papelera o ya fue restaurado.';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al verificar el servicio: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Restaurar el servicio
try {
    // Verificar si existe la columna fecha_eliminacion
    $column_check = $conex->query("SHOW COLUMNS FROM servicios LIKE 'fecha_eliminacion'");
    $has_fecha_eliminacion = $column_check->rowCount() > 0;
    
    // Establecer el usuario actual para el trigger
    $stmt = $conex->prepare("CALL SetUsuarioActual(?)");
    $stmt->execute([$id_usuario]);
    
    if ($has_fecha_eliminacion) {
        // Reactivar el servicio con fecha_eliminacion
        $stmt = $conex->prepare("UPDATE servicios SET activo = 1, fecha_eliminacion = NULL WHERE id_servicio = ?");
    } else {
        // Reactivar el servicio sin fecha_eliminacion
        $stmt = $conex->prepare("UPDATE servicios SET activo = 1 WHERE id_servicio = ?");
    }
    
    $stmt->execute([$id_servicio]);
    
    // Limpiar el usuario actual
    $stmt = $conex->prepare("CALL LimpiarUsuarioActual()");
    $stmt->execute();
    
    $_SESSION['mensaje'] = 'Servicio "' . $servicio['nombre_servicio'] . '" restaurado correctamente.';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al restaurar el servicio: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>