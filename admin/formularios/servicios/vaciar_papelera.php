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
    $_SESSION['mensaje'] = 'No tienes permisos para vaciar la papelera de servicios.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

try {
    $conex->beginTransaction();
    
    // Obtener servicios que serán eliminados para el registro
    $stmt = $conex->prepare("SELECT * FROM servicios WHERE activo = 0");
    $stmt->execute();
    $serviciosEliminados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Registrar cada eliminación permanente
    foreach ($serviciosEliminados as $servicio) {
        $stmt = $conex->prepare("
            INSERT INTO registro_eliminaciones 
            (tabla, id_registro, eliminado_por, accion, datos, datos_anteriores) 
            VALUES ('servicios', ?, ?, 'ELIMINACION_PERMANENTE', ?, ?)
        ");
        
        $datos_eliminados = "Servicio eliminado permanentemente al vaciar papelera: " . $servicio['nombre_servicio'];
        $datos_completos = json_encode([
            'id_servicio' => $servicio['id_servicio'],
            'nombre_servicio' => $servicio['nombre_servicio'],
            'descripcion_servicio' => $servicio['descripcion_servicio'],
            'precio_servicio' => $servicio['precio_servicio'],
            'tiempo_estimado' => $servicio['tiempo_estimado'],
            'categoria_servicio' => $servicio['categoria_servicio'],
            'fecha_registro' => $servicio['fecha_registro'],
            'fecha_eliminacion' => $servicio['fecha_eliminacion']
        ]);
        
        $stmt->execute([$servicio['id_servicio'], $_SESSION['usuario_id'], $datos_eliminados, $datos_completos]);
    }
    
    // Eliminar relaciones en cotizacion_servicios para servicios eliminados
    $stmt = $conex->prepare("DELETE FROM cotizacion_servicios WHERE id_servicio IN (SELECT id_servicio FROM servicios WHERE activo = 0)");
    $stmt->execute();
    
    // Eliminar permanentemente todos los servicios en papelera
    $stmt = $conex->prepare("DELETE FROM servicios WHERE activo = 0");
    $stmt->execute();
    
    $conex->commit();
    
    $_SESSION['mensaje'] = 'Papelera de servicios vaciada correctamente. Se eliminaron ' . count($serviciosEliminados) . ' servicios permanentemente.';
    $_SESSION['tipo_mensaje'] = 'success';
    
} catch (PDOException $e) {
    $conex->rollBack();
    $_SESSION['mensaje'] = 'Error al vaciar la papelera de servicios: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>