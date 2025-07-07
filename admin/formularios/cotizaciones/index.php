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
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$pagina = max(1, intval($_GET['pagina'] ?? 1));
$porPagina = 10;

$sql = "SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
        u.nombre_completo as nombre_usuario
        FROM cotizaciones c
        JOIN clientes cl ON c.id_cliente = cl.id_cliente
        JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
        JOIN usuarios u ON c.id_usuario = u.id_usuario";
$params = [];
$conditions = [];

// Búsqueda por texto (nombre cliente, placa o estado)
if (!empty($busqueda)) {
    $conditions[] = "(cl.nombre_cliente LIKE ? OR v.placa_vehiculo LIKE ? OR c.estado_cotizacion LIKE ?)";
    array_push($params, "%$busqueda%", "%$busqueda%", "%$busqueda%");
}

// Búsqueda por rango de fechas
if (!empty($fecha_inicio)) {
    $conditions[] = "DATE(c.fecha_cotizacion) >= ?";
    $params[] = $fecha_inicio;
}

if (!empty($fecha_fin)) {
    $conditions[] = "DATE(c.fecha_cotizacion) <= ?";
    $params[] = $fecha_fin;
}

// Construir la cláusula WHERE si hay condiciones
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Consulta para contar el total de registros
$sqlCount = "SELECT COUNT(*) FROM cotizaciones c 
             JOIN clientes cl ON c.id_cliente = cl.id_cliente
             JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo" . 
             (!empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "");

$stmt = $conex->prepare($sqlCount);
$stmt->execute($params);
$total = $stmt->fetchColumn();
$totalPaginas = ceil($total / $porPagina);

// Consulta principal con paginación
$offset = ($pagina - 1) * $porPagina;
$sql .= " ORDER BY c.fecha_cotizacion DESC LIMIT $offset, $porPagina";

$stmt = $conex->prepare($sql);
$stmt->execute($params);
$cotizaciones = $stmt->fetchAll();


require_once __DIR__ . '/../../includes/head.php';
$title = 'Gestión de Cotizaciones | Nacional Tapizados';
include __DIR__ . '/../../includes/navbar.php';
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
        <div class="row g-3">
            <div class="col-md-4">
                <label for="busqueda" class="form-label">Buscar (cliente, placa o estado)</label>
                <input type="text" class="form-control" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Buscar...">
            </div>
            <div class="col-md-3">
                <label for="fecha_inicio" class="form-label">Fecha desde</label>
                <input type="date" class="form-control" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>">
            </div>
            <div class="col-md-3">
                <label for="fecha_fin" class="form-label">Fecha hasta</label>
                <input type="date" class="form-control" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i> Buscar
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
                        <a class="page-link" href="?<?= 
                            http_build_query(array_merge(
                                $_GET,
                                ['pagina' => $pagina-1]
                            )) 
                        ?>">
                            Anterior
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= 
                            http_build_query(array_merge(
                                $_GET,
                                ['pagina' => $i]
                            )) 
                        ?>">
                            <?= $i ?>
                        </a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($pagina < $totalPaginas): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= 
                            http_build_query(array_merge(
                                $_GET,
                                ['pagina' => $pagina+1]
                            )) 
                        ?>">
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
    // Confirmación antes de eliminar
    document.querySelectorAll('.btn-danger').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('¿Está seguro de eliminar esta cotización?')) {
                e.preventDefault();
            }
        });
    });
</script>
</body>
</html>