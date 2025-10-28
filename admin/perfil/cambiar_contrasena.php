<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

$usuario_id = getUserId();

// Obtener datos del usuario
$stmt = $conex->prepare("SELECT * FROM usuarios WHERE id_usuario = ? AND activo = 1");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header('Location: perfil.php');
    exit;
}

// MOSTRAR MENSAJES DE ÉXITO O ERROR
if (isset($_SESSION['mensaje_contrasena'])) {
    $mensaje_contrasena = $_SESSION['mensaje_contrasena'];
    unset($_SESSION['mensaje_contrasena']);
}

if (isset($_SESSION['errores_contrasena'])) {
    $errores_contrasena = $_SESSION['errores_contrasena'];
    unset($_SESSION['errores_contrasena']);
}

// Procesar cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errores = [];
    
    // Recoger datos del formulario
    $contrasena_actual = $_POST['contrasena_actual'] ?? '';
    $nueva_contrasena = $_POST['nueva_contrasena'] ?? '';
    $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';
    
    // Validaciones
    if (empty($contrasena_actual) || empty($nueva_contrasena) || empty($confirmar_contrasena)) {
        $errores[] = "Todos los campos de contraseña son obligatorios.";
    }
    
    // Verificar contraseña actual - COMPARACIÓN DIRECTA EN TEXTO PLANO
    if (empty($errores)) {
        if ($contrasena_actual !== $usuario['contrasena_usuario']) {
            $errores[] = "La contraseña actual es incorrecta.";
        }
    }
    
    // Validar nueva contraseña
    if (empty($errores) && strlen($nueva_contrasena) < 8) {
        $errores[] = "La nueva contraseña debe tener al menos 8 caracteres.";
    }
    
    // Verificar que las contraseñas coincidan
    if (empty($errores) && $nueva_contrasena !== $confirmar_contrasena) {
        $errores[] = "Las contraseñas nuevas no coinciden.";
    }
    
    // Si hay errores, mostrar mensajes
    if (!empty($errores)) {
        $_SESSION['errores_contrasena'] = $errores;
        header('Location: cambiar_contrasena.php');
        exit;
    }
    
    // Actualizar contraseña - GUARDAR EN TEXTO PLANO
    try {
        $conex->beginTransaction();
        
        // Guardar directamente en texto plano como está en la BD actual
        $stmt = $conex->prepare("UPDATE usuarios SET contrasena_usuario = ?, ultima_actividad = NOW() WHERE id_usuario = ?");
        $stmt->execute([$nueva_contrasena, $usuario_id]);
        
        $conex->commit();
        
        $_SESSION['mensaje_contrasena'] = "Contraseña actualizada correctamente.";
        
    } catch (Exception $e) {
        $conex->rollBack();
        $_SESSION['errores_contrasena'] = ["Error al actualizar la contraseña: " . $e->getMessage()];
    }
    
    header('Location: cambiar_contrasena.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña - Sistema de Tapicería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            max-width: 600px;
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

        .profile-card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header {
            background-color: var(--primary-color);
            border-bottom: 1px solid var(--border-color);
            padding: 1.2rem 1.5rem;
        }

        .card-title {
            margin: 0;
            color: white;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-label {
            color: var(--text-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label i {
            color: var(--primary-color);
            width: 16px;
        }

        .form-control {
            background-color: var(--bg-input);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: rgba(0, 0, 0, 0.7);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px var(--primary-color);
            color: var(--text-color);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .password-group {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 5px;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--text-color);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-secondary:hover {
            background-color: rgba(108, 117, 125, 1);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(5px);
            border-left: 4px solid var(--primary-color);
            background-color: rgba(13, 202, 240, 0.2);
            color: var(--text-color);
        }

        .alert-success {
            background-color: rgba(25, 135, 84, 0.2);
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            border-left: 4px solid var(--danger-color);
        }

        .user-info {
            background-color: rgba(140, 74, 63, 0.1);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid var(--primary-color);
        }

        .user-info-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            font-size: 1rem;
        }

        .user-info-item i {
            color: var(--primary-color);
            width: 20px;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
                margin: 1rem;
            }
            .page-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-key"></i> Cambiar Contraseña
            </h1>
            <div class="d-flex gap-2">
                <a href="perfil.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Perfil
                </a>
            </div>
        </div>

        <!-- MOSTRAR MENSAJES -->
        <?php if (isset($mensaje_contrasena)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($mensaje_contrasena); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($errores_contrasena)): ?>
            <?php foreach ($errores_contrasena as $error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Información del usuario -->
        <div class="user-info">
            <h5 class="mb-3">
                <i class="fas fa-user me-2"></i>Información del Usuario
            </h5>
            <div class="user-info-item">
                <i class="fas fa-user"></i>
                <span><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre_completo']); ?></span>
            </div>
            <div class="user-info-item">
                <i class="fas fa-envelope"></i>
                <span><strong>Correo:</strong> <?php echo htmlspecialchars($usuario['correo_usuario']); ?></span>
            </div>
            <div class="user-info-item">
                <i class="fas fa-user-circle"></i>
                <span><strong>Usuario:</strong> <?php echo htmlspecialchars($usuario['username_usuario']); ?></span>
            </div>
        </div>

        <!-- Formulario de cambio de contraseña -->
        <div class="profile-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lock me-2"></i>Cambiar Contraseña
                </h5>
            </div>
            <div class="card-body">
                <form id="passwordForm" action="cambiar_contrasena.php" method="POST">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="contrasena_actual" class="form-label">
                                <i class="fas fa-key"></i>
                                Contraseña Actual
                            </label>
                            <div class="password-group">
                                <input type="password" class="form-control" id="contrasena_actual" name="contrasena_actual" 
                                       placeholder="Ingrese su contraseña actual" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('contrasena_actual')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Contraseña actual: <?php echo htmlspecialchars($usuario['contrasena_usuario']); ?></small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nueva_contrasena" class="form-label">
                                <i class="fas fa-lock"></i>
                                Nueva Contraseña
                            </label>
                            <div class="password-group">
                                <input type="password" class="form-control" id="nueva_contrasena" name="nueva_contrasena" 
                                       placeholder="Ingrese nueva contraseña" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('nueva_contrasena')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Mínimo 8 caracteres</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="confirmar_contrasena" class="form-label">
                                <i class="fas fa-lock"></i>
                                Confirmar Nueva Contraseña
                            </label>
                            <div class="password-group">
                                <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" 
                                       placeholder="Confirme la nueva contraseña" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('confirmar_contrasena')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="mostrar_contrasenas">
                                <label class="form-check-label" for="mostrar_contrasenas">
                                    Mostrar todas las contraseñas
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Cambiar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Información de seguridad -->
        <div class="alert alert-warning">
            <div>
                <i class="fas fa-shield-alt me-2"></i>
                <strong>Nota:</strong> Las contraseñas se almacenan en texto plano en el sistema actual.
                <ul class="mb-0 mt-2">
                    <li>Use una contraseña de al menos 8 caracteres</li>
                    <li>Combine letras, números y caracteres especiales</li>
                    <li>No comparta su contraseña con nadie</li>
                    <li>Cambie su contraseña regularmente</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para mostrar/ocultar contraseña individual
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const toggleBtn = field.parentNode.querySelector('.password-toggle i');
            
            if (field.type === 'password') {
                field.type = 'text';
                toggleBtn.className = 'fas fa-eye-slash';
            } else {
                field.type = 'password';
                toggleBtn.className = 'fas fa-eye';
            }
        }

        // Configurar el checkbox para mostrar/ocultar todas las contraseñas
        document.getElementById('mostrar_contrasenas').addEventListener('change', function() {
            const show = this.checked;
            const passwordFields = ['contrasena_actual', 'nueva_contrasena', 'confirmar_contrasena'];
            
            passwordFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                const toggleBtn = field.parentNode.querySelector('.password-toggle i');
                
                if (field) {
                    field.type = show ? 'text' : 'password';
                    toggleBtn.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
                }
            });
        });

        // Validación del formulario
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const contrasenaActual = document.getElementById('contrasena_actual').value;
            const nuevaContrasena = document.getElementById('nueva_contrasena').value;
            const confirmarContrasena = document.getElementById('confirmar_contrasena').value;

            // Limpiar mensajes anteriores
            const alertas = document.querySelectorAll('.alert');
            alertas.forEach(alerta => {
                if (alerta.classList.contains('alert-danger')) {
                    alerta.remove();
                }
            });

            // Validaciones
            if (nuevaContrasena.length < 8) {
                e.preventDefault();
                mostrarError('La nueva contraseña debe tener al menos 8 caracteres.');
                return;
            }

            if (nuevaContrasena !== confirmarContrasena) {
                e.preventDefault();
                mostrarError('Las contraseñas nuevas no coinciden.');
                return;
            }
        });

        // Función para mostrar errores
        function mostrarError(mensaje) {
            const alerta = document.createElement('div');
            alerta.className = 'alert alert-danger alert-dismissible fade show';
            alerta.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            // Insertar después del header
            const header = document.querySelector('.header-section');
            header.parentNode.insertBefore(alerta, header.nextSibling);
            
            // Hacer scroll a la alerta
            alerta.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    </script>
</body>
</html>