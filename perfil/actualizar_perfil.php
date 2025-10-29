<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

$usuario_id = getUserId();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: perfil.php');
    exit;
}

$errores = [];

// Recoger datos del formulario
$nombre_completo = trim($_POST['nombre_completo'] ?? '');
$correo_usuario = trim($_POST['correo_usuario'] ?? '');
$telefono_usuario = trim($_POST['telefono_usuario'] ?? '');
$username_usuario = trim($_POST['username_usuario'] ?? '');

// Validaciones básicas
if (empty($nombre_completo) || empty($correo_usuario) || empty($telefono_usuario) || empty($username_usuario)) {
    $errores[] = "Todos los campos obligatorios deben ser completados.";
}

// Validar email
if (!filter_var($correo_usuario, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El formato del correo electrónico no es válido.";
}

// Verificar si el username ya existe (excluyendo el usuario actual)
$stmt = $conex->prepare("SELECT id_usuario FROM usuarios WHERE username_usuario = ? AND id_usuario != ? AND activo = 1");
$stmt->execute([$username_usuario, $usuario_id]);
if ($stmt->fetch()) {
    $errores[] = "El nombre de usuario ya está en uso por otro usuario.";
}

// Verificar si el correo ya existe (excluyendo el usuario actual)
$stmt = $conex->prepare("SELECT id_usuario FROM usuarios WHERE correo_usuario = ? AND id_usuario != ? AND activo = 1");
$stmt->execute([$correo_usuario, $usuario_id]);
if ($stmt->fetch()) {
    $errores[] = "El correo electrónico ya está registrado por otro usuario.";
}

// Si hay errores, redirigir de vuelta al perfil
if (!empty($errores)) {
    $_SESSION['errores_perfil'] = $errores;
    header('Location: perfil.php');
    exit;
}

// Actualizar datos del usuario
try {
    $conex->beginTransaction();
    
    // Actualizar datos personales
    $stmt = $conex->prepare("UPDATE usuarios SET nombre_completo = ?, correo_usuario = ?, telefono_usuario = ?, username_usuario = ?, ultima_actividad = NOW() WHERE id_usuario = ?");
    $stmt->execute([$nombre_completo, $correo_usuario, $telefono_usuario, $username_usuario, $usuario_id]);
    
    $conex->commit();
    
    // Actualizar datos en la sesión
    $_SESSION['usuario_nombre'] = $nombre_completo;
    $_SESSION['usuario_correo'] = $correo_usuario;
    
    $_SESSION['mensaje_exito'] = "Perfil actualizado correctamente.";
    
} catch (Exception $e) {
    $conex->rollBack();
    $_SESSION['errores_perfil'] = ["Error al actualizar el perfil: " . $e->getMessage()];
}

header('Location: perfil.php');
exit;
?>