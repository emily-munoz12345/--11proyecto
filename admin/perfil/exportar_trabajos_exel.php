<?php
require_once __DIR__ . '/../../php/auth.php';
require_once __DIR__ . '/../../php/conexion.php';
requireAuth();

$usuario_id = getUserId();

// Verificar si el usuario tiene permiso para exportar trabajos
if (!isTechnician() && !isAdmin()) {
    die("No tienes permiso para acceder a esta página");
}

// Obtener parámetros de filtrado
$estado = $_GET['estado'] ?? null;
$fecha = $_GET['fecha'] ?? null;

try {
    // Construir consulta con filtros
    $sql = "SELECT t.id_trabajos, t.fecha_inicio, t.fecha_fin, t.estado, 
           cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo,
           c.id_cotizacion, t.notas
           FROM trabajos t
           JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
           JOIN clientes cl ON c.id_cliente = cl.id_cliente
           JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
           WHERE c.id_usuario = ?";
    
    $params = [$usuario_id];
    
    // Aplicar filtros
    if ($estado) {
        $sql .= " AND t.estado = ?";
        $params[] = $estado;
    }
    
    if ($fecha) {
        $sql .= " AND t.fecha_inicio >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        $params[] = $fecha;
    }
    
    $sql .= " ORDER BY t.fecha_inicio DESC";
    
    $stmt = $conex->prepare($sql);
    $stmt->execute($params);
    $trabajos = $stmt->fetchAll();

    // Obtener servicios para cada trabajo
    foreach ($trabajos as &$trabajo) {
        $stmt = $conex->prepare("SELECT s.nombre_servicio, cs.precio
                               FROM cotizacion_servicios cs
                               JOIN servicios s ON cs.id_servicio = s.id_servicio
                               WHERE cs.id_cotizacion = ?");
        $stmt->execute([$trabajo['id_cotizacion']]);
        $trabajo['servicios'] = $stmt->fetchAll();
    }

    // Configurar headers para descarga de Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="trabajos_' . date('Y-m-d') . '.xls"');
    
    // Crear contenido Excel
    echo "<table border='1'>";
    echo "<tr>";
    echo "<th colspan='9' style='background-color: #5E3023; color: white;'>REPORTE DE TRABAJOS</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>Cotización</th>";
    echo "<th>Cliente</th>";
    echo "<th>Vehículo</th>";
    echo "<th>Servicios</th>";
    echo "<th>Fecha Inicio</th>";
    echo "<th>Fecha Fin</th>";
    echo "<th>Estado</th>";
    echo "<th>Notas</th>";
    echo "</tr>";
    
    foreach ($trabajos as $trabajo) {
        // Formatear servicios
        $servicios = "";
        foreach ($trabajo['servicios'] as $servicio) {
            $servicios .= $servicio['nombre_servicio'] . " ($" . number_format($servicio['precio'], 2) . ")\n";
        }
        
        // Formatear fechas
        $fecha_inicio = date('d/m/Y', strtotime($trabajo['fecha_inicio']));
        $fecha_fin = ($trabajo['fecha_fin'] != '0000-00-00') ? date('d/m/Y', strtotime($trabajo['fecha_fin'])) : 'Pendiente';
        
        echo "<tr>";
        echo "<td>" . $trabajo['id_trabajos'] . "</td>";
        echo "<td>" . $trabajo['id_cotizacion'] . "</td>";
        echo "<td>" . htmlspecialchars($trabajo['nombre_cliente']) . "</td>";
        echo "<td>" . htmlspecialchars($trabajo['marca_vehiculo']) . " " . htmlspecialchars($trabajo['modelo_vehiculo']) . "</td>";
        echo "<td>" . nl2br($servicios) . "</td>";
        echo "<td>" . $fecha_inicio . "</td>";
        echo "<td>" . $fecha_fin . "</td>";
        echo "<td>" . $trabajo['estado'] . "</td>";
        echo "<td>" . nl2br(htmlspecialchars($trabajo['notas'])) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
} catch (PDOException $e) {
    die("Error al generar el reporte: " . $e->getMessage());
}