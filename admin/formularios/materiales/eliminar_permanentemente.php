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
    $_SESSION['mensaje'] = 'No tienes permisos para eliminar permanentemente materiales.';
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

// Verificar que el material existe y está en la papelera
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

// Eliminar permanentemente el material
try {
    $conex->beginTransaction();
    
    // 1. Guardar registro manual de eliminación permanente
    $stmt = $conex->prepare("
        INSERT INTO registro_eliminaciones 
        (tabla, id_registro, eliminado_por, accion, datos, datos_anteriores) 
        VALUES ('materiales', ?, ?, 'ELIMINACION_PERMANENTE', ?, ?)
    ");
    
    $datos_eliminados = "Material eliminado permanentemente: " . $material['nombre_material'] . " - Categoría: " . $material['categoria_material'];
    $datos_completos = json_encode([
        'id_material' => $material['id_material'],
        'nombre_material' => $material['nombre_material'],
        'descripcion_material' => $material['descripcion_material'],
        'precio_metro' => $material['precio_metro'],
        'stock_material' => $material['stock_material'],
        'categoria_material' => $material['categoria_material'],
        'proveedor_material' => $material['proveedor_material'],
        'fecha_registro' => $material['fecha_registro'],
        'fecha_actualizacion' => $material['fecha_actualizacion']
    ]);
    
    $stmt->execute([$id_material, $id_usuario, $datos_eliminados, $datos_completos]);
    
    // 2. Eliminar físicamente el registro del material
    $stmt = $conex->prepare("DELETE FROM materiales WHERE id_material = ?");
    $stmt->execute([$id_material]);
    
    $conex->commit();
    
    $_SESSION['mensaje'] = 'Material "' . $material['nombre_material'] . '" eliminado permanentemente del sistema.';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (PDOException $e) {
    $conex->rollBack();
    $_SESSION['mensaje'] = 'Error al eliminar permanentemente el material: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>