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

$id_cotizacion = $_GET['id'];

// Verificar que la cotización pertenece al usuario actual
$stmt = $conex->prepare("
    SELECT c.*, cl.nombre_cliente, cl.telefono_cliente, cl.correo_cliente,
           v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
           u.nombre_completo as vendedor
    FROM cotizaciones c
    INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
    INNER JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
    WHERE c.id_cotizacion = ? AND c.id_usuario = ? AND c.activo = 1
");
$stmt->execute([$id_cotizacion, $usuario_id]);
$cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cotizacion) {
    header('Location: perfil.php');
    exit;
}

// Obtener servicios de la cotización
$stmt_servicios = $conex->prepare("
    SELECT cs.*, s.nombre_servicio, s.descripcion_servicio
    FROM cotizacion_servicios cs
    INNER JOIN servicios s ON cs.id_servicio = s.id_servicio
    WHERE cs.id_cotizacion = ? AND cs.activo = 1
");
$stmt_servicios->execute([$id_cotizacion]);
$servicios = $stmt_servicios->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Cotización - Sistema de Tapicería</title>
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
        .bg-success { background-color: rgba(25, 135, 84, 0.8) !important; }
        .bg-danger { background-color: rgba(220, 53, 69, 0.8) !important; }
        .bg-info { background-color: rgba(13, 202, 240, 0.8) !important; }

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

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
                margin: 1rem;
            }
            .page-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-file-invoice-dollar"></i> Cotización #<?= $cotizacion['id_cotizacion'] ?>
            </h1>
            <div class="d-flex gap-2">
                <a href="perfil.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Perfil
                </a>
                <a href="imprimir_cotizacion.php?id=<?= $id_cotizacion ?>" class="btn btn-primary" target="_blank">
                    <i class="fas fa-print"></i> Imprimir
                </a>
            </div>
        </div>

        <!-- Información del cliente -->
        <div class="info-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>Información del Cliente
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">Nombre:</div>
                            <div class="detail-value"><?= htmlspecialchars($cotizacion['nombre_cliente']) ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Teléfono:</div>
                            <div class="detail-value"><?= htmlspecialchars($cotizacion['telefono_cliente']) ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">Correo:</div>
                            <div class="detail-value"><?= htmlspecialchars($cotizacion['correo_cliente']) ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Vendedor:</div>
                            <div class="detail-value"><?= htmlspecialchars($cotizacion['vendedor']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del vehículo -->
        <div class="info-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-car me-2"></i>Información del Vehículo
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="detail-item">
                            <div class="detail-label">Marca:</div>
                            <div class="detail-value"><?= htmlspecialchars($cotizacion['marca_vehiculo']) ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-item">
                            <div class="detail-label">Modelo:</div>
                            <div class="detail-value"><?= htmlspecialchars($cotizacion['modelo_vehiculo']) ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-item">
                            <div class="detail-label">Placa:</div>
                            <div class="detail-value"><?= htmlspecialchars($cotizacion['placa_vehiculo']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servicios -->
        <div class="info-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-concierge-bell me-2"></i>Servicios Cotizados
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($servicios)): ?>
                    <?php foreach ($servicios as $servicio): ?>
                        <div class="detail-item">
                            <div>
                                <div class="detail-label"><?= htmlspecialchars($servicio['nombre_servicio']) ?></div>
                                <div class="detail-value"><?= htmlspecialchars($servicio['descripcion_servicio']) ?></div>
                            </div>
                            <div class="detail-value">$<?= number_format($servicio['precio'], 2) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-3">
                        No hay servicios en esta cotización.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Resumen financiero -->
        <div class="info-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calculator me-2"></i>Resumen Financiero
                </h5>
            </div>
            <div class="card-body">
                <div class="detail-item">
                    <div class="detail-label">Subtotal:</div>
                    <div class="detail-value">$<?= number_format($cotizacion['subtotal_cotizacion'], 2) ?></div>
                </div>
                <?php if ($cotizacion['valor_adicional'] > 0): ?>
                <div class="detail-item">
                    <div class="detail-label">Valor Adicional:</div>
                    <div class="detail-value">$<?= number_format($cotizacion['valor_adicional'], 2) ?></div>
                </div>
                <?php endif; ?>
                <div class="detail-item">
                    <div class="detail-label">IVA (19%):</div>
                    <div class="detail-value">$<?= number_format($cotizacion['iva'], 2) ?></div>
                </div>
                <div class="detail-item" style="border-top: 2px solid var(--border-color); font-size: 1.1rem;">
                    <div class="detail-label"><strong>Total:</strong></div>
                    <div class="detail-value"><strong>$<?= number_format($cotizacion['total_cotizacion'], 2) ?></strong></div>
                </div>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="info-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Información Adicional
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">Estado:</div>
                            <div class="detail-value">
                                <span class="badge <?php 
                                    switch($cotizacion['estado_cotizacion']) {
                                        case 'Pendiente': echo 'bg-warning'; break;
                                        case 'Aprobado': echo 'bg-success'; break;
                                        case 'Rechazada': echo 'bg-danger'; break;
                                        case 'Completada': echo 'bg-info'; break;
                                        default: echo 'bg-secondary';
                                    }
                                ?>">
                                    <?= $cotizacion['estado_cotizacion'] ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">Fecha:</div>
                            <div class="detail-value"><?= date('d/m/Y H:i', strtotime($cotizacion['fecha_cotizacion'])) ?></div>
                        </div>
                    </div>
                </div>
                <?php if (!empty($cotizacion['notas_cotizacion'])): ?>
                <div class="mt-3">
                    <div class="detail-label">Notas:</div>
                    <div class="detail-value mt-2 p-3" style="background-color: rgba(0,0,0,0.3); border-radius: 8px;">
                        <?= nl2br(htmlspecialchars($cotizacion['notas_cotizacion'])) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>