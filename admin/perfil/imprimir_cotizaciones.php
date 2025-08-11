<?php
require_once __DIR__ . '/../../php/auth.php';
require_once __DIR__ . '/../../php/conexion.php';
requireAuth();

$cotizacion_id = $_GET['id'] ?? null;
$usuario_id = getUserId();

if (!$cotizacion_id) {
    header("Location: cotizaciones.php");
    exit;
}

try {
    // Obtener información de la cotización
    $stmt = $conex->prepare("SELECT c.*, cl.nombre_cliente, cl.correo_cliente, cl.telefono_cliente, 
                            v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
                            u.nombre_completo as nombre_vendedor
                           FROM cotizaciones c 
                           JOIN clientes cl ON c.id_cliente = cl.id_cliente 
                           JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
                           JOIN usuarios u ON c.id_usuario = u.id_usuario
                           WHERE c.id_cotizacion = ? AND c.id_usuario = ?");
    $stmt->execute([$cotizacion_id, $usuario_id]);
    $cotizacion = $stmt->fetch();

    if (!$cotizacion) {
        die("Cotización no encontrada o no tienes permiso para verla");
    }

    // Obtener servicios asociados a la cotización
    $stmt = $conex->prepare("SELECT s.nombre_servicio, s.descripcion_servicio, cs.precio
                            FROM cotizacion_servicios cs
                            JOIN servicios s ON cs.id_servicio = s.id_servicio
                            WHERE cs.id_cotizacion = ?");
    $stmt->execute([$cotizacion_id]);
    $servicios = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error al obtener detalles de la cotización: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización #<?= $cotizacion_id ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 2px solid #5E3023;
            padding-bottom: 10px;
        }
        .logo {
            max-width: 150px;
            max-height: 80px;
        }
        .titulo {
            text-align: center;
            color: #5E3023;
            margin: 20px 0;
        }
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-card {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }
        .info-card h3 {
            margin-top: 0;
            color: #5E3023;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #5E3023;
            color: white;
        }
        .total-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .notas {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .firma {
            margin-top: 40px;
            text-align: right;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
            .container {
                border: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h2>Taller de Tapicería</h2>
                <p>Calle Principal #123, Ciudad</p>
                <p>Tel: (123) 456-7890</p>
                <p>Email: contacto@tallertapiceria.com</p>
            </div>
            <div>
                <h1 class="titulo">COTIZACIÓN #<?= $cotizacion_id ?></h1>
                <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])) ?></p>
            </div>
        </div>

        <div class="info-section">
            <div class="info-card">
                <h3>Información del Cliente</h3>
                <p><strong>Nombre:</strong> <?= htmlspecialchars($cotizacion['nombre_cliente']) ?></p>
                <p><strong>Teléfono:</strong> <?= htmlspecialchars($cotizacion['telefono_cliente']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($cotizacion['correo_cliente']) ?></p>
            </div>

            <div class="info-card">
                <h3>Información del Vehículo</h3>
                <p><strong>Marca:</strong> <?= htmlspecialchars($cotizacion['marca_vehiculo']) ?></p>
                <p><strong>Modelo:</strong> <?= htmlspecialchars($cotizacion['modelo_vehiculo']) ?></p>
                <p><strong>Placa:</strong> <?= htmlspecialchars($cotizacion['placa_vehiculo']) ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicios as $servicio): ?>
                <tr>
                    <td><?= htmlspecialchars($servicio['nombre_servicio']) ?></td>
                    <td><?= htmlspecialchars($servicio['descripcion_servicio']) ?></td>
                    <td>$<?= number_format($servicio['precio'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" style="text-align: right;"><strong>Subtotal:</strong></td>
                    <td>$<?= number_format($cotizacion['subtotal_cotizacion'], 2) ?></td>
                </tr>
                <?php if ($cotizacion['valor_adicional'] > 0): ?>
                <tr class="total-row">
                    <td colspan="2" style="text-align: right;"><strong>Valor Adicional:</strong></td>
                    <td>$<?= number_format($cotizacion['valor_adicional'], 2) ?></td>
                </tr>
                <?php endif; ?>
                <tr class="total-row">
                    <td colspan="2" style="text-align: right;"><strong>IVA (<?= $cotizacion['iva'] ?>%):</strong></td>
                    <td>$<?= number_format(($cotizacion['subtotal_cotizacion'] + $cotizacion['valor_adicional']) * ($cotizacion['iva'] / 100), 2) ?></td>
                </tr>
                <tr style="background-color: #D4A373; font-weight: bold;">
                    <td colspan="2" style="text-align: right;"><strong>Total:</strong></td>
                    <td>$<?= number_format($cotizacion['total_cotizacion'], 2) ?></td>
                </tr>
            </tfoot>
        </table>

        <?php if (!empty($cotizacion['notas_cotizacion'])): ?>
        <div class="notas">
            <h3>Notas Adicionales</h3>
            <p><?= nl2br(htmlspecialchars($cotizacion['notas_cotizacion'])) ?></p>
        </div>
        <?php endif; ?>

        <div class="firma">
            <p>Atentamente,</p>
            <p><strong><?= htmlspecialchars($cotizacion['nombre_vendedor']) ?></strong></p>
            <p>Vendedor</p>
        </div>

        <div class="no-print" style="margin-top: 30px; text-align: center;">
            <button onclick="window.print()" style="padding: 10px 20px; background-color: #5E3023; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Imprimir Cotización
            </button>
            <p style="font-size: 12px; margin-top: 10px;">Este documento es válido por 15 días a partir de la fecha de emisión.</p>
        </div>
    </div>

    <script>
        // Auto-imprimir al cargar (opcional)
        window.onload = function() {
            // Descomentar la siguiente línea para imprimir automáticamente
            // window.print();
        };
    </script>
</body>
</html>