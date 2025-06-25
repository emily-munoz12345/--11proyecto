<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

if (!isAdmin()) {
    header('Location: ../dashboard.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $conex->prepare("SELECT * FROM materiales WHERE id_material = ?");
    $stmt->execute([$id]);
    $material = $stmt->fetch();
    
    if (!$material) {
        header('Location: index.php?error=Material no encontrado');
        exit;
    }
} catch (PDOException $e) {
    header('Location: index.php?error=Error al obtener material');
    exit;
}
?>

<?php
require_once __DIR__ . '/../../includes/head.php';
$title = 'Nacional Tapizados - Editar Material';
?>
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-boxes me-2"></i>Editar Material</h1>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
        </div>
        
        <div class="card shadow">
            <div class="card-body">
                <form action="procesar.php" method="POST">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="id" value="<?= $material['id_material'] ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre del Material *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?= htmlspecialchars($material['nombre_material']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="categoria" class="form-label">Categoría *</label>
                            <select class="form-select" id="categoria" name="categoria" required>
                                <option value="Cueros" <?= $material['categoria_material'] == 'Cueros' ? 'selected' : '' ?>>Cueros</option>
                                <option value="Telas" <?= $material['categoria_material'] == 'Telas' ? 'selected' : '' ?>>Telas</option>
                                <option value="Espumas" <?= $material['categoria_material'] == 'Espumas' ? 'selected' : '' ?>>Espumas</option>
                                <option value="Adhesivos" <?= $material['categoria_material'] == 'Adhesivos' ? 'selected' : '' ?>>Adhesivos</option>
                                <option value="Herrajes" <?= $material['categoria_material'] == 'Herrajes' ? 'selected' : '' ?>>Herrajes</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="precio" class="form-label">Precio por metro ($) *</label>
                            <input type="number" class="form-control" id="precio" name="precio" 
                                   value="<?= htmlspecialchars($material['precio_metro']) ?>" min="0" step="100" required>
                        </div>
                        <div class="col-md-4">
                            <label for="stock" class="form-label">Stock (metros) *</label>
                            <input type="number" class="form-control" id="stock" name="stock" 
                                   value="<?= htmlspecialchars($material['stock_material']) ?>" min="0" step="0.5" required>
                        </div>
                        <div class="col-md-4">
                            <label for="proveedor" class="form-label">Proveedor *</label>
                            <input type="text" class="form-control" id="proveedor" name="proveedor" 
                                   value="<?= htmlspecialchars($material['proveedor_material']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción *</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required><?= 
                            htmlspecialchars($material['descripcion_material']) 
                        ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Actualizar Material
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