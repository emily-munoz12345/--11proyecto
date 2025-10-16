<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

// Verificar permisos (Admin y Vendedor)
if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Obtener ID del mensaje
$id_mensaje = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_mensaje <= 0) {
    header('Location: buzon.php');
    exit;
}

// Obtener información del mensaje
$query = "SELECT m.*, 
                 COALESCE(u.nombre_completo, 'Sistema') as visto_por,
                 u.id_usuario as id_usuario_vio
          FROM mensajes_contacto m 
          LEFT JOIN registro_eliminaciones re ON m.id_mensaje = re.id_registro 
            AND re.tabla = 'mensajes_contacto' 
            AND re.accion = 'MODIFICACION'
            AND re.datos LIKE '%marcado como leído%'
          LEFT JOIN usuarios u ON re.eliminado_por = u.id_usuario
          WHERE m.id_mensaje = ? AND m.activo = 1";

$stmt = $conex->prepare($query);
$stmt->execute([$id_mensaje]);
$mensaje = $stmt->fetch();

if (!$mensaje) {
    header('Location: buzon.php');
    exit;
}

// Marcar como leído si no lo está
if (!$mensaje['leido']) {
    $usuario_id = getUserId();
    $stmt = $conex->prepare("UPDATE mensajes_contacto SET leido = 1 WHERE id_mensaje = ?");
    $stmt->execute([$id_mensaje]);
    
    // Registrar en el log de auditoría
    $stmt_log = $conex->prepare("CALL SetUsuarioActual(?)");
    $stmt_log->execute([$usuario_id]);
    
    $stmt_audit = $conex->prepare("
        INSERT INTO registro_eliminaciones (tabla, id_registro, eliminado_por, accion, datos) 
        VALUES ('mensajes_contacto', ?, ?, 'MODIFICACION', ?)
    ");
    $stmt_audit->execute([
        $id_mensaje, 
        $usuario_id, 
        "Mensaje marcado como leído por " . getUserName()
    ]);
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'marcar_leido':
                $stmt = $conex->prepare("UPDATE mensajes_contacto SET leido = 1 WHERE id_mensaje = ?");
                $stmt->execute([$id_mensaje]);
                $_SESSION['mensaje'] = "Mensaje marcado como leído";
                $_SESSION['tipo_mensaje'] = 'success';
                break;
                
            case 'marcar_no_leido':
                $stmt = $conex->prepare("UPDATE mensajes_contacto SET leido = 0 WHERE id_mensaje = ?");
                $stmt->execute([$id_mensaje]);
                $_SESSION['mensaje'] = "Mensaje marcado como no leído";
                $_SESSION['tipo_mensaje'] = 'success';
                break;
                
            case 'eliminar_mensaje':
                $usuario_id = getUserId();
                
                // Establecer usuario actual para el trigger
                $stmt_user = $conex->prepare("CALL SetUsuarioActual(?)");
                $stmt_user->execute([$usuario_id]);
                
                // Marcar como inactivo (eliminación lógica)
                $stmt = $conex->prepare("UPDATE mensajes_contacto SET activo = 0 WHERE id_mensaje = ?");
                $stmt->execute([$id_mensaje]);
                
                $_SESSION['mensaje'] = "Mensaje movido a la papelera";
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: buzon.php');
                exit;
        }
        
        // Recargar la página para actualizar el estado
        header('Location: ver.php?id=' . $id_mensaje);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Mensaje - Sistema de Tapicería</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.9);
            --primary-hover: rgba(140, 74, 63, 1);
            --secondary-color: rgba(108, 117, 125, 0.9);
            --text-color: #ffffff;
            --text-dark: #2c3e50;
            --text-muted: rgba(255, 255, 255, 0.8);
            --bg-transparent: rgba(0, 0, 0, 0.7);
            --bg-transparent-light: rgba(0, 0, 0, 0.5);
            --bg-input: rgba(0, 0, 0, 0.6);
            --border-color: rgba(255, 255, 255, 0.3);
            --success-color: rgba(25, 135, 84, 0.9);
            --danger-color: rgba(220, 53, 69, 0.9);
            --warning-color: rgba(255, 193, 7, 0.9);
            --info-color: rgba(13, 202, 240, 0.9);
            --card-bg: rgba(255, 255, 255, 0.95);
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
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--bg-transparent);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
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
            font-size: 2.2rem;
            font-weight: 700;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
            color: var(--text-color);
        }

        .page-title i {
            margin-right: 12px;
            color: var(--primary-color);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            gap: 0.5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .btn i {
            font-size: 0.9rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-hover), #8c4a3f);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(140, 74, 63, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary-color), #6c757d);
            color: white;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #dc3545);
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #dc3545, #c82333);
            transform: translateY(-2px);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #198754);
            color: white;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #198754, #157347);
            transform: translateY(-2px);
        }

        .btn-info {
            background: linear-gradient(135deg, var(--info-color), #0dcaf0);
            color: white;
        }

        .btn-info:hover {
            background: linear-gradient(135deg, #0dcaf0, #0bb5d4);
            transform: translateY(-2px);
        }

        .mensaje-detalle {
            background: linear-gradient(135deg, var(--bg-transparent-light), rgba(0, 0, 0, 0.4));
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-color);
            backdrop-filter: blur(8px);
        }

        .mensaje-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .mensaje-info {
            flex-grow: 1;
        }

        .mensaje-nombre {
            font-weight: 700;
            font-size: 1.4rem;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .mensaje-meta {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .mensaje-asunto {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: linear-gradient(135deg, rgba(140, 74, 63, 0.2), rgba(140, 74, 63, 0.1));
            border-radius: 8px;
            border-left: 4px solid var(--primary-color);
        }

        .mensaje-contenido {
            color: var(--text-color);
            font-size: 1rem;
            line-height: 1.7;
            white-space: pre-wrap;
            padding: 1.5rem;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.2));
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .badge-nuevo {
            background: linear-gradient(135deg, var(--info-color), #0dcaf0);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-leido {
            background: linear-gradient(135deg, var(--success-color), #198754);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

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
                font-size: 1.8rem;
            }

            .mensaje-detalle {
                padding: 1.5rem;
            }

            .mensaje-header {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-eye"></i> Ver Mensaje
            </h1>
            <div class="d-flex gap-2 align-items-center">
                <a href="buzon.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Buzón
                </a>
            </div>
        </div>

        <!-- Mensaje -->
        <?php if (!empty($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>" style="background: linear-gradient(135deg, rgba(25, 135, 84, 0.25), rgba(25, 135, 84, 0.1)); border-left: 5px solid var(--success-color); color: var(--text-color); padding: 1.2rem; border-radius: 10px; margin-bottom: 1.5rem;">
                <div>
                    <i class="fas fa-check-circle me-2"></i>
                    <?= $_SESSION['mensaje'] ?>
                </div>
                <button type="button" class="btn-close btn-close-white" onclick="this.parentElement.style.display='none'"></button>
            </div>
            <?php
            $_SESSION['mensaje'] = '';
            $_SESSION['tipo_mensaje'] = '';
            ?>
        <?php endif; ?>

        <div class="mensaje-detalle">
            <div class="mensaje-header">
                <div class="mensaje-info">
                    <div class="mensaje-nombre">
                        <?= htmlspecialchars($mensaje['nombre_completo']) ?>
                        <?php if ($mensaje['leido']): ?>
                            <span class="badge-leido ms-2">LEÍDO</span>
                        <?php else: ?>
                            <span class="badge-nuevo ms-2">NUEVO</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mensaje-acciones">
                    <div class="btn-group" role="group">
                        <?php if ($mensaje['leido']): ?>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="accion" value="marcar_no_leido">
                                <button type="submit" class="btn btn-info btn-sm">
                                    <i class="fas fa-envelope"></i> Marcar No Leído
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="accion" value="marcar_leido">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-envelope-open"></i> Marcar Leído
                                </button>
                            </form>
                        <?php endif; ?>
                        <form method="post" style="display: inline;" onsubmit="return confirm('¿Está seguro de que desea mover este mensaje a la papelera?');">
                            <input type="hidden" name="accion" value="eliminar_mensaje">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="mensaje-asunto">
                <i class="fas fa-tag me-2"></i><?= htmlspecialchars($mensaje['asunto']) ?>
            </div>

            <div class="mensaje-contenido">
                <?= nl2br(htmlspecialchars($mensaje['mensaje'])) ?>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="row">
            <div class="col">
                <div class="card" style="background: linear-gradient(135deg, var(--bg-transparent-light), rgba(0, 0, 0, 0.4)); border: 1px solid var(--border-color);">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-hover)); border-bottom: 1px solid var(--border-color);">
                        <h5 class="m-0"><i class="fas fa-info-circle me-2"></i>Información General</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Fecha de envío:</strong>
                            <?= date('d/m/Y H:i', strtotime($mensaje['fecha_envio'])) ?>
                        </div>
                        <div class="mb-2">
                            <strong>Email:</strong>
                            <?= htmlspecialchars($mensaje['correo_electronico']) ?>
                        </div>
                        <?php if ($mensaje['telefono']): ?>
                            <div class="mb-2">
                                <strong>Teléfono:</strong>
                                <?= htmlspecialchars($mensaje['telefono']) ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($mensaje['leido'] && $mensaje['visto_por']): ?>
                            <div class="mb-2">
                                <strong>Visto por:</strong>
                                <?= htmlspecialchars($mensaje['visto_por']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>