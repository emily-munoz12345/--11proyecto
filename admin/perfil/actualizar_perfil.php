<?php
require_once 'auth.php';
require_once 'conexion.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: perfil.php");
    exit;
}

$usuario_id = getUserId();

// Obtener datos actuales del usuario
$stmt = $conex->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$usuario_id]);
$usuario_actual = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario_actual) {
    $_SESSION['error'] = "Usuario no encontrado";
    header("Location: perfil.php");
    exit;
}

// Recoger y validar datos del formulario
$nombre_completo = trim($_POST['nombre_completo']);
$correo_usuario = trim($_POST['correo_usuario']);
$telefono_usuario = trim($_POST['telefono_usuario']);
$username_usuario = trim($_POST['username_usuario']);
$contrasena_actual = $_POST['contrasena_actual'];
$nueva_contrasena = $_POST['nueva_contrasena'];

// Validaciones básicas
if (empty($nombre_completo) || empty($correo_usuario) || empty($telefono_usuario) || empty($username_usuario)) {
    $_SESSION['error'] = "Todos los campos obligatorios deben ser completados";
    header("Location: perfil.php");
    exit;
}

// Verificar si el correo ya existe (excluyendo el usuario actual)
$stmt = $conex->prepare("SELECT id_usuario FROM usuarios WHERE correo_usuario = ? AND id_usuario != ?");
$stmt->execute([$correo_usuario, $usuario_id]);
if ($stmt->fetch()) {
    $_SESSION['error'] = "El correo electrónico ya está en uso por otro usuario";
    header("Location: perfil.php");
    exit;
}

// Verificar si el nombre de usuario ya existe (excluyendo el usuario actual)
$stmt = $conex->prepare("SELECT id_usuario FROM usuarios WHERE username_usuario = ? AND id_usuario != ?");
$stmt->execute([$username_usuario, $usuario_id]);
if ($stmt->fetch()) {
    $_SESSION['error'] = "El nombre de usuario ya está en uso";
    header("Location: perfil.php");
    exit;
}

// Manejar cambio de contraseña si se proporcionó
if (!empty($nueva_contrasena)) {
    if (empty($contrasena_actual)) {
        $_SESSION['error'] = "Debe ingresar la contraseña actual para cambiarla";
        header("Location: perfil.php");
        exit;
    }
    
    // Verificar contraseña actual
    if (!password_verify($contrasena_actual, $usuario_actual['contrasena_usuario'])) {
        $_SESSION['error'] = "La contraseña actual es incorrecta";
        header("Location: perfil.php");
        exit;
    }
    
    // Validar nueva contraseña
    if (strlen($nueva_contrasena) < 6) {
        $_SESSION['error'] = "La nueva contraseña debe tener al menos 6 caracteres";
        header("Location: perfil.php");
        exit;
    }
    
    $contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
    $actualizar_contrasena = true;
} else {
    $actualizar_contrasena = false;
}

// Preparar la consulta de actualización
if ($actualizar_contrasena) {
    $sql = "UPDATE usuarios SET 
            nombre_completo = ?, 
            correo_usuario = ?, 
            telefono_usuario = ?, 
            username_usuario = ?, 
            contrasena_usuario = ?,
            ultima_actividad = NOW()
            WHERE id_usuario = ?";
    $params = [$nombre_completo, $correo_usuario, $telefono_usuario, $username_usuario, $contrasena_hash, $usuario_id];
} else {
    $sql = "UPDATE usuarios SET 
            nombre_completo = ?, 
            correo_usuario = ?, 
            telefono_usuario = ?, 
            username_usuario = ?,
            ultima_actividad = NOW()
            WHERE id_usuario = ?";
    $params = [$nombre_completo, $correo_usuario, $telefono_usuario, $username_usuario, $usuario_id];
}

try {
    $stmt = $conex->prepare($sql);
    $stmt->execute($params);
    
    // Actualizar datos en la sesión
    $_SESSION['usuario_nombre'] = $nombre_completo;
    $_SESSION['usuario_correo'] = $correo_usuario;
    
    $_SESSION['success'] = "Perfil actualizado correctamente";
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al actualizar el perfil: " . $e->getMessage();
}

header("Location: perfil.php");
exit;
?>