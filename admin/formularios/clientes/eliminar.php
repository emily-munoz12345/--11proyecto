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
    $_SESSION['mensaje'] = 'ID de cliente no válido.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

$id_cliente = $_GET['id'];
$id_usuario = $_SESSION['usuario_id'];

// Obtener datos del cliente antes de eliminar
try {
    $stmt = $conex->prepare("SELECT * FROM clientes WHERE id_cliente = ? AND activo = 1");
    $stmt->execute([$id_cliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$cliente) {
        $_SESSION['mensaje'] = 'Cliente no encontrado o ya eliminado.';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al obtener datos del cliente: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Realizar eliminación lógica (soft delete)
try {
    // Verificar si existe la columna fecha_eliminacion
    $column_check = $conex->query("SHOW COLUMNS FROM clientes LIKE 'fecha_eliminacion'");
    $has_fecha_eliminacion = $column_check->rowCount() > 0;
    
    if ($has_fecha_eliminacion) {
        // Usar el trigger existente con fecha_eliminacion
        $stmt = $conex->prepare("CALL SetUsuarioActual(?)");
        $stmt->execute([$id_usuario]);
        
        $stmt = $conex->prepare("UPDATE clientes SET activo = 0, fecha_eliminacion = NOW() WHERE id_cliente = ?");
        $stmt->execute([$id_cliente]);
        
        $stmt = $conex->prepare("CALL LimpiarUsuarioActual()");
        $stmt->execute();
    } else {
        // Alternativa sin fecha_eliminacion
        $stmt = $conex->prepare("CALL SetUsuarioActual(?)");
        $stmt->execute([$id_usuario]);
        
        $stmt = $conex->prepare("UPDATE clientes SET activo = 0 WHERE id_cliente = ?");
        $stmt->execute([$id_cliente]);
        
        $stmt = $conex->prepare("CALL LimpiarUsuarioActual()");
        $stmt->execute();
    }
    
    $_SESSION['mensaje'] = 'Cliente "' . $cliente['nombre_cliente'] . '" movido a la papelera correctamente.';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al eliminar el cliente: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>