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
    $_SESSION['mensaje'] = 'No tienes permisos para eliminar permanentemente servicios.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
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

// Verificar que el servicio existe y está en la papelera
try {
    $stmt = $conex->prepare("SELECT * FROM servicios WHERE id_servicio = ? AND activo = 0");
    $stmt->execute([$id_servicio]);
    $servicio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$servicio) {
        $_SESSION['mensaje'] = 'Servicio no encontrado en la papelera.';
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

// Eliminar permanentemente el servicio
try {
    $conex->beginTransaction();
    
    // 1. Guardar registro manual de eliminación permanente
    $stmt = $conex->prepare("
        INSERT INTO registro_eliminaciones 
        (tabla, id_registro, eliminado_por, accion, datos, datos_anteriores) 
        VALUES ('servicios', ?, ?, 'ELIMINACION_PERMANENTE', ?, ?)
    ");
    
    $datos_eliminados = "Servicio eliminado permanentemente: " . $servicio['nombre_servicio'];
    $datos_completos = json_encode([
        'id_servicio' => $servicio['id_servicio'],
        'nombre_servicio' => $servicio['nombre_servicio'],
        'descripcion_servicio' => $servicio['descripcion_servicio'],
        'precio_servicio' => $servicio['precio_servicio'],
        'tiempo_estimado' => $servicio['tiempo_estimado'],
        'categoria_servicio' => $servicio['categoria_servicio'],
        'fecha_registro' => $servicio['fecha_registro']
    ]);
    
    $stmt->execute([$id_servicio, $id_usuario, $datos_eliminados, $datos_completos]);
    
    // 2. Eliminar relaciones en cotizacion_servicios
    $stmt = $conex->prepare("DELETE FROM cotizacion_servicios WHERE id_servicio = ?");
    $stmt->execute([$id_servicio]);
    
    // 3. Eliminar físicamente el registro del servicio
    $stmt = $conex->prepare("DELETE FROM servicios WHERE id_servicio = ?");
    $stmt->execute([$id_servicio]);
    
    $conex->commit();
    
    $_SESSION['mensaje'] = 'Servicio "' . $servicio['nombre_servicio'] . '" eliminado permanentemente del sistema.';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (PDOException $e) {
    $conex->rollBack();
    $_SESSION['mensaje'] = 'Error al eliminar permanentemente el servicio: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>