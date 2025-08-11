<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos (solo Admin)
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
        $stmt = $conex->prepare("DELETE FROM mensajes_contacto WHERE id = ?");
        if ($stmt->execute([$id])) {
            $_SESSION['mensaje'] = 'Mensaje eliminado correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
        }
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'Error al eliminar: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    header('Location: mensajes.php');
    exit;
}

// Búsqueda y paginación
$busqueda = $_GET['busqueda'] ?? '';
$pagina = max(1, intval($_GET['pagina'] ?? 1));
$porPagina = 10;

$sql = "SELECT * FROM mensajes_contacto";
$params = [];
$where = '';

if (!empty($busqueda)) {
    $where = " WHERE nombre LIKE ? OR email LIKE ? OR mensaje LIKE ?";
    $params = ["%$busqueda%", "%$busqueda%", "%$busqueda%"];
}

// Contar total para paginación
$sqlCount = "SELECT COUNT(*) FROM mensajes_contacto $where";
$stmt = $conex->prepare($sqlCount);
$stmt->execute($params);
$totalMensajes = $stmt->fetchColumn();
$totalPaginas = ceil($totalMensajes / $porPagina);

// Obtener mensajes
$offset = ($pagina - 1) * $porPagina;
$sql .= " $where ORDER BY fecha DESC LIMIT $offset, $porPagina";

$stmt = $conex->prepare($sql);
if (!empty($busqueda)) {
    $stmt->execute($params);
} else {
    $stmt->execute();
}
$mensajes = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/head.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../../includes/head.php'; ?>
    <title>Buzón de Mensajes | Nacional Tapizados</title>
    <style>
        /* Mantener todos los estilos del código de referencia */
        /* Agregar estilos específicos para mensajes */
        .mensaje-preview {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }
        .no-leido {
            font-weight: bold;
            background-color: rgba(140, 74, 63, 0.1) !important;
        }
        .badge-no-leido {
            background-color: rgba(140, 74, 63, 0.8);
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
                            <i class="fas fa-inbox me-2"></i>Buzón de Mensajes
                        </h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <div class="text-muted small">
                                Rol actual: <span class="user-role-badge">Administrador</span>
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
                                        placeholder="Buscar por nombre, email o mensaje">
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-primary w-100" type="submit">
                                        <i class="fas fa-search me-1"></i> Buscar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tabla de mensajes -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Mensaje</th>
                                            <th>Fecha</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($mensajes) > 0): ?>
                                            <?php foreach ($mensajes as $mensaje): ?>
                                                <tr class="<?= ($mensaje['leido'] ?? 0) ? '' : 'no-leido' ?>">
                                                    <td><?= $mensaje['id'] ?></td>
                                                    <td><?= htmlspecialchars($mensaje['nombre']) ?></td>
                                                    <td><?= htmlspecialchars($mensaje['email']) ?></td>
                                                    <td>
                                                        <div class="mensaje-preview" title="<?= htmlspecialchars($mensaje['mensaje']) ?>">
                                                            <?= htmlspecialchars($mensaje['mensaje']) ?>
                                                        </div>
                                                        <?php if (!($mensaje['leido'] ?? 0)): ?>
                                                            <span class="badge badge-no-leido">Nuevo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= date('d/m/Y H:i', strtotime($mensaje['fecha'])) ?></td>
                                                    <td class="text-end">
                                                        <div class="btn-group" role="group">
                                                            <a href="ver_mensaje.php?id=<?= $mensaje['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="mensajes.php?eliminar=<?= $mensaje['id'] ?>" 
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('¿Eliminar este mensaje?')">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4">No hay mensajes en el buzón</td>
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
</body>
</html>