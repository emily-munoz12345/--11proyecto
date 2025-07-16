<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos
if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Obtener ID del vehículo
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Obtener datos del vehículo
try {
    $stmt = $conex->prepare("SELECT * FROM vehiculos WHERE id_vehiculo = ?");
    $stmt->execute([$id]);
    $vehiculo = $stmt->fetch();
    
    if (!$vehiculo) {
        header('Location: index.php?error=Vehículo no encontrado');
        exit;
    }
} catch (PDOException $e) {
    header('Location: index.php?error=Error al obtener vehículo');
    exit;
}
?>

<?php
require_once __DIR__ . '/../../includes/head.php';
$title = 'Nacional Tapizados - Editar Vehículo';
?>
        <?php
require_once __DIR__ . '/../../includes/sidebar.php';
?>
    
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-car me-2"></i>Editar Vehículo</h1>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
        </div>
        
        <div class="card shadow">
            <div class="card-body">
                <form action="procesar.php" method="POST">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="id" value="<?= $vehiculo['id_vehiculo'] ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="marca" class="form-label">Marca *</label>
                            <input type="text" class="form-control" id="marca" name="marca" 
                                   value="<?= htmlspecialchars($vehiculo['marca_vehiculo']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="modelo" class="form-label">Modelo *</label>
                            <input type="text" class="form-control" id="modelo" name="modelo" 
                                   value="<?= htmlspecialchars($vehiculo['modelo_vehiculo']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="placa" class="form-label">Placa *</label>
                            <input type="text" class="form-control" id="placa" name="placa" 
                                   value="<?= htmlspecialchars($vehiculo['placa_vehiculo']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notas" class="form-label">Notas Adicionales</label>
                        <textarea class="form-control" id="notas" name="notas" rows="3"><?= 
                            htmlspecialchars($vehiculo['notas_vehiculo']) 
                        ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Actualizar Vehículo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>