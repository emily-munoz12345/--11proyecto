<?php
require_once __DIR__ . '/../../php/auth.php';
require_once __DIR__ . '/../../php/conexion.php';
requireAuth();

$usuario_id = getUserId();

// Verificar si el usuario tiene permiso para exportar cotizaciones
if (!isSeller() && !isAdmin()) {
    die("No tienes permiso para acceder a esta página");
}

// Obtener parámetros de filtrado
$estado = $_GET['estado'] ?? null;
$fecha = $_GET['fecha'] ?? null;

try {
    // Construir consulta con filtros
    $sql = "SELECT c.id_cotizacion, c.fecha_cotizacion, cl.nombre_cliente, 
           v.marca_vehiculo, v.modelo_vehiculo, c.subtotal_cotizacion,
           c.valor_adicional, c.iva, c.total_cotizacion, c.estado_cotizacion
           FROM cotizaciones c 
           JOIN clientes cl ON c.id_cliente = cl.id_cliente 
           JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo 
           WHERE c.id_usuario = ?";
    
    $params = [$usuario_id];
    
    // Aplicar filtros
    if ($estado) {
        $sql .= " AND c.estado_cotizacion = ?";
        $params[] = $estado;
    }
    
    if ($fecha) {
        $sql .= " AND c.fecha_cotizacion >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        $params[] = $fecha;
    }
    
    $sql .= " ORDER BY c.fecha_cotizacion DESC";
    
    $stmt = $conex->prepare($sql);
    $stmt->execute($params);
    $cotizaciones = $stmt->fetchAll();

    // Obtener servicios para cada cotización
    foreach ($cotizaciones as &$cotizacion) {
        $stmt = $conex->prepare("SELECT s.nombre_servicio, cs.precio
                               FROM cotizacion_servicios cs
                               JOIN servicios s ON cs.id_servicio = s.id_servicio
                               WHERE cs.id_cotizacion = ?");
        $stmt->execute([$cotizacion['id_cotizacion']]);
        $cotizacion['servicios'] = $stmt->fetchAll();
    }

    // Configurar headers para descarga de Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="cotizaciones_' . date('Y-m-d') . '.xls"');
    
    // Crear contenido Excel
    echo "<table border='1'>";
    echo "<tr>";
    echo "<th colspan='9' style='background-color: #5E3023; color: white;'>REPORTE DE COTIZACIONES</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>Fecha</th>";
    echo "<th>Cliente</th>";
    echo "<th>Vehículo</th>";
    echo "<th>Servicios</th>";
    echo "<th>Subtotal</th>";
    echo "<th>Adicional</th>";
    echo "<th>IVA</th>";
    echo "<th>Total</th>";
    echo "<th>Estado</th>";
    echo "</tr>";
    
    foreach ($cotizaciones as $cotizacion) {
        // Formatear servicios
        $servicios = "";
        foreach ($cotizacion['servicios'] as $servicio) {
            $servicios .= $servicio['nombre_servicio'] . " ($" . number_format($servicio['precio'], 2) . ")\n";
        }
        
        echo "<tr>";
        echo "<td>" . $cotizacion['id_cotizacion'] . "</td>";
        echo "<td>" . date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])) . "</td>";
        echo "<td>" . htmlspecialchars($cotizacion['nombre_cliente']) . "</td>";
        echo "<td>" . htmlspecialchars($cotizacion['marca_vehiculo']) . " " . htmlspecialchars($cotizacion['modelo_vehiculo']) . "</td>";
        echo "<td>" . nl2br($servicios) . "</td>";
        echo "<td>$" . number_format($cotizacion['subtotal_cotizacion'], 2) . "</td>";
        echo "<td>$" . number_format($cotizacion['valor_adicional'], 2) . "</td>";
        echo "<td>$" . number_format(($cotizacion['subtotal_cotizacion'] + $cotizacion['valor_adicional']) * ($cotizacion['iva'] / 100), 2) . "</td>";
        echo "<td>$" . number_format($cotizacion['total_cotizacion'], 2) . "</td>";
        echo "<td>" . $cotizacion['estado_cotizacion'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
} catch (PDOException $e) {
    die("Error al generar el reporte: " . $e->getMessage());
}