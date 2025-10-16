<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos
if (!isAdmin()) {
    header('Location: ../dashboard.php');
    exit;
}

// Obtener ID del usuario
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Obtener datos del usuario
try {
    $stmt = $conex->prepare("
        SELECT u.*, r.nombre_rol 
        FROM usuarios u 
        JOIN roles r ON u.id_rol = r.id_rol 
        WHERE u.id_usuario = ?
    ");
    $stmt->execute([$id]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        header('Location: index.php?error=Usuario no encontrado');
        exit;
    }
} catch (PDOException $e) {
    header('Location: index.php?error=Error al obtener usuario');
    exit;
}

// Obtener roles para el select
try {
    $stmt = $conex->prepare("SELECT * FROM roles WHERE activo = 1 ORDER BY nombre_rol");
    $stmt->execute();
    $roles = $stmt->fetchAll();
} catch (PDOException $e) {
    $roles = [];
}

require_once __DIR__ . '/../../includes/head.php';
$title = 'Editar Usuario | Nacional Tapizados';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../../includes/head.php'; ?>
    <title>Editar Usuario</title>
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-color);
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
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }

        .form-control:focus {
            background-color: rgba(0, 0, 0, 0.7);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(140, 74, 63, 0.25);
            color: var(--text-color);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-control:disabled {
            background-color: rgba(0, 0, 0, 0.4);
            color: var(--text-muted);
            cursor: not-allowed;
        }

        .form-select {
            background-color: var(--bg-input);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }

        .form-select:focus {
            background-color: rgba(0, 0, 0, 0.7);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(140, 74, 63, 0.25);
            color: var(--text-color);
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
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-secondary:hover {
            background-color: rgba(108, 117, 125, 1);
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: rgba(220, 53, 69, 1);
            transform: translateY(-2px);
        }

        .btn-info {
            background-color: var(--info-color);
            color: white;
        }

        .btn-info:hover {
            background-color: rgba(13, 202, 240, 1);
            transform: translateY(-2px);
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
            background-color: rgba(25, 135, 84, 0.2);
            border-left: 4px solid var(--success-color);
        }

        .alert-warning {
            background-color: rgba(255, 193, 7, 0.2);
            border-left: 4px solid var(--warning-color);
        }

        .alert-info {
            background-color: rgba(13, 202, 240, 0.2);
            border-left: 4px solid var(--info-color);
        }

        .required-field::after {
            content: " *";
            color: var(--danger-color);
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-section-title i {
            color: var(--primary-color);
        }

        .user-info-card {
            background-color: var(--bg-transparent-light);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .user-info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .user-info-item:last-child {
            border-bottom: none;
        }

        .user-info-label {
            font-weight: 500;
            color: var(--text-muted);
        }

        .user-info-value {
            color: var(--text-color);
        }

        /* Estilos para el toggle switch */
        .toggle-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--danger-color);
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: var(--success-color);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }

        .toggle-label {
            font-weight: 500;
            color: var(--text-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                margin: 1rem;
                padding: 1.5rem;
            }

            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .d-md-flex {
                flex-direction: column;
            }

            .user-info-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.25rem;
            }
        }

        @media (max-width: 576px) {
            .form-section-title {
                font-size: 1.1rem;
            }
            
            .row {
                margin-left: -0.5rem;
                margin-right: -0.5rem;
            }
            
            .col-md-6 {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="header-section">
            <h1 class="page-title"><i class="fas fa-user-edit"></i>Editar Usuario</h1>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
        </div>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <div>
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <div>
                    <i class="fas fa-check-circle me-2"></i>
                    <?= htmlspecialchars($_GET['success']) ?>
                </div>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form action="procesar.php" method="POST">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="id" value="<?= $usuario['id_usuario'] ?>">
                    
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-info-circle"></i>
                            Información del Usuario
                        </h3>
                        
                        <div class="user-info-card ">
                            <a href="ver.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-primary" target="_blank">
                                <i class="fas fa-eye me-1"></i> Ver Información 
                            </a>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-user-cog"></i>
                            Configuración de Acceso
                        </h3>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="rol" class="form-label required-field">
                                    <i class="fas fa-user-tag"></i>
                                    Rol de Usuario
                                </label>
                                <select class="form-select" id="rol" name="rol" required>
                                    <option value="">Seleccione un rol</option>
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?= $rol['id_rol'] ?>" 
                                            <?= $usuario['id_rol'] == $rol['id_rol'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($rol['nombre_rol']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required-field">
                                    <i class="fas fa-toggle-on"></i>
                                    Estado
                                </label>
                                <div class="toggle-container">
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="estado" value="Activo" <?= $usuario['activo'] ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <span class="toggle-label"><?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="index.php" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const rol = document.getElementById('rol').value;
            
            // Validar campos requeridos
            if (!rol) {
                e.preventDefault();
                alert('Por favor complete todos los campos requeridos (*)');
                return false;
            }
        });

        // Efectos visuales para los campos
        const inputs = document.querySelectorAll('.form-control, .form-select');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });

        // Actualizar etiqueta del toggle switch
        const toggleSwitch = document.querySelector('input[name="estado"]');
        const toggleLabel = document.querySelector('.toggle-label');
        
        toggleSwitch.addEventListener('change', function() {
            toggleLabel.textContent = this.checked ? 'Activo' : 'Inactivo';
        });
    </script>
</body>
</html>