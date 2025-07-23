<?php
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

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    try {
        // Verificar si el material está asociado a algún trabajo
        $stmt = $conex->prepare("SELECT COUNT(*) FROM trabajo_materiales WHERE id_material = ?");
        $stmt->execute([$id]);
        $tieneTrabajos = $stmt->fetchColumn();
        
        if ($tieneTrabajos > 0) {
            $_SESSION['mensaje'] = 'No se puede eliminar: material está asociado a trabajos';
            $_SESSION['tipo_mensaje'] = 'danger';
        } else {
            $stmt = $conex->prepare("DELETE FROM materiales WHERE id_material = ?");
            if ($stmt->execute([$id])) {
                $_SESSION['mensaje'] = 'Material eliminado correctamente';
                $_SESSION['tipo_mensaje'] = 'success';
            }
        }
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'Error al eliminar: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    header('Location: index.php');
    exit;
}

// Búsqueda y paginación
$busqueda = $_GET['busqueda'] ?? '';
$pagina = max(1, intval($_GET['pagina'] ?? 1));
$porPagina = 10;

$sql = "SELECT * FROM materiales";
$params = [];
$where = '';

if (!empty($busqueda)) {
    $where = " WHERE nombre_material LIKE ? OR categoria_material LIKE ? OR proveedor_material LIKE ?";
    $params = ["%$busqueda%", "%$busqueda%", "%$busqueda%"];
}

// Contar total para paginación
$sqlCount = "SELECT COUNT(*) FROM materiales $where";
$stmt = $conex->prepare($sqlCount);
$stmt->execute($params);
$totalMateriales = $stmt->fetchColumn();
$totalPaginas = ceil($totalMateriales / $porPagina);

// Obtener materiales
$offset = ($pagina - 1) * $porPagina;
$sql .= " $where ORDER BY nombre_material ASC LIMIT $offset, $porPagina";

$stmt = $conex->prepare($sql);
if (!empty($busqueda)) {
    $stmt->execute($params);
} else {
    $stmt->execute();
}
$materiales = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/head.php';
$title = 'Gestión de Materiales | Nacional Tapizados';
?>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-boxes me-2"></i>Gestión de Materiales
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="crear.php" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus-circle me-1"></i> Nuevo Material
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
                                    placeholder="Buscar por nombre, categoría o proveedor">
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary w-100" type="submit">
                                    <i class="fas fa-search me-1"></i> Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Precio/m</th>
                                        <th>Stock</th>
                                        <th>Categoría</th>
                                        <th>Proveedor</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($materiales) > 0): ?>
                                        <?php foreach ($materiales as $material): ?>
                                            <tr>
                                                <td><?= $material['id_material'] ?></td>
                                                <td><?= htmlspecialchars($material['nombre_material']) ?></td>
                                                <td>$<?= number_format($material['precio_metro'], 0, ',', '.') ?></td>
                                                <td><?= $material['stock_material'] ?> m</td>
                                                <td><?= htmlspecialchars($material['categoria_material']) ?></td>
                                                <td><?= htmlspecialchars($material['proveedor_material']) ?></td>
                                                <td class="text-end">
                                                    <div class="btn-group" role="group">
                                                        <a href="editar.php?id=<?= $material['id_material'] ?>" class="btn btn-sm btn-outline-secondary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="index.php?eliminar=<?= $material['id_material'] ?>" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('¿Eliminar este material?')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                No se encontraron materiales
                                                <?php if (!empty($busqueda)): ?>
                                                <div class="mt-2">
                                                    <a href="index.php" class="btn btn-sm btn-outline-secondary">Mostrar todos</a>
                                                </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($totalPaginas > 1): ?>
                            <nav aria-label="Paginación" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($pagina > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?pagina=<?= $pagina-1 ?>&busqueda=<?= urlencode($busqueda) ?>">
                                                <i class="fas fa-chevron-left"></i>
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
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>