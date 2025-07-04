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
    $sql = "SELECT t.*, c.id_cotizacion, cl.nombre_cliente, 
            v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo
            FROM trabajos t
            JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
            JOIN clientes cl ON c.id_cliente = cl.id_cliente
            JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
            WHERE t.id_trabajos = ?";
    $stmt = $conex->prepare($sql);
    $stmt->execute([$id]);
    $trabajo = $stmt->fetch();

    if (!$trabajo) {
        header('Location: index.php?error=Trabajo no encontrado');
        exit;
    }

    // Obtener fotos (maneja tanto string como array)
    $fotos = [];
    if (!empty($trabajo['fotos'])) {
        if (is_array($trabajo['fotos'])) {
            $fotos = $trabajo['fotos'];
        } else {
            // Si es string, separar por comas y limpiar espacios
            $fotos = array_filter(array_map('trim', explode(',', $trabajo['fotos'])));
        }
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al obtener datos del trabajo: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../../includes/head.php';
$title = 'Editar Trabajo | Nacional Tapizados';
include __DIR__ . '/../../includes/navbar.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-edit me-2"></i>Editar Trabajo #<?= $trabajo['id_trabajos'] ?></h1>
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
            <form action="procesar.php" method="POST" enctype="multipart/form-data" id="formEditarTrabajo">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" value="<?= $trabajo['id_trabajos'] ?>">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Cotización</label>
                        <input type="text" class="form-control" value="#<?= $trabajo['id_cotizacion'] ?> - <?= htmlspecialchars($trabajo['nombre_cliente']) ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Vehículo</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($trabajo['marca_vehiculo']) ?> <?= htmlspecialchars($trabajo['modelo_vehiculo']) ?> (<?= htmlspecialchars($trabajo['placa_vehiculo']) ?>)" readonly>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="fecha_inicio" class="form-label">Fecha de inicio *</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                               value="<?= date('Y-m-d', strtotime($trabajo['fecha_inicio'])) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_fin" class="form-label">Fecha de fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                               value="<?= $trabajo['fecha_fin'] ? date('Y-m-d', strtotime($trabajo['fecha_fin'])) : '' ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="estado" class="form-label">Estado *</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="Pendiente" <?= $trabajo['estado'] == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="En progreso" <?= $trabajo['estado'] == 'En progreso' ? 'selected' : '' ?>>En progreso</option>
                            <option value="Entregado" <?= $trabajo['estado'] == 'Entregado' ? 'selected' : '' ?>>Entregado</option>
                            <option value="Cancelado" <?= $trabajo['estado'] == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="nuevas_fotos" class="form-label">Fotos adicionales (máximo 5)</label>
                        <input type="file" class="form-control" id="nuevas_fotos" name="fotos[]" multiple accept="image/*">
                        <small class="text-muted">Puedes seleccionar hasta 5 imágenes adicionales (Max. 5MB cada una)</small>
                        <div id="previewNuevasFotos" class="mt-2 d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
                
                <?php if (!empty($fotos)): ?>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Fotos existentes</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($fotos as $index => $foto): ?>
                                <?php if (!empty($foto)): ?>
                                <div class="position-relative" style="width: 150px;">
                                    <a href="<?= htmlspecialchars($foto) ?>" data-fancybox="gallery">
                                        <img src="<?= htmlspecialchars($foto) ?>" class="img-thumbnail" 
                                             style="width: 150px; height: 150px; object-fit: cover;">
                                    </a>
                                    <a href="procesar.php?accion=eliminar_foto&id=<?= $trabajo['id_trabajos'] ?>&foto=<?= urlencode($foto) ?>" 
                                       class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger" 
                                       onclick="return confirm('¿Eliminar esta foto?')"
                                       style="cursor: pointer;">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="notas" class="form-label">Notas</label>
                    <textarea class="form-control" id="notas" name="notas" rows="3"><?= htmlspecialchars($trabajo['notas']) ?></textarea>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary me-md-2">
                        <i class="fas fa-undo me-1"></i> Restablecer
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Vista previa de nuevas fotos seleccionadas
document.getElementById('nuevas_fotos').addEventListener('change', function(e) {
    const preview = document.getElementById('previewNuevasFotos');
    preview.innerHTML = '';
    const files = e.target.files;
    
    if (files.length > 5) {
        alert('Solo puedes subir un máximo de 5 fotos adicionales');
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
document.getElementById('formEditarTrabajo').addEventListener('submit', function(e) {
    const fotosInput = document.getElementById('nuevas_fotos');
    const maxSize = 5 * 1024 * 1024; // 5MB
    
    if (fotosInput.files) {
        // Validar cantidad de fotos
        if (fotosInput.files.length > 5) {
            alert('Solo puedes subir un máximo de 5 fotos adicionales');
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

// Actualizar fecha fin cuando se selecciona estado Entregado
document.getElementById('estado').addEventListener('change', function() {
    if (this.value === 'Entregado' && !document.getElementById('fecha_fin').value) {
        document.getElementById('fecha_fin').valueAsDate = new Date();
    }
});
</script>
</body>
</html>