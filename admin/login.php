<?php
require_once __DIR__ . '/../php/auth.php';
require_once __DIR__ . '/../php/conexion.php';

redirectIfAuthenticated();

$error = '';
$success = '';
$show_recovery = isset($_GET['recovery']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['recovery_email'])) {
        // Proceso de recuperación de contraseña
        $email = trim($_POST['recovery_email'] ?? '');

        if (empty($email)) {
            $error = 'Por favor ingrese su correo electrónico';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Por favor ingrese un correo electrónico válido';
        } else {
            // Verificar email en base de datos y enviar correo de recuperación
            // (Implementación existente)
        }
    } else {
        // Proceso de login normal
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Por favor ingrese usuario y contraseña';
        } else {
            try {
                $sql = "SELECT u.id_usuario, u.username_usuario, u.contrasena_usuario, 
                               u.nombre_completo, r.nombre_rol, u.activo_usuario
                        FROM usuarios u
                        JOIN roles r ON u.id_rol = r.id_rol
                        WHERE u.username_usuario = ? LIMIT 1";
                $stmt = $conex->prepare($sql);
                $stmt->execute([$username]);

                if ($stmt->rowCount() === 1) {
                    $usuario = $stmt->fetch();

                    if ($usuario['activo_usuario'] !== 'Activo') {
                        $error = 'Cuenta inactiva. Contacte al administrador.';
                    } elseif ($password === $usuario['contrasena_usuario']) {
                        $_SESSION['usuario_id'] = $usuario['id_usuario'];
                        $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];
                        $_SESSION['usuario_rol'] = $usuario['nombre_rol'];
                        $_SESSION['username'] = $usuario['username_usuario'];

                        // Redirección después del login
                        if (isset($_SESSION['redirect_url'])) {
                            $redirect_to = $_SESSION['redirect_url'];
                            unset($_SESSION['redirect_url']);
                            header("Location: " . $redirect_to);
                            exit;
                        } else {
                            header("Location: " . getBaseUrl() . "/admin/dashboard.php");
                            exit;
                        }
                    } else {
                        $error = 'Credenciales incorrectas';
                    }
                } else {
                    $error = 'Credenciales incorrectas';
                }
            } catch (PDOException $e) {
                error_log('Error en login: ' . $e->getMessage());
                $error = 'Error al procesar la solicitud';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
    <title>Login - Sistema de Tapicería</title>

<style>
    /* Fondo con imagen de tapicería */
    body {
        background-image: url('https://cdn.pixabay.com/photo/2015/12/19/10/27/seat-cushion-1099616_1280.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        min-height: 100vh;
        position: relative;
    }

    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: -1;
    }

    /* Efectos para mensajes */
    .alert {
        opacity: 0;
        transform: translateY(-20px);
        transition: all 0.3s ease-out;
    }

    .alert.show {
        opacity: 1;
        transform: translateY(0);
    }

    /* Estilo para el card */
    .login-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 10px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    /* Estilo para el botón de recuperación */
    .recovery-link {
        color: var(--accent-color);
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .recovery-link:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }

    /* Transición para el formulario de recuperación */
    .recovery-form {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }

    .recovery-form.show {
        max-height: 200px;
    }

    /* Mantener otros estilos existentes */
    .ripple-effect {
        position: absolute;
        background: rgba(255, 255, 255, 0.4);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
        width: 20px;
        height: 20px;
        margin-left: -10px;
        margin-top: -10px;
    }

    @keyframes ripple {
        to {
            transform: scale(10);
            opacity: 0;
        }
    }

    button[type="submit"] {
        position: relative;
        overflow: hidden;
        transition: all var(--transition-normal);
        border: none;
    }

    button[type="submit"]:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .form-control:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 0.25rem rgba(181, 113, 87, 0.25);
    }
</style>

</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card shadow-lg border-0 animate__animated animate__fadeIn">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="fas fa-car-alt me-2"></i>
                            <span>Nacional</span>Tapizados
                            <h2 class="fw-bold" style="color: var(--primary-dark);">Iniciar Sesión</h2>
                            <p class="text-muted">Ingresa tus credenciales</p>
                        </div>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger mb-3" id="errorAlert">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="loginForm" class="<?= $show_recovery ? 'd-none' : '' ?>">
                            <div class="mb-3">
                                <label for="username" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        <button type="submit" class="btn w-100 py-2" style="background: var(--gradient-leather); color: white; font-weight: 600;">
                            <span class="login-text">Ingresar</span>
                            <span class="login-icon"><i class="fas fa-sign-in-alt ms-2"></i></span>
                        </button>
                            <div class="text-center mt-3">
                                <a href="?recovery=1" class="text-muted" id="forgotPassword">¿Olvidaste tu contraseña?</a>
                            </div>
                        </form>

                        <form method="POST" id="recoveryForm" class="<?= $show_recovery ? '' : 'd-none' ?>">
                            <div class="mb-3">
                                <label for="recovery_email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="recovery_email" name="recovery_email" required>
                            </div>
                        <button type="submit" class="btn w-100 py-2" style="background: var(--gradient-leather); color: white; font-weight: 600;">
                            <span class="login-text">Enviar enlace de recuperación</span>
                        </button>
                            <button type="button" class="btn btn-outline-secondary w-100 py-2" id="cancelRecovery">
                                Cancelar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script para mostrar/ocultar contraseña y manejar formularios
        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar/ocultar contraseña
            document.getElementById('togglePassword').addEventListener('click', function() {
                const passwordInput = document.getElementById('password');
                const icon = this.querySelector('i');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });

            // Mostrar formulario de recuperación
            document.getElementById('forgotPassword')?.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('loginForm').classList.add('d-none');
                document.getElementById('recoveryForm').classList.remove('d-none');
            });

            // Cancelar recuperación
            document.getElementById('cancelRecovery').addEventListener('click', function() {
                document.getElementById('recoveryForm').classList.add('d-none');
                document.getElementById('loginForm').classList.remove('d-none');
            });
        });
    </script>
</body>
</html>