<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Obtener clientes, vehículos y servicios
try {
    $clientes = $conex->query("SELECT id_cliente, nombre_cliente FROM clientes ORDER BY nombre_cliente")->fetchAll();
    $vehiculos = $conex->query("SELECT id_vehiculo, marca_vehiculo, modelo_vehiculo, placa_vehiculo FROM vehiculos ORDER BY marca_vehiculo")->fetchAll();
    $servicios = $conex->query("SELECT id_servicio, nombre_servicio, precio_servicio FROM servicios ORDER BY nombre_servicio")->fetchAll();
} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}
?>

<?php
require_once __DIR__ . '/../../includes/head.php';
$title = 'Nacional Tapizados - Nueva Cotización';
?>
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-file-invoice-dollar me-2"></i>Nueva Cotización</h1>
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
                <form action="procesar.php" method="POST" id="formCotizacion">
                    <input type="hidden" name="accion" value="crear">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="cliente" class="form-label">Cliente *</label>
                            <select class="form-select" id="cliente" name="cliente" required>
                                <option value="">Seleccione un cliente...</option>
                                <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['id_cliente'] ?>"><?= htmlspecialchars($cliente['nombre_cliente']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="vehiculo" class="form-label">Vehículo *</label>
                            <select class="form-select" id="vehiculo" name="vehiculo" required>
                                <option value="">Seleccione un vehículo...</option>
                                <?php foreach ($vehiculos as $vehiculo): ?>
                                <option value="<?= $vehiculo['id_vehiculo'] ?>">
                                    <?= htmlspecialchars($vehiculo['marca_vehiculo']) ?> 
                                    <?= htmlspecialchars($vehiculo['modelo_vehiculo']) ?> 
                                    (<?= htmlspecialchars($vehiculo['placa_vehiculo']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="mb-3"><i class="fas fa-list me-2"></i>Servicios</h5>
                        <div class="table-responsive">
                            <table class="table" id="tablaServicios">
                                <thead>
                                    <tr>
                                        <th>Servicio</th>
                                        <th width="120">Precio</th>
                                        <th width="100">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="serviciosAgregados">
                                    <!-- Servicios se agregarán aquí dinámicamente -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>
                                            <select class="form-select" id="selectServicio">
                                                <option value="">Seleccione un servicio...</option>
                                                <?php foreach ($servicios as $servicio): ?>
                                                <option value="<?= $servicio['id_servicio'] ?>" data-precio="<?= $servicio['precio_servicio'] ?>">
                                                    <?= htmlspecialchars($servicio['nombre_servicio']) ?> ($<?= number_format($servicio['precio_servicio'], 0, ',', '.') ?>)
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" id="precioServicio" readonly>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" id="btnAgregarServicio">
                                                <i class="fas fa-plus"></i> Agregar
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="subtotal" class="form-label">Subtotal</label>
                            <input type="text" class="form-control" id="subtotal" name="subtotal" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="iva" class="form-label">IVA (19%)</label>
                            <input type="text" class="form-control" id="iva" name="iva" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="total" class="form-label">Total</label>
                            <input type="text" class="form-control" id="total" name="total" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notas" class="form-label">Notas Adicionales</label>
                        <textarea class="form-control" id="notas" name="notas" rows="3"></textarea>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-undo me-1"></i>Limpiar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Guardar Cotización
                        </button>
                    </div>
                    
                    <!-- Campo oculto para enviar los servicios seleccionados -->
                    <input type="hidden" name="servicios_json" id="servicios_json">
                </form>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Script para manejar la selección de servicios
    document.addEventListener('DOMContentLoaded', function() {
        const selectServicio = document.getElementById('selectServicio');
        const precioServicio = document.getElementById('precioServicio');
        const btnAgregar = document.getElementById('btnAgregarServicio');
        const tablaServicios = document.getElementById('serviciosAgregados');
        const serviciosJson = document.getElementById('servicios_json');
        const subtotalInput = document.getElementById('subtotal');
        const ivaInput = document.getElementById('iva');
        const totalInput = document.getElementById('total');
        
        let serviciosSeleccionados = [];
        let subtotal = 0;
        
        // Actualizar precio al seleccionar servicio
        selectServicio.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            precioServicio.value = selectedOption.dataset.precio || '';
        });
        
        // Agregar servicio a la lista
        btnAgregar.addEventListener('click', function() {
            const selectedOption = selectServicio.options[selectServicio.selectedIndex];
            const idServicio = selectedOption.value;
            const nombreServicio = selectedOption.text.split('($')[0].trim();
            const precio = parseFloat(selectedOption.dataset.precio);
            
            if (!idServicio) {
                alert('Seleccione un servicio válido');
                return;
            }
            
            // Verificar si el servicio ya fue agregado
            if (serviciosSeleccionados.some(s => s.id === idServicio)) {
                alert('Este servicio ya fue agregado');
                return;
            }
            
            // Agregar servicio al array
            serviciosSeleccionados.push({
                id: idServicio,
                nombre: nombreServicio,
                precio: precio
            });
            
            // Actualizar tabla
            actualizarTablaServicios();
            
            // Calcular totales
            calcularTotales();
            
            // Limpiar selección
            selectServicio.selectedIndex = 0;
            precioServicio.value = '';
        });
        
        // Función para actualizar la tabla de servicios
        function actualizarTablaServicios() {
            tablaServicios.innerHTML = '';
            
            serviciosSeleccionados.forEach((servicio, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${servicio.nombre}</td>
                    <td>$ ${servicio.precio.toLocaleString()}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger" onclick="eliminarServicio(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tablaServicios.appendChild(row);
            });
            
            // Actualizar campo oculto con los servicios en JSON
            serviciosJson.value = JSON.stringify(serviciosSeleccionados);
        }
        
        // Función para eliminar un servicio
        window.eliminarServicio = function(index) {
            serviciosSeleccionados.splice(index, 1);
            actualizarTablaServicios();
            calcularTotales();
        };
        
        // Función para calcular subtotal, IVA y total
        function calcularTotales() {
            subtotal = serviciosSeleccionados.reduce((sum, servicio) => sum + servicio.precio, 0);
            const iva = subtotal * 0.19;
            const total = subtotal + iva;
            
            subtotalInput.value = subtotal.toLocaleString();
            ivaInput.value = iva.toLocaleString();
            totalInput.value = total.toLocaleString();
        }
    });
    </script>
</body>
</html>