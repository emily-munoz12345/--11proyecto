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

$id_usuario = $_SESSION['usuario_id'];

try {
    $conex->beginTransaction();
    
    // Obtener todos los vehículos en la papelera
    $stmt = $conex->prepare("SELECT * FROM vehiculos WHERE activo = 0");
    $stmt->execute();
    $vehiculos_eliminados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total_eliminados = 0;
    $errores = [];
    
    foreach ($vehiculos_eliminados as $vehiculo) {
        // Verificar si hay cotizaciones relacionadas (aunque estén inactivas)
        $stmt = $conex->prepare("SELECT COUNT(*) FROM cotizaciones WHERE id_vehiculo = ?");
        $stmt->execute([$vehiculo['id_vehiculo']]);
        $cotizaciones_relacionadas = $stmt->fetchColumn();
        
        if ($cotizaciones_relacionadas > 0) {
            $errores[] = "Vehículo {$vehiculo['marca_vehiculo']} {$vehiculo['modelo_vehiculo']} tiene cotizaciones relacionadas";
            continue;
        }
        
        // 1. Guardar registro manual de eliminación permanente
        $stmt = $conex->prepare("
            INSERT INTO registro_eliminaciones 
            (tabla, id_registro, eliminado_por, accion, datos, datos_anteriores) 
            VALUES ('vehiculos', ?, ?, 'ELIMINACION_PERMANENTE', ?, ?)
        ");
        
        $datos_eliminados = "Vehículo eliminado permanentemente: " . $vehiculo['marca_vehiculo'] . " " . $vehiculo['modelo_vehiculo'] . " - " . $vehiculo['placa_vehiculo'];
        $datos_completos = json_encode([
            'id_vehiculo' => $vehiculo['id_vehiculo'],
            'marca_vehiculo' => $vehiculo['marca_vehiculo'],
            'modelo_vehiculo' => $vehiculo['modelo_vehiculo'],
            'placa_vehiculo' => $vehiculo['placa_vehiculo'],
            'anio_vehiculo' => $vehiculo['anio_vehiculo'],
            'color_vehiculo' => $vehiculo['color_vehiculo'],
            'notas_vehiculo' => $vehiculo['notas_vehiculo']
        ]);
        
        $stmt->execute([$vehiculo['id_vehiculo'], $id_usuario, $datos_eliminados, $datos_completos]);
        
        // 2. Eliminar relaciones en cliente_vehiculo
        $stmt = $conex->prepare("DELETE FROM cliente_vehiculo WHERE id_vehiculo = ?");
        $stmt->execute([$vehiculo['id_vehiculo']]);
        
        // 3. Eliminar físicamente el registro del vehículo
        $stmt = $conex->prepare("DELETE FROM vehiculos WHERE id_vehiculo = ?");
        $stmt->execute([$vehiculo['id_vehiculo']]);
        
        $total_eliminados++;
    }
    
    $conex->commit();
    
    if ($total_eliminados > 0) {
        $_SESSION['mensaje'] = "Papelera vaciada correctamente. Se eliminaron permanentemente $total_eliminados vehículos.";
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = "No se eliminó ningún vehículo.";
        $_SESSION['tipo_mensaje'] = 'info';
    }
    
    if (!empty($errores)) {
        $_SESSION['mensaje_adicional'] = "Algunos vehículos no se pudieron eliminar debido a cotizaciones relacionadas.";
        $_SESSION['errores'] = $errores;
    }
    
} catch (PDOException $e) {
    $conex->rollBack();
    $_SESSION['mensaje'] = 'Error al vaciar la papelera: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: index.php');
exit;
?>