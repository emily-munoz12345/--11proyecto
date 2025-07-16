<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos
if (!isAdmin() && !isSeller()) {
    $_SESSION['mensaje'] = 'No tienes permisos para esta acción';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ../dashboard.php');
    exit;
}

// Obtener lista de clientes para el select
try {
    $stmt = $conex->query("SELECT id_cliente, nombre_cliente FROM clientes ORDER BY nombre_cliente");
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al obtener clientes: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    $clientes = [];
}

require_once __DIR__ . '/../../includes/head.php';
$title = 'Nacional Tapizados - Nuevo Vehículo';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-car me-2"></i>Nuevo Vehículo</h1>
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Volver
        </a>
    </div>
    
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?> alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['mensaje']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['mensaje']); unset($_SESSION['tipo_mensaje']); ?>
    <?php endif; ?>
    
    <div class="card shadow">
        <div class="card-body">
            <form action="procesar.php" method="POST" id="formVehiculo">
                <input type="hidden" name="accion" value="crear">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="cliente" class="form-label">Cliente *</label>
                        <select class="form-select" id="cliente" name="id_cliente" required>
                            <option value="">Seleccione un cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['id_cliente'] ?>">
                                    <?= htmlspecialchars($cliente['nombre_cliente']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="marca" class="form-label">Marca *</label>
                        <input type="text" class="form-control" id="marca" name="marca" required
                               pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]{2,50}" 
                               title="Solo letras (2-50 caracteres)">
                    </div>
                    <div class="col-md-6">
                        <label for="modelo" class="form-label">Modelo *</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" required
                               pattern="[A-Za-záéíóúÁÉÍÓÚñÑ0-9\s]{1,50}" 
                               title="Letras y números (1-50 caracteres)">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="placa" class="form-label">Placa *</label>
                        <input type="text" class="form-control" id="placa" name="placa" 
                               placeholder="Ejemplo: ABC123" required
                               pattern="[A-Za-z]{3}[0-9]{3,4}" 
                               title="3 letras seguidas de 3-4 números (ej: ABC123)">
                        <div class="form-text">Formato: 3 letras seguidas de 3-4 números (ej: ABC123)</div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notas" class="form-label">Notas Adicionales</label>
                    <textarea class="form-control" id="notas" name="notas" rows="3" 
                              maxlength="500"></textarea>
                    <div class="form-text">Máximo 500 caracteres</div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary me-md-2">
                        <i class="fas fa-undo me-1"></i>Limpiar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Guardar Vehículo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Validación adicional del formulario
document.getElementById('formVehiculo').addEventListener('submit', function(e) {
    const placa = document.getElementById('placa').value.toUpperCase();
    document.getElementById('placa').value = placa;
    
    // Puedes agregar más validaciones aquí si necesitas
});
</script>
</body>
</html>