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
    $_SESSION['mensaje'] = 'No tienes permisos para eliminar permanentemente vehículos.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
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

// Verificar que el vehículo existe y está en la papelera
try {
    $stmt = $conex->prepare("SELECT * FROM vehiculos WHERE id_vehiculo = ? AND activo = 0");
    $stmt->execute([$id_vehiculo]);
    $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$vehiculo) {
        $_SESSION['mensaje'] = 'Vehículo no encontrado en la papelera.';
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

// Eliminar permanentemente el vehículo
try {
    $conex->beginTransaction();
    
    // 1. Guardar registro manual de eliminación permanente
    $stmt = $conex->prepare("
        INSERT INTO registro_eliminaciones 
        (tabla, id_registro, eliminado_por, accion, datos, datos_anteriores) 
        VALUES ('vehiculos', ?, ?, 'ELIMINACION_PERMANENTE', ?, ?)
    ");
    
    $datos_eliminados = "Vehículo eliminado permanentemente: " . $vehiculo['marca_vehiculo'] . " " . $vehiculo['modelo_vehiculo'] . " - " . $vehiculo['placa_vehiculo'];
    $datos_completos = json_encode([
        'id_vehiculo' => $vehiculo['id_vehiculo'],
        'marca_vehiculo' => $vehiculo['marca_vehiculo'],
        'modelo_vehiculo' => $vehiculo['modelo_vehiculo'],
        'placa_vehiculo' => $vehiculo['placa_vehiculo'],
        'notas_vehiculo' => $vehiculo['notas_vehiculo']
    ]);
    
    $stmt->execute([$id_vehiculo, $id_usuario, $datos_eliminados, $datos_completos]);
    
    // 2. Eliminar relaciones en cliente_vehiculo
    $stmt = $conex->prepare("DELETE FROM cliente_vehiculo WHERE id_vehiculo = ?");
    $stmt->execute([$id_vehiculo]);
    
    // 3. Verificar si hay cotizaciones relacionadas (aunque estén inactivas)
    $stmt = $conex->prepare("SELECT COUNT(*) FROM cotizaciones WHERE id_vehiculo = ?");
    $stmt->execute([$id_vehiculo]);
    $cotizaciones_relacionadas = $stmt->fetchColumn();
    
    if ($cotizaciones_relacionadas > 0) {
        $_SESSION['mensaje'] = 'No se puede eliminar permanentemente el vehículo porque tiene cotizaciones relacionadas en el historial.';
        $_SESSION['tipo_mensaje'] = 'danger';
        $conex->rollBack();
        header('Location: index.php');
        exit;
    }
    
    // 4. Eliminar físicamente el registro del vehículo
    $stmt = $conex->prepare("DELETE FROM vehiculos WHERE id_vehiculo = ?");
    $stmt->execute([$id_vehiculo]);
    
    $conex->commit();
    
    $_SESSION['mensaje'] = 'Vehículo "' . $vehiculo['marca_vehiculo'] . ' ' . $vehiculo['modelo_vehiculo'] . '" eliminado permanentemente del sistema.';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (PDOException $e) {
    $conex->rollBack();
    $_SESSION['mensaje'] = 'Error al eliminar permanentemente el vehículo: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>