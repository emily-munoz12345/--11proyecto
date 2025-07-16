<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

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
    $stmt = $conex->prepare("SELECT * FROM servicios WHERE id_servicio = ?");
    $stmt->execute([$id]);
    $servicio = $stmt->fetch();
    
    if (!$servicio) {
        header('Location: index.php?error=Servicio no encontrado');
        exit;
    }
} catch (PDOException $e) {
    header('Location: index.php?error=Error al obtener servicio');
    exit;
}
?>

<?php
require_once __DIR__ . '/../../includes/head.php';
$title = 'Nacional Tapizados - Editar Servicio';
?>
    <?php include '../../includes/sidebar.php'; ?>
    
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-concierge-bell me-2"></i>Editar Servicio</h1>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
        </div>
        
        <div class="card shadow">
            <div class="card-body">
                <form action="procesar.php" method="POST">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="id" value="<?= $servicio['id_servicio'] ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre del Servicio *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?= htmlspecialchars($servicio['nombre_servicio']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="categoria" class="form-label">Categoría *</label>
                            <input type="text" class="form-control" id="categoria" name="categoria" 
                                   value="<?= htmlspecialchars($servicio['categoria_servicio']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="precio" class="form-label">Precio ($) *</label>
                            <input type="number" class="form-control" id="precio" name="precio" 
                                   value="<?= htmlspecialchars($servicio['precio_servicio']) ?>" min="0" step="1000" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tiempo" class="form-label">Tiempo Estimado *</label>
                            <input type="text" class="form-control" id="tiempo" name="tiempo" 
                                   value="<?= htmlspecialchars($servicio['tiempo_estimado']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción Completa *</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required><?= 
                            htmlspecialchars($servicio['descripcion_servicio']) 
                        ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Actualizar Servicio
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>