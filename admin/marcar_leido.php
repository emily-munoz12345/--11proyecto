<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../php/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_mensaje'])) {
    $idMensaje = intval($_POST['id_mensaje']);
    
    try {
        $stmt = $conex->prepare("UPDATE mensajes_contacto SET leido = 1 WHERE id_mensaje = ?");
        $stmt->execute([$idMensaje]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Solicitud inválida']);
}