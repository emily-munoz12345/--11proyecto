<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin()) {
    header('Location: ../dashboard.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

try {
    // Obtener datos del usuario
    $stmt = $conex->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        header('Location: index.php?error=Usuario no encontrado');
        exit;
    }
    
    // Obtener roles para el select
    $stmt = $conex->query("SELECT * FROM roles ORDER BY nombre_rol");
    $roles = $stmt->fetchAll();
} catch (PDOException $e) {
    header('Location: index.php?error=Error al obtener datos');
    exit;
}
?>

<?php
require_once __DIR__ . '/../../includes/head.php';
$title = 'Nacional Tapizados - Editar Usuario';
?>
<style>
    :root {
        --primary-color: rgba(140, 74, 63, 0.8);
        --primary-hover: rgba(140, 74, 63, 1);
        --secondary-color: rgba(108, 117, 125, 0.8);
        --text-color: #ffffff;
        --text-muted: rgba(255, 255, 255, 0.7);
        --bg-transparent: rgba(255, 255, 255, 0.1);
        --bg-transparent-light: rgba(255, 255, 255, 0.15);
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
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
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
    }

    .page-title i {
        margin-right: 12px;
        color: var(--primary-color);
    }

    /* Estilos para botones */
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

    .btn-secondary {
        background-color: var(--secondary-color);
        color: white;
    }

    .btn-secondary:hover {
        background-color: rgba(108, 117, 125, 1);
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

    /* Estilos para formularios */
    .form-container {
        background-color: var(--bg-transparent-light);
        backdrop-filter: blur(8px);
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--border-color);
    }

    .form-label {
        font-weight: 500;
        color: var(--text-color);
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid var(--border-color);
        color: var(--text-color);
        padding: 0.75rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        background-color: rgba(255, 255, 255, 0.15);
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(140, 74, 63, 0.25);
        color: var(--text-color);
    }

    .form-control::placeholder {
        color: var(--text-muted);
    }

    .form-text {
        color: var(--text-muted);
        font-size: 0.875rem;
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

        .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>

<body>
    <?php include '../../includes/sidebar.php'; ?>
    
    <div class="main-container">
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-user-edit"></i> Editar Usuario
            </h1>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
        
        <div class="form-container">
            <form action="procesar.php" method="POST">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" value="<?= $usuario['id_usuario'] ?>">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre Completo *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?= htmlspecialchars($usuario['nombre_completo']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="username" class="form-label">Nombre de Usuario *</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?= htmlspecialchars($usuario['username_usuario']) ?>" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="correo" class="form-label">Correo Electrónico *</label>
                        <input type="email" class="form-control" id="correo" name="correo" 
                               value="<?= htmlspecialchars($usuario['correo_usuario']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="telefono" class="form-label">Teléfono *</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" 
                               value="<?= htmlspecialchars($usuario['telefono_usuario']) ?>" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <div class="form-text">Dejar en blanco para mantener la actual</div>
                    </div>
                    <div class="col-md-6">
                        <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="rol" class="form-label">Rol *</label>
                        <select class="form-select" id="rol" name="rol" required>
                            <?php foreach ($roles as $rol): ?>
                            <option value="<?= $rol['id_rol'] ?>" <?= $rol['id_rol'] == $usuario['id_rol'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($rol['nombre_rol']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="estado" class="form-label">Estado *</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="Activo" <?= $usuario['activo_usuario'] == 'Activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="Inactivo" <?= $usuario['activo_usuario'] == 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
                
                <div class="d-flex gap-2 justify-content-end">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Actualizar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>