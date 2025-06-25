<?php
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

if (!isAdmin() && !isTechnician()) {
    header('Location: ../dashboard.php');
    exit;
}

// Procesar cambio de estado
if (isset($_GET['cambiar_estado'])) {
    $id = intval($_GET['id']);
    $nuevo_estado = $_GET['estado'];
    
    try {
        $stmt = $conex->prepare("UPDATE trabajos SET estado = ? WHERE id_trabajos = ?");
        if ($stmt->execute([$nuevo_estado, $id])) {
            $_SESSION['mensaje'] = 'Estado del trabajo actualizado';
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

$sql = "SELECT t.*, c.id_cotizacion, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, 
               u.nombre_completo as tecnico
        FROM trabajos t
        JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
        JOIN clientes cl ON c.id_cliente = cl.id_cliente
        JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
        LEFT JOIN usuarios u ON t.id_tecnico = u.id_usuario";
$params = [];
$where = '';

if (!empty($busqueda)) {
    $where = " WHERE cl.nombre_cliente LIKE ? OR v.placa_vehiculo LIKE ? OR t.estado LIKE ?";
    $params = ["%$busqueda%", "%$busqueda%", "%$busqueda%"];
}

$sqlCount = "SELECT COUNT(*) FROM trabajos t
             JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
             JOIN clientes cl ON c.id_cliente = cl.id_cliente
             JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo $where";
$stmt = $conex->prepare($sqlCount);
$stmt->execute($params);
$total = $stmt->fetchColumn();
$totalPaginas = ceil($total / $porPagina);

$sql .= " $where ORDER BY t.fecha_inicio DESC LIMIT :offset, :limit";
$stmt = $conex->prepare($sql);
if (!empty($busqueda)) {
    $stmt->execute($params);
} else {
    $stmt->execute();
}
$trabajos = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/head.php';
$title = 'Gestión de Trabajos';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-tools me-2"></i>Trabajos Realizados</h1>
        <?php if (isAdmin()): ?>
        <a href="crear.php" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Trabajo
        </a>
        <?php endif; ?>
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
                    <input type="text" class="form-control" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Buscar trabajos...">
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
                            <th>Cotización</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Técnico</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trabajos as $trabajo): ?>
                        <tr>
                            <td><?= $trabajo['id_trabajos'] ?></td>
                            <td><?= htmlspecialchars($trabajo['nombre_cliente']) ?></td>
                            <td><?= htmlspecialchars($trabajo['marca_vehiculo']) ?> <?= htmlspecialchars($trabajo['modelo_vehiculo']) ?></td>
                            <td>#<?= $trabajo['id_cotizacion'] ?></td>
                            <td><?= date('d/m/Y', strtotime($trabajo['fecha_inicio'])) ?></td>
                            <td><?= $trabajo['fecha_fin'] ? date('d/m/Y', strtotime($trabajo['fecha_fin'])) : '--' ?></td>
                            <td><?= $trabajo['tecnico'] ?? '--' ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $trabajo['estado'] == 'Entregado' ? 'success' : 
                                    ($trabajo['estado'] == 'En progreso' ? 'primary' : 
                                    ($trabajo['estado'] == 'Cancelado' ? 'danger' : 'warning')) 
                                ?>">
                                    <?= $trabajo['estado'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <a href="ver.php?id=<?= $trabajo['id_trabajos'] ?>" class="btn btn-sm btn-info me-2">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if (isAdmin()): ?>
                                    <a href="editar.php?id=<?= $trabajo['id_trabajos'] ?>" class="btn btn-sm btn-warning me-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-cogs"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="index.php?cambiar_estado&id=<?= $trabajo['id_trabajos'] ?>&estado=En progreso">En progreso</a></li>
                                            <li><a class="dropdown-item" href="index.php?cambiar_estado&id=<?= $trabajo['id_trabajos'] ?>&estado=Entregado">Entregado</a></li>
                                            <li><a class="dropdown-item" href="index.php?cambiar_estado&id=<?= $trabajo['id_trabajos'] ?>&estado=Cancelado">Cancelar</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="index.php?eliminar=<?= $trabajo['id_trabajos'] ?>" onclick="return confirm('¿Eliminar este trabajo?')">Eliminar</a></li>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
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