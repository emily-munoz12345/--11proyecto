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
    <title>Cotización #<?= $id_cotizacion ?> - Sistema de Tapicería</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 20px;
                font-family: Arial, sans-serif;
                color: #000;
                background: white;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
        }

        body {
            font-family: Arial, sans-serif;
            color: #000;
            background: white;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }

        .document-title {
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            color: #333;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            background-color: #f5f5f5;
            padding: 8px 12px;
            border-left: 4px solid #333;
            margin-bottom: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }

        .info-item {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            color: #333;
        }

        .info-value {
            color: #666;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .table th {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }

        .table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .totals {
            margin-left: auto;
            width: 300px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }

        .total-row:last-child {
            border-bottom: 2px solid #333;
            font-weight: bold;
            font-size: 16px;
        }

        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #333;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .print-button {
            text-align: center;
            margin: 20px 0;
        }

        .btn-print {
            background-color: #8c4a3f;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-print:hover {
            background-color: #7a4035;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }

        .status-pendiente { background-color: #ffc107; }
        .status-aprobado { background-color: #28a745; }
        .status-rechazada { background-color: #dc3545; }
        .status-completada { background-color: #17a2b8; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Botón de impresión (solo visible en pantalla) -->
        <div class="print-button no-print">
            <button class="btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir Cotización
            </button>
            <button class="btn-print" onclick="window.close()" style="background-color: #6c757d; margin-left: 10px;">
                <i class="fas fa-times"></i> Cerrar
            </button>
        </div>

        <!-- Encabezado -->
        <div class="header">
            <div class="company-name">TAPICERÍA AUTOMOTRIZ</div>
            <div class="company-info">
                Especialistas en tapicería de vehículos<br>
                Teléfono: (123) 456-7890 | Email: info@tapiceria.com<br>
                Dirección: Calle Principal #123, Ciudad
            </div>
            <div class="document-title">COTIZACIÓN #<?= $id_cotizacion ?></div>
        </div>

        <!-- Información del cliente y vehículo -->
        <div class="info-grid">
            <div class="section">
                <div class="section-title">INFORMACIÓN DEL CLIENTE</div>
                <div class="info-item">
                    <span class="info-label">Nombre:</span>
                    <span class="info-value"><?= htmlspecialchars($cotizacion['nombre_cliente']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Teléfono:</span>
                    <span class="info-value"><?= htmlspecialchars($cotizacion['telefono_cliente']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Correo:</span>
                    <span class="info-value"><?= htmlspecialchars($cotizacion['correo_cliente']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Vendedor:</span>
                    <span class="info-value"><?= htmlspecialchars($cotizacion['vendedor']) ?></span>
                </div>
            </div>

            <div class="section">
                <div class="section-title">INFORMACIÓN DEL VEHÍCULO</div>
                <div class="info-item">
                    <span class="info-label">Marca:</span>
                    <span class="info-value"><?= htmlspecialchars($cotizacion['marca_vehiculo']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Modelo:</span>
                    <span class="info-value"><?= htmlspecialchars($cotizacion['modelo_vehiculo']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Placa:</span>
                    <span class="info-value"><?= htmlspecialchars($cotizacion['placa_vehiculo']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Estado:</span>
                    <span class="info-value">
                        <span class="status-badge status-<?= strtolower($cotizacion['estado_cotizacion']) ?>">
                            <?= $cotizacion['estado_cotizacion'] ?>
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Servicios cotizados -->
        <div class="section">
            <div class="section-title">SERVICIOS COTIZADOS</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th>Descripción</th>
                        <th style="text-align: right;">Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($servicios)): ?>
                        <?php foreach ($servicios as $servicio): ?>
                            <tr>
                                <td><?= htmlspecialchars($servicio['nombre_servicio']) ?></td>
                                <td><?= htmlspecialchars($servicio['descripcion_servicio']) ?></td>
                                <td style="text-align: right;">$<?= number_format($servicio['precio'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center;">No hay servicios en esta cotización</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Resumen financiero -->
        <div class="section">
            <div class="section-title">RESUMEN FINANCIERO</div>
            <div class="totals">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>$<?= number_format($cotizacion['subtotal_cotizacion'], 2) ?></span>
                </div>
                <?php if ($cotizacion['valor_adicional'] > 0): ?>
                <div class="total-row">
                    <span>Valor Adicional:</span>
                    <span>$<?= number_format($cotizacion['valor_adicional'], 2) ?></span>
                </div>
                <?php endif; ?>
                <div class="total-row">
                    <span>IVA (19%):</span>
                    <span>$<?= number_format($cotizacion['iva'], 2) ?></span>
                </div>
                <div class="total-row">
                    <span><strong>TOTAL:</strong></span>
                    <span><strong>$<?= number_format($cotizacion['total_cotizacion'], 2) ?></strong></span>
                </div>
            </div>
        </div>

        <!-- Notas -->
        <?php if (!empty($cotizacion['notas_cotizacion'])): ?>
        <div class="section">
            <div class="section-title">NOTAS ADICIONALES</div>
            <div class="notes">
                <?= nl2br(htmlspecialchars($cotizacion['notas_cotizacion'])) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Información de la cotización -->
        <div class="section">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Fecha de cotización:</span>
                    <span class="info-value"><?= date('d/m/Y H:i', strtotime($cotizacion['fecha_cotizacion'])) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Válida hasta:</span>
                    <span class="info-value">
                        <?= ($cotizacion['fecha_vencimiento'] && $cotizacion['fecha_vencimiento'] != '0000-00-00') ? 
                            date('d/m/Y', strtotime($cotizacion['fecha_vencimiento'])) : 'No definida' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            <p>Esta cotización es válida por 30 días a partir de la fecha de emisión.</p>
            <p>Para más información, contacte a nuestro personal al (123) 456-7890</p>
            <p>Documento generado el <?= date('d/m/Y H:i') ?></p>
        </div>
    </div>

    <script>
        // Auto-impresión al cargar la página
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>