<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos (solo Admin y Vendedor)
if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Inicializar variables de sesión para mensajes si no existen
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
    $_SESSION['tipo_mensaje'] = '';
}

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    
    try {
        // Verificar si tiene vehículos asociados
        $stmt = $conex->prepare("SELECT COUNT(*) FROM cliente_vehiculo WHERE id_cliente = ?");
        $stmt->execute([$id]);
        $tieneVehiculos = $stmt->fetchColumn();
        
        if ($tieneVehiculos > 0) {
            $_SESSION['mensaje'] = 'No se puede eliminar: cliente tiene vehículos asociados';
            $_SESSION['tipo_mensaje'] = 'danger';
        } else {
            $stmt = $conex->prepare("DELETE FROM clientes WHERE id_cliente = ?");
            if ($stmt->execute([$id])) {
                $_SESSION['mensaje'] = 'Cliente eliminado correctamente';
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
$offset = ($pagina - 1) * $porPagina;
$sql .= " $where ORDER BY nombre_cliente ASC LIMIT $offset, $porPagina";

$stmt = $conex->prepare($sql);
if (!empty($busqueda)) {
    $stmt->execute($params);
} else {
    $stmt->execute();
}
$clientes = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/head.php';
$title = 'Gestión de Clientes | Nacional Tapizados';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../../includes/head.php'; ?>
    <title>Gestión de Clientes | Nacional Tapizados</title>
    <style>
        body {
            background-image: url('https://pfst.cf2.poecdn.net/base/image/fe72e5f0bf336b4faca086bc6a42c20a45e904d165e796b52eca655a143283b8?w=1024&h=768&pmaid=426747789');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Roboto', sans-serif;
            color: #333;
        }

        .main-content {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin: 1rem;
            padding: 2rem;
            min-height: calc(100vh - 2rem);
        }

        .card {
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: rgba(255, 255, 255, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.25rem 1.5rem;
            border-radius: 12px 12px 0 0 !important;
        }

        .table {
            color: #fff;
            margin-bottom: 0;
        }

        .table th {
            background-color: rgba(140, 74, 63, 0.3);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .table td, .table th {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .btn-primary {
            background-color: rgba(140, 74, 63, 0.8);
            border-color: rgba(140, 74, 63, 0.9);
        }

        .btn-primary:hover {
            background-color: rgba(140, 74, 63, 1);
            border-color: rgba(140, 74, 63, 1);
        }

        .btn-outline-secondary {
            color: rgba(255, 255, 255, 0.8);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .btn-outline-secondary:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .btn-outline-danger {
            color: rgba(255, 107, 107, 0.8);
            border-color: rgba(255, 107, 107, 0.3);
        }

        .btn-outline-danger:hover {
            background-color: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
        }

        .page-link {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
        }

        .page-item.active .page-link {
            background-color: rgba(140, 74, 63, 0.8);
            border-color: rgba(140, 74, 63, 0.9);
        }

        .page-item.disabled .page-link {
            background-color: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.3);
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: rgba(140, 74, 63, 0.5);
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(140, 74, 63, 0.25);
        }

        .alert {
            backdrop-filter: blur(8px);
        }

        h1, h2, h3, h4, h5, h6 {
            color: white;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .text-muted {
            color: rgba(255, 255, 255, 0.6) !important;
        }

        .border-bottom {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .user-role-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            color: white;
            font-weight: 500;
            font-size: 0.9rem;
        }
    </style>
</head>

<body class="bg-transparent">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

            <!-- Contenido principal -->
            <main class="col-md-9 ms-sm-auto col-lg-11 px-md-4">
                <div class="main-content">
                    <!-- Barra superior -->
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">
                            <i class="fas fa-users me-2"></i>Gestión de Clientes
                        </h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <div class="btn-group me-2">
                                <a href="crear.php" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus-circle me-1"></i> Nuevo Cliente
                                </a>
                            </div>
                            <div class="text-muted small">
                                Rol actual: <span class="user-role-badge"><?= isAdmin() ? 'Administrador' : 'Vendedor' ?></span>
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
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" 
                                        placeholder="Buscar por nombre o correo">
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-primary w-100" type="submit">
                                        <i class="fas fa-search me-1"></i> Buscar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tabla de clientes -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Correo</th>
                                            <th>Teléfono</th>
                                            <th>Registro</th>
                                            <th class="text-end">Acciones</th>
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
                                                    <td class="text-end">
                                                        <div class="btn-group" role="group">
                                                            <a href="editar.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-sm btn-outline-secondary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="index.php?eliminar=<?= $cliente['id_cliente'] ?>" 
                                                                class="btn btn-sm btn-outline-danger"
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
                            </div>

                            <!-- Paginación -->
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