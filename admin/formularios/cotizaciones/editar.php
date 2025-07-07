<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Manejar la solicitud AJAX para obtener vehículos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'get_vehiculos') {
    if (!isset($_POST['id_cliente']) || !is_numeric($_POST['id_cliente'])) {
        header('Content-Type: application/json');
        echo json_encode([]);
        exit;
    }

    $idCliente = intval($_POST['id_cliente']);
    
    try {
        $sql = "SELECT v.id_vehiculo, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo
                FROM vehiculos v
                JOIN cliente_vehiculo cv ON v.id_vehiculo = cv.id_vehiculo
                WHERE cv.id_cliente = ?";
        $stmt = $conex->prepare($sql);
        $stmt->execute([$idCliente]);
        $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($vehiculos);
        exit;
    } catch (PDOException $e) {
        error_log("Error al obtener vehículos: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode([]);
        exit;
    }
}

// Generar token CSRF (sesión ya iniciada en auth.php)
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

try {
    // Obtener datos de la cotización
    $sql = "SELECT c.*, 
                   cl.nombre_cliente, 
                   v.marca_vehiculo, 
                   v.modelo_vehiculo, 
                   v.placa_vehiculo
            FROM cotizaciones c
            JOIN clientes cl ON c.id_cliente = cl.id_cliente
            JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
            WHERE c.id_cotizacion = ?";
    $stmt = $conex->prepare($sql);
    $stmt->execute([$id]);
    $cotizacion = $stmt->fetch();

    if (!$cotizacion) {
        header('Location: index.php?error=Cotización no encontrada');
        exit;
    }

    // Obtener servicios de la cotización
    $sqlServicios = "SELECT cs.id_servicio, s.nombre_servicio, s.precio_servicio, cs.precio 
                    FROM cotizacion_servicios cs
                    JOIN servicios s ON cs.id_servicio = s.id_servicio
                    WHERE cs.id_cotizacion = ?";
    $stmtServicios = $conex->prepare($sqlServicios);
    $stmtServicios->execute([$id]);
    $serviciosCotizacion = $stmtServicios->fetchAll();

    // Obtener listados para los select
    $clientes = $conex->query("SELECT id_cliente, nombre_cliente FROM clientes ORDER BY nombre_cliente")->fetchAll();
    
    // Vehículos del cliente actual
    $sqlVehiculos = "SELECT v.id_vehiculo, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo
                    FROM vehiculos v
                    JOIN cliente_vehiculo cv ON v.id_vehiculo = cv.id_vehiculo
                    WHERE cv.id_cliente = ?";
    $stmtVehiculos = $conex->prepare($sqlVehiculos);
    $stmtVehiculos->execute([$cotizacion['id_cliente']]);
    $vehiculosCliente = $stmtVehiculos->fetchAll();
    
    $servicios = $conex->query("SELECT id_servicio, nombre_servicio, precio_servicio FROM servicios ORDER BY nombre_servicio")->fetchAll();
} catch (PDOException $e) {
    error_log("Error al obtener datos: " . $e->getMessage());
    header('Location: index.php?error=Error al cargar datos');
    exit;
}

require_once __DIR__ . '/../../includes/head.php';
$title = 'Nacional Tapizados - Editar Cotización';
include __DIR__ . '/../../includes/navbar.php';
?>

<div class="container py-4 ">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-file-invoice-dollar me-2"></i>Editar Cotización #<?= $cotizacion['id_cotizacion'] ?></h1>
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
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" value="<?= $cotizacion['id_cotizacion'] ?>">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="cliente" class="form-label">Cliente *</label>
                        <select class="form-select" id="cliente" name="cliente" required>
                            <option value="">Seleccione un cliente...</option>
                            <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= $cliente['id_cliente'] ?>" <?= $cliente['id_cliente'] == $cotizacion['id_cliente'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cliente['nombre_cliente']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="vehiculo" class="form-label">Vehículo *</label>
                        <select class="form-select" id="vehiculo" name="vehiculo" required>
                            <?php if (!empty($vehiculosCliente)): ?>
                                <?php foreach ($vehiculosCliente as $vehiculo): ?>
                                <option value="<?= $vehiculo['id_vehiculo'] ?>" <?= $vehiculo['id_vehiculo'] == $cotizacion['id_vehiculo'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($vehiculo['marca_vehiculo']) ?> 
                                    <?= htmlspecialchars($vehiculo['modelo_vehiculo']) ?> 
                                    (<?= htmlspecialchars($vehiculo['placa_vehiculo']) ?>)
                                </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">Este cliente no tiene vehículos</option>
                            <?php endif; ?>
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
                                <?php foreach ($serviciosCotizacion as $servicio): ?>
                                <tr data-id="<?= $servicio['id_servicio'] ?>">
                                    <td><?= htmlspecialchars($servicio['nombre_servicio']) ?></td>
                                    <td>$ <?= number_format($servicio['precio'], 2, ',', '.') ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="eliminarServicio(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>
                                        <select class="form-select" id="selectServicio">
                                            <option value="">Seleccione un servicio...</option>
                                            <?php foreach ($servicios as $servicio): 
                                                $yaAgregado = in_array($servicio['id_servicio'], array_column($serviciosCotizacion, 'id_servicio'));
                                            ?>
                                            <option value="<?= $servicio['id_servicio'] ?>" 
                                                data-precio="<?= $servicio['precio_servicio'] ?>"
                                                <?= $yaAgregado ? 'disabled' : '' ?>>
                                                <?= htmlspecialchars($servicio['nombre_servicio']) ?> ($<?= number_format($servicio['precio_servicio'], 2, ',', '.') ?>)
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
                        <input type="text" class="form-control" id="subtotal" name="subtotal" value="<?= number_format($cotizacion['subtotal_cotizacion'], 2, '.', '') ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="iva" class="form-label">IVA (19%)</label>
                        <input type="text" class="form-control" id="iva" name="iva" value="<?= number_format($cotizacion['iva'], 2, '.', '') ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="valor_adicional" class="form-label">Valor Adicional</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="valor_adicional" name="valor_adicional" value="<?= number_format($cotizacion['valor_adicional'] ?? 0, 2, '.', '') ?>" min="0" step="0.01">
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 offset-md-8">
                        <label for="total" class="form-label">Total</label>
                        <input type="text" class="form-control" id="total" name="total" value="<?= number_format($cotizacion['total_cotizacion'], 2, '.', '') ?>" readonly>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notas" class="form-label">Notas Adicionales</label>
                    <textarea class="form-control" id="notas" name="notas" rows="3"><?= htmlspecialchars($cotizacion['notas_cotizacion']) ?></textarea>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary me-md-2" onclick="resetForm()">
                        <i class="fas fa-undo me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Guardar Cambios
                    </button>
                </div>
                
                <input type="hidden" name="servicios_json" id="servicios_json" value='<?= json_encode([
                    'servicios' => array_map(function($s) {
                        return [
                            'id' => $s['id_servicio'],
                            'nombre' => $s['nombre_servicio'],
                            'precio' => $s['precio']
                        ];
                    }, $serviciosCotizacion),
                    'valor_adicional' => $cotizacion['valor_adicional'] ?? 0
                ]) ?>'>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Variables globales
let serviciosSeleccionados = <?= json_encode(array_map(function($s) {
    return [
        'id' => $s['id_servicio'],
        'nombre' => $s['nombre_servicio'],
        'precio' => $s['precio']
    ];
}, $serviciosCotizacion)) ?>;
let valorAdicional = <?= $cotizacion['valor_adicional'] ?? 0 ?>;

// Función para cargar vehículos del cliente
function cargarVehiculos(idCliente, idVehiculoActual = null) {
    if (!idCliente) {
        document.getElementById('vehiculo').innerHTML = '<option value="">Primero seleccione un cliente</option>';
        document.getElementById('vehiculo').disabled = true;
        return;
    }

    const selectVehiculo = document.getElementById('vehiculo');
    selectVehiculo.disabled = true;
    selectVehiculo.innerHTML = '<option value="">Cargando vehículos...</option>';
    
    const formData = new FormData();
    formData.append('action', 'get_vehiculos');
    formData.append('id_cliente', idCliente);

    fetch('editar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        return response.json();
    })
    .then(data => {
        let options = '<option value="">Seleccione un vehículo...</option>';
        
        if (data.length > 0) {
            data.forEach(vehiculo => {
                const selected = idVehiculoActual && vehiculo.id_vehiculo == idVehiculoActual ? 'selected' : '';
                options += `<option value="${vehiculo.id_vehiculo}" ${selected}>
                    ${vehiculo.marca_vehiculo} ${vehiculo.modelo_vehiculo} (${vehiculo.placa_vehiculo})
                </option>`;
            });
        } else {
            options = '<option value="">Este cliente no tiene vehículos registrados</option>';
        }
        
        selectVehiculo.innerHTML = options;
        selectVehiculo.disabled = false;
    })
    .catch(error => {
        console.error('Error:', error);
        selectVehiculo.innerHTML = '<option value="">Error al cargar vehículos. Intente nuevamente.</option>';
    });
}

// Función para formatear números con decimales
function formatearNumero(num) {
    return new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(num);
}

// Función segura para sumar decimales
function sumarDecimales(...numeros) {
    const suma = numeros.reduce((total, num) => {
        return total + parseFloat(num);
    }, 0);
    return parseFloat(suma.toFixed(2));
}

// Función para calcular totales
function calcularTotales() {
    // Calcular subtotal de servicios
    const subtotalServicios = serviciosSeleccionados.reduce((sum, servicio) => {
        return sum + parseFloat(servicio.precio);
    }, 0);
    
    // Obtener valor adicional
    valorAdicional = parseFloat(document.getElementById('valor_adicional').value) || 0;
    
    // Calcular IVA solo sobre los servicios
    const iva = parseFloat((subtotalServicios * 0.19).toFixed(2));
    
    // Calcular total sumando todo
    const total = sumarDecimales(subtotalServicios, iva, valorAdicional);
    
    // Mostrar valores con formato
    document.getElementById('subtotal').value = formatearNumero(subtotalServicios);
    document.getElementById('iva').value = formatearNumero(iva);
    document.getElementById('total').value = formatearNumero(total);
    
    // Actualizar campo hidden para el formulario
    document.getElementById('servicios_json').value = JSON.stringify({
        servicios: serviciosSeleccionados,
        valor_adicional: valorAdicional
    });
}

// Función para resetear el formulario
function resetForm() {
    serviciosSeleccionados = <?= json_encode(array_map(function($s) {
        return [
            'id' => $s['id_servicio'],
            'nombre' => $s['nombre_servicio'],
            'precio' => $s['precio']
        ];
    }, $serviciosCotizacion)) ?>;
    valorAdicional = <?= $cotizacion['valor_adicional'] ?? 0 ?>;
    document.getElementById('valor_adicional').value = valorAdicional.toFixed(2);
    calcularTotales();
    actualizarTablaServicios();
}

// Función para actualizar la tabla de servicios
function actualizarTablaServicios() {
    const tablaServicios = document.getElementById('serviciosAgregados');
    tablaServicios.innerHTML = '';
    
    serviciosSeleccionados.forEach((servicio, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${servicio.nombre}</td>
            <td>${formatearNumero(servicio.precio)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="eliminarServicio(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tablaServicios.appendChild(row);
    });
}

// Función para eliminar un servicio
window.eliminarServicio = function(index) {
    if (confirm('¿Está seguro de eliminar este servicio?')) {
        serviciosSeleccionados.splice(index, 1);
        actualizarTablaServicios();
        calcularTotales();
    }
};

// Configurar eventos al cargar el documento
document.addEventListener('DOMContentLoaded', function() {
    // Configurar evento para selección de cliente
    document.getElementById('cliente').addEventListener('change', function() {
        const idVehiculoActual = <?= $cotizacion['id_vehiculo'] ?>;
        cargarVehiculos(this.value, idVehiculoActual);
    });

    // Configurar eventos para servicios
    const selectServicio = document.getElementById('selectServicio');
    const precioServicio = document.getElementById('precioServicio');
    const btnAgregar = document.getElementById('btnAgregarServicio');
    
    selectServicio.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        precioServicio.value = selectedOption.dataset.precio || '';
    });
    
    btnAgregar.addEventListener('click', function() {
        const selectedOption = selectServicio.options[selectServicio.selectedIndex];
        const idServicio = selectedOption.value;
        const nombreServicio = selectedOption.text.split('($')[0].trim();
        const precio = parseFloat(selectedOption.dataset.precio);
        
        if (!idServicio) {
            alert('Seleccione un servicio válido');
            return;
        }
        
        if (serviciosSeleccionados.some(s => s.id == idServicio)) {
            alert('Este servicio ya fue agregado');
            return;
        }
        
        serviciosSeleccionados.push({
            id: idServicio,
            nombre: nombreServicio,
            precio: precio
        });
        
        actualizarTablaServicios();
        calcularTotales();
        
        selectServicio.selectedIndex = 0;
        precioServicio.value = '';
    });

    // Configurar evento para valor adicional
    document.getElementById('valor_adicional').addEventListener('change', calcularTotales);
    document.getElementById('valor_adicional').addEventListener('input', calcularTotales);

    // Calcular totales iniciales
    calcularTotales();
    actualizarTablaServicios();
});
</script>
</body>
</html>