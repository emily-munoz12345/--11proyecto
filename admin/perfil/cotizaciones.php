<?php
require_once __DIR__ . '/../../php/auth.php';
require_once __DIR__ . '/../../php/conexion.php';
requireAuth();

$usuario_id = getUserId();

try {
    // Verificar si el usuario tiene permiso para ver cotizaciones
    if (!isSeller() && !isAdmin()) {
        die("No tienes permiso para acceder a esta página");
    }

    // Obtener información del usuario
    $stmt = $conex->prepare("SELECT nombre_completo FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();

    // Obtener todas las cotizaciones del usuario con paginación
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;

    // Consulta para obtener el total de cotizaciones
    $stmt = $conex->prepare("SELECT COUNT(*) as total FROM cotizaciones WHERE id_usuario = ?");
    $stmt->execute([$usuario_id]);
    $total_cotizaciones = $stmt->fetch()['total'];
    $total_pages = ceil($total_cotizaciones / $per_page);

    // Consulta para obtener las cotizaciones paginadas
    $stmt = $conex->prepare("SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo 
                           FROM cotizaciones c 
                           JOIN clientes cl ON c.id_cliente = cl.id_cliente 
                           JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo 
                           WHERE c.id_usuario = ? 
                           ORDER BY c.fecha_cotizacion DESC 
                           LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $usuario_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $per_page, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $cotizaciones = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error al obtener cotizaciones: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Cotizaciones</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="admin-body">
    <?php include '../includes/head.php'; ?>
            <?php include __DIR__ . '../../includes/sidebar.php'; ?>
    <div class="content-wrapper">
        <main class="cotizaciones-container">
            <h1 class="page-title">
                <i class="fas fa-file-invoice-dollar"></i> Mis Cotizaciones
            </h1>
            
            <div class="filters">
                <div class="filter-group">
                    <label for="estado">Filtrar por estado:</label>
                    <select id="estado" onchange="filterCotizaciones()">
                        <option value="">Todos</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Aprobado">Aprobado</option>
                        <option value="Rechazada">Rechazada</option>
                        <option value="Completada">Completada</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="fecha">Filtrar por fecha:</label>
                    <select id="fecha" onchange="filterCotizaciones()">
                        <option value="">Todas</option>
                        <option value="7">Última semana</option>
                        <option value="30">Último mes</option>
                        <option value="90">Últimos 3 meses</option>
                    </select>
                </div>
            </div>
            
            <a href="exportar_cotizaciones_exel.php" class="export-btn" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </a>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Vehículo</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cotizaciones)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No hay cotizaciones registradas</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cotizaciones as $cotizacion): ?>
                            <tr>
                                <td><?= $cotizacion['id_cotizacion'] ?></td>
                                <td><?= date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])) ?></td>
                                <td><?= htmlspecialchars($cotizacion['nombre_cliente']) ?></td>
                                <td><?= htmlspecialchars($cotizacion['marca_vehiculo']) ?> <?= htmlspecialchars($cotizacion['modelo_vehiculo']) ?></td>
                                <td>$<?= number_format($cotizacion['total_cotizacion'], 2) ?></td>
                                <td>
                                    <span class="status-badge <?= strtolower($cotizacion['estado_cotizacion']) ?>">
                                        <?= $cotizacion['estado_cotizacion'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="detalle_cotizacion.php?id=<?= $cotizacion['id_cotizacion'] ?>" class="btn btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="page-link">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="page-link <?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>" class="page-link">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script>
        function filterCotizaciones() {
            const estado = document.getElementById('estado').value;
            const fecha = document.getElementById('fecha').value;
            
            let url = 'cotizaciones_usuario.php?';
            if (estado) url += `estado=${estado}&`;
            if (fecha) url += `fecha=${fecha}&`;
            
            window.location.href = url.slice(0, -1); // Eliminar el último & o ?
        }
        
        function exportToExcel() {
            // Implementar lógica de exportación a Excel
            alert('Exportar a Excel será implementado próximamente');
        }
    </script>
</body>
</html>