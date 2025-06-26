<?php
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

if (!isAdmin() && !isTecnico()) {
    header('Location: ../dashboard.php');
    exit;
}

// Paginación y búsqueda
$busqueda = $_GET['busqueda'] ?? '';
$estado = $_GET['estado'] ?? '';
$pagina = max(1, intval($_GET['pagina'] ?? 1));
$porPagina = 10;

$sql = "SELECT t.*, c.id_cotizacion, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, 
        u.nombre_completo as nombre_tecnico
        FROM trabajos t
        JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
        JOIN clientes cl ON c.id_cliente = cl.id_cliente
        JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
        JOIN usuarios u ON c.id_usuario = u.id_usuario";
$params = [];
$conditions = [];

// Búsqueda por texto (nombre cliente, placa o estado)
if (!empty($busqueda)) {
    $conditions[] = "(cl.nombre_cliente LIKE ? OR v.placa_vehiculo LIKE ?)";
    array_push($params, "%$busqueda%", "%$busqueda%");
}

// Filtro por estado
if (!empty($estado) && in_array($estado, ['Pendiente', 'En progreso', 'Entregado', 'Cancelado'])) {
    $conditions[] = "t.estado = ?";
    $params[] = $estado;
}

// Construir WHERE
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Consulta para contar
$sqlCount = "SELECT COUNT(*) FROM trabajos t 
             JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion" . 
             (!empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "");

$stmt = $conex->prepare($sqlCount);
$stmt->execute($params);
$total = $stmt->fetchColumn();
$totalPaginas = ceil($total / $porPagina);

// Consulta principal con paginación
$offset = ($pagina - 1) * $porPagina;
$sql .= " ORDER BY t.fecha_inicio DESC LIMIT $offset, $porPagina";

$stmt = $conex->prepare($sql);
$stmt->execute($params);
$trabajos = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/head.php';
$title = 'Gestión de Trabajos | Nacional Tapizados';
include __DIR__ . '/../../includes/navbar.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-tools me-2"></i>Trabajos</h1>
        <a href="crear.php" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Trabajo
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
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="busqueda" class="form-label">Buscar (cliente o placa)</label>
                        <input type="text" class="form-control" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>">
                    </div>
                    <div class="col-md-5">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" name="estado">
                            <option value="">Todos los estados</option>
                            <option value="Pendiente" <?= $estado == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="En progreso" <?= $estado == 'En progreso' ? 'selected' : '' ?>>En progreso</option>
                            <option value="Entregado" <?= $estado == 'Entregado' ? 'selected' : '' ?>>Entregado</option>
                            <option value="Cancelado" <?= $estado == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Vehículo</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th>Técnico</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trabajos as $trabajo): ?>
                        <tr>
                            <td><?= $trabajo['id_trabajos'] ?></td>
                            <td><?= htmlspecialchars($trabajo['nombre_cliente']) ?></td>
                            <td><?= htmlspecialchars($trabajo['marca_vehiculo']) ?> <?= htmlspecialchars($trabajo['modelo_vehiculo']) ?></td>
                            <td><?= date('d/m/Y', strtotime($trabajo['fecha_inicio'])) ?></td>
                            <td><?= $trabajo['fecha_fin'] ? date('d/m/Y', strtotime($trabajo['fecha_fin'])) : '--' ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $trabajo['estado'] == 'Entregado' ? 'success' : 
                                    ($trabajo['estado'] == 'Cancelado' ? 'danger' : 
                                    ($trabajo['estado'] == 'En progreso' ? 'primary' : 'warning')) 
                                ?>">
                                    <?= $trabajo['estado'] ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($trabajo['nombre_tecnico']) ?></td>
                            <td>
                                <div class="d-flex">
                                    <a href="ver.php?id=<?= $trabajo['id_trabajos'] ?>" class="btn btn-sm btn-info me-2">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="editar.php?id=<?= $trabajo['id_trabajos'] ?>" class="btn btn-sm btn-warning me-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($trabajo['estado'] != 'Entregado' && $trabajo['estado'] != 'Cancelado'): ?>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-cogs"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="procesar.php?accion=cambiar_estado&id=<?= $trabajo['id_trabajos'] ?>&estado=En progreso">En progreso</a></li>
                                            <li><a class="dropdown-item" href="procesar.php?accion=cambiar_estado&id=<?= $trabajo['id_trabajos'] ?>&estado=Entregado">Entregado</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="procesar.php?accion=cambiar_estado&id=<?= $trabajo['id_trabajos'] ?>&estado=Cancelado">Cancelar</a></li>
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
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $pagina-1])) ?>">
                            Anterior
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($pagina < $totalPaginas): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $pagina+1])) ?>">
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

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Confirmación antes de cambiar estado
    document.querySelectorAll('.dropdown-item').forEach(item => {
        if(item.classList.contains('text-danger')) {
            item.addEventListener('click', function(e) {
                if (!confirm('¿Está seguro de cancelar este trabajo?')) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
</body>
</html>