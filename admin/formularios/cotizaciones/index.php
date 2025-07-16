<?php
require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Inicializar variables de sesión para mensajes si no existen
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
    $_SESSION['tipo_mensaje'] = '';
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

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    
    try {
        $stmt = $conex->prepare("DELETE FROM cotizaciones WHERE id_cotizacion = ?");
        if ($stmt->execute([$id])) {
            $_SESSION['mensaje'] = 'Cotización eliminada correctamente';
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
?>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

            <!-- Contenido principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- Barra superior -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Gestión de Cotizaciones
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="crear.php" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus-circle me-1"></i> Nueva Cotización
                            </a>
                        </div>
                        <div class="text-muted small">
                            Rol actual: <strong><?= isAdmin() ? 'Administrador' : 'Vendedor' ?></strong>
                        </div>
                    </div>
                </div>

                <!-- Mensajes -->
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

                <!-- Buscador -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form class="row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" 
                                    placeholder="Buscar (cliente, placa o estado)">
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>" placeholder="Fecha desde">
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>" placeholder="Fecha hasta">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100" type="submit">
                                    <i class="fas fa-search me-1"></i> Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla de cotizaciones -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Vehículo</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Vendedor</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($cotizaciones) > 0): ?>
                                        <?php foreach ($cotizaciones as $cotizacion): ?>
                                            <tr>
                                                <td><?= $cotizacion['id_cotizacion'] ?></td>
                                                <td><?= htmlspecialchars($cotizacion['nombre_cliente']) ?></td>
                                                <td>
                                                    <?= htmlspecialchars($cotizacion['marca_vehiculo']) ?> 
                                                    <?= htmlspecialchars($cotizacion['modelo_vehiculo']) ?>
                                                    <small class="text-muted d-block"><?= htmlspecialchars($cotizacion['placa_vehiculo']) ?></small>
                                                </td>
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
                                                <td class="text-end">
                                                    <div class="btn-group" role="group">
                                                        <a href="ver.php?id=<?= $cotizacion['id_cotizacion'] ?>" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="editar.php?id=<?= $cotizacion['id_cotizacion'] ?>" class="btn btn-sm btn-outline-secondary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                                <i class="fas fa-cog"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item" href="index.php?cambiar_estado&id=<?= $cotizacion['id_cotizacion'] ?>&estado=Aprobado">Aprobar</a></li>
                                                                <li><a class="dropdown-item" href="index.php?cambiar_estado&id=<?= $cotizacion['id_cotizacion'] ?>&estado=Rechazada">Rechazar</a></li>
                                                                <li><a class="dropdown-item" href="index.php?cambiar_estado&id=<?= $cotizacion['id_cotizacion'] ?>&estado=Completada">Completar</a></li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <a class="dropdown-item text-danger" href="index.php?eliminar=<?= $cotizacion['id_cotizacion'] ?>" 
                                                                       onclick="return confirm('¿Eliminar esta cotización?')">
                                                                        <i class="fas fa-trash-alt me-1"></i> Eliminar
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">No se encontraron cotizaciones</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <?php if ($totalPaginas > 1): ?>
                            <nav aria-label="Paginación" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($pagina > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?<?= 
                                                http_build_query(array_merge(
                                                    $_GET,
                                                    ['pagina' => $pagina-1]
                                                )) 
                                            ?>">
                                                <i class="fas fa-chevron-left"></i>
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
    <script>
        // Toggle sidebar para móviles
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.querySelector('[data-bs-toggle="collapse"]');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.querySelector('#sidebarMenu').classList.toggle('show');
                });
            }
        });
    </script>
</body>
</html>