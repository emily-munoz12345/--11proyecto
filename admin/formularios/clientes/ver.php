<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos (solo Admin y Vendedor)
if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Verificar que se haya proporcionado un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje'] = 'ID de cliente no válido';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

$id_cliente = $_GET['id'];

// Obtener información del cliente
$stmt = $conex->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
$stmt->execute([$id_cliente]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si el cliente existe
if (!$cliente) {
    $_SESSION['mensaje'] = 'Cliente no encontrado';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Verificar si existe la tabla registro_eliminaciones antes de usarla
$tablaExiste = false;
try {
    $result = $conex->query("SELECT 1 FROM registro_eliminaciones LIMIT 1");
    $tablaExiste = true;
} catch (Exception $e) {
    $tablaExiste = false;
}

// Obtener historial de ediciones si la tabla existe
$historialEdiciones = [];
if ($tablaExiste) {
    $stmt = $conex->prepare("
        SELECT re.*, u.nombre_completo as editor 
        FROM registro_eliminaciones re 
        LEFT JOIN usuarios u ON re.eliminado_por = u.id_usuario 
        WHERE re.tabla = 'clientes' AND re.id_registro = ? AND re.accion = 'MODIFICACION'
        ORDER BY re.fecha_eliminacion DESC
    ");
    $stmt->execute([$id_cliente]);
    $historialEdiciones = $stmt->fetchAll();
}

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
    <title>Detalles del Cliente | Nacional Tapizados</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.8);
            --primary-hover: rgba(140, 74, 63, 1);
            --secondary-color: rgba(108, 117, 125, 0.8);
            --text-color: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --bg-transparent: rgba(255, 255, 255, 0.1);
            --bg-transparent-light: rgba(255, 255, 255, 0.15);
            --bg-input: rgba(0, 0, 0, 0.4); /* Fondo más oscuro para inputs */
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
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--bg-transparent);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
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
        }

        .page-title i {
            margin-right: 12px;
            color: var(--primary-color);
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
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
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

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: rgba(220, 53, 69, 1);
        }

        .btn-info {
            background-color: var(--info-color);
            color: white;
        }

        .btn-info:hover {
            background-color: rgba(13, 202, 240, 1);
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
            border-left: 4px solid var(--danger-color);
            background-color: rgba(220, 53, 69, 0.2);
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

        /* Estilos para tarjetas de información */
        .info-card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .info-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .info-card-title {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .detail-item {
            margin-bottom: 1.5rem;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .detail-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 1.1rem;
            word-break: break-word;
            color: var(--text-color);
            font-weight: 500;
        }

        .notes-section {
            background-color: rgba(0, 0, 0, 0.2);
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1rem;
            grid-column: 1 / -1;
            border: 1px solid var(--border-color);
        }

        /* Estilos para historial de ediciones */
        .history-section {
            margin-top: 2rem;
        }

        .history-item {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--info-color);
            border: 1px solid var(--border-color);
        }
        
        .edit-field {
            font-weight: bold;
            color: var(--info-color);
        }
        
        .edit-old-value, .edit-new-value {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            margin: 0.25rem 0;
            display: inline-block;
        }
        
        .edit-old-value {
            background-color: rgba(220, 53, 69, 0.2);
            text-decoration: line-through;
        }
        
        .edit-new-value {
            background-color: rgba(25, 135, 84, 0.2);
        }
        
        .edit-meta {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .no-history {
            text-align: center;
            padding: 2rem;
            color: var(--text-muted);
            font-style: italic;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        /* Badge styles */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .bg-success {
            background-color: var(--success-color) !important;
        }

        .bg-danger {
            background-color: var(--danger-color) !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                margin: 1rem;
                padding: 1.5rem;
            }

            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .edit-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .info-card {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .btn {
                width: 100%;
                justify-content: center;
                margin-bottom: 0.5rem;
            }
            
            .button-group {
                display: flex;
                flex-direction: column;
                width: 100%;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .info-card-title {
                font-size: 1.5rem;
            }
        }
    </style>   
</head>

<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-user"></i> Detalles del Cliente
            </h1>
            <div class="d-flex gap-2 flex-wrap button-group">
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
                <a href="editar.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if (!empty($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
                <div>
                    <i class="fas fa-<?=
                                        $_SESSION['tipo_mensaje'] === 'success' ? 'check-circle' : ($_SESSION['tipo_mensaje'] === 'danger' ? 'times-circle' : ($_SESSION['tipo_mensaje'] === 'warning' ? 'exclamation-triangle' : 'info-circle'))
                                        ?> me-2"></i>
                    <?= $_SESSION['mensaje'] ?>
                </div>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
            </div>
            <?php
            $_SESSION['mensaje'] = '';
            $_SESSION['tipo_mensaje'] = '';
            ?>
        <?php endif; ?>

        <!-- Información del cliente -->
        <div class="info-card">
            <div class="info-card-header">
                <h2 class="info-card-title"><?= htmlspecialchars($cliente['nombre_cliente']) ?></h2>
                <span class="badge bg-<?= $cliente['activo'] == 1 ? 'success' : 'danger' ?>">
                    <?= $cliente['activo'] == 1 ? 'Activo' : 'Eliminado' ?>
                </span>
            </div>
            
            <div class="info-grid">
                <div class="detail-item">
                    <div class="detail-label">ID del Cliente</div>
                    <div class="detail-value"><?= $cliente['id_cliente'] ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Teléfono</div>
                    <div class="detail-value"><?= htmlspecialchars($cliente['telefono_cliente']) ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Correo Electrónico</div>
                    <div class="detail-value"><?= htmlspecialchars($cliente['correo_cliente']) ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Fecha de Registro</div>
                    <div class="detail-value"><?= date('d/m/Y H:i', strtotime($cliente['fecha_registro'])) ?></div>
                </div>
                
                <?php if (!empty($cliente['fecha_actualizacion']) && $cliente['fecha_actualizacion'] != $cliente['fecha_registro']): ?>
                <div class="detail-item">
                    <div class="detail-label">Última Actualización</div>
                    <div class="detail-value"><?= date('d/m/Y H:i', strtotime($cliente['fecha_actualizacion'])) ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($cliente['fecha_eliminacion'])): ?>
                <div class="detail-item">
                    <div class="detail-label">Fecha de Eliminación</div>
                    <div class="detail-value"><?= date('d/m/Y H:i', strtotime($cliente['fecha_eliminacion'])) ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($cliente['direccion_cliente'])): ?>
                <div class="detail-item">
                    <div class="detail-label">Dirección</div>
                    <div class="detail-value"><?= htmlspecialchars($cliente['direccion_cliente']) ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($cliente['notas_cliente'])): ?>
                <div class="notes-section">
                    <div class="detail-label">Notas Adicionales</div>
                    <div class="detail-value"><?= nl2br(htmlspecialchars($cliente['notas_cliente'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Historial de ediciones -->
        <?php if (!empty($historialEdiciones)): ?>
        <div class="info-card">
            <div class="info-card-header">
                <h2 class="info-card-title">
                    <i class="fas fa-history me-2"></i> Historial de Ediciones
                </h2>
            </div>
            
            <div class="history-section">
                <?php foreach ($historialEdiciones as $edicion): ?>
                <div class="history-item">
                    <div class="edit-meta">
                        <span>
                            <i class="fas fa-user-edit me-1"></i>
                            Editado por: <?= $edicion['editor'] ?? 'Sistema' ?>
                        </span>
                        <span>
                            <i class="fas fa-clock me-1"></i>
                            <?= date('d/m/Y H:i', strtotime($edicion['fecha_eliminacion'])) ?>
                        </span>
                    </div>
                    
                    <?php 
                    // Parsear datos de la edición si están disponibles
                    if (!empty($edicion['datos_anteriores']) && !empty($edicion['datos_nuevos'])) {
                        $datosAntiguos = explode('|', $edicion['datos_anteriores']);
                        $datosNuevos = explode('|', $edicion['datos_nuevos']);
                        
                        $campos = ['ID', 'Nombre', 'Correo', 'Teléfono', 'Dirección', 'Notas'];
                        
                        for ($i = 0; $i < count($campos); $i++) {
                            if (isset($datosAntiguos[$i]) && isset($datosNuevos[$i]) && $datosAntiguos[$i] !== $datosNuevos[$i]) {
                                echo '<div class="mb-2">';
                                echo '<span class="edit-field">' . $campos[$i] . ':</span><br>';
                                echo '<span class="edit-old-value">' . htmlspecialchars($datosAntiguos[$i]) . '</span> → ';
                                echo '<span class="edit-new-value">' . htmlspecialchars($datosNuevos[$i]) . '</span>';
                                echo '</div>';
                            }
                        }
                    } else {
                        echo '<div class="detail-value">' . htmlspecialchars($edicion['datos'] ?? 'Información de modificación') . '</div>';
                    }
                    ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>