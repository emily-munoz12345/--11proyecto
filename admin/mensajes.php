<?php
require_once __DIR__ . '../../php/conexion.php';
require_once __DIR__ . '../../php/auth.php';

// Verificar permisos (solo Admin y Vendedor)
if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Marcar mensaje como leído si se recibe un ID
if (isset($_GET['marcar_leido']) && is_numeric($_GET['marcar_leido'])) {
    $id = $_GET['marcar_leido'];
    $stmt = $conex->prepare("UPDATE mensajes_contacto SET leido = 1 WHERE id_mensaje = ?");
    $stmt->execute([$id]);
    header('Location: mensajes.php?status=success');
    exit;
}

// Consulta para obtener los mensajes de contacto
$stmt = $conex->query("SELECT * FROM mensajes_contacto ORDER BY fecha_envio DESC");
$mensajes = $stmt->fetchAll();

// Contar mensajes no leídos
$stmt = $conex->query("SELECT COUNT(*) as no_leidos FROM mensajes_contacto WHERE leido = 0");
$no_leidos = $stmt->fetch(PDO::FETCH_ASSOC)['no_leidos'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes de Contacto | Nacional Tapizados</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.8);
            --primary-hover: rgba(140, 74, 63, 1);
            --secondary-color: rgba(108, 117, 125, 0.8);
            --text-color: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --bg-transparent: rgba(255, 255, 255, 0.1);
            --bg-transparent-light: rgba(255, 255, 255, 0.15);
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

        .unread {
            background-color: rgba(140, 74, 63, 0.2);
            border-left: 4px solid var(--primary-color);
        }

        .message-preview {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 300px;
        }

        .message-card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .message-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .message-sender {
            font-weight: 600;
            font-size: 1.2rem;
        }

        .message-date {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .message-subject {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .message-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .badge-unread {
            background-color: var(--warning-color);
            color: black;
        }

        .badge-read {
            background-color: var(--success-color);
            color: white;
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
            
            .message-preview {
                max-width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-envelope"></i> Buzón de Entrada
                <?php if ($no_leidos > 0): ?>
                    <span class="badge bg-warning ms-2"><?= $no_leidos ?> no leídos</span>
                <?php endif; ?>
            </h1>
            <div class="d-flex gap-2">
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <!-- Mensajes de estado -->
        <?php if (isset($_GET['status'])): ?>
            <div class="alert alert-<?= $_GET['status'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                <?= $_GET['status'] === 'success' ? 'Operación realizada con éxito.' : htmlspecialchars($_GET['message'] ?? 'Error en la operación.') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Lista de mensajes -->
        <div class="messages-list">
            <?php if (count($mensajes) > 0): ?>
                <?php foreach ($mensajes as $mensaje): ?>
                    <div class="message-card <?= $mensaje['leido'] ? '' : 'unread' ?>">
                        <div class="message-header">
                            <div class="message-sender">
                                <?= htmlspecialchars($mensaje['nombre_completo']) ?>
                                <span class="badge <?= $mensaje['leido'] ? 'badge-read' : 'badge-unread' ?> ms-2">
                                    <?= $mensaje['leido'] ? 'Leído' : 'No leído' ?>
                                </span>
                            </div>
                            <div class="message-date">
                                <?= date('d/m/Y H:i', strtotime($mensaje['fecha_envio'])) ?>
                            </div>
                        </div>
                        
                        <div class="message-subject">
                            <i class="fas fa-tag me-1"></i> <?= htmlspecialchars($mensaje['asunto']) ?>
                        </div>
                        
                        <div class="message-preview" title="<?= htmlspecialchars($mensaje['mensaje']) ?>">
                            <?= htmlspecialchars($mensaje['mensaje']) ?>
                        </div>
                        
                        <div class="message-actions">
                            <a href="ver_mensaje.php?id=<?= $mensaje['id_mensaje'] ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Ver completo
                            </a>
                            <?php if (!$mensaje['leido']): ?>
                                <a href="?marcar_leido=<?= $mensaje['id_mensaje'] ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-check"></i> Marcar como leído
                                </a>
                            <?php endif; ?>
                            <a href="mailto:<?= htmlspecialchars($mensaje['correo_electronico']) ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-reply"></i> Responder
                            </a>
                            <?php if (!empty($mensaje['telefono'])): ?>
                                <a href="tel:<?= htmlspecialchars($mensaje['telefono']) ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-phone"></i> Llamar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No hay mensajes de contacto en este momento.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>