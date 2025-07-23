<?php
require_once __DIR__ . '/../php/auth.php';
require_once __DIR__ . '/../php/conexion.php';

// Supongamos que el ID del usuario se pasa por la sesión
session_start();
$id_usuario = $_SESSION['id_usuario']; // Asegúrate de que la sesión esté configurada

// Obtener datos del usuario
$stmt = $conex->prepare("SELECT * FROM usuarios WHERE id_usuario = :id_usuario");
$stmt->bindParam(':id_usuario', $id_usuario);
$stmt->execute();
$usuario = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<h1>Perfil de Usuario</h1>

<form action="guardar_perfil.php" method="post">
    <label for="nombre_completo">Nombre Completo:</label>
    <input type="text" id="nombre_completo" name="nombre_completo" value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" required>

    <label for="correo_usuario">Correo Electrónico:</label>
    <input type="email" id="correo_usuario" name="correo_usuario" value="<?php echo htmlspecialchars($usuario['correo_usuario']); ?>" required>

    <label for="telefono_usuario">Teléfono:</label>
    <input type="text" id="telefono_usuario" name="telefono_usuario" value="<?php echo htmlspecialchars($usuario['telefono_usuario']); ?>">

    <h2>Cambiar Contraseña</h2>
    <label for="contrasena_actual">Contraseña Actual:</label>
    <input type="password" id="contrasena_actual" name="contrasena_actual" required>

    <label for="nueva_contrasena">Nueva Contraseña:</label>
    <input type="password" id="nueva_contrasena" name="nueva_contrasena" required>

    <label for="confirmar_contrasena">Confirmar Nueva Contraseña:</label>
    <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required>

    <button type="submit">Guardar Cambios</button>
</form>

</body>
</html>