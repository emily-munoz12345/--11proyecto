<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

if (!isAdmin()) {
    header('Location: ../dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$accion = $_POST['accion'] ?? '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

$nombre = trim($_POST['nombre'] ?? '');
$username = trim($_POST['username'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$rol = intval($_POST['rol'] ?? 0);
$estado = trim($_POST['estado'] ?? '');

$errores = [];

// Validaciones comunes
if (empty($nombre)) $errores[] = 'El nombre es obligatorio';
if (empty($username)) $errores[] = 'El nombre de usuario es obligatorio';
if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = 'Correo electrónico inválido';
if (empty($telefono)) $errores[] = 'El teléfono es obligatorio';
if (empty($rol) || $rol <= 0) $errores[] = 'Debe seleccionar un rol válido';
if (empty($estado)) $errores[] = 'Debe seleccionar un estado';

// Validaciones específicas para creación
if ($accion === 'crear') {
    if (empty($password)) {
        $errores[] = 'La contraseña es obligatoria';
    } elseif (strlen($password) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres';
    } elseif ($password !== $confirm_password) {
        $errores[] = 'Las contraseñas no coinciden';
    }
}

// Validaciones específicas para edición (si se cambia la contraseña)
if ($accion === 'editar' && !empty($password)) {
    if (strlen($password) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres';
    } elseif ($password !== $confirm_password) {
        $errores[] = 'Las contraseñas no coinciden';
    }
}

if (!empty($errores)) {
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id") . 
          '?error=' . urlencode(implode(', ', $errores)));
    exit;
}

try {
    // Verificar si el username o correo ya existen (excepto para el mismo usuario en edición)
    $sqlVerificar = "SELECT id_usuario FROM usuarios WHERE (username_usuario = ? OR correo_usuario = ?)";
    $paramsVerificar = [$username, $correo];
    
    if ($accion === 'editar') {
        $sqlVerificar .= " AND id_usuario != ?";
        $paramsVerificar[] = $id;
    }
    
    $stmt = $conex->prepare($sqlVerificar);
    $stmt->execute($paramsVerificar);
    
    if ($stmt->fetch()) {
        throw new Exception('El nombre de usuario o correo electrónico ya están en uso');
    }

    if ($accion === 'crear') {
        // Hash de la contraseña
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (id_rol, username_usuario, contrasena_usuario, nombre_completo, 
                correo_usuario, telefono_ususario, activo_usuario, fecha_creacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conex->prepare($sql);
        $stmt->execute([$rol, $username, $passwordHash, $nombre, $correo, $telefono, $estado]);
        
        $mensaje = 'Usuario creado exitosamente';
    } elseif ($accion === 'editar') {
        // Preparar consulta de actualización
        $sql = "UPDATE usuarios SET 
                id_rol = ?, 
                username_usuario = ?, 
                nombre_completo = ?, 
                correo_usuario = ?, 
                telefono_ususario = ?, 
                activo_usuario = ?";
        
        $params = [$rol, $username, $nombre, $correo, $telefono, $estado];
        
        // Si se proporcionó nueva contraseña
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", contrasena_usuario = ?";
            $params[] = $passwordHash;
        }
        
        $sql .= " WHERE id_usuario = ?";
        $params[] = $id;
        
        $stmt = $conex->prepare($sql);
        $stmt->execute($params);
        
        $mensaje = 'Usuario actualizado exitosamente';
    }
    
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = 'success';
    header("Location: index.php");
} catch (Exception $e) {
    $error = $e->getMessage();
    $_SESSION['mensaje'] = $error;
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
}