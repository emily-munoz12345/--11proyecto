<?php
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Procesar cambio de estado
if (isset($_GET['cambiar_estado'])) {
    $id = intval($_GET['id']);
    $nuevo_estado = $_GET['estado'];
    
    try {
        $stmt = $conex->prepare("UPDATE cotizaciones SET estado_cotizacion = ? WHERE id_cotizacion = ?");
        if ($stmt->execute([$nuevo_estado, $id])) {
            $_SESSION['mensaje'] = 'Estado de cotización actualizado';
            $_SESSION['tipo_mensaje'] = 'success';
        }
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'Error al actualizar: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
}

// Paginación y búsqueda
$busqueda = $_GET['busqueda'] ?? '';
$pagina = max(1, intval($_GET['pagina'] ?? 1));
$porPagina = 10;

$sql = "SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo, 
               u.nombre_completo as nombre_usuario
        FROM cotizaciones c
        JOIN clientes cl ON c.id_cliente = cl.id_cliente
        JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
        JOIN usuarios u ON c.id_usuario = u.id_usuario";
$params = [];
$where = '';

if (!empty($busqueda)) {
    $where = " WHERE cl.nombre_cliente LIKE ? OR v.placa_vehiculo LIKE ? OR c.estado_cotizacion LIKE ?";
    $params = ["%$busqueda%", "%$busqueda%", "%$busqueda%"];
}

$sqlCount = "SELECT COUNT(*) FROM cotizaciones $where";
$stmt = $conex->prepare($sqlCount);
$stmt->execute($params);
$total = $stmt->fetchColumn();
$totalPaginas = ceil($total / $porPagina);

$sql .= " $where ORDER BY c.fecha_cotizacion DESC LIMIT :offset, :limit";
$stmt = $conex->prepare($sql);
foreach ($params as $i => $param) {
    $stmt->bindValue($i+1, $param);
}
$stmt->bindValue(':offset', ($pagina - 1) * $porPagina, PDO::PARAM_INT);
$stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
$stmt->execute();
$cotizaciones = $stmt->fetchAll();



require_once __DIR__ . '/../../includes/head.php';
$title = 'Gestión de Cotizaciones | Nacional Tapizados';
?>
    <?php
require_once __DIR__ . '/../../includes/navbar.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-file-invoice-dollar me-2"></i>Cotizaciones</h1>
        <a href="crear.php" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nueva Cotización
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
                    <input type="text" class="form-control" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Buscar cotizaciones...">
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
                            <th>Cliente</th>
                            <th>Vehículo</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Vendedor</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cotizaciones as $cotizacion): ?>
                        <tr>
                            <td><?= $cotizacion['id_cotizacion'] ?></td>
                            <td><?= htmlspecialchars($cotizacion['nombre_cliente']) ?></td>
                            <td><?= htmlspecialchars($cotizacion['marca_vehiculo']) ?> <?= htmlspecialchars($cotizacion['modelo_vehiculo']) ?></td>
                            <td><?= date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])) ?></td>
                            <td>$<?= number_format($cotizacion['total_cotizacion'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $cotizacion['estado_cotizacion'] == 'Aprobado' ? 'success' : 
                                    ($cotizacion['estado_cotizacion'] == 'Rechazada' ? 'danger' : 
                                    ($cotizacion['estado_cotizacion'] == 'Completada' ? 'primary' : 'warning')) 
                                ?>">
                                    <?= $cotizacion['estado_cotizacion'] ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($cotizacion['nombre_usuario']) ?></td>
                            <td>
                                <div class="d-flex">
                                    <a href="ver.php?id=<?= $cotizacion['id_cotizacion'] ?>" class="btn btn-sm btn-info me-2">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="editar.php?id=<?= $cotizacion['id_cotizacion'] ?>" class="btn btn-sm btn-warning me-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-cogs"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="index.php?cambiar_estado&id=<?= $cotizacion['id_cotizacion'] ?>&estado=Aprobado">Aprobar</a></li>
                                            <li><a class="dropdown-item" href="index.php?cambiar_estado&id=<?= $cotizacion['id_cotizacion'] ?>&estado=Rechazada">Rechazar</a></li>
                                            <li><a class="dropdown-item" href="index.php?cambiar_estado&id=<?= $cotizacion['id_cotizacion'] ?>&estado=Completada">Completar</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="index.php?eliminar=<?= $cotizacion['id_cotizacion'] ?>" onclick="return confirm('¿Eliminar esta cotización?')">Eliminar</a></li>
                                        </ul>
                                    </div>
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