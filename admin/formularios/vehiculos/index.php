<?php
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    try {
        $stmt = $conex->prepare("DELETE FROM vehiculos WHERE id_vehiculo = ?");
        if ($stmt->execute([$id])) {
            $_SESSION['mensaje'] = 'Vehículo eliminado correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
        }
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'Error al eliminar: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    header('Location: index.php');
    exit;
}

// Paginación y búsqueda
$busqueda = $_GET['busqueda'] ?? '';
$pagina = max(1, intval($_GET['pagina'] ?? 1));
$porPagina = 10;

$sql = "SELECT * FROM vehiculos";
$params = [];
$where = '';

if (!empty($busqueda)) {
    $where = " WHERE marca_vehiculo LIKE ? OR modelo_vehiculo LIKE ? OR placa_vehiculo LIKE ?";
    $params = ["%$busqueda%", "%$busqueda%", "%$busqueda%"];
}

$sqlCount = "SELECT COUNT(*) FROM vehiculos $where";
$stmt = $conex->prepare($sqlCount);
$stmt->execute($params);
$totalVehiculos = $stmt->fetchColumn();
$totalPaginas = ceil($totalVehiculos / $porPagina);

// SOLUCIÓN AL ERROR LIMIT
$offset = ($pagina - 1) * $porPagina;
$sql .= " $where ORDER BY marca_vehiculo ASC LIMIT $offset, $porPagina";

$stmt = $conex->prepare($sql);
if (!empty($busqueda)) {
    $stmt->execute($params);
} else {
    $stmt->execute();
}
$vehiculos = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/head.php';
$title = 'Gestión de Vehículos | Nacional Tapizados';
?>

    <?php
require_once __DIR__ . '/../../includes/navbar.php';
?>


<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-car me-2"></i>Vehículos</h1>
        <a href="crear.php" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Vehículo
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
                    <input type="text" class="form-control" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Buscar...">
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
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Placa</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehiculos as $vehiculo): ?>
                        <tr>
                            <td><?= $vehiculo['id_vehiculo'] ?></td>
                            <td><?= htmlspecialchars($vehiculo['marca_vehiculo']) ?></td>
                            <td><?= htmlspecialchars($vehiculo['modelo_vehiculo']) ?></td>
                            <td><?= htmlspecialchars($vehiculo['placa_vehiculo']) ?></td>
                            <td>
                                <div class="d-flex">
                                    <a href="editar.php?id=<?= $vehiculo['id_vehiculo'] ?>" class="btn btn-sm btn-warning me-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?eliminar=<?= $vehiculo['id_vehiculo'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('¿Eliminar este vehículo?')">
                                        <i class="fas fa-trash"></i>
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