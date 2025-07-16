<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin()) {
    header('Location: ../dashboard.php');
    exit;
}
?>
<?php
require_once __DIR__ . '/../../includes/head.php';
$title = 'Nacional Tapizados - Nuevo Material';
?>
    <?php include '../../includes/sidebar.php'; ?>
    
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-boxes me-2"></i>Nuevo Material</h1>
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
                <form action="procesar.php" method="POST">
                    <input type="hidden" name="accion" value="crear">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre del Material *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="categoria" class="form-label">Categoría *</label>
                            <select class="form-select" id="categoria" name="categoria" required>
                                <option value="">Seleccione...</option>
                                <option value="Cueros">Cueros</option>
                                <option value="Telas">Telas</option>
                                <option value="Espumas">Espumas</option>
                                <option value="Adhesivos">Adhesivos</option>
                                <option value="Herrajes">Herrajes</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="precio" class="form-label">Precio por metro ($) *</label>
                            <input type="number" class="form-control" id="precio" name="precio" min="0" step="100" required>
                        </div>
                        <div class="col-md-4">
                            <label for="stock" class="form-label">Stock (metros) *</label>
                            <input type="number" class="form-control" id="stock" name="stock" min="0" step="0.5" required>
                        </div>
                        <div class="col-md-4">
                            <label for="proveedor" class="form-label">Proveedor *</label>
                            <input type="text" class="form-control" id="proveedor" name="proveedor" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción *</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-undo me-1"></i>Limpiar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Guardar Material
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