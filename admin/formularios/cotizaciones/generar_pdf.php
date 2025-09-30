<?php
// generar_pdf.php - Versión HTML simple
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

try {
    // Obtener datos de la cotización (mismo código que arriba)
    $sql = "SELECT c.*, cl.nombre_cliente, cl.telefono_cliente, cl.correo_cliente,
            v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
            u.nombre_completo as nombre_vendedor
            FROM cotizaciones c
            JOIN clientes cl ON c.id_cliente = cl.id_cliente
            JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
            JOIN usuarios u ON c.id_usuario = u.id_usuario
            WHERE c.id_cotizacion = ?";
    $stmt = $conex->prepare($sql);
    $stmt->execute([$id]);
    $cotizacion = $stmt->fetch();

    if (!$cotizacion) {
        header('Location: index.php?error=Cotización no encontrada');
        exit;
    }

    // Obtener servicios
    $sqlServicios = "SELECT s.nombre_servicio, cs.precio 
                    FROM cotizacion_servicios cs
                    JOIN servicios s ON cs.id_servicio = s.id_servicio
                    WHERE cs.id_cotizacion = ?";
    $stmtServicios = $conex->prepare($sqlServicios);
    $stmtServicios->execute([$id]);
    $servicios = $stmtServicios->fetchAll();

} catch (PDOException $e) {
    error_log("Error al obtener cotización: " . $e->getMessage());
    header('Location: index.php?error=Error al obtener cotización');
    exit;
}

// Calcular totales
$subtotal = $cotizacion['subtotal_cotizacion'];
$iva = $cotizacion['iva'];
$valor_adicional = $cotizacion['valor_adicional'] ?? 0;
$total = $cotizacion['total_cotizacion'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización #<?= $cotizacion['id_cotizacion'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #8C4A3F;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .company-info {
            text-align: center;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #8C4A3F;
            color: white;
            padding: 8px;
            font-weight: bold;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }
        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .services-table th {
            background-color: #8C4A3F;
            color: white;
            padding: 10px;
            text-align: left;
        }
        .services-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .total-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .grand-total {
            font-size: 1.2em;
            background-color: #8C4A3F;
            color: white;
        }
        .notes {
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #8C4A3F;
            margin: 20px 0;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 0.9em;
            color: #666;
        }
        .signature {
            margin-top: 80px;
            text-align: center;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>NACIONAL TAPIZADOS</h1>
        <p>Nit: 900.123.456-7 | Dirección: Cra 45 # 26-85, Medellín</p>
        <p>Teléfono: (604) 444 1234 | Email: info@nacionaltapizados.com</p>
    </div>

    <h2 style="text-align: center; color: #8C4A3F;">COTIZACIÓN #<?= $cotizacion['id_cotizacion'] ?></h2>

    <div class="section">
        <div class="section-title">INFORMACIÓN DE LA COTIZACIÓN</div>
        <table class="info-table">
            <tr>
                <td width="30%"><strong>No. Cotización:</strong></td>
                <td><?= $cotizacion['id_cotizacion'] ?></td>
            </tr>
            <tr>
                <td><strong>Fecha:</strong></td>
                <td><?= date('d/m/Y H:i', strtotime($cotizacion['fecha_cotizacion'])) ?></td>
            </tr>
            <tr>
                <td><strong>Estado:</strong></td>
                <td><?= $cotizacion['estado_cotizacion'] ?></td>
            </tr>
            <tr>
                <td><strong>Vendedor:</strong></td>
                <td><?= htmlspecialchars($cotizacion['nombre_vendedor']) ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">INFORMACIÓN DEL CLIENTE</div>
        <table class="info-table">
            <tr>
                <td width="30%"><strong>Nombre:</strong></td>
                <td><?= htmlspecialchars($cotizacion['nombre_cliente']) ?></td>
            </tr>
            <tr>
                <td><strong>Teléfono:</strong></td>
                <td><?= htmlspecialchars($cotizacion['telefono_cliente']) ?></td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td><?= htmlspecialchars($cotizacion['correo_cliente']) ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">INFORMACIÓN DEL VEHÍCULO</div>
        <table class="info-table">
            <tr>
                <td width="30%"><strong>Marca:</strong></td>
                <td><?= htmlspecialchars($cotizacion['marca_vehiculo']) ?></td>
            </tr>
            <tr>
                <td><strong>Modelo:</strong></td>
                <td><?= htmlspecialchars($cotizacion['modelo_vehiculo']) ?></td>
            </tr>
            <tr>
                <td><strong>Placa:</strong></td>
                <td><?= htmlspecialchars($cotizacion['placa_vehiculo']) ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">DETALLE DE SERVICIOS</div>
        <table class="services-table">
            <thead>
                <tr>
                    <th width="70%">SERVICIO</th>
                    <th width="30%">PRECIO</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicios as $servicio): ?>
                <tr>
                    <td><?= htmlspecialchars($servicio['nombre_servicio']) ?></td>
                    <td>$ <?= number_format($servicio['precio'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td>SUBTOTAL SERVICIOS</td>
                    <td>$ <?= number_format($subtotal, 0, ',', '.') ?></td>
                </tr>
                <tr class="total-row">
                    <td>IVA (19%)</td>
                    <td>$ <?= number_format($iva, 0, ',', '.') ?></td>
                </tr>
                <?php if ($valor_adicional > 0): ?>
                <tr class="total-row">
                    <td>VALOR ADICIONAL</td>
                    <td>$ <?= number_format($valor_adicional, 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>
                <tr class="grand-total">
                    <td>TOTAL</td>
                    <td>$ <?= number_format($total, 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php if (!empty($cotizacion['notas_cotizacion'])): ?>
    <div class="section">
        <div class="section-title">NOTAS ADICIONALES</div>
        <div class="notes">
            <?= nl2br(htmlspecialchars($cotizacion['notas_cotizacion'])) ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="section">
        <div class="section-title">TÉRMINOS Y CONDICIONES</div>
        <ul>
            <li>Esta cotización tiene una validez de 30 días desde la fecha de emisión.</li>
            <li>Los precios no incluyen instalación a menos que se especifique.</li>
            <li>El cliente debe aprobar la cotización para proceder con el trabajo.</li>
            <li>Nacional Tapizados se reserva el derecho de modificar precios por cambios en el alcance del trabajo.</li>
        </ul>
    </div>

    <div class="signature">
        <p>_________________________</p>
        <p>FIRMA DEL CLIENTE</p>
    </div>

    <div class="footer">
        <p>Gracias por confiar en Nacional Tapizados</p>
        <p>www.nacionaltapizados.com</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 30px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #8C4A3F; color: white; border: none; cursor: pointer;">
            Imprimir Cotización
        </button>
    </div>
</body>
</html>