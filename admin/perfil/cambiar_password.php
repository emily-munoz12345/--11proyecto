<?php
require_once __DIR__ . '/../../php/auth.php';
require_once __DIR__ . '/../../php/conexion.php';
requireAuth();

$usuario_id = getUserId();
$error = '';
$success = '';

try {
    // Obtener información del usuario
    $stmt = $conex->prepare("SELECT username_usuario FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        die("Usuario no encontrado");
    }

    // Procesar formulario de cambio de contraseña
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Validaciones
        if (empty($current_password)) {
            $error = 'La contraseña actual es requerida';
        } elseif (empty($new_password)) {
            $error = 'La nueva contraseña es requerida';
        } elseif (strlen($new_password) < 8) {
            $error = 'La nueva contraseña debe tener al menos 8 caracteres';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Las contraseñas no coinciden';
        } else {
            // Verificar contraseña actual
            $stmt = $conex->prepare("SELECT contrasena_usuario FROM usuarios WHERE id_usuario = ?");
            $stmt->execute([$usuario_id]);
            $db_password = $stmt->fetchColumn();

            if (!password_verify($current_password, $db_password)) {
                $error = 'La contraseña actual es incorrecta';
            } else {
                // Actualizar contraseña
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conex->prepare("UPDATE usuarios SET contrasena_usuario = ? WHERE id_usuario = ?");
                $stmt->execute([$hashed_password, $usuario_id]);

                $success = 'Contraseña actualizada correctamente';
            }
        }
    }
} catch (PDOException $e) {
    $error = "Error al cambiar la contraseña: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="admin-body">
    <?php include '../includes/head.php'; ?>
            <?php include __DIR__ . '../../includes/sidebar.php'; ?>
    <div class="content-wrapper">
        <main class="password-form-container">
            <h1>Cambiar Contraseña</h1>
            
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
                    <label for="current_password">Contraseña actual *</label>
                    <input type="password" id="current_password" name="current_password" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('current_password')"></i>
                </div>
                
                <div class="form-group">
                    <label for="new_password">Nueva contraseña *</label>
                    <input type="password" id="new_password" name="new_password" required oninput="checkPasswordStrength(this.value)">
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('new_password')"></i>
                    <div class="password-strength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="strength-text" id="strengthText"></div>
                    <small>La contraseña debe tener al menos 8 caracteres</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmar nueva contraseña *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password')"></i>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-key"></i> Cambiar contraseña
                </button>
                
                <a href="perfil.php" class="btn btn-secondary" style="margin-left: 1rem;">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </form>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
        
        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            
            // Reset
            strengthBar.className = 'strength-bar';
            strengthText.textContent = '';
            
            if (password.length === 0) return;
            
            // Calcular fortaleza
            let strength = 0;
            
            // Longitud
            if (password.length >= 8) strength += 1;
            if (password.length >= 12) strength += 1;
            
            // Caracteres especiales
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 1;
            
            // Números
            if (/\d/.test(password)) strength += 1;
            
            // Mayúsculas y minúsculas
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 1;
            
            // Aplicar estilos según fortaleza
            if (strength <= 2) {
                strengthBar.className = 'strength-bar strength-weak';
                strengthText.textContent = 'Débil';
                strengthText.style.color = 'var(--error-color)';
            } else if (strength <= 4) {
                strengthBar.className = 'strength-bar strength-medium';
                strengthText.textContent = 'Moderada';
                strengthText.style.color = 'var(--warning-color)';
            } else {
                strengthBar.className = 'strength-bar strength-strong';
                strengthText.textContent = 'Fuerte';
                strengthText.style.color = 'var(--success-color)';
            }
        }
    </script>
</body>
</html>