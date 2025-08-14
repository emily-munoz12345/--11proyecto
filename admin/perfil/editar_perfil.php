<?php
require_once __DIR__ . '/../../php/auth.php';
require_once __DIR__ . '/../../php/conexion.php';
requireAuth();

$usuario_id = getUserId();
$error = '';
$success = '';

try {
    // Obtener información actual del usuario
    $stmt = $conex->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        die("Usuario no encontrado");
    }

    // Procesar formulario de actualización
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre_completo = trim($_POST['nombre_completo']);
        $correo = trim($_POST['correo']);
        $telefono = trim($_POST['telefono']);

        // Validaciones básicas
        if (empty($nombre_completo) ){
            $error = 'El nombre completo es requerido';
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $error = 'El correo electrónico no es válido';
        } else {
            // Actualizar en la base de datos
            $stmt = $conex->prepare("UPDATE usuarios SET nombre_completo = ?, correo_usuario = ?, telefono_usuario = ? WHERE id_usuario = ?");
            $stmt->execute([$nombre_completo, $correo, $telefono, $usuario_id]);

            // Actualizar datos en sesión
            $_SESSION['usuario_nombre'] = $nombre_completo;

            $success = 'Perfil actualizado correctamente';
            
            // Refrescar datos del usuario
            $stmt = $conex->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
            $stmt->execute([$usuario_id]);
            $usuario = $stmt->fetch();
        }
    }
} catch (PDOException $e) {
    $error = "Error al actualizar el perfil: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="admin-body">
    <?php include '../includes/head.php'; ?>
            <?php include __DIR__ . '../../includes/sidebar.php'; ?>

    <div class="content-wrapper">
        <main class="profile-container">
            <div class="edit-form-container">
                <h1>Editar Perfil</h1>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="username">Nombre de usuario</label>
                        <input type="text" id="username" value="<?= htmlspecialchars($usuario['username_usuario']) ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="nombre_completo">Nombre completo *</label>
                        <input type="text" id="nombre_completo" name="nombre_completo" value="<?= htmlspecialchars($usuario['nombre_completo']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="correo">Correo electrónico *</label>
                        <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($usuario['correo_usuario']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($usuario['telefono_usuario']) ?>">
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Guardar cambios
                    </button>
                    
                    <a href="perfil.php" class="btn btn-secondary" style="margin-left: 1rem;">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                </form>
            </div>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>