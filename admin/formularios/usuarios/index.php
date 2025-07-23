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

// Inicializar variables de sesión para mensajes
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
    $_SESSION['tipo_mensaje'] = '';
}

// Procesar eliminación (solo si no es el propio usuario)
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    
    // No permitir auto-eliminación
    if ($id != $_SESSION['id_usuario']) {
        try {
            // Verificar si el usuario tiene trabajos asociados
            $stmt = $conex->prepare("SELECT COUNT(*) FROM trabajos WHERE id_usuario = ?");
            $stmt->execute([$id]);
            $tieneTrabajos = $stmt->fetchColumn();
            
            if ($tieneTrabajos > 0) {
                $_SESSION['mensaje'] = 'No se puede eliminar: usuario tiene trabajos asociados';
                $_SESSION['tipo_mensaje'] = 'danger';
            } else {
                $stmt = $conex->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
                if ($stmt->execute([$id])) {
                    $_SESSION['mensaje'] = 'Usuario eliminado correctamente';
                    $_SESSION['tipo_mensaje'] = 'success';
                }
            }
        } catch (PDOException $e) {
            $_SESSION['mensaje'] = 'Error al eliminar: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }
    } else {
        $_SESSION['mensaje'] = 'No puedes eliminar tu propio usuario';
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    header('Location: index.php');
    exit;
}

// Búsqueda y paginación
$busqueda = $_GET['busqueda'] ?? '';
$pagina = max(1, intval($_GET['pagina'] ?? 1));
$porPagina = 10;

$sql = "SELECT u.*, r.nombre_rol FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol";
$params = [];
$where = '';

if (!empty($busqueda)) {
    $where = " WHERE u.nombre_completo LIKE ? OR u.username_usuario LIKE ? OR u.correo_usuario LIKE ? OR r.nombre_rol LIKE ?";
    $params = ["%$busqueda%", "%$busqueda%", "%$busqueda%", "%$busqueda%"];
}

// Contar total para paginación
$sqlCount = "SELECT COUNT(*) FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol $where";
$stmt = $conex->prepare($sqlCount);
$stmt->execute($params);
$totalUsuarios = $stmt->fetchColumn();
$totalPaginas = ceil($totalUsuarios / $porPagina);

// Obtener usuarios
$offset = ($pagina - 1) * $porPagina;
$sql .= " $where ORDER BY username_usuario ASC LIMIT $offset, $porPagina";

$stmt = $conex->prepare($sql);
if (!empty($busqueda)) {
    $stmt->execute($params);
} else {
    $stmt->execute();
}
$usuarios = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/head.php';
$title = 'Gestión de Usuarios | Nacional Tapizados';
?>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-users-cog me-2"></i>Gestión de Usuarios
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="crear.php" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus-circle me-1"></i> Nuevo Usuario
                            </a>
                        </div>
                        <div class="text-muted small">
                            Rol actual: <strong>Administrador</strong>
                        </div>
                    </div>
                </div>

                <?php if (!empty($_SESSION['mensaje'])): ?>
                    <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?> alert-dismissible fade show">
                        <?= $_SESSION['mensaje'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php 
                    $_SESSION['mensaje'] = '';
                    $_SESSION['tipo_mensaje'] = '';
                    ?>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-body">
                        <form class="row g-3">
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" 
                                    placeholder="Buscar por nombre, usuario, correo o rol">
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary w-100" type="submit">
                                    <i class="fas fa-search me-1"></i> Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>


            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= $usuario['id_usuario'] ?></td>
                            <td><?= htmlspecialchars($usuario['nombre_completo']) ?></td>
                            <td><?= htmlspecialchars($usuario['username_usuario']) ?></td>
                            <td><?= htmlspecialchars($usuario['correo_usuario']) ?></td>
                            <td><?= htmlspecialchars($usuario['nombre_rol']) ?></td>
                            <td>
                                <span class="badge bg-<?= $usuario['activo_usuario'] == 'Activo' ? 'success' : 'secondary' ?>">
                                    <?= $usuario['activo_usuario'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <a href="editar.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-sm btn-warning me-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            </main>
        </div>
    </div>

    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>