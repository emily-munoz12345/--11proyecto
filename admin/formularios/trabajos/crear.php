<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos (solo Admin y Técnico)
if (!isAdmin() && !isTechnician()) {
    header('Location: ../dashboard.php');
    exit;
}

// Generar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Obtener cotizaciones disponibles (solo pendientes y sin trabajos activos)
$cotizaciones = $conex->query("
    SELECT c.id_cotizacion, c.id_cliente, c.id_vehiculo, 
           cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
           c.total_cotizacion, c.estado_cotizacion
    FROM cotizaciones c
    LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
    LEFT JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    WHERE c.activo = 1 
    AND c.estado_cotizacion = 'Pendiente'
    AND NOT EXISTS (
        SELECT 1 FROM trabajos t 
        WHERE t.id_cotizacion = c.id_cotizacion AND t.activo = 1
    )
    ORDER BY c.fecha_cotizacion DESC
")->fetchAll();

// Inicializar variables de sesión para mensajes
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
    $_SESSION['tipo_mensaje'] = '';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Trabajo</title>
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
            max-width: 900px;
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

        /* Estilos para formularios */
        .form-container {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        .form-control, .form-select {
            background-color: var(--bg-input);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            padding: 0.75rem;
            border-radius: 8px;
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

        /* Estilos para botones */
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

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: rgba(25, 135, 84, 1);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        /* Estilos para alertas */
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

        .alert-success {
            background-color: rgba(25, 135, 84, 0.2);
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            border-left: 4px solid var(--danger-color);
        }

        .alert-warning {
            background-color: rgba(255, 193, 7, 0.2);
            border-left: 4px solid var(--warning-color);
        }

        .alert-info {
            background-color: rgba(13, 202, 240, 0.2);
            border-left: 4px solid var(--info-color);
        }

        /* Estilos para tarjetas de cotización */
        .cotizacion-card {
            background-color: var(--bg-transparent-light);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .cotizacion-card:hover {
            background-color: rgba(140, 74, 63, 0.2);
            transform: translateY(-3px);
        }

        .cotizacion-card.selected {
            background-color: rgba(25, 135, 84, 0.2);
            border-color: var(--success-color);
        }

        .cotizacion-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .cotizacion-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
            color: var(--text-color);
        }

        .cotizacion-badge {
            background-color: var(--warning-color);
            color: black;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .cotizacion-detail {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
            color: var(--text-color);
        }

        .cotizacion-detail i {
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }

        .cotizacion-price {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color);
            margin-top: 0.5rem;
        }

        .create-cotizacion-btn {
            margin-top: 1rem;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
                margin: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .form-container {
                padding: 1.5rem;
            }

            .cotizacion-header {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-plus-circle"></i>Crear Nuevo Trabajo
            </h1>
            <div class="d-flex gap-2">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <!-- Mensajes de alerta -->
        <?php if ($_SESSION['mensaje']): ?>
            <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?>">
                <span><?php echo $_SESSION['mensaje']; ?></span>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php
            // Limpiar mensaje después de mostrarlo
            $_SESSION['mensaje'] = '';
            $_SESSION['tipo_mensaje'] = '';
            ?>
        <?php endif; ?>

        <!-- Formulario para crear trabajo -->
        <div class="form-container">
            <form id="formTrabajo" action="procesar.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="accion" value="crear">
                
                <!-- Selección de cotización -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3><i class="fas fa-file-invoice-dollar me-2"></i>Seleccionar Cotización Pendiente</h3>
                        <a href="../cotizaciones/crear.php" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Nueva Cotización
                        </a>
                    </div>
                    
                    <?php if (count($cotizaciones) > 0): ?>
                        <div id="cotizacionesContainer">
                            <?php foreach ($cotizaciones as $cotizacion): ?>
                                <div class="cotizacion-card" onclick="selectCotizacion(<?php echo $cotizacion['id_cotizacion']; ?>)">
                                    <div class="cotizacion-header">
                                        <h4 class="cotizacion-title">Cotización #<?php echo $cotizacion['id_cotizacion']; ?></h4>
                                        <span class="cotizacion-badge"><?php echo $cotizacion['estado_cotizacion']; ?></span>
                                    </div>
                                    <div class="cotizacion-detail">
                                        <i class="fas fa-user"></i>
                                        <span><?php echo htmlspecialchars($cotizacion['nombre_cliente']); ?></span>
                                    </div>
                                    <div class="cotizacion-detail">
                                        <i class="fas fa-car"></i>
                                        <span><?php echo htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo']); ?></span>
                                    </div>
                                    <div class="cotizacion-detail">
                                        <i class="fas fa-tag"></i>
                                        <span><?php echo htmlspecialchars($cotizacion['placa_vehiculo']); ?></span>
                                    </div>
                                    <div class="cotizacion-price">
                                        <i class="fas fa-dollar-sign"></i>
                                        Total: $<?php echo number_format($cotizacion['total_cotizacion'], 2); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="cotizacion" name="cotizacion" required>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Al crear un trabajo, la cotización se cambiará automáticamente a estado "Aprobado".
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> 
                            No hay cotizaciones pendientes disponibles para crear trabajos. 
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Información del trabajo -->
                <div class="mb-4">
                    <h3 class="mb-3"><i class="fas fa-tools me-2"></i>Información del Trabajo</h3>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio *</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha_fin" class="form-label">Fecha de Finalización</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado del Trabajo *</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="">Seleccionar estado</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="En progreso">En progreso</option>
                            <option value="Entregado">Entregado</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notas" class="form-label">Notas del Trabajo</label>
                        <textarea class="form-control" id="notas" name="notas" rows="4" placeholder="Agregar notas adicionales sobre el trabajo..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fotos" class="form-label">Fotos del Trabajo</label>
                        <input type="file" class="form-control" id="fotos" name="fotos[]" multiple accept="image/*">
                        <div class="form-text text-muted">Puedes seleccionar múltiples imágenes (JPEG, PNG, GIF, WEBP)</div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary" id="btnSubmit" <?php echo count($cotizaciones) === 0 ? 'disabled' : ''; ?>>
                        <i class="fas fa-save"></i> Crear Trabajo
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php include '../../includes/bot.php'; ?>
    <script>setHelpModule('Trabajos');</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedCotizacion = null;
        
        // Función para seleccionar una cotización
        function selectCotizacion(cotizacionId) {
            // Deseleccionar todas las tarjetas
            document.querySelectorAll('.cotizacion-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Seleccionar la tarjeta clickeada
            const selectedCard = document.querySelector(`.cotizacion-card[onclick="selectCotizacion(${cotizacionId})"]`);
            if (selectedCard) {
                selectedCard.classList.add('selected');
                selectedCotizacion = cotizacionId;
                document.getElementById('cotizacion').value = cotizacionId;
                
                // Habilitar el botón de enviar
                document.getElementById('btnSubmit').disabled = false;
            }
        }
        
        // Validación del formulario antes de enviar
        document.getElementById('formTrabajo').addEventListener('submit', function(e) {
            if (!selectedCotizacion) {
                e.preventDefault();
                alert('Por favor, selecciona una cotización para continuar.');
                return false;
            }
            
            const fechaInicio = document.getElementById('fecha_inicio').value;
            if (!fechaInicio) {
                e.preventDefault();
                alert('Por favor, ingresa la fecha de inicio del trabajo.');
                return false;
            }
            
            const estado = document.getElementById('estado').value;
            if (!estado) {
                e.preventDefault();
                alert('Por favor, selecciona el estado del trabajo.');
                return false;
            }
            
            // Validación adicional de fechas
            const fechaFin = document.getElementById('fecha_fin').value;
            if (fechaFin && fechaFin < fechaInicio) {
                e.preventDefault();
                alert('La fecha de finalización no puede ser anterior a la fecha de inicio.');
                return false;
            }
        });
        
        // Establecer fecha de inicio como hoy por defecto
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('fecha_inicio').value = today;
            
            // Si hay cotizaciones, seleccionar la primera automáticamente
            const primeraCotizacion = document.querySelector('.cotizacion-card');
            if (primeraCotizacion) {
                const cotizacionId = primeraCotizacion.getAttribute('onclick').match(/\d+/)[0];
                selectCotizacion(parseInt(cotizacionId));
            }
        });
    </script>
</body>
</html>