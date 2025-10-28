<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

$usuario_id = getUserId();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: perfil.php');
    exit;
}

$id_trabajo = $_GET['id'];

// Verificar que el trabajo pertenece al usuario actual
$stmt = $conex->prepare("
    SELECT t.*, c.id_cotizacion, cl.nombre_cliente, cl.telefono_cliente,
           v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo
    FROM trabajos t
    INNER JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
    INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
    INNER JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    WHERE t.id_trabajos = ? AND c.id_usuario = ? AND t.activo = 1
");
$stmt->execute([$id_trabajo, $usuario_id]);
$trabajo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trabajo) {
    header('Location: perfil.php');
    exit;
}

// Procesar fotos si existen
$fotos = [];
if (!empty($trabajo['fotos'])) {
    $fotos = explode(',', $trabajo['fotos']);
    $fotos = array_filter($fotos); // Remover elementos vacíos
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Trabajo - Sistema de Tapicería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.8);
            --primary-hover: rgba(140, 74, 63, 1);
            --text-color: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --bg-transparent: rgba(0, 0, 0, 0.5);
            --bg-transparent-light: rgba(0, 0, 0, 0.4);
            --border-color: rgba(255, 255, 255, 0.2);
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

        .info-card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-header {
            background-color: var(--primary-color);
            border-bottom: 1px solid var(--border-color);
            padding: 1.2rem 1.5rem;
        }

        .card-title {
            margin: 0;
            color: white;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-color);
        }

        .detail-value {
            color: var(--text-muted);
        }

        .badge {
            padding: 0.35rem 0.65rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 6px;
        }

        .bg-warning { background-color: rgba(255, 193, 7, 0.8) !important; }
        .bg-primary { background-color: var(--primary-color) !important; }
        .bg-success { background-color: rgba(25, 135, 84, 0.8) !important; }
        .bg-danger { background-color: rgba(220, 53, 69, 0.8) !important; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: rgba(108, 117, 125, 0.8);
            color: white;
        }

        .btn-secondary:hover {
            background-color: rgba(108, 117, 125, 1);
        }

        .photo-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .photo-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            aspect-ratio: 1;
            background-color: var(--bg-transparent-light);
            border: 1px solid var(--border-color);
        }

        .photo-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: var(--text-muted);
        }

        .photo-placeholder i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
                margin: 1rem;
            }
            .page-title {
                font-size: 1.5rem;
            }
            .photo-gallery {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-tools"></i> Trabajo #<?= $trabajo['id_trabajos'] ?>
            </h1>
            <div class="d-flex gap-2">
                <a href="perfil.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Perfil
                </a>
            </div>
        </div>

        <!-- Información del cliente y vehículo -->
        <div class="info-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Información General
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">Cliente:</div>
                            <div class="detail-value"><?= htmlspecialchars($trabajo['nombre_cliente']) ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Teléfono:</div>
                            <div class="detail-value"><?= htmlspecialchars($trabajo['telefono_cliente']) ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">Vehículo:</div>
                            <div class="detail-value"><?= htmlspecialchars($trabajo['marca_vehiculo'] . ' ' . $trabajo['modelo_vehiculo']) ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Placa:</div>
                            <div class="detail-value"><?= htmlspecialchars($trabajo['placa_vehiculo']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del trabajo -->
        <div class="info-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tasks me-2"></i>Detalles del Trabajo
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">Estado:</div>
                            <div class="detail-value">
                                <span class="badge <?php 
                                    switch($trabajo['estado']) {
                                        case 'Pendiente': echo 'bg-warning'; break;
                                        case 'En progreso': echo 'bg-primary'; break;
                                        case 'Entregado': echo 'bg-success'; break;
                                        case 'Cancelado': echo 'bg-danger'; break;
                                        default: echo 'bg-secondary';
                                    }
                                ?>">
                                    <?= $trabajo['estado'] ?>
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Fecha Inicio:</div>
                            <div class="detail-value"><?= date('d/m/Y', strtotime($trabajo['fecha_inicio'])) ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">Fecha Fin:</div>
                            <div class="detail-value">
                                <?= ($trabajo['fecha_fin'] && $trabajo['fecha_fin'] != '0000-00-00') ? date('d/m/Y', strtotime($trabajo['fecha_fin'])) : 'No definida' ?>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Cotización:</div>
                            <div class="detail-value">#<?= $trabajo['id_cotizacion'] ?></div>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($trabajo['notas'])): ?>
                <div class="mt-3">
                    <div class="detail-label">Notas del Trabajo:</div>
                    <div class="detail-value mt-2 p-3" style="background-color: rgba(0,0,0,0.3); border-radius: 8px;">
                        <?= nl2br(htmlspecialchars($trabajo['notas'])) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Galería de fotos -->
        <div class="info-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-images me-2"></i>Galería de Fotos
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($fotos)): ?>
                    <div class="photo-gallery">
                        <?php foreach ($fotos as $foto): ?>
                            <div class="photo-item">
                                <img src="<?= htmlspecialchars($foto) ?>" alt="Foto del trabajo" 
                                     onclick="openModal('<?= htmlspecialchars($foto) ?>')" 
                                     style="cursor: pointer;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <div class="photo-placeholder">
                            <i class="fas fa-camera"></i>
                            <span>No hay fotos disponibles</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal para ver foto en grande -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background-color: var(--bg-transparent); backdrop-filter: blur(12px);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                    <h5 class="modal-title" style="color: var(--text-color);">Foto del Trabajo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalPhoto" src="" alt="" style="max-width: 100%; max-height: 70vh; border-radius: 8px;">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openModal(photoUrl) {
            document.getElementById('modalPhoto').src = photoUrl;
            const modal = new bootstrap.Modal(document.getElementById('photoModal'));
            modal.show();
        }
    </script>
</body>
</html>