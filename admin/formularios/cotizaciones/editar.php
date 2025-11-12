<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
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
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cotización | Nacional Tapizados</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.8);
            --primary-hover: rgba(140, 74, 63, 1);
            --secondary-color: rgba(108, 117, 125, 0.8);
            --text-color: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --bg-transparent: rgba(0, 0, 0, 0.5);
            --bg-transparent-light: rgba(0, 0, 0, 0.4);
            --bg-input: rgba(0, 0, 0, 0.6);
            --border-color: rgba(255, 255, 255, 0.2);
            --success-color: rgba(25, 135, 84, 0.8);
            --danger-color: rgba(220, 53, 69, 0.8);
            --warning-color: rgba(255, 193, 7, 0.8);
            --info-color: rgba(13, 202, 240, 0.8);
        }

        body {
            background-image: url('https://pfst.cf2.poecdn.net/base/image/60ab54eef562f30f85a67bde31f924f078199dae0b7bc6c333dfb467a2c13471?w=1024&h=768&pmaid=442168253');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            min-height: 100vh;
        }

        .main-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--bg-transparent);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            color: var(--text-color);
        }

        .page-title i {
            margin-right: 12px;
            color: var(--primary-color);
        }

        .card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
            color: var(--text-color);
        }

        .form-label {
            color: var(--text-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            background-color: var(--bg-input);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: rgba(0, 0, 0, 0.7);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px var(--primary-color);
            color: var(--text-color);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-control:read-only, .form-select:read-only {
            background-color: rgba(0, 0, 0, 0.4);
            border-color: var(--border-color);
            color: var(--text-muted);
            cursor: not-allowed;
        }

        .input-group-text {
            background-color: var(--primary-color);
            border: 1px solid var(--border-color);
            color: var(--text-color);
        }

        /* Estilos específicos para la tabla de servicios */
        .table-container {
            overflow-x: auto;
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
        }

        .servicios-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
            background-color: var(--bg-transparent-light);
        }

        .servicios-table th, 
        .servicios-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-color);
            background-color: transparent;
        }

        .servicios-table th {
            background-color: var(--primary-color);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .servicios-table tbody tr {
            background-color: rgba(0, 0, 0, 0.3);
        }

        .servicios-table tbody tr:nth-child(even) {
            background-color: rgba(0, 0, 0, 0.4);
        }

        .servicios-table tbody tr:hover {
            background-color: rgba(140, 74, 63, 0.3);
        }

        .servicios-table tfoot tr {
            background-color: rgba(0, 0, 0, 0.5);
        }

        .servicios-table tfoot td {
            padding: 0.75rem;
        }

        .servicios-table .form-select,
        .servicios-table .form-control {
            background-color: rgba(0, 0, 0, 0.6);
            border: 1px solid var(--border-color);
            color: var(--text-color);
        }

        .servicios-table .form-select:focus,
        .servicios-table .form-control:focus {
            background-color: rgba(0, 0, 0, 0.8);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px var(--primary-color);
            color: var(--text-color);
        }

        .servicios-table .form-control:read-only {
            background-color: rgba(0, 0, 0, 0.4);
            color: var(--text-muted);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            gap: 0.5rem;
        }

        .btn i {
            font-size: 1rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-secondary:hover {
            background-color: rgba(108, 117, 125, 1);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: rgba(220, 53, 69, 1);
        }

        .btn-outline-secondary {
            background-color: transparent;
            border: 1px solid var(--secondary-color);
            color: var(--text-color);
        }

        .btn-outline-secondary:hover {
            background-color: var(--secondary-color);
            color: white;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(5px);
            border-left: 4px solid var(--info-color);
            background-color: rgba(13, 202, 240, 0.2);
            color: var(--text-color);
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            border-left: 4px solid var(--danger-color);
        }

        .alert .btn-close {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.3rem;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .alert .btn-close:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
                margin: 1rem;
            }

            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .card-body {
                padding: 1rem;
            }

            .servicios-table {
                min-width: 600px;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-file-invoice-dollar"></i>Editar Cotización #<?= $cotizacion['id_cotizacion'] ?>
            </h1>
            <div>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <span><?= htmlspecialchars($_GET['error']) ?></span>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Editar Cotización</h5>
            </div>
            <div class="card-body">
                <form action="procesar.php" method="POST" id="formCotizacion">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="id" value="<?= $cotizacion['id_cotizacion'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <!-- Información del Cliente y Vehículo (solo lectura) -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="cliente" class="form-label">Cliente</label>
                            <input type="text" class="form-control" id="cliente" value="<?= htmlspecialchars($cotizacion['nombre_cliente']) ?>" readonly>
                            <input type="hidden" name="cliente" value="<?= $cotizacion['id_cliente'] ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="vehiculo" class="form-label">Vehículo</label>
                            <input type="text" class="form-control" id="vehiculo" value="<?= htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo'] . ' (' . $cotizacion['placa_vehiculo'] . ')') ?>" readonly>
                            <input type="hidden" name="vehiculo" value="<?= $cotizacion['id_vehiculo'] ?>">
                        </div>
                    </div>
                    
                    <!-- Servicios -->
                    <div class="mb-4">
                        <h5 class="mb-3" style="color: var(--text-color);"><i class="fas fa-list me-2"></i>Servicios</h5>
                        <div class="table-container">
                            <table class="servicios-table" id="tablaServicios">
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
                    
                    <!-- Totales -->
                    <div class="row mb-4">
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
                    <div class="row mb-4">
                        <div class="col-md-4 offset-md-8">
                            <label for="total" class="form-label">Total</label>
                            <input type="text" class="form-control" id="total" name="total" value="<?= number_format($cotizacion['total_cotizacion'], 2, '.', '') ?>" readonly>
                        </div>
                    </div>
                    
                    <!-- Notas -->
                    <div class="mb-4">
                        <label for="notas" class="form-label">Notas Adicionales</label>
                        <textarea class="form-control" id="notas" name="notas" rows="3"><?= htmlspecialchars($cotizacion['notas_cotizacion']) ?></textarea>
                    </div>
                    
                    <!-- Botones -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Guardar Cambios
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
    <?php include '../../includes/bot.php'; ?>
    <script>setHelpModule('Cotizaciones');</script>

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
