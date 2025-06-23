<?php
require_once '../../php/auth.php';
requireAuth();

// Verificar permisos (solo Admin y Vendedor)
if (!isAdmin() && !isSeller()) {
    header('Location: ../admin/dashboard.php');
    exit;
}

require_once '../../php/conexion.php';

// Mensajes de éxito/error
$mensaje = '';
$tipoMensaje = '';

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    
    try {
        // Verificar si tiene vehículos asociados
        $stmt = $conex->prepare("SELECT COUNT(*) FROM cliente_vehiculo WHERE id_cliente = ?");
        $stmt->execute([$id]);
        $tieneVehiculos = $stmt->fetchColumn();
        
        if ($tieneVehiculos > 0) {
            $mensaje = 'No se puede eliminar: cliente tiene vehículos asociados';
            $tipoMensaje = 'danger';
        } else {
            $stmt = $conex->prepare("DELETE FROM clientes WHERE id_cliente = ?");
            if ($stmt->execute([$id])) {
                $mensaje = 'Cliente eliminado correctamente';
                $tipoMensaje = 'success';
            }
        }
    } catch (PDOException $e) {
        $mensaje = 'Error al eliminar: ' . $e->getMessage();
        $tipoMensaje = 'danger';
    }
}

// Búsqueda y paginación
$busqueda = $_GET['busqueda'] ?? '';
$pagina = max(1, intval($_GET['pagina'] ?? 1));
$porPagina = 10;

$sql = "SELECT * FROM clientes";
$params = [];
$where = '';

if (!empty($busqueda)) {
    $where = " WHERE nombre_cliente LIKE ? OR correo_cliente LIKE ?";
    $params = ["%$busqueda%", "%$busqueda%"];
}

// Contar total para paginación
$sqlCount = "SELECT COUNT(*) FROM clientes $where";
$stmt = $conex->prepare($sqlCount);
$stmt->execute($params);
$totalClientes = $stmt->fetchColumn();
$totalPaginas = ceil($totalClientes / $porPagina);

// Obtener clientes
$sql .= " $where ORDER BY nombre_cliente ASC LIMIT :offset, :limit";
$stmt = $conex->prepare($sql);

foreach ($params as $i => $param) {
    $stmt->bindValue($i+1, $param);
}

$stmt->bindValue(':offset', ($pagina - 1) * $porPagina, PDO::PARAM_INT);
$stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
$stmt->execute();
$clientes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Nacional Tapizados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-users me-2"></i>Gestión de Clientes</h1>
            <a href="crear.php" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i>Nuevo Cliente
            </a>
        </div>
        
        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show">
                <?= $mensaje ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Buscador -->
        <form class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" 
                       placeholder="Buscar por nombre o correo">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
                <?php if (!empty($busqueda)): ?>
                    <a href="index.php" class="btn btn-outline-danger">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
        
        <!-- Tabla de clientes -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($clientes) > 0): ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?= $cliente['id_cliente'] ?></td>
                                <td><?= htmlspecialchars($cliente['nombre_cliente']) ?></td>
                                <td><?= htmlspecialchars($cliente['correo_cliente']) ?></td>
                                <td><?= htmlspecialchars($cliente['telefono_cliente']) ?></td>
                                <td><?= date('d/m/Y', strtotime($cliente['fecha_registro'])) ?></td>
                                <td>
                                    <div class="d-flex">
                                        <a href="editar.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-sm btn-warning me-2">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?eliminar=<?= $cliente['id_cliente'] ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('¿Eliminar este cliente?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">No se encontraron clientes</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Paginación -->
            <?php if ($totalPaginas > 1): ?>
                <nav aria-label="Paginación">
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

    <?php include '../includes/footer.php'; ?>
    
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