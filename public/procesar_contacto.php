<?php
require_once __DIR__ . '../../php/conexion.php';

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar los datos del formulario
    $nombre = filter_input(INPUT_POST, 'nombre_completo', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $correo = filter_input(INPUT_POST, 'correo_electronico', FILTER_SANITIZE_EMAIL);
    $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $asunto = filter_input(INPUT_POST, 'asunto', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $mensaje = filter_input(INPUT_POST, 'mensaje', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validar los datos requeridos
    if (empty($nombre) || empty($correo) || empty($asunto) || empty($mensaje)) {
        header('Location: contacto.php?status=error&message=' . urlencode('Por favor complete todos los campos requeridos.'));
        exit();
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header('Location: contacto.php?status=error&message=' . urlencode('El correo electrónico no es válido.'));
        exit();
    }

    try {
        // Preparar la consulta SQL
        $stmt = $conex->prepare("INSERT INTO mensajes_contacto 
                                (nombre_completo, correo_electronico, telefono, asunto, mensaje, fecha_envio, leido) 
                                VALUES (:nombre, :correo, :telefono, :asunto, :mensaje, NOW(), 0)");
        
        // Bind de parámetros
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':asunto', $asunto);
        $stmt->bindParam(':mensaje', $mensaje);
        
        // Ejecutar la consulta
        $stmt->execute();

        // Redirigir al usuario con un mensaje de éxito
        header('Location: contacto.php?status=success');
        exit();
    } catch (PDOException $e) {
        // Manejar errores de base de datos
        error_log('Error al guardar mensaje de contacto: ' . $e->getMessage());
        header('Location: contacto.php?status=error&message=' . urlencode('Error al enviar el mensaje. Por favor inténtelo más tarde.'));
        exit();
    }
} else {
    // Si no es POST, redirigir al formulario
    header('Location: contacto.php');
    exit();
}