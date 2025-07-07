<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión para mensajes flash
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

// Verificar permisos
if (!isAdmin()) {
    $_SESSION['mensaje'] = 'No tienes permisos para esta acción';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ../dashboard.php');
    exit;
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['mensaje'] = 'Método no permitido';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Obtener y limpiar datos
$accion = $_POST['accion'] ?? '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

$nombre = trim($_POST['nombre'] ?? '');
$username = trim($_POST['username'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$rol = intval($_POST['rol'] ?? 0);
$estado = in_array($_POST['estado'] ?? '', ['Activo', 'Inactivo']) ? $_POST['estado'] : '';

// Validaciones
$errores = [];

// Validaciones comunes
if (empty($nombre)) $errores[] = 'El nombre es obligatorio';
if (empty($username)) $errores[] = 'El nombre de usuario es obligatorio';
if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = 'Correo electrónico inválido';
if (empty($telefono)) $errores[] = 'El teléfono es obligatorio';
if (empty($rol) || $rol <= 0) $errores[] = 'Debe seleccionar un rol válido';
if (empty($estado)) $errores[] = 'Debe seleccionar un estado válido';

// Validaciones de contraseña
if ($accion === 'crear') {
    if (empty($password)) {
        $errores[] = 'La contraseña es obligatoria';
    } elseif (strlen($password) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres';
    } elseif ($password !== $confirm_password) {
        $errores[] = 'Las contraseñas no coinciden';
    }
} elseif ($accion === 'editar' && !empty($password)) {
    if (strlen($password) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres';
    } elseif ($password !== $confirm_password) {
        $errores[] = 'Las contraseñas no coinciden';
    }
}

// Manejar errores
if (!empty($errores)) {
    $_SESSION['mensaje'] = implode('<br>', $errores);
    $_SESSION['tipo_mensaje'] = 'danger';
    $_SESSION['old_input'] = $_POST; // Guardar datos para repoblar formulario
    
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
    exit;
}

try {
    // Verificar unicidad de username y correo (excepto para el mismo usuario en edición)
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

    // Procesar según acción
    if ($accion === 'crear') {
        // MODIFICACIÓN: Almacenar contraseña en texto plano (NO SEGURO)
        $sql = "INSERT INTO usuarios (id_rol, username_usuario, contrasena_usuario, nombre_completo, 
                correo_usuario, telefono_usuario, activo_usuario, fecha_creacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conex->prepare($sql);
        $stmt->execute([$rol, $username, $password, $nombre, $correo, $telefono, $estado]);
        
        $mensaje = 'Usuario creado exitosamente';
    } elseif ($accion === 'editar') {
        $sql = "UPDATE usuarios SET 
                id_rol = ?, 
                username_usuario = ?, 
                nombre_completo = ?, 
                correo_usuario = ?, 
                telefono_usuario = ?, 
                activo_usuario = ?";
        
        $params = [$rol, $username, $nombre, $correo, $telefono, $estado];
        
        if (!empty($password)) {
            // MODIFICACIÓN: Actualizar contraseña en texto plano (NO SEGURO)
            $sql .= ", contrasena_usuario = ?";
            $params[] = $password;
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
    exit;
    
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error en la base de datos: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    $_SESSION['old_input'] = $_POST;
    
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
    exit;
} catch (Exception $e) {
    $_SESSION['mensaje'] = $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    $_SESSION['old_input'] = $_POST;
    
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
    exit;
}