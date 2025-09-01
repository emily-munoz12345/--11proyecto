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
    $_SESSION['mensaje'] = 'No tienes permisos para eliminar permanentemente clientes.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Verificar si se proporcionó un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje'] = 'ID de cliente no válido.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

$id_cliente = $_GET['id'];
$id_usuario = $_SESSION['usuario_id'];

// Verificar que el cliente existe y está en la papelera
try {
    $stmt = $conex->prepare("SELECT * FROM clientes WHERE id_cliente = ? AND activo = 0");
    $stmt->execute([$id_cliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$cliente) {
        $_SESSION['mensaje'] = 'Cliente no encontrado en la papelera.';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al verificar el cliente: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Eliminar permanentemente el cliente
try {
    $conex->beginTransaction();
    
    // 1. Guardar registro manual de eliminación permanente
    $stmt = $conex->prepare("
        INSERT INTO registro_eliminaciones 
        (tabla, id_registro, eliminado_por, accion, datos, datos_anteriores) 
        VALUES ('clientes', ?, ?, 'ELIMINACION_PERMANENTE', ?, ?)
    ");
    
    $datos_eliminados = "Cliente eliminado permanentemente: " . $cliente['nombre_cliente'];
    $datos_completos = json_encode([
        'id_cliente' => $cliente['id_cliente'],
        'nombre_cliente' => $cliente['nombre_cliente'],
        'correo_cliente' => $cliente['correo_cliente'],
        'telefono_cliente' => $cliente['telefono_cliente'],
        'direccion_cliente' => $cliente['direccion_cliente'],
        'notas_cliente' => $cliente['notas_cliente'],
        'fecha_registro' => $cliente['fecha_registro']
    ]);
    
    $stmt->execute([$id_cliente, $id_usuario, $datos_eliminados, $datos_completos]);
    
    // 2. Eliminar relaciones en cliente_vehiculo
    $stmt = $conex->prepare("DELETE FROM cliente_vehiculo WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    
    // 3. Eliminar físicamente el registro del cliente
    $stmt = $conex->prepare("DELETE FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    
    $conex->commit();
    
    $_SESSION['mensaje'] = 'Cliente "' . $cliente['nombre_cliente'] . '" eliminado permanentemente del sistema.';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (PDOException $e) {
    $conex->rollBack();
    $_SESSION['mensaje'] = 'Error al eliminar permanentemente el cliente: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>