<?php
session_start();
require_once '../php/conexion.php';
require_once '../php/auth.php';

// Solo administradores pueden restaurar clientes
if (!isAdmin()) {
    $_SESSION['mensaje'] = 'No tiene permisos para realizar esta acción';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['mensaje'] = 'ID de cliente no especificado';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

$id_cliente = $_GET['id'];

try {
    // Restaurar el cliente
    $stmt = $conex->prepare("UPDATE clientes SET eliminado = 0, fecha_eliminacion = NULL, eliminado_por = NULL WHERE id_cliente = :id");
    $stmt->bindParam(':id', $id_cliente);
    $stmt->execute();
    
    // Registrar en logs
    $stmt_log = $conex->prepare("INSERT INTO logs_sistema (accion, tabla_afectada, id_elemento, realizado_por, detalles) 
                                VALUES ('RESTAURACION', 'clientes', :id, :usuario, 'Cliente restaurado desde la papelera')");
    $stmt_log->bindParam(':id', $id_cliente);
    $stmt_log->bindParam(':usuario', $_SESSION['id_usuario']);
    $stmt_log->execute();
    
    $_SESSION['mensaje'] = 'Cliente restaurado correctamente';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al restaurar el cliente: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>