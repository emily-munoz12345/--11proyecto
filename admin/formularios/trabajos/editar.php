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

// Verificar que se proporcionó un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje'] = 'ID de trabajo no válido';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

$id_trabajo = $_GET['id'];

// Obtener datos del trabajo
$stmt = $conex->prepare("
    SELECT t.*, c.id_cotizacion, cl.nombre_cliente, v.marca_vehiculo, 
           v.modelo_vehiculo, v.placa_vehiculo, c.estado_cotizacion
    FROM trabajos t
    LEFT JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
    LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
    LEFT JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    WHERE t.id_trabajos = ? AND t.activo = 1
");

$stmt->execute([$id_trabajo]);
$trabajo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trabajo) {
    $_SESSION['mensaje'] = 'Trabajo no encontrado';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Generar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Inicializar variables de sesión para mensajes
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
    $_SESSION['tipo_mensaje'] = '';
}

// Obtener fotos existentes
$fotos_existentes = [];
if (!empty($trabajo['fotos'])) {
    $fotos_existentes = explode(',', $trabajo['fotos']);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Trabajo</title>
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

        .btn-warning {
            background-color: var(--warning-color);
            color: black;
        }

        .btn-warning:hover {
            background-color: rgba(255, 193, 7, 1);
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

        /* Estilos para información del trabajo */
        .info-card {
            background-color: var(--bg-transparent-light);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-color);
        }

        .info-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .info-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
            color: var(--text-color);
        }

        .info-badge {
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .info-detail {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
            color: var(--text-color);
        }

        .info-detail i {
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }

        /* Estilos para galería de fotos */
        .fotos-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .foto-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .foto-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }

        .foto-remove {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--danger-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.8rem;
        }

        .foto-remove:hover {
            background: var(--danger-hover);
        }

        .completado-section {
            background-color: rgba(25, 135, 84, 0.1);
            border: 1px solid var(--success-color);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
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

            .info-header {
                flex-direction: column;
                gap: 0.5rem;
            }

            .fotos-container {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-edit"></i>Editar Trabajo
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

        <!-- Formulario para editar trabajo -->
        <div class="form-container">
            <form id="formTrabajo" action="procesar_editar.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id_trabajo" value="<?php echo $id_trabajo; ?>">
                <input type="hidden" name="fotos_existentes" id="fotosExistentes" value="<?php echo htmlspecialchars($trabajo['fotos']); ?>">
                
                <!-- Información de la cotización -->
                <div class="info-card">
                    <div class="info-header">
                        <h3 class="info-title">Cotización #<?php echo $trabajo['id_cotizacion']; ?></h3>
                        <span class="info-badge"><?php echo $trabajo['estado_cotizacion']; ?></span>
                    </div>
                    <div class="info-detail">
                        <i class="fas fa-user"></i>
                        <span><?php echo htmlspecialchars($trabajo['nombre_cliente']); ?></span>
                    </div>
                    <div class="info-detail">
                        <i class="fas fa-car"></i>
                        <span><?php echo htmlspecialchars($trabajo['marca_vehiculo'] . ' ' . $trabajo['modelo_vehiculo']); ?></span>
                    </div>
                    <div class="info-detail">
                        <i class="fas fa-tag"></i>
                        <span><?php echo htmlspecialchars($trabajo['placa_vehiculo']); ?></span>
                    </div>
                </div>

                <!-- Información del trabajo -->
                <div class="mb-4">
                    <h3 class="mb-3"><i class="fas fa-tools me-2"></i>Información del Trabajo</h3>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio *</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                   value="<?php echo htmlspecialchars($trabajo['fecha_inicio']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha_fin" class="form-label">Fecha de Finalización</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                   value="<?php echo ($trabajo['fecha_fin'] && $trabajo['fecha_fin'] != '0000-00-00') ? htmlspecialchars($trabajo['fecha_fin']) : ''; ?>">
                            <div class="form-text text-muted">
                                <small>Dejar vacío si el trabajo aún no está completado</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado del Trabajo *</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="Pendiente" <?php echo $trabajo['estado'] == 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="En progreso" <?php echo $trabajo['estado'] == 'En progreso' ? 'selected' : ''; ?>>En progreso</option>
                            <option value="Entregado" <?php echo $trabajo['estado'] == 'Entregado' ? 'selected' : ''; ?>>Entregado</option>
                            <option value="Cancelado" <?php echo $trabajo['estado'] == 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notas" class="form-label">Notas del Trabajo</label>
                        <textarea class="form-control" id="notas" name="notas" rows="4" 
                                  placeholder="Agregar notas adicionales sobre el trabajo..."><?php echo htmlspecialchars($trabajo['notas']); ?></textarea>
                    </div>

                    <!-- Sección de completado -->
                    <div class="completado-section">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="marcar_completado" 
                                   onchange="toggleFechaCompletado()">
                            <label class="form-check-label" for="marcar_completado">
                                <strong>Marcar como completado hoy</strong>
                            </label>
                        </div>
                        <div class="form-text text-muted">
                            Al activar esta opción, se establecerá la fecha de finalización como hoy y el estado como "Entregado"
                        </div>
                    </div>
                </div>

                <!-- Gestión de fotos -->
                <div class="mb-4">
                    <h3 class="mb-3"><i class="fas fa-images me-2"></i>Fotos del Trabajo</h3>
                    
                    <!-- Fotos existentes -->
                    <?php if (!empty($fotos_existentes) && !empty($fotos_existentes[0])): ?>
                        <div class="mb-3">
                            <label class="form-label">Fotos Actuales</label>
                            <div class="fotos-container" id="fotosExistentesContainer">
                                <?php foreach ($fotos_existentes as $index => $foto): ?>
                                    <?php if (!empty(trim($foto))): ?>
                                        <div class="foto-item">
                                            <img src="/--11proyecto/<?php echo htmlspecialchars(trim($foto)); ?>" 
                                                 alt="Foto del trabajo" 
                                                 onerror="this.style.display='none'">
                                            <button type="button" class="foto-remove" 
                                                    onclick="eliminarFotoExistente('<?php echo htmlspecialchars(trim($foto)); ?>', this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Subir nuevas fotos -->
                    <div class="mb-3">
                        <label for="nuevas_fotos" class="form-label">Agregar Nuevas Fotos</label>
                        <input type="file" class="form-control" id="nuevas_fotos" name="nuevas_fotos[]" multiple accept="image/*">
                        <div class="form-text text-muted">
                            Puedes seleccionar múltiples imágenes (JPEG, PNG, GIF, WEBP). Máximo 5MB por imagen.
                        </div>
                    </div>

                    <!-- Vista previa de nuevas fotos -->
                    <div id="vistaPreviaContainer" class="fotos-container" style="display: none;"></div>
                </div>

                <!-- Botones de acción -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let fotosAEliminar = [];
        
        // Función para marcar como completado
        function toggleFechaCompletado() {
            const marcarCompletado = document.getElementById('marcar_completado');
            const fechaFin = document.getElementById('fecha_fin');
            const estado = document.getElementById('estado');
            
            if (marcarCompletado.checked) {
                // Establecer fecha de hoy
                const today = new Date().toISOString().split('T')[0];
                fechaFin.value = today;
                // Cambiar estado a Entregado
                estado.value = 'Entregado';
            } else {
                // Limpiar fecha de finalización
                fechaFin.value = '';
                // Volver al estado anterior (o mantener el actual)
            }
        }
        
        // Función para eliminar foto existente
        function eliminarFotoExistente(fotoPath, boton) {
            if (confirm('¿Estás seguro de que quieres eliminar esta foto?')) {
                fotosAEliminar.push(fotoPath);
                boton.parentElement.remove();
                
                // Actualizar el campo hidden con las fotos que quedan
                actualizarFotosExistentes();
            }
        }
        
        // Función para actualizar el campo hidden de fotos existentes
        function actualizarFotosExistentes() {
            const fotosContainer = document.getElementById('fotosExistentesContainer');
            const fotosActuales = [];
            
            if (fotosContainer) {
                const fotos = fotosContainer.querySelectorAll('.foto-item');
                fotos.forEach(foto => {
                    const img = foto.querySelector('img');
                    if (img && img.src) {
                        // Extraer la ruta relativa de la URL completa
                        const url = new URL(img.src);
                        const path = url.pathname;
                        // Remover el base path del proyecto
                        const relativePath = path.replace('/--11proyecto/', '');
                        if (relativePath) {
                            fotosActuales.push(relativePath);
                        }
                    }
                });
            }
            
            document.getElementById('fotosExistentes').value = fotosActuales.join(',');
        }
        
        // Vista previa de nuevas fotos
        document.getElementById('nuevas_fotos').addEventListener('change', function(e) {
            const container = document.getElementById('vistaPreviaContainer');
            container.innerHTML = '';
            container.style.display = 'none';
            
            const files = e.target.files;
            if (files.length > 0) {
                container.style.display = 'grid';
                
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'foto-item';
                        div.innerHTML = `
                            <img src="${e.target.result}" alt="Vista previa">
                            <button type="button" class="foto-remove" onclick="this.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                        container.appendChild(div);
                    }
                    
                    reader.readAsDataURL(file);
                }
            }
        });
        
        // Validación del formulario
        document.getElementById('formTrabajo').addEventListener('submit', function(e) {
            const fechaInicio = document.getElementById('fecha_inicio').value;
            const fechaFin = document.getElementById('fecha_fin').value;
            
            if (!fechaInicio) {
                e.preventDefault();
                alert('Por favor, ingresa la fecha de inicio del trabajo.');
                return false;
            }
            
            if (fechaFin && fechaFin < fechaInicio) {
                e.preventDefault();
                alert('La fecha de finalización no puede ser anterior a la fecha de inicio.');
                return false;
            }
            
            // Agregar las fotos a eliminar al formulario
            const fotosEliminarInput = document.createElement('input');
            fotosEliminarInput.type = 'hidden';
            fotosEliminarInput.name = 'fotos_eliminar';
            fotosEliminarInput.value = JSON.stringify(fotosAEliminar);
            this.appendChild(fotosEliminarInput);
        });
    </script>
</body>
</html>