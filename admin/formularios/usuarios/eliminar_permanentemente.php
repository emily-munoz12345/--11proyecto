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
    $_SESSION['mensaje'] = 'No tienes permisos para eliminar usuarios.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Verificar si se proporcionó un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje'] = 'ID de usuario no válido.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

$id_usuario = $_GET['id'];
$id_usuario_actual = $_SESSION['usuario_id'];

// No permitir auto-eliminación
if ($id_usuario == $id_usuario_actual) {
    $_SESSION['mensaje'] = 'No puedes eliminar tu propio usuario.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Verificar que el usuario existe
try {
    $stmt = $conex->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        $_SESSION['mensaje'] = 'Usuario no encontrado.';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al verificar el usuario: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Eliminar el usuario directamente
try {
    $conex->beginTransaction();
    
    // Guardar registro manual de eliminación
    $stmt = $conex->prepare("
        INSERT INTO registro_eliminaciones 
        (tabla, id_registro, eliminado_por, accion, datos, datos_anteriores) 
        VALUES ('usuarios', ?, ?, 'ELIMINACION_DIRECTA', ?, ?)
    ");
    
    $datos_eliminados = "Usuario eliminado: " . $usuario['nombre_completo'];
    $datos_completos = json_encode([
        'id_usuario' => $usuario['id_usuario'],
        'username_usuario' => $usuario['username_usuario'],
        'nombre_completo' => $usuario['nombre_completo'],
        'correo_usuario' => $usuario['correo_usuario'],
        'telefono_usuario' => $usuario['telefono_usuario'],
        'id_rol' => $usuario['id_rol'],
        'fecha_creacion' => $usuario['fecha_creacion'],
        'ultima_actividad' => $usuario['ultima_actividad']
    ]);
    
    $stmt->execute([$id_usuario, $id_usuario_actual, $datos_eliminados, $datos_completos]);
    
    // Eliminar físicamente el registro del usuario
    $stmt = $conex->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    
    $conex->commit();
    
    $_SESSION['mensaje'] = 'Usuario "' . $usuario['nombre_completo'] . '" eliminado permanentemente del sistema.';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (PDOException $e) {
    $conex->rollBack();
    $_SESSION['mensaje'] = 'Error al eliminar el usuario: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>