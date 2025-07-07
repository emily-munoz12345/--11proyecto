<?php
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

if (!isAdmin() && !isTecnico()) {
    header('Location: ../dashboard.php');
    exit;
}

// Obtener cotizaciones aprobadas sin trabajo asociado
try {
    $cotizaciones = $conex->query("
        SELECT c.id_cotizacion, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo
        FROM cotizaciones c
        JOIN clientes cl ON c.id_cliente = cl.id_cliente
        JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
        WHERE c.estado_cotizacion = 'Aprobado'
        AND NOT EXISTS (SELECT 1 FROM trabajos t WHERE t.id_cotizacion = c.id_cotizacion)
        ORDER BY c.fecha_cotizacion DESC
    ")->fetchAll();
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al obtener cotizaciones: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../../includes/head.php';
$title = 'Nuevo Trabajo | Nacional Tapizados';
include __DIR__ . '/../../includes/navbar.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-plus-circle me-2"></i>Nuevo Trabajo</h1>
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
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
            <form action="procesar.php" method="POST" enctype="multipart/form-data" id="formTrabajo">
                <input type="hidden" name="accion" value="crear">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="id_cotizacion" class="form-label">Cotización *</label>
                        <select class="form-select" id="id_cotizacion" name="id_cotizacion" required>
                            <option value="">Seleccione una cotización...</option>
                            <?php foreach ($cotizaciones as $cotizacion): ?>
                            <option value="<?= $cotizacion['id_cotizacion'] ?>">
                                #<?= $cotizacion['id_cotizacion'] ?> - 
                                <?= htmlspecialchars($cotizacion['nombre_cliente']) ?> - 
                                <?= htmlspecialchars($cotizacion['marca_vehiculo']) ?> 
                                <?= htmlspecialchars($cotizacion['modelo_vehiculo']) ?>
                                (<?= htmlspecialchars($cotizacion['placa_vehiculo']) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (empty($cotizaciones)): ?>
                        <small class="text-muted">No hay cotizaciones aprobadas sin trabajo asociado</small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="fecha_inicio" class="form-label">Fecha de inicio *</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="estado" class="form-label">Estado *</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="Pendiente">Pendiente</option>
                            <option value="En progreso">En progreso</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="fotos" class="form-label">Fotos (opcional, máximo 5)</label>
                        <input type="file" class="form-control" id="fotos" name="fotos[]" multiple accept="image/*">
                        <small class="text-muted">Formatos permitidos: JPG, PNG, GIF. Máx. 5MB cada una</small>
                        <div id="previewFotos" class="mt-2 d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notas" class="form-label">Notas</label>
                    <textarea class="form-control" id="notas" name="notas" rows="3"></textarea>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary me-md-2">
                        <i class="fas fa-undo me-1"></i> Limpiar
                    </button>
                    <button type="submit" class="btn btn-primary" <?= empty($cotizaciones) ? 'disabled' : '' ?>>
                        <i class="fas fa-save me-1"></i> Guardar Trabajo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Vista previa de fotos seleccionadas
document.getElementById('fotos').addEventListener('change', function(e) {
    const preview = document.getElementById('previewFotos');
    preview.innerHTML = '';
    const files = e.target.files;
    
    if (files.length > 5) {
        alert('Solo puedes subir un máximo de 5 fotos');
        this.value = '';
        return;
    }
    
    for (let i = 0; i < Math.min(files.length, 5); i++) {
        const file = files[i];
        if (!file.type.match('image.*')) continue;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '100px';
            img.style.height = '100px';
            img.style.objectFit = 'cover';
            img.className = 'img-thumbnail';
            preview.appendChild(img);
        }
        reader.readAsDataURL(file);
    }
});

// Validación antes de enviar
document.getElementById('formTrabajo').addEventListener('submit', function(e) {
    const fotosInput = document.getElementById('fotos');
    const maxSize = 5 * 1024 * 1024; // 5MB
    
    if (fotosInput.files) {
        // Validar cantidad de fotos
        if (fotosInput.files.length > 5) {
            alert('Solo puedes subir un máximo de 5 fotos');
            e.preventDefault();
            return;
        }
        
        // Validar tamaño de cada foto
        for (let i = 0; i < fotosInput.files.length; i++) {
            if (fotosInput.files[i].size > maxSize) {
                alert('El archivo ' + fotosInput.files[i].name + ' excede el tamaño máximo de 5MB');
                e.preventDefault();
                return;
            }
        }
    }
});
</script>
</body>
</html>