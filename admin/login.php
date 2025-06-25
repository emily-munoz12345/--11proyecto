
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                // Verificar contraseña (sin hash en tu BD)
                elseif ($password === $usuario['contrasena_usuario']) {
                    // Iniciar sesión
                    session_start();
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Nacional Tapizados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= getBaseUrl() ?>/css/style.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Iniciar Sesión</h2>
                            <p class="text-muted">Ingresa tus credenciales</p>
                        </div>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
require_once __DIR__ . '/../includes/footer.php';
?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>