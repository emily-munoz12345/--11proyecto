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
    $_SESSION['mensaje'] = 'ID de cotización no válido.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

$id_cotizacion = $_GET['id'];
$id_usuario = $_SESSION['usuario_id'];

// Verificar que la cotización existe y está eliminada
try {
    $stmt = $conex->prepare("SELECT * FROM cotizaciones WHERE id_cotizacion = ? AND activo = 0");
    $stmt->execute([$id_cotizacion]);
    $cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$cotizacion) {
        $_SESSION['mensaje'] = 'Cotización no encontrada en la papelera o ya fue restaurada.';
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

// Restaurar la cotización
try {
    // Verificar si existe la columna fecha_eliminacion
    $column_check = $conex->query("SHOW COLUMNS FROM cotizaciones LIKE 'fecha_eliminacion'");
    $has_fecha_eliminacion = $column_check->rowCount() > 0;
    
    // Establecer el usuario actual para el trigger
    $stmt = $conex->prepare("CALL SetUsuarioActual(?)");
    $stmt->execute([$id_usuario]);
    
    if ($has_fecha_eliminacion) {
        // Reactivar la cotización con fecha_eliminacion
        $stmt = $conex->prepare("UPDATE cotizaciones SET activo = 1, fecha_eliminacion = NULL WHERE id_cotizacion = ?");
    } else {
        // Reactivar la cotización sin fecha_eliminacion
        $stmt = $conex->prepare("UPDATE cotizaciones SET activo = 1 WHERE id_cotizacion = ?");
    }
    
    $stmt->execute([$id_cotizacion]);
    
    // Limpiar el usuario actual
    $stmt = $conex->prepare("CALL LimpiarUsuarioActual()");
    $stmt->execute();
    
    $_SESSION['mensaje'] = 'Cotización #' . $cotizacion['id_cotizacion'] . ' restaurada correctamente.';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al restaurar la cotización: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>