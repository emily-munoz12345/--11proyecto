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
    $_SESSION['mensaje'] = 'No tienes permisos para restaurar materiales.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Verificar si se proporcionó un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje'] = 'ID de material no válido.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

$id_material = $_GET['id'];
$id_usuario = $_SESSION['usuario_id'];

// Verificar que el material existe y está eliminado
try {
    $stmt = $conex->prepare("SELECT * FROM materiales WHERE id_material = ? AND activo = 0");
    $stmt->execute([$id_material]);
    $material = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$material) {
        $_SESSION['mensaje'] = 'Material no encontrado en la papelera o ya fue restaurado.';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al verificar el material: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Restaurar el material
try {
    // Verificar si existe la columna fecha_eliminacion
    $column_check = $conex->query("SHOW COLUMNS FROM materiales LIKE 'fecha_eliminacion'");
    $has_fecha_eliminacion = $column_check->rowCount() > 0;
    
    // Establecer el usuario actual para el trigger
    $stmt = $conex->prepare("CALL SetUsuarioActual(?)");
    $stmt->execute([$id_usuario]);
    
    if ($has_fecha_eliminacion) {
        // Reactivar el material con fecha_eliminacion
        $stmt = $conex->prepare("UPDATE materiales SET activo = 1, fecha_eliminacion = NULL WHERE id_material = ?");
    } else {
        // Reactivar el material sin fecha_eliminacion
        $stmt = $conex->prepare("UPDATE materiales SET activo = 1 WHERE id_material = ?");
    }
    
    $stmt->execute([$id_material]);
    
    // Limpiar el usuario actual
    $stmt = $conex->prepare("CALL LimpiarUsuarioActual()");
    $stmt->execute();
    
    $_SESSION['mensaje'] = 'Material "' . $material['nombre_material'] . '" restaurado correctamente.';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al restaurar el material: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>