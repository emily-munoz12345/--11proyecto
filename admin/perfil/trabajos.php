<?php
require_once __DIR__ . '/../../php/auth.php';
require_once __DIR__ . '/../../php/conexion.php';
requireAuth();

$usuario_id = getUserId();

try {
    // Verificar si el usuario tiene permiso para ver trabajos
    if (!isTechnician() && !isAdmin()) {
        die("No tienes permiso para acceder a esta página");
    }

    // Obtener información del usuario
    $stmt = $conex->prepare("SELECT nombre_completo FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();

    // Obtener todos los trabajos asignados al usuario con paginación
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;

    // Consulta para obtener el total de trabajos
    $stmt = $conex->prepare("SELECT COUNT(*) as total FROM trabajos t 
                            JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion 
                            WHERE c.id_usuario = ?");
    $stmt->execute([$usuario_id]);
    $total_trabajos = $stmt->fetch()['total'];
    $total_pages = ceil($total_trabajos / $per_page);

    // Consulta para obtener los trabajos paginados
    $stmt = $conex->prepare("SELECT t.*, c.id_cliente, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo
                           FROM trabajos t
                           JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
                           JOIN clientes cl ON c.id_cliente = cl.id_cliente
                           JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
                           WHERE c.id_usuario = ?
                           ORDER BY t.fecha_inicio DESC
                           LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $usuario_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $per_page, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $trabajos = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error al obtener trabajos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Trabajos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="admin-body">
    <?php include '../includes/head.php'; ?>
            <?php include __DIR__ . '../../includes/sidebar.php'; ?>
    <div class="content-wrapper">
        <main class="trabajos-container">
            <h1 class="page-title">
                <i class="fas fa-tools"></i> Mis Trabajos
            </h1>
            
            <div class="filters">
                <div class="filter-group">
                    <label for="estado">Filtrar por estado:</label>
                    <select id="estado" onchange="filterTrabajos()">
                        <option value="">Todos</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="En progreso">En progreso</option>
                        <option value="Entregado">Entregado</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="fecha">Filtrar por fecha:</label>
                    <select id="fecha" onchange="filterTrabajos()">
                        <option value="">Todas</option>
                        <option value="7">Última semana</option>
                        <option value="30">Último mes</option>
                        <option value="90">Últimos 3 meses</option>
                    </select>
                </div>
            </div>
                        <a href="exportar_trabajos_exel.php" class="export-btn" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </a>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Vehículo</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th>Fotos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($trabajos)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">No hay trabajos asignados</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($trabajos as $trabajo): ?>
                            <tr>
                                <td><?= $trabajo['id_trabajos'] ?></td>
                                <td><?= htmlspecialchars($trabajo['nombre_cliente']) ?></td>
                                <td><?= htmlspecialchars($trabajo['marca_vehiculo']) ?> <?= htmlspecialchars($trabajo['modelo_vehiculo']) ?></td>
                                <td><?= date('d/m/Y', strtotime($trabajo['fecha_inicio'])) ?></td>
                                <td><?= $trabajo['fecha_fin'] != '0000-00-00' ? date('d/m/Y', strtotime($trabajo['fecha_fin'])) : 'Pendiente' ?></td>
                                <td>
                                    <span class="status-badge <?= strtolower(str_replace(' ', '-', $trabajo['estado'])) ?>">
                                        <?= $trabajo['estado'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($trabajo['fotos'])): ?>
                                        <?php 
                                            $fotos = explode(',', $trabajo['fotos']);
                                            foreach ($fotos as $foto): 
                                                if (!empty($foto)):
                                        ?>
                                            <img src="<?= htmlspecialchars(trim($foto)) ?>" class="photo-preview" onclick="openModal('<?= htmlspecialchars(trim($foto)) ?>')">
                                        <?php 
                                                endif;
                                            endforeach; 
                                        ?>
                                    <?php else: ?>
                                        Sin fotos
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="detalle_trabajo.php?id=<?= $trabajo['id_trabajos'] ?>" class="btn btn-sm" title="Ver detalles">
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

    <!-- Modal para visualización de fotos -->
    <div id="imageModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script>
        function filterTrabajos() {
            const estado = document.getElementById('estado').value;
            const fecha = document.getElementById('fecha').value;
            
            let url = 'trabajos_usuario.php?';
            if (estado) url += `estado=${estado}&`;
            if (fecha) url += `fecha=${fecha}&`;
            
            window.location.href = url.slice(0, -1); // Eliminar el último & o ?
        }
        
        function openModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            
            modal.style.display = "block";
            modalImg.src = imageSrc;
        }
        
        function closeModal() {
            document.getElementById('imageModal').style.display = "none";
        }
        
        // Cerrar modal al hacer clic fuera de la imagen
        window.onclick = function(event) {
            const modal = document.getElementById('imageModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
                function exportToExcel() {
            // Implementar lógica de exportación a Excel
            alert('Exportar a Excel será implementado próximamente');
        }
    </script>
</body>
</html>