<?php
session_start();
require_once '../php/conexion.php';
require_once '../php/auth.php';

// Solo administradores pueden eliminar permanentemente
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
    // Obtener datos del cliente antes de eliminarlo
    $stmt_cliente = $conex->prepare("SELECT * FROM clientes WHERE id_cliente = :id");
    $stmt_cliente->bindParam(':id', $id_cliente);
    $stmt_cliente->execute();
    $cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);
    
    if ($cliente) {
        // Guardar en papelera_sistema antes de eliminar
        $stmt_papelera = $conex->prepare("INSERT INTO papelera_sistema (tabla_origen, id_elemento, nombre_elemento, datos_originales, eliminado_por) 
                                        VALUES ('clientes', :id, :nombre, :datos, :usuario)");
        $stmt_papelera->bindParam(':id', $id_cliente);
        $stmt_papelera->bindParam(':nombre', $cliente['nombre_cliente']);
        $datos_json = json_encode([
            'id_cliente' => $cliente['id_cliente'],
            'nombre_cliente' => $cliente['nombre_cliente'],
            'correo_cliente' => $cliente['correo_cliente'],
            'telefono_cliente' => $cliente['telefono_cliente'],
            'direccion_cliente' => $cliente['direccion_cliente'],
            'notas_cliente' => $cliente['notas_cliente']
        ]);
        $stmt_papelera->bindParam(':datos', $datos_json);
        $stmt_papelera->bindParam(':usuario', $_SESSION['id_usuario']);
        $stmt_papelera->execute();
        
        // Eliminar el cliente permanentemente
        $stmt_eliminar = $conex->prepare("DELETE FROM clientes WHERE id_cliente = :id");
        $stmt_eliminar->bindParam(':id', $id_cliente);
        $stmt_eliminar->execute();
        
        // Registrar en logs
        $stmt_log = $conex->prepare("INSERT INTO logs_sistema (accion, tabla_afectada, id_elemento, realizado_por, detalles) 
                                    VALUES ('ELIMINACION_PERMANENTE', 'clientes', :id, :usuario, 'Cliente eliminado permanentemente de la papelera')");
        $stmt_log->bindParam(':id', $id_cliente);
        $stmt_log->bindParam(':usuario', $_SESSION['id_usuario']);
        $stmt_log->execute();
        
        $_SESSION['mensaje'] = 'Cliente eliminado permanentemente';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Cliente no encontrado';
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al eliminar el cliente: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>