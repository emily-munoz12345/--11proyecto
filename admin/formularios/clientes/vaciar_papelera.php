<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/conexion.php';
require_once ROOT_PATH . '/php/auth.php';

// Verificar permisos (solo Admin)
if (!isAdmin()) {
    $_SESSION['mensaje'] = 'No tienes permisos para vaciar la papelera.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

try {
    $conex->beginTransaction();
    
    // Obtener clientes que serán eliminados para el registro
    $stmt = $conex->prepare("SELECT * FROM clientes WHERE activo = 0");
    $stmt->execute();
    $clientesEliminados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Registrar cada eliminación permanente
    foreach ($clientesEliminados as $cliente) {
        $stmt = $conex->prepare("
            INSERT INTO registro_eliminaciones 
            (tabla, id_registro, eliminado_por, accion, datos, datos_anteriores) 
            VALUES ('clientes', ?, ?, 'ELIMINACION_PERMANENTE', ?, ?)
        ");
        
        $datos_eliminados = "Cliente eliminado permanentemente al vaciar papelera: " . $cliente['nombre_cliente'];
        $datos_completos = json_encode([
            'id_cliente' => $cliente['id_cliente'],
            'nombre_cliente' => $cliente['nombre_cliente'],
            'correo_cliente' => $cliente['correo_cliente'],
            'telefono_cliente' => $cliente['telefono_cliente'],
            'direccion_cliente' => $cliente['direccion_cliente'],
            'notas_cliente' => $cliente['notas_cliente'],
            'fecha_registro' => $cliente['fecha_registro'],
            'fecha_eliminacion' => $cliente['fecha_eliminacion']
        ]);
        
        $stmt->execute([$cliente['id_cliente'], $_SESSION['usuario_id'], $datos_eliminados, $datos_completos]);
    }
    
    // Eliminar relaciones en cliente_vehiculo
    $stmt = $conex->prepare("DELETE FROM cliente_vehiculo WHERE id_cliente IN (SELECT id_cliente FROM clientes WHERE activo = 0)");
    $stmt->execute();
    
    // Eliminar permanentemente todos los clientes en papelera
    $stmt = $conex->prepare("DELETE FROM clientes WHERE activo = 0");
    $stmt->execute();
    
    $conex->commit();
    
    $_SESSION['mensaje'] = 'Papelera vaciada correctamente. Se eliminaron ' . count($clientesEliminados) . ' clientes permanentemente.';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (PDOException $e) {
    $conex->rollBack();
    $_SESSION['mensaje'] = 'Error al vaciar la papelera: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>