<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin()) {
    header('Location: ../dashboard.php');
    exit;
}

// Obtener roles disponibles
$roles = $conex->query("SELECT * FROM roles WHERE activo = 1 ORDER BY nombre_rol")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.8);
            --primary-hover: rgba(140, 74, 63, 1);
            --secondary-color: rgba(108, 117, 125, 0.8);
            --text-color: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --bg-transparent: rgba(0, 0, 0, 0.5);
            --bg-transparent-light: rgba(0, 0, 0, 0.4);
            --bg-input: rgba(0, 0, 0, 0.6);
            --border-color: rgba(255, 255, 255, 0.2);
            --success-color: rgba(25, 135, 84, 0.8);
            --danger-color: rgba(220, 53, 69, 0.8);
            --warning-color: rgba(255, 193, 7, 0.8);
            --info-color: rgba(13, 202, 240, 0.8);
        }

        body {
            background-image: url('https://pfst.cf2.poecdn.net/base/image/60ab54eef562f30f85a67bde31f924f078199dae0b7bc6c333dfb467a2c13471?w=1024&h=768&pmaid=442168253');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            min-height: 100vh;
        }

        .main-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--bg-transparent);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            color: var(--text-color);
        }

        .page-title i {
            margin-right: 12px;
            color: var(--primary-color);
        }

        .card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
            color: var(--text-color);
        }

        .form-label {
            color: var(--text-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            background-color: var(--bg-input);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: rgba(0, 0, 0, 0.7);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px var(--primary-color);
            color: var(--text-color);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-text {
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .password-strength {
            margin-top: 0.5rem;
            padding: 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            display: none;
        }

        .password-strength.weak {
            background-color: rgba(220, 53, 69, 0.3);
            color: #ff6b6b;
            border-left: 4px solid var(--danger-color);
        }

        .password-strength.medium {
            background-color: rgba(255, 193, 7, 0.3);
            color: #ffd43b;
            border-left: 4px solid var(--warning-color);
        }

        .password-strength.strong {
            background-color: rgba(25, 135, 84, 0.3);
            color: #51cf66;
            border-left: 4px solid var(--success-color);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            gap: 0.5rem;
        }

        .btn i {
            font-size: 1rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-outline-secondary {
            background-color: transparent;
            border: 1px solid var(--secondary-color);
            color: var(--text-color);
        }

        .btn-outline-secondary:hover {
            background-color: var(--secondary-color);
            color: white;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(5px);
            border-left: 4px solid var(--danger-color);
            background-color: rgba(220, 53, 69, 0.2);
            color: var(--text-color);
        }

        .alert-success {
            border-left: 4px solid var(--success-color);
            background-color: rgba(25, 135, 84, 0.2);
        }

        .alert .btn-close {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.3rem;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .alert .btn-close:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
                margin: 1rem;
            }

            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .card-body {
                padding: 1rem;
            }
        }

        @media (max-width: 576px) {
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .d-md-flex {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="header-section">
            <h1 class="page-title"><i class="fas fa-user-plus"></i>Nuevo Usuario</h1>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
        </div>
        
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] === 'success' ? 'success' : '' ?>">
                <span><?= htmlspecialchars($_SESSION['mensaje']) ?></span>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php
            unset($_SESSION['mensaje']);
            unset($_SESSION['tipo_mensaje']);
            ?>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Información del Usuario</h5>
            </div>
            <div class="card-body">
                <form action="procesar.php" method="POST" id="usuarioForm">
                    <input type="hidden" name="accion" value="crear">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nombre_completo" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" required 
                                   placeholder="Ingrese el nombre completo">
                        </div>
                        <div class="col-md-6">
                            <label for="username_usuario" class="form-label">Nombre de Usuario *</label>
                            <input type="text" class="form-control" id="username_usuario" name="username_usuario" required 
                                   placeholder="Ingrese el nombre de usuario">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="correo_usuario" class="form-label">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="correo_usuario" name="correo_usuario" required 
                                   placeholder="ejemplo@dominio.com">
                        </div>
                        <div class="col-md-6">
                            <label for="telefono_usuario" class="form-label">Teléfono *</label>
                            <input type="tel" class="form-control" id="telefono_usuario" name="telefono_usuario" required 
                                   placeholder="Ingrese el número de teléfono">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="contrasena_usuario" class="form-label">Contraseña *</label>
                            <input type="password" class="form-control" id="contrasena_usuario" name="contrasena_usuario" required 
                                   placeholder="Ingrese la contraseña" minlength="6">
                            <div class="form-text">La contraseña debe tener al menos 6 caracteres</div>
                            <div class="password-strength" id="passwordStrength"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña *</label>
                            <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" required 
                                   placeholder="Confirme la contraseña">
                            <div class="form-text" id="passwordMatch"></div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="id_rol" class="form-label">Rol *</label>
                            <select class="form-select" id="id_rol" name="id_rol" required>
                                <option value="">Seleccione un rol</option>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre_rol']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" checked>
                                <label class="form-check-label" for="activo">
                                    Usuario Activo
                                </label>
                            </div>
                            <div class="form-text">Los usuarios inactivos no podrán iniciar sesión</div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-undo me-1"></i>Limpiar
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save me-1"></i>Guardar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('contrasena_usuario');
            const confirmPasswordInput = document.getElementById('confirmar_contrasena');
            const passwordStrength = document.getElementById('passwordStrength');
            const passwordMatch = document.getElementById('passwordMatch');
            const submitBtn = document.getElementById('submitBtn');
            const form = document.getElementById('usuarioForm');

            // Función para verificar fortaleza de contraseña
            function checkPasswordStrength(password) {
                let strength = 0;
                let messages = [];

                if (password.length >= 6) strength++;
                if (password.length >= 8) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;

                if (strength < 2) {
                    return { level: 'weak', message: 'Contraseña débil' };
                } else if (strength < 4) {
                    return { level: 'medium', message: 'Contraseña media' };
                } else {
                    return { level: 'strong', message: 'Contraseña fuerte' };
                }
            }

            // Event listener para verificar contraseña
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                if (password.length > 0) {
                    const strength = checkPasswordStrength(password);
                    passwordStrength.textContent = strength.message;
                    passwordStrength.className = 'password-strength ' + strength.level;
                    passwordStrength.style.display = 'block';
                } else {
                    passwordStrength.style.display = 'none';
                }
                checkPasswordMatch();
            });

            // Event listener para verificar coincidencia de contraseñas
            confirmPasswordInput.addEventListener('input', checkPasswordMatch);

            function checkPasswordMatch() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (confirmPassword.length > 0) {
                    if (password === confirmPassword) {
                        passwordMatch.textContent = 'Las contraseñas coinciden';
                        passwordMatch.style.color = '#51cf66';
                        submitBtn.disabled = false;
                    } else {
                        passwordMatch.textContent = 'Las contraseñas no coinciden';
                        passwordMatch.style.color = '#ff6b6b';
                        submitBtn.disabled = true;
                    }
                } else {
                    passwordMatch.textContent = '';
                    submitBtn.disabled = false;
                }
            }

            // Validación del formulario antes de enviar
            form.addEventListener('submit', function(e) {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden. Por favor, verifique.');
                    return false;
                }

                if (password.length < 6) {
                    e.preventDefault();
                    alert('La contraseña debe tener al menos 6 caracteres.');
                    return false;
                }

                return true;
            });

            // Limpiar mensajes al resetear el formulario
            form.addEventListener('reset', function() {
                passwordStrength.style.display = 'none';
                passwordMatch.textContent = '';
                submitBtn.disabled = false;
            });
        });
    </script>
</body>
</html>