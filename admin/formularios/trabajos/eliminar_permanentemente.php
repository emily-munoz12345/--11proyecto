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
    $_SESSION['mensaje'] = 'No tienes permisos para eliminar permanentemente trabajos.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Verificar si se proporcionó un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje'] = 'ID de trabajo no válido.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

$id_trabajo = $_GET['id'];
$id_usuario = $_SESSION['usuario_id'];

// Verificar que el trabajo existe y está en la papelera
try {
    $stmt = $conex->prepare("SELECT * FROM trabajos WHERE id_trabajos = ? AND activo = 0");
    $stmt->execute([$id_trabajo]);
    $trabajo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$trabajo) {
        $_SESSION['mensaje'] = 'Trabajo no encontrado en la papelera.';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al verificar el trabajo: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Eliminar permanentemente el trabajo
try {
    $conex->beginTransaction();
    
    // 1. Guardar registro manual de eliminación permanente
    $stmt = $conex->prepare("
        INSERT INTO registro_eliminaciones 
        (tabla, id_registro, eliminado_por, accion, datos, datos_anteriores) 
        VALUES ('trabajos', ?, ?, 'ELIMINACION_PERMANENTE', ?, ?)
    ");
    
    $datos_eliminados = "Trabajo eliminado permanentemente: #" . $trabajo['id_trabajos'] . " - Estado: " . $trabajo['estado'];
    $datos_completos = json_encode([
        'id_trabajos' => $trabajo['id_trabajos'],
        'id_cotizacion' => $trabajo['id_cotizacion'],
        'fecha_inicio' => $trabajo['fecha_inicio'],
        'fecha_fin' => $trabajo['fecha_fin'],
        'estado' => $trabajo['estado'],
        'notas' => $trabajo['notas'],
        'fotos' => $trabajo['fotos']
    ]);
    
    $stmt->execute([$id_trabajo, $id_usuario, $datos_eliminados, $datos_completos]);
    
    // 2. Eliminar físicamente el registro del trabajo
    $stmt = $conex->prepare("DELETE FROM trabajos WHERE id_trabajos = ?");
    $stmt->execute([$id_trabajo]);
    
    $conex->commit();
    
    $_SESSION['mensaje'] = 'Trabajo #' . $trabajo['id_trabajos'] . ' eliminado permanentemente del sistema.';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (PDOException $e) {
    $conex->rollBack();
    $_SESSION['mensaje'] = 'Error al eliminar permanentemente el trabajo: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>