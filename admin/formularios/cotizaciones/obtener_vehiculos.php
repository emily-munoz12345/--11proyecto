<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin() && !isSeller()) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

$cliente_id = isset($_GET['cliente_id']) ? intval($_GET['cliente_id']) : 0;

if ($cliente_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de cliente inválido']);
    exit;
}

try {
    $sql = "SELECT v.id_vehiculo, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo
            FROM vehiculos v
            JOIN cliente_vehiculo cv ON v.id_vehiculo = cv.id_vehiculo
            WHERE cv.id_cliente = ? AND v.activo = 1 AND cv.activo = 1
            ORDER BY v.marca_vehiculo, v.modelo_vehiculo";
    
    $stmt = $conex->prepare($sql);
    $stmt->execute([$cliente_id]);
    $vehiculos = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'vehiculos' => $vehiculos
    ]);
    
} catch (PDOException $e) {
    error_log("Error al obtener vehículos: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar vehículos'
    ]);
}
?>