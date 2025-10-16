<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos (solo Admin puede gestionar usuarios)
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

// Obtener datos básicos
$accion = $_POST['accion'] ?? '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Validar acción
if (!in_array($accion, ['crear', 'editar'])) {
    $_SESSION['mensaje'] = 'Acción no válida';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Para edición, validar ID
if ($accion === 'editar' && $id <= 0) {
    $_SESSION['mensaje'] = 'ID de usuario no válido';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Obtener datos del formulario
$nombre_completo = trim($_POST['nombre_completo'] ?? '');
$username_usuario = trim($_POST['username_usuario'] ?? '');
$correo_usuario = trim($_POST['correo_usuario'] ?? '');
$telefono_usuario = trim($_POST['telefono_usuario'] ?? '');
$id_rol = intval($_POST['id_rol'] ?? 0);
$activo = isset($_POST['activo']) ? 1 : 0;

// Para creación, obtener contraseña
if ($accion === 'crear') {
    $contrasena_usuario = $_POST['contrasena_usuario'] ?? '';
    $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';
}

// Validaciones básicas
$errores = [];

if (empty($nombre_completo)) {
    $errores[] = 'El nombre completo es obligatorio';
}

if (empty($username_usuario)) {
    $errores[] = 'El nombre de usuario es obligatorio';
} elseif (strlen($username_usuario) < 3) {
    $errores[] = 'El nombre de usuario debe tener al menos 3 caracteres';
}

if (empty($correo_usuario)) {
    $errores[] = 'El correo electrónico es obligatorio';
} elseif (!filter_var($correo_usuario, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'El formato del correo electrónico no es válido';
}

if (empty($telefono_usuario)) {
    $errores[] = 'El teléfono es obligatorio';
}

if ($id_rol <= 0) {
    $errores[] = 'Debe seleccionar un rol válido';
}

// Validaciones específicas para creación
if ($accion === 'crear') {
    if (empty($contrasena_usuario)) {
        $errores[] = 'La contraseña es obligatoria';
    } elseif (strlen($contrasena_usuario) < 6) {
        $errores[] = 'La contraseña debe tener al menos 6 caracteres';
    } elseif ($contrasena_usuario !== $confirmar_contrasena) {
        $errores[] = 'Las contraseñas no coinciden';
    }
}

// Si hay errores, redirigir con mensajes
if (!empty($errores)) {
    $_SESSION['mensaje'] = implode('<br>', $errores);
    $_SESSION['tipo_mensaje'] = 'danger';
    
    if ($accion === 'crear') {
        header('Location: crear.php');
    } else {
        header("Location: editar.php?id=$id");
    }
    exit;
}

try {
    // Verificar que el rol existe
    $stmt = $conex->prepare("SELECT id_rol FROM roles WHERE id_rol = ? AND activo = 1");
    $stmt->execute([$id_rol]);
    
    if (!$stmt->fetch()) {
        throw new Exception('El rol seleccionado no es válido');
    }

    // Verificar unicidad del username y correo
    if ($accion === 'crear') {
        // Verificar si el username ya existe
        $stmt = $conex->prepare("SELECT id_usuario FROM usuarios WHERE username_usuario = ?");
        $stmt->execute([$username_usuario]);
        if ($stmt->fetch()) {
            throw new Exception('El nombre de usuario ya está en uso');
        }

        // Verificar si el correo ya existe
        $stmt = $conex->prepare("SELECT id_usuario FROM usuarios WHERE correo_usuario = ?");
        $stmt->execute([$correo_usuario]);
        if ($stmt->fetch()) {
            throw new Exception('El correo electrónico ya está registrado');
        }
    } else {
        // Para edición, verificar que el usuario existe
        $stmt = $conex->prepare("SELECT id_usuario FROM usuarios WHERE id_usuario = ?");
        $stmt->execute([$id]);
        
        if (!$stmt->fetch()) {
            throw new Exception('Usuario no encontrado');
        }

        // Verificar si el username ya existe (excluyendo el usuario actual)
        $stmt = $conex->prepare("SELECT id_usuario FROM usuarios WHERE username_usuario = ? AND id_usuario != ?");
        $stmt->execute([$username_usuario, $id]);
        if ($stmt->fetch()) {
            throw new Exception('El nombre de usuario ya está en uso por otro usuario');
        }

        // Verificar si el correo ya existe (excluyendo el usuario actual)
        $stmt = $conex->prepare("SELECT id_usuario FROM usuarios WHERE correo_usuario = ? AND id_usuario != ?");
        $stmt->execute([$correo_usuario, $id]);
        if ($stmt->fetch()) {
            throw new Exception('El correo electrónico ya está registrado por otro usuario');
        }
    }

    // Procesar según la acción
    if ($accion === 'crear') {
        // Hash de la contraseña
        $contrasena_hash = password_hash($contrasena_usuario, PASSWORD_DEFAULT);
        
        // Insertar nuevo usuario
        $sql = "INSERT INTO usuarios (id_rol, username_usuario, contrasena_usuario, nombre_completo, correo_usuario, telefono_usuario, activo, ultima_actividad) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conex->prepare($sql);
        $resultado = $stmt->execute([
            $id_rol,
            $username_usuario,
            $contrasena_hash,
            $nombre_completo,
            $correo_usuario,
            $telefono_usuario,
            $activo
        ]);

        if ($resultado) {
            $_SESSION['mensaje'] = 'Usuario creado exitosamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            throw new Exception('No se pudo crear el usuario');
        }
        
    } else {
        // Actualizar usuario existente
        $sql = "UPDATE usuarios SET 
                id_rol = ?, 
                username_usuario = ?, 
                nombre_completo = ?, 
                correo_usuario = ?, 
                telefono_usuario = ?, 
                activo = ? 
                WHERE id_usuario = ?";
        
        $stmt = $conex->prepare($sql);
        $resultado = $stmt->execute([
            $id_rol,
            $username_usuario,
            $nombre_completo,
            $correo_usuario,
            $telefono_usuario,
            $activo,
            $id
        ]);

        if ($resultado) {
            $_SESSION['mensaje'] = 'Usuario actualizado exitosamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            throw new Exception('No se pudieron guardar los cambios');
        }
    }
    
    // Redirigir a la lista de usuarios
    header('Location: index.php');
    exit;
    
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error en la base de datos: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    
    if ($accion === 'crear') {
        header('Location: crear.php');
    } else {
        header("Location: editar.php?id=$id");
    }
    exit;
} catch (Exception $e) {
    $_SESSION['mensaje'] = $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    
    if ($accion === 'crear') {
        header('Location: crear.php');
    } else {
        header("Location: editar.php?id=$id");
    }
    exit;
}