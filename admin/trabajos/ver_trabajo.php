<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

if (!isAdmin() && !isTechnician()) {
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
    $sql = "SELECT t.*, c.id_cotizacion, cl.nombre_cliente, cl.telefono_cliente, cl.correo_cliente,
                   v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
                   u.nombre_completo as tecnico, u2.nombre_completo as creador
            FROM trabajos t
            JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
            JOIN clientes cl ON c.id_cliente = cl.id_cliente
            JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
            LEFT JOIN usuarios u ON t.id_tecnico = u.id_usuario
            LEFT JOIN usuarios u2 ON t.id_creador = u2.id_usuario
            WHERE t.id_trabajos = ?";
    $stmt = $conex->prepare($sql);
    $stmt->execute([$id]);
    $trabajo = $stmt->fetch();
    
    if (!$trabajo) {
        header('Location: index.php?error=Trabajo no encontrado');
        exit;
    }
    
    // Obtener fotos del trabajo
    $fotos = $conex->prepare("SELECT id, ruta FROM trabajo_fotos WHERE id_trabajo = ?")->execute([$id])->fetchAll();
} catch (PDOException $e) {
    header('Location: index.php?error=Error al obtener trabajo');
    exit;
}

require_once __DIR__ . '/../../includes/head.php';
$title = 'Nacional Tapizados - Ver Trabajo';
?>
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-tools me-2"></i>Trabajo #<?= $trabajo['id_trabajos'] ?></h1>
            <div>
                <a href="index.php" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Volver
                </a>
                <?php if (isAdmin()): ?>
                <a href="editar.php?id=<?= $trabajo['id_trabajos'] ?>" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i>Editar
                </a>
                <?php endif; ?>
                <a href="../cotizaciones/ver.php?id=<?= $trabajo['id_cotizacion'] ?>" class="btn btn-info">
                    <i class="fas fa-file-invoice-dollar me-1"></i>Ver Cotización
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
                        <p><strong>Correo:</strong> <?= htmlspecialchars($trabajo['correo_cliente']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Vehículo:</strong> <?= htmlspecialchars($trabajo['marca_vehiculo']) ?> <?= htmlspecialchars($trabajo['modelo_vehiculo']) ?></p>
                        <p><strong>Placa:</strong> <?= htmlspecialchars($trabajo['placa_vehiculo']) ?></p>
                        <p><strong>Cotización:</strong> #<?= $trabajo['id_cotizacion'] ?></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <p><strong>Técnico:</strong> <?= $trabajo['tecnico'] ?? '--' ?></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Fecha Inicio:</strong> <?= date('d/m/Y', strtotime($trabajo['fecha_inicio'])) ?></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Fecha Fin:</strong> <?= $trabajo['fecha_fin'] ? date('d/m/Y', strtotime($trabajo['fecha_fin'])) : '--' ?></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <p><strong>Estado:</strong> 
                            <span class="badge bg-<?= 
                                $trabajo['estado'] == 'Entregado' ? 'success' : 
                                ($trabajo['estado'] == 'En progreso' ? 'primary' : 
                                ($trabajo['estado'] == 'Cancelado' ? 'danger' : 'warning')) 
                            ?>">
                                <?= $trabajo['estado'] ?>
                            </span>
                        </p>
                    </div>
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
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Fotos del Trabajo</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($fotos as $foto): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <img src="<?= htmlspecialchars($foto['ruta']) ?>" class="card-img-top" alt="Foto del trabajo">
                            <div class="card-footer text-center">
                                <a href="<?= htmlspecialchars($foto['ruta']) ?>" target="_blank" class="btn btn-sm btn-info me-2">
                                    <i class="fas fa-expand"></i> Ampliar
                                </a>
                                <?php if (isAdmin()): ?>
                                <a href="eliminar_foto.php?id=<?= $foto['id'] ?>&trabajo=<?= $trabajo['id_trabajos'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta foto?')">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (isAdmin() || (isTechnician() && $trabajo['estado'] !== 'Entregado' && $trabajo['estado'] !== 'Cancelado')): ?>
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Agregar Fotos</h5>
            </div>
            <div class="card-body">
                <form action="agregar_fotos.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_trabajo" value="<?= $trabajo['id_trabajos'] ?>">
                    
                    <div class="mb-3">
                        <label for="nuevas_fotos" class="form-label">Seleccionar Fotos</label>
                        <input type="file" class="form-control" id="nuevas_fotos" name="nuevas_fotos[]" multiple accept="image/*" required>
                        <div class="form-text">Puede seleccionar múltiples fotos</div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>Subir Fotos
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>