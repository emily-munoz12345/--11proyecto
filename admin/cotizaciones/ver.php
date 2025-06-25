<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

try {
    // Obtener datos de la cotización
    $sql = "SELECT c.*, cl.nombre_cliente, cl.telefono_cliente, cl.correo_cliente,
                   v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
                   u.nombre_completo as nombre_vendedor
            FROM cotizaciones c
            JOIN clientes cl ON c.id_cliente = cl.id_cliente
            JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
            JOIN usuarios u ON c.id_usuario = u.id_usuario
            WHERE c.id_cotizacion = ?";
    $stmt = $conex->prepare($sql);
    $stmt->execute([$id]);
    $cotizacion = $stmt->fetch();
    
    if (!$cotizacion) {
        header('Location: index.php?error=Cotización no encontrada');
        exit;
    }
    
    // Obtener servicios de la cotización
    $sqlServicios = "SELECT s.nombre_servicio, cs.precio 
                     FROM cotizacion_servicios cs
                     JOIN servicios s ON cs.id_servicio = s.id_servicio
                     WHERE cs.id_cotizacion = ?";
    $stmtServicios = $conex->prepare($sqlServicios);
    $stmtServicios->execute([$id]);
    $servicios = $stmtServicios->fetchAll();
    
    // Verificar si ya existe un trabajo para esta cotización
    $trabajo = $conex->prepare("SELECT * FROM trabajos WHERE id_cotizacion = ?")->execute([$id])->fetch();
} catch (PDOException $e) {
    header('Location: index.php?error=Error al obtener cotización');
    exit;
}

require_once __DIR__ . '/../../includes/head.php';
$title = 'Nacional Tapizados - Ver Cotización';
?>
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-file-invoice-dollar me-2"></i>Cotización #<?= $cotizacion['id_cotizacion'] ?></h1>
            <div>
                <a href="index.php" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Volver
                </a>
                <a href="editar.php?id=<?= $cotizacion['id_cotizacion'] ?>" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i>Editar
                </a>
                <?php if (!$trabajo): ?>
                <a href="../trabajos/crear.php?cotizacion=<?= $cotizacion['id_cotizacion'] ?>" class="btn btn-primary">
                    <i class="fas fa-hammer me-1"></i>Crear Trabajo
                </a>
                <?php else: ?>
                <a href="../trabajos/ver.php?id=<?= $trabajo['id_trabajos'] ?>" class="btn btn-info">
                    <i class="fas fa-eye me-1"></i>Ver Trabajo
                </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Cliente:</strong> <?= htmlspecialchars($cotizacion['nombre_cliente']) ?></p>
                        <p><strong>Teléfono:</strong> <?= htmlspecialchars($cotizacion['telefono_cliente']) ?></p>
                        <p><strong>Correo:</strong> <?= htmlspecialchars($cotizacion['correo_cliente']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Vehículo:</strong> <?= htmlspecialchars($cotizacion['marca_vehiculo']) ?> <?= htmlspecialchars($cotizacion['modelo_vehiculo']) ?></p>
                        <p><strong>Placa:</strong> <?= htmlspecialchars($cotizacion['placa_vehiculo']) ?></p>
                        <p><strong>Vendedor:</strong> <?= htmlspecialchars($cotizacion['nombre_vendedor']) ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($cotizacion['fecha_cotizacion'])) ?></p>
                        <p><strong>Estado:</strong> 
                            <span class="badge bg-<?= 
                                $cotizacion['estado_cotizacion'] == 'Aprobado' ? 'success' : 
                                ($cotizacion['estado_cotizacion'] == 'Rechazada' ? 'danger' : 
                                ($cotizacion['estado_cotizacion'] == 'Completada' ? 'primary' : 'warning')) 
                            ?>">
                                <?= $cotizacion['estado_cotizacion'] ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Servicios</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th>Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicios as $servicio): ?>
                            <tr>
                                <td><?= htmlspecialchars($servicio['nombre_servicio']) ?></td>
                                <td>$ <?= number_format($servicio['precio'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Subtotal</th>
                                <td>$ <?= number_format($cotizacion['subtotal_cotizacion'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <th>IVA (19%)</th>
                                <td>$ <?= number_format($cotizacion['iva'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <td>$ <?= number_format($cotizacion['total_cotizacion'], 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <?php if (!empty($cotizacion['notas_cotizacion'])): ?>
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notas Adicionales</h5>
            </div>
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($cotizacion['notas_cotizacion'])) ?></p>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="mt-4 text-center">
            <button class="btn btn-success me-2" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Imprimir Cotización
            </button>
            <a href="generar_pdf.php?id=<?= $cotizacion['id_cotizacion'] ?>" class="btn btn-danger">
                <i class="fas fa-file-pdf me-1"></i>Generar PDF
            </a>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>