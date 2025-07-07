<?php
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

// Verificar permisos - optimizado
if (!isAdmin() && !isTecnico()) {
    $_SESSION['mensaje'] = 'No tienes permisos para acceder a esta sección';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ../dashboard.php');
    exit;
}

// Definir estados permitidos según la base de datos
$estadosPermitidos = ['Pendiente', 'En progreso', 'Entregado', 'Cancelado'];

// Obtener y validar parámetros GET
$busqueda = isset($_GET['busqueda']) ? trim(strip_tags($_GET['busqueda'])) : '';
$estado = isset($_GET['estado']) && in_array($_GET['estado'], $estadosPermitidos) 
          ? $_GET['estado'] : '';
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$porPagina = 10;

try {
    // Consulta base con todos los JOINs necesarios
    $baseQuery = "FROM trabajos t
                 JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
                 JOIN clientes cl ON c.id_cliente = cl.id_cliente
                 JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
                 JOIN usuarios u ON c.id_usuario = u.id_usuario";
    
    $params = [];
    $conditions = [];

    // Búsqueda por texto (nombre cliente o placa) - sanitizado
    if (!empty($busqueda)) {
        $conditions[] = "(cl.nombre_cliente LIKE ? OR v.placa_vehiculo LIKE ?)";
        array_push($params, "%$busqueda%", "%$busqueda%");
    }

    // Filtro por estado
    if (!empty($estado)) {
        $conditions[] = "t.estado = ?";
        $params[] = $estado;
    }

    // Construir WHERE
    $whereClause = !empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "";

    // Consulta para contar (usa los mismos JOINs que la consulta principal)
    $sqlCount = "SELECT COUNT(*) " . $baseQuery . $whereClause;
    $stmt = $conex->prepare($sqlCount);
    $stmt->execute($params);
    $total = $stmt->fetchColumn();
    $totalPaginas = max(1, ceil($total / $porPagina)); // Asegurar al menos 1 página

    // Consulta principal con paginación
    $sql = "SELECT t.*, c.id_cotizacion, cl.nombre_cliente, v.marca_vehiculo, 
                   v.modelo_vehiculo, v.placa_vehiculo, u.nombre_completo as nombre_tecnico 
            " . $baseQuery . $whereClause . " 
            ORDER BY t.fecha_inicio DESC 
            LIMIT " . (($pagina - 1) * $porPagina) . ", $porPagina";

    $stmt = $conex->prepare($sql);
    $stmt->execute($params);
    $trabajos = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Error en la consulta de trabajos: " . $e->getMessage());
    $_SESSION['mensaje'] = 'Ocurrió un error al cargar los trabajos';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../../includes/head.php';
$title = 'Gestión de Trabajos | Nacional Tapizados';
include __DIR__ . '/../../includes/navbar.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-tools me-2"></i>Trabajos</h1>
        <?php if (isAdmin()): ?>
        <a href="crear.php" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Trabajo
        </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?> alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['mensaje']) ?>
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
                        <input type="text" class="form-control" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Nombre cliente o placa">
                    </div>
                    <div class="col-md-5">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" name="estado">
                            <option value="">Todos los estados</option>
                            <?php foreach ($estadosPermitidos as $estadoOption): ?>
                                <option value="<?= $estadoOption ?>" <?= $estado == $estadoOption ? 'selected' : '' ?>>
                                    <?= $estadoOption ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>

            <?php if (count($trabajos) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Vehículo</th>
                            <th>Placa</th>
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
                            <td><?= htmlspecialchars($trabajo['placa_vehiculo']) ?></td>
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
                                    <a href="ver_trabajo.php?id=<?= $trabajo['id_trabajos'] ?>" class="btn btn-sm btn-info me-2" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if (isAdmin()): ?>
                                    <a href="editar.php?id=<?= $trabajo['id_trabajos'] ?>" class="btn btn-sm btn-warning me-2" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if ($trabajo['estado'] != 'Entregado' && $trabajo['estado'] != 'Cancelado'): ?>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" title="Cambiar estado">
                                            <i class="fas fa-cogs"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php foreach ($estadosPermitidos as $estadoOption): ?>
                                                <?php if ($estadoOption != $trabajo['estado'] && $estadoOption != 'Pendiente'): ?>
                                                    <li>
                                                        <a class="dropdown-item <?= $estadoOption == 'Cancelado' ? 'text-danger' : '' ?>" 
                                                           href="procesar.php?accion=cambiar_estado&id=<?= $trabajo['id_trabajos'] ?>&estado=<?= $estadoOption ?>">
                                                            <?= $estadoOption ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
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
                        <a class="page-link" href="?<?= 
                            htmlspecialchars(http_build_query(array_merge($_GET, ['pagina' => $pagina-1]))) 
                        ?>">
                            Anterior
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php 
                    // Mostrar solo un rango de páginas alrededor de la actual
                    $inicio = max(1, $pagina - 2);
                    $fin = min($totalPaginas, $pagina + 2);
                    
                    if ($inicio > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?' . 
                             htmlspecialchars(http_build_query(array_merge($_GET, ['pagina' => 1]))) . 
                             '">1</a></li>';
                        if ($inicio > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    
                    for ($i = $inicio; $i <= $fin; $i++): ?>
                    <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= 
                            htmlspecialchars(http_build_query(array_merge($_GET, ['pagina' => $i]))) 
                        ?>">
                            <?= $i ?>
                        </a>
                    </li>
                    <?php endfor; 
                    
                    if ($fin < $totalPaginas) {
                        if ($fin < $totalPaginas - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="?' . 
                             htmlspecialchars(http_build_query(array_merge($_GET, ['pagina' => $totalPaginas]))) . 
                             '">' . $totalPaginas . '</a></li>';
                    }
                    ?>

                    <?php if ($pagina < $totalPaginas): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= 
                            htmlspecialchars(http_build_query(array_merge($_GET, ['pagina' => $pagina+1]))) 
                        ?>">
                            Siguiente
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
            <?php else: ?>
            <div class="alert alert-info">
                No se encontraron trabajos con los filtros aplicados.
                <?php if (!empty($busqueda) || !empty($estado)): ?>
                <a href="index.php" class="alert-link">Mostrar todos</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Confirmación antes de cambiar estado a Cancelado
    document.querySelectorAll('.dropdown-item.text-danger').forEach(item => {
        item.addEventListener('click', function(e) {
            if (!confirm('¿Está seguro de cancelar este trabajo? Esta acción no se puede deshacer.')) {
                e.preventDefault();
            }
        });
    });
</script>
</body>
</html>