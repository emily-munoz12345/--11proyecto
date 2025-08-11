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
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/perfil.css">
    <style>
        .edit-form-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--neutral-light);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: var(--border-soft);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-dark);
            font-weight: 500;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="tel"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--neutral-medium);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition-fast);
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(94, 48, 35, 0.1);
        }
        
        .btn-submit {
            background: var(--gradient-gold);
            color: var(--primary-dark);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-normal);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-submit:hover {
            background: linear-gradient(135deg, var(--gold-pastel), var(--gold-cream));
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }
        
        .alert {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }
        
        .alert-error {
            background-color: rgba(196, 90, 77, 0.2);
            border: 1px solid var(--error-color);
            color: var(--error-color);
        }
        
        .alert-success {
            background-color: rgba(138, 155, 110, 0.2);
            border: 1px solid var(--success-color);
            color: var(--success-color);
        }
    </style>
</head>
<body class="admin-body">
    <?php include '../includes/head.php'; ?>

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