<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../php/conexion.php';
require_once __DIR__ . '/../php/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si ya está autenticado
if (isset($_SESSION['usuario_id'])) {
    header('Location: ' . getBaseUrl() . '/admin/dashboard.php');
    exit;
}

$error = '';
$success = '';
$show_recovery = isset($_GET['recovery']) ? true : false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Proceso de recuperación de contraseña
    if (isset($_POST['recovery_email'])) {
        $email = trim($_POST['recovery_email'] ?? '');
        
        if (empty($email)) {
            $error = 'Por favor ingrese su correo electrónico';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Por favor ingrese un correo electrónico válido';
        } else {
            try {
                // Verificar si el email existe en la base de datos
                $sql = "SELECT id_usuario, nombre_completo, username_usuario FROM usuarios WHERE correo_usuario = ? LIMIT 1";
                $stmt = $conex->prepare($sql);
                $stmt->execute([$email]);
                
                if ($stmt->rowCount() === 1) {
                    $usuario = $stmt->fetch();
                    $token = bin2hex(random_bytes(32));
                    $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Guardar token en la base de datos
                    $update_sql = "UPDATE usuarios SET token_recuperacion = ?, token_expiracion = ? WHERE id_usuario = ?";
                    $update_stmt = $conex->prepare($update_sql);
                    $update_stmt->execute([$token, $expira, $usuario['id_usuario']]);
                    
                    // Enviar correo electrónico (simulado en desarrollo)
                    $reset_link = getBaseUrl() . "/reset_password.php?token=$token";
                    $asunto = "Recuperación de contraseña - Nacional Tapizados";
                    $mensaje = "Hola {$usuario['nombre_completo']},\n\n";
                    $mensaje .= "Hemos recibido una solicitud para restablecer tu contraseña.\n";
                    $mensaje .= "Por favor haz clic en el siguiente enlace para continuar:\n";
                    $mensaje .= "$reset_link\n\n";
                    $mensaje .= "Si no solicitaste este cambio, ignora este mensaje.\n";
                    $mensaje .= "Este enlace expirará en 1 hora.\n\n";
                    $mensaje .= "Atentamente,\nEl equipo de Nacional Tapizados";
                    
                    // Para desarrollo: Mostrar el enlace en pantalla
                    $success = 'Se ha generado el enlace de recuperación (en producción se enviaría por email):<br>';
                    $success .= '<a href="'.$reset_link.'">'.$reset_link.'</a>';
                    
                    $show_recovery = false;
                } else {
                    $error = 'No se encontró una cuenta asociada a este correo';
                }
            } catch (PDOException $e) {
                error_log('Error en recuperación: ' . $e->getMessage());
                $error = 'Error al procesar la solicitud. Por favor intente más tarde.';
            }
        }
    } 
    // Proceso de login normal
    else {
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
                    
                    // Verificar estado del usuario
                    if ($usuario['activo_usuario'] !== 'Activo') {
                        $error = 'Cuenta inactiva. Contacte al administrador.';
                    }
                    // Verificar contraseña (sin hash)
                    elseif ($password === $usuario['contrasena_usuario']) {
                        // Iniciar sesión
                        $_SESSION['usuario_id'] = $usuario['id_usuario'];
                        $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];
                        $_SESSION['usuario_rol'] = $usuario['nombre_rol'];
                        $_SESSION['username'] = $usuario['username_usuario'];
                        
                        // Actualizar última actividad
                        $update_sql = "UPDATE usuarios SET ultima_actividad = NOW() WHERE id_usuario = ?";
                        $update_stmt = $conex->prepare($update_sql);
                        $update_stmt->execute([$usuario['id_usuario']]);
                        
                        session_regenerate_id(true);
                        
                        // Redirigir según rol
                        header('Location: ' . getBaseUrl() . '/admin/dashboard.php');
                        exit;
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
require_once __DIR__ . '/includes/head.php';
$title = 'Gestión de Trabajos | Nacional Tapizados';
?>

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

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card login-card shadow-lg border-0 animate__animated animate__fadeIn">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="<?= getBaseUrl() ?>/assets/img/logo.png" alt="Nacional Tapizados" style="height: 60px; margin-bottom: 1rem;">
                        <h2 class="fw-bold" style="color: var(--primary-dark);">Iniciar Sesión</h2>
                        <p class="text-muted">Ingresa tus credenciales</p>
                    </div>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger mb-3" id="errorAlert" style="background-color: var(--error-color); border-color: var(--error-color); color: white;">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success mb-3" id="successAlert">
                            <?= $success ?> <!-- No usar htmlspecialchars aquí porque puede contener HTML seguro -->
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="loginForm" class="<?= $show_recovery ? 'd-none' : '' ?>">
                        <div class="mb-3">
                            <label for="username" class="form-label" style="color: var(--primary-dark);">Usuario</label>
                            <input type="text" class="form-control" id="username" name="username" required style="border-color: var(--neutral-dark);" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label" style="color: var(--primary-dark);">Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required style="border-color: var(--neutral-dark);">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-color: var(--neutral-dark); color: var(--accent-color);">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn w-100 py-2" style="background: var(--gradient-leather); color: white; font-weight: 600;">
                            <span class="login-text">Ingresar</span>
                            <span class="login-icon"><i class="fas fa-sign-in-alt ms-2"></i></span>
                        </button>
                        
                        <div class="text-center mt-3">
                            <a href="?recovery=1" class="recovery-link" id="forgotPassword">¿Olvidaste tu contraseña?</a>
                        </div>
                    </form>
                    
                    <form method="POST" id="recoveryForm" class="recovery-form <?= $show_recovery ? 'show' : '' ?>">
                        <div class="mb-3">
                            <label for="recovery_email" class="form-label" style="color: var(--primary-dark);">Correo electrónico</label>
                            <input type="email" class="form-control" id="recovery_email" name="recovery_email" required value="<?= isset($_POST['recovery_email']) ? htmlspecialchars($_POST['recovery_email']) : '' ?>">
                        </div>
                        <button type="submit" class="btn w-100 py-2 mb-2" style="background: var(--accent-color); color: white; font-weight: 600;">
                            Enviar enlace de recuperación
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

<?php
require_once __DIR__ . '/includes/footer.php';
?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script>
    // Mostrar mensajes con animación
    document.addEventListener('DOMContentLoaded', function() {
        const errorAlert = document.getElementById('errorAlert');
        const successAlert = document.getElementById('successAlert');
        
        if (errorAlert) {
            setTimeout(() => {
                errorAlert.classList.add('show');
            }, 100);
        }
        
        if (successAlert) {
            setTimeout(() => {
                successAlert.classList.add('show');
            }, 100);
        }
        
        // Función para mostrar/ocultar contraseña
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                this.style.backgroundColor = 'var(--accent-color)';
                this.style.color = 'white';
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                this.style.backgroundColor = 'transparent';
                this.style.color = 'var(--accent-color)';
            }
        });
        
        // Mostrar formulario de recuperación
        document.getElementById('forgotPassword')?.addEventListener('click', function(e) {
            if (window.location.search.includes('recovery=1')) {
                e.preventDefault();
                document.getElementById('loginForm').classList.add('d-none');
                document.getElementById('recoveryForm').classList.add('show');
            }
        });
        
        // Cancelar recuperación
        document.getElementById('cancelRecovery').addEventListener('click', function() {
            window.location.href = window.location.pathname;
        });
        
        // Mostrar formulario de recuperación si viene por GET
        if (window.location.search.includes('recovery=1')) {
            document.getElementById('loginForm').classList.add('d-none');
            document.getElementById('recoveryForm').classList.add('show');
        }
        
        // Efecto ripple para el botón de login
        document.querySelector('button[type="submit"]')?.addEventListener('click', function(e) {
            let x = e.clientX - e.target.getBoundingClientRect().left;
            let y = e.clientY - e.target.getBoundingClientRect().top;
            
            let ripple = document.createElement('span');
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            ripple.classList.add('ripple-effect');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 1000);
        });
    });
</script>
</body>
</html>