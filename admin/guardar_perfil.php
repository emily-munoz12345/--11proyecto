<?php
include 'conexion.php';
session_start();

$id_usuario = $_SESSION['id_usuario'];
$nombre_completo = $_POST['nombre_completo'];
$correo_usuario = $_POST['correo_usuario'];
$telefono_usuario = $_POST['telefono_usuario'];
$contrasena_actual = $_POST['contrasena_actual'];
$nueva_contrasena = $_POST['nueva_contrasena'];
$confirmar_contrasena = $_POST['confirmar_contrasena'];

// Verificar la contraseña actual y actualizar la información
$stmt = $conex->prepare("SELECT contrasena_usuario FROM usuarios WHERE id_usuario = :id_usuario");
$stmt->bindParam(':id_usuario', $id_usuario);
$stmt->execute();
$result = $stmt->fetch();

if (password_verify($contrasena_actual, $result['contrasena_usuario'])) {
    // Actualizar datos del usuario
    $stmt = $conex->prepare("UPDATE usuarios SET nombre_completo = :nombre, correo_usuario = :correo, telefono_usuario = :telefono WHERE id_usuario = :id_usuario");
    $stmt->bindParam(':nombre', $nombre_completo);
    $stmt->bindParam(':correo', $correo_usuario);
    $stmt->bindParam(':telefono', $telefono_usuario);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();

    // Actualizar contraseña si se proporciona
    if (!empty($nueva_contrasena) && $nueva_contrasena === $confirmar_contrasena) {
        $hashed_password = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
        $stmt = $conex->prepare("UPDATE usuarios SET contrasena_usuario = :contrasena WHERE id_usuario = :id_usuario");
        $stmt->bindParam(':contrasena', $hashed_password);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
    }

    echo "Cambios guardados exitosamente.";
} else {
    echo "La contraseña actual es incorrecta.";
}
?>