<?php
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

if (!isAdmin() && !isTecnico()) {
    header('Location: ../dashboard.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

try {
    // Obtener datos del trabajo
    $sql = "SELECT t.*, c.id_cotizacion, cl.nombre_cliente, cl.telefono_cliente, 
            v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
            u.nombre_completo as nombre_tecnico
            FROM trabajos t
            JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
            JOIN clientes cl ON c.id_cliente = cl.id_cliente
            JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
            JOIN usuarios u ON c.id_usuario = u.id_usuario
            WHERE t.id_trabajos = ?";
    $stmt = $conex->prepare($sql);
    $stmt->execute([$id]);
    $trabajo = $stmt->fetch();

    if (!$trabajo) {
        header('Location: index.php?error=Trabajo no encontrado');
        exit;
    }

    // Obtener servicios de la cotización asociada
    $sqlServicios = "SELECT s.nombre_servicio, cs.precio 
                    FROM cotizacion_servicios cs
                    JOIN servicios s ON cs.id_servicio = s.id_servicio
                    WHERE cs.id_cotizacion = ?";
    $stmtServicios = $conex->prepare($sqlServicios);
    $stmtServicios->execute([$trabajo['id_cotizacion']]);
    $servicios = $stmtServicios->fetchAll();

    // Obtener fotos (si es un string con la ruta)
    $fotos = [];
    if (!empty($trabajo['fotos'])) {
        $fotos = explode(',', $trabajo['fotos']);
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al obtener datos del trabajo: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../../includes/head.php';
$title = 'Detalles del Trabajo | Nacional Tapizados';
include __DIR__ . '/../../includes/navbar.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-tools me-2"></i>Trabajo #<?= $trabajo['id_trabajos'] ?></h1>
        <div>
            <a href="index.php" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
            <a href="editar.php?id=<?= $trabajo['id_trabajos'] ?>" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Cliente:</strong> <?= htmlspecialchars($trabajo['nombre_cliente']) ?></p>
                    <p><strong>Teléfono:</strong> <?= htmlspecialchars($trabajo['telefono_cliente']) ?></p>
                    <p><strong>Vehículo:</strong> <?= htmlspecialchars($trabajo['marca_vehiculo']) ?> <?= htmlspecialchars($trabajo['modelo_vehiculo']) ?></p>
                    <p><strong>Placa:</strong> <?= htmlspecialchars($trabajo['placa_vehiculo']) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Cotización:</strong> #<?= $trabajo['id_cotizacion'] ?></p>
                    <p><strong>Técnico:</strong> <?= htmlspecialchars($trabajo['nombre_tecnico']) ?></p>
                    <p><strong>Fecha inicio:</strong> <?= date('d/m/Y', strtotime($trabajo['fecha_inicio'])) ?></p>
                    <p><strong>Fecha fin:</strong> <?= $trabajo['fecha_fin'] ? date('d/m/Y', strtotime($trabajo['fecha_fin'])) : '--' ?></p>
                    <p><strong>Estado:</strong> 
                        <span class="badge bg-<?= 
                            $trabajo['estado'] == 'Entregado' ? 'success' : 
                            ($trabajo['estado'] == 'Cancelado' ? 'danger' : 
                            ($trabajo['estado'] == 'En progreso' ? 'primary' : 'warning')) 
                        ?>">
                            <?= $trabajo['estado'] ?>
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
                </table>
            </div>
        </div>
    </div>

    <?php if (!empty($trabajo['notas'])): ?>
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notas</h5>
        </div>
        <div class="card-body">
            <p><?= nl2br(htmlspecialchars($trabajo['notas'])) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($fotos)): ?>
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Fotos del Trabajo</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($fotos as $foto): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($foto) ?>" class="card-img-top img-thumbnail" alt="Foto del trabajo">
                        <div class="card-body text-center">
                            <a href="<?= htmlspecialchars($foto) ?>" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fas fa-expand me-1"></i> Ampliar
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>