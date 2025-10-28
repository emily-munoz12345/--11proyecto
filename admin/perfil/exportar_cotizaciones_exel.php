<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

$usuario_id = getUserId();

// Obtener cotizaciones del usuario
$stmt = $conex->prepare("
    SELECT c.*, cl.nombre_cliente, cl.telefono_cliente, cl.correo_cliente,
           v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
           u.nombre_completo as vendedor
    FROM cotizaciones c
    INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
    INNER JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
    WHERE c.id_usuario = ? AND c.activo = 1
    ORDER BY c.fecha_cotizacion DESC
");
$stmt->execute([$usuario_id]);
$cotizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener servicios para cada cotización
foreach ($cotizaciones as &$cotizacion) {
    $stmt_servicios = $conex->prepare("
        SELECT cs.*, s.nombre_servicio
        FROM cotizacion_servicios cs
        INNER JOIN servicios s ON cs.id_servicio = s.id_servicio
        WHERE cs.id_cotizacion = ? AND cs.activo = 1
    ");
    $stmt_servicios->execute([$cotizacion['id_cotizacion']]);
    $cotizacion['servicios'] = $stmt_servicios->fetchAll(PDO::FETCH_ASSOC);
}

// Configurar headers para descarga Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="cotizaciones_' . date('Y-m-d') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

// Crear contenido Excel
?>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .numero {
            text-align: right;
        }
        .total {
            font-weight: bold;
            background-color: #e6f3ff;
        }
    </style>
</head>
<body>
    <h2>Reporte de Cotizaciones</h2>
    <p><strong>Fecha de generación:</strong> <?= date('d/m/Y H:i:s') ?></p>
    <p><strong>Usuario:</strong> <?= $_SESSION['usuario_nombre'] ?? 'Usuario' ?></p>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Placa</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Subtotal</th>
                <th>IVA</th>
                <th>Total</th>
                <th>Servicios</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cotizaciones as $cotizacion): ?>
            <tr>
                <td><?= $cotizacion['id_cotizacion'] ?></td>
                <td><?= htmlspecialchars($cotizacion['nombre_cliente']) ?></td>
                <td><?= htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo']) ?></td>
                <td><?= htmlspecialchars($cotizacion['placa_vehiculo']) ?></td>
                <td><?= date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])) ?></td>
                <td><?= $cotizacion['estado_cotizacion'] ?></td>
                <td class="numero">$<?= number_format($cotizacion['subtotal_cotizacion'], 2) ?></td>
                <td class="numero">$<?= number_format($cotizacion['iva'], 2) ?></td>
                <td class="numero total">$<?= number_format($cotizacion['total_cotizacion'], 2) ?></td>
                <td>
                    <?php 
                    $servicios_nombres = [];
                    foreach ($cotizacion['servicios'] as $servicio) {
                        $servicios_nombres[] = $servicio['nombre_servicio'] . ' ($' . number_format($servicio['precio'], 2) . ')';
                    }
                    echo implode(', ', $servicios_nombres);
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($cotizaciones)): ?>
            <tr>
                <td colspan="10" style="text-align: center;">No hay cotizaciones para exportar</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Resumen estadístico -->
    <?php if (!empty($cotizaciones)): ?>
    <br><br>
    <h3>Resumen Estadístico</h3>
    <table>
        <tr>
            <th>Total de Cotizaciones</th>
            <td><?= count($cotizaciones) ?></td>
        </tr>
        <tr>
            <th>Valor Total General</th>
            <td class="numero total">$<?= number_format(array_sum(array_column($cotizaciones, 'total_cotizacion')), 2) ?></td>
        </tr>
        <tr>
            <th>Cotización Promedio</th>
            <td class="numero">$<?= number_format(array_sum(array_column($cotizaciones, 'total_cotizacion')) / count($cotizaciones), 2) ?></td>
        </tr>
        <tr>
            <th>Cotización Más Alta</th>
            <td class="numero">$<?= number_format(max(array_column($cotizaciones, 'total_cotizacion')), 2) ?></td>
        </tr>
        <tr>
            <th>Cotización Más Baja</th>
            <td class="numero">$<?= number_format(min(array_column($cotizaciones, 'total_cotizacion')), 2) ?></td>
        </tr>
    </table>

    <!-- Resumen por estado -->
    <br>
    <h3>Resumen por Estado</h3>
    <table>
        <thead>
            <tr>
                <th>Estado</th>
                <th>Cantidad</th>
                <th>Porcentaje</th>
                <th>Valor Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $estados = [];
            foreach ($cotizaciones as $cotizacion) {
                $estado = $cotizacion['estado_cotizacion'];
                if (!isset($estados[$estado])) {
                    $estados[$estado] = [
                        'count' => 0,
                        'total' => 0
                    ];
                }
                $estados[$estado]['count']++;
                $estados[$estado]['total'] += $cotizacion['total_cotizacion'];
            }
            
            foreach ($estados as $estado => $datos):
            ?>
            <tr>
                <td><?= $estado ?></td>
                <td><?= $datos['count'] ?></td>
                <td><?= number_format(($datos['count'] / count($cotizaciones)) * 100, 1) ?>%</td>
                <td class="numero">$<?= number_format($datos['total'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</body>
</html>