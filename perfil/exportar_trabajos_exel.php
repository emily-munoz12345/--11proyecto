<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas

require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

$usuario_id = getUserId();

// Obtener trabajos del usuario
$stmt = $conex->prepare("
    SELECT t.*, c.id_cotizacion, cl.nombre_cliente, cl.telefono_cliente,
           v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo
    FROM trabajos t
    INNER JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
    INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
    INNER JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    WHERE c.id_usuario = ? AND t.activo = 1
    ORDER BY t.fecha_inicio DESC
");
$stmt->execute([$usuario_id]);
$trabajos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Configurar headers para descarga Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="trabajos_' . date('Y-m-d') . '.xls"');
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
        .estado-pendiente { background-color: #fff3cd; }
        .estado-progreso { background-color: #cce7ff; }
        .estado-entregado { background-color: #d4edda; }
        .estado-cancelado { background-color: #f8d7da; }
    </style>
</head>
<body>
    <h2>Reporte de Trabajos</h2>
    <p><strong>Fecha de generación:</strong> <?= date('d/m/Y H:i:s') ?></p>
    <p><strong>Usuario:</strong> <?= $_SESSION['usuario_nombre'] ?? 'Usuario' ?></p>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Placa</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Estado</th>
                <th>Cotización</th>
                <th>Notas</th>
                <th>Fotos</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trabajos as $trabajo): ?>
            <tr>
                <td><?= $trabajo['id_trabajos'] ?></td>
                <td><?= htmlspecialchars($trabajo['nombre_cliente']) ?></td>
                <td><?= htmlspecialchars($trabajo['marca_vehiculo'] . ' ' . $trabajo['modelo_vehiculo']) ?></td>
                <td><?= htmlspecialchars($trabajo['placa_vehiculo']) ?></td>
                <td><?= date('d/m/Y', strtotime($trabajo['fecha_inicio'])) ?></td>
                <td>
                    <?= ($trabajo['fecha_fin'] && $trabajo['fecha_fin'] != '0000-00-00') ? 
                        date('d/m/Y', strtotime($trabajo['fecha_fin'])) : 'En proceso' ?>
                </td>
                <td class="estado-<?= strtolower(str_replace(' ', '-', $trabajo['estado'])) ?>">
                    <?= $trabajo['estado'] ?>
                </td>
                <td>#<?= $trabajo['id_cotizacion'] ?></td>
                <td><?= htmlspecialchars(substr($trabajo['notas'] ?? '', 0, 100)) ?></td>
                <td>
                    <?php
                    if (!empty($trabajo['fotos'])) {
                        $fotos = explode(',', $trabajo['fotos']);
                        echo count($fotos) . ' foto(s)';
                    } else {
                        echo 'Sin fotos';
                    }
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($trabajos)): ?>
            <tr>
                <td colspan="10" style="text-align: center;">No hay trabajos para exportar</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Resumen estadístico -->
    <?php if (!empty($trabajos)): ?>
    <br><br>
    <h3>Resumen Estadístico</h3>
    <table>
        <tr>
            <th>Total de Trabajos</th>
            <td><?= count($trabajos) ?></td>
        </tr>
        <tr>
            <th>Trabajos Completados</th>
            <td>
                <?= count(array_filter($trabajos, function($t) { return $t['estado'] === 'Entregado'; })) ?>
            </td>
        </tr>
        <tr>
            <th>Trabajos en Progreso</th>
            <td>
                <?= count(array_filter($trabajos, function($t) { return $t['estado'] === 'En progreso'; })) ?>
            </td>
        </tr>
        <tr>
            <th>Trabajos Pendientes</th>
            <td>
                <?= count(array_filter($trabajos, function($t) { return $t['estado'] === 'Pendiente'; })) ?>
            </td>
        </tr>
        <tr>
            <th>Trabajos Cancelados</th>
            <td>
                <?= count(array_filter($trabajos, function($t) { return $t['estado'] === 'Cancelado'; })) ?>
            </td>
        </tr>
    </table>

    <!-- Resumen por estado -->
    <br>
    <h3>Distribución por Estado</h3>
    <table>
        <thead>
            <tr>
                <th>Estado</th>
                <th>Cantidad</th>
                <th>Porcentaje</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $estados = [];
            foreach ($trabajos as $trabajo) {
                $estado = $trabajo['estado'];
                if (!isset($estados[$estado])) {
                    $estados[$estado] = 0;
                }
                $estados[$estado]++;
            }
            
            foreach ($estados as $estado => $cantidad):
            ?>
            <tr>
                <td><?= $estado ?></td>
                <td><?= $cantidad ?></td>
                <td><?= number_format(($cantidad / count($trabajos)) * 100, 1) ?>%</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Trabajos recientes -->
    <br>
    <h3>Trabajos Recientes (Últimos 7 días)</h3>
    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Fecha Inicio</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $trabajos_recientes = array_filter($trabajos, function($t) {
                return strtotime($t['fecha_inicio']) >= strtotime('-7 days');
            });
            
            if (empty($trabajos_recientes)):
            ?>
            <tr>
                <td colspan="4" style="text-align: center;">No hay trabajos recientes</td>
            </tr>
            <?php else: ?>
                <?php foreach ($trabajos_recientes as $trabajo): ?>
                <tr>
                    <td><?= htmlspecialchars($trabajo['nombre_cliente']) ?></td>
                    <td><?= htmlspecialchars($trabajo['marca_vehiculo']) ?></td>
                    <td><?= date('d/m/Y', strtotime($trabajo['fecha_inicio'])) ?></td>
                    <td><?= $trabajo['estado'] ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php endif; ?>
</body>
</html>