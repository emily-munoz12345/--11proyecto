<?php
require_once __DIR__ . '/../../php/auth.php';
require_once __DIR__ . '/../../php/conexion.php';
requireAuth();
// Solo administradores pueden crear usuarios
if (!isAdmin()) {
    $_SESSION['error'] = "No tienes permiso para realizar esta acción";
    header("Location: usuarios.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $nombre_completo = trim($_POST['nombre_completo']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $id_rol = intval($_POST['id_rol']);
    $activo = $_POST['activo'] === 'Activo' ? 'Activo' : 'Inactivo';
    $contrasena = $_POST['contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];

    // Validaciones
    $errores = [];

    if (empty($username) || empty($nombre_completo) || empty($correo) || empty($telefono)) {
        $errores[] = "Todos los campos son obligatorios";
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido";
    }

    if (strlen($contrasena) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres";
    }

    if ($contrasena !== $confirmar_contrasena) {
        $errores[] = "Las contraseñas no coinciden";
    }

    // Verificar si el username o correo ya existen
    try {
        $stmt = $conex->prepare("SELECT id_usuario FROM usuarios WHERE username_usuario = ? OR correo_usuario = ?");
        $stmt->execute([$username, $correo]);
        if ($stmt->fetch()) {
            $errores[] = "El nombre de usuario o correo electrónico ya está en uso";
        }
    } catch (PDOException $e) {
        $errores[] = "Error al verificar usuario existente: " . $e->getMessage();
    }

    if (empty($errores)) {
        // Hash de la contraseña
        $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

        try {
            $stmt = $conex->prepare("INSERT INTO usuarios (id_rol, username_usuario, contrasena_usuario, nombre_completo, correo_usuario, telefono_usuario, activo_usuario) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id_rol, $username, $hashed_password, $nombre_completo, $correo, $telefono, $activo]);

            $_SESSION['success'] = "Usuario creado exitosamente";
            header("Location: usuarios.php");
            exit;
        } catch (PDOException $e) {
            $errores[] = "Error al guardar el usuario: " . $e->getMessage();
        }
    }

    if (!empty($errores)) {
        $_SESSION['error'] = implode("<br>", $errores);
        $_SESSION['form_data'] = $_POST;
        header("Location: nuevo_usuario.php");
        exit;
    }
} else {
    header("Location: nuevo_usuario.php");
    exit;
}