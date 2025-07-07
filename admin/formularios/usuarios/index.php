<?php
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

// Solo administradores pueden gestionar usuarios
if (!isAdmin()) {
    header('Location: ../dashboard.php');
    exit;
}

// Procesar eliminación (solo si no es el propio usuario)
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    
    // No permitir auto-eliminación
    if ($id != $_SESSION['id_usuario']) {
        try {
            $stmt = $conex->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
            if ($stmt->execute([$id])) {
                $_SESSION['mensaje'] = 'Usuario eliminado correctamente';
                $_SESSION['tipo_mensaje'] = 'success';
            }
        } catch (PDOException $e) {
            $_SESSION['mensaje'] = 'Error al eliminar: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }
    } else {
        $_SESSION['mensaje'] = 'No puedes eliminar tu propio usuario';
        $_SESSION['tipo_mensaje'] = 'danger';
    }
}

// Paginación y búsqueda
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
$total = $stmt->fetchColumn();
$totalPaginas = ceil($total / $porPagina);

// Obtener usuario
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

    <?php
require_once __DIR__ . '/../../includes/navbar.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-users-cog me-2"></i>Usuarios</h1>
        <a href="crear.php" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Usuario
        </a>
    </div>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?> alert-dismissible fade show">
            <?= $_SESSION['mensaje'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <form class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Buscar usuarios...">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

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

            <?php if ($totalPaginas > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($pagina > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?= $pagina-1 ?>&busqueda=<?= urlencode($busqueda) ?>">
                            Anterior
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $i ?>&busqueda=<?= urlencode($busqueda) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($pagina < $totalPaginas): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?= $pagina+1 ?>&busqueda=<?= urlencode($busqueda) ?>">
                            Siguiente
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Confirmación antes de eliminar
        document.querySelectorAll('.btn-danger').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('¿Está seguro de eliminar este cliente?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>