<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

if (!isAdmin()) {
    header('Location: ../dashboard.php');
    exit;
}

// Obtener cotización si se pasa como parámetro
$id_cotizacion = isset($_GET['cotizacion']) ? intval($_GET['cotizacion']) : 0;

// Obtener técnicos y cotizaciones aprobadas
try {
    $tecnicos = $conex->query("SELECT id_usuario, nombre_completo FROM usuarios WHERE id_rol = 2 ORDER BY nombre_completo")->fetchAll();
    
    $sqlCotizaciones = "SELECT c.id_cotizacion, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo 
                        FROM cotizaciones c
                        JOIN clientes cl ON c.id_cliente = cl.id_cliente
                        JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
                        WHERE c.estado_cotizacion = 'Aprobado' 
                        AND NOT EXISTS (SELECT 1 FROM trabajos t WHERE t.id_cotizacion = c.id_cotizacion)
                        ORDER BY c.fecha_cotizacion DESC";
    $cotizaciones = $conex->query($sqlCotizaciones)->fetchAll();
    
    // Si se especificó una cotización, obtener sus datos
    $cotizacion = null;
    if ($id_cotizacion > 0) {
        $stmt = $conex->prepare("SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo 
                                FROM cotizaciones c
                                JOIN clientes cl ON c.id_cliente = cl.id_cliente
                                JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
                                WHERE c.id_cotizacion = ? AND c.estado_cotizacion = 'Aprobado'");
        $stmt->execute([$id_cotizacion]);
        $cotizacion = $stmt->fetch();
        
        if (!$cotizacion) {
            $_SESSION['mensaje'] = 'La cotización no existe o no está aprobada';
            $_SESSION['tipo_mensaje'] = 'danger';
            header('Location: index.php');
            exit;
        }
    }
} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}
?>

<?php
require_once __DIR__ . '/../../includes/head.php';
$title = 'Nacional Tapizados - Nuevo Trabajo';
?>
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-tools me-2"></i>Nuevo Trabajo</h1>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
        </div>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="card shadow">
            <div class="card-body">
                <form action="procesar.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="accion" value="crear">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="cotizacion" class="form-label">Cotización *</label>
                            <select class="form-select" id="cotizacion" name="cotizacion" required <?= $id_cotizacion > 0 ? 'disabled' : '' ?>>
                                <?php if ($id_cotizacion > 0): ?>
                                <option value="<?= $cotizacion['id_cotizacion'] ?>" selected>
                                    #<?= $cotizacion['id_cotizacion'] ?> - <?= htmlspecialchars($cotizacion['nombre_cliente']) ?> (<?= htmlspecialchars($cotizacion['marca_vehiculo']) ?> <?= htmlspecialchars($cotizacion['modelo_vehiculo']) ?>)
                                </option>
                                <?php else: ?>
                                <option value="">Seleccione una cotización...</option>
                                <?php foreach ($cotizaciones as $cot): ?>
                                <option value="<?= $cot['id_cotizacion'] ?>">
                                    #<?= $cot['id_cotizacion'] ?> - <?= htmlspecialchars($cot['nombre_cliente']) ?> (<?= htmlspecialchars($cot['marca_vehiculo']) ?> <?= htmlspecialchars($cot['modelo_vehiculo']) ?>)
                                </option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if ($id_cotizacion > 0): ?>
                            <input type="hidden" name="cotizacion" value="<?= $cotizacion['id_cotizacion'] ?>">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="tecnico" class="form-label">Técnico Asignado *</label>
                            <select class="form-select" id="tecnico" name="tecnico" required>
                                <option value="">Seleccione un técnico...</option>
                                <?php foreach ($tecnicos as $tecnico): ?>
                                <option value="<?= $tecnico['id_usuario'] ?>"><?= htmlspecialchars($tecnico['nombre_completo']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio *</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_fin_estimada" class="form-label">Fecha de Fin Estimada</label>
                            <input type="date" class="form-control" id="fecha_fin_estimada" name="fecha_fin_estimada">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notas" class="form-label">Notas / Instrucciones</label>
                        <textarea class="form-control" id="notas" name="notas" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fotos" class="form-label">Fotos (Opcional)</label>
                        <input type="file" class="form-control" id="fotos" name="fotos[]" multiple accept="image/*">
                        <div class="form-text">Puede seleccionar múltiples fotos</div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-undo me-1"></i>Limpiar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Guardar Trabajo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Establecer fecha mínima como hoy
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('fecha_inicio').min = today;
        document.getElementById('fecha_fin_estimada').min = today;
    });
    </script>
</body>
</html>