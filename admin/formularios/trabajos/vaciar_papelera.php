<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos (solo Admin)
if (!isAdmin()) {
    $_SESSION['mensaje'] = 'No tienes permisos para realizar esta acción';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

try {
    $conex->beginTransaction();
    
    // Contar cuántos trabajos hay en la papelera antes de eliminar
    $stmt = $conex->prepare("SELECT COUNT(*) FROM trabajos WHERE activo = 0");
    $stmt->execute();
    $totalTrabajos = $stmt->fetchColumn();
    
    if ($totalTrabajos > 0) {
        // 1. Registrar cada eliminación en el registro
        $stmt = $conex->prepare("SELECT * FROM trabajos WHERE activo = 0");
        $stmt->execute();
        $trabajos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $id_usuario = $_SESSION['usuario_id'];
        $registro_stmt = $conex->prepare("
            INSERT INTO registro_eliminaciones 
            (tabla, id_registro, eliminado_por, accion, datos, datos_anteriores) 
            VALUES ('trabajos', ?, ?, 'ELIMINACION_PERMANENTE', ?, ?)
        ");
        
        foreach ($trabajos as $trabajo) {
            $datos_eliminados = "Trabajo eliminado permanentemente: #" . $trabajo['id_trabajos'] . " - Estado: " . $trabajo['estado'];
            $datos_completos = json_encode([
                'id_trabajos' => $trabajo['id_trabajos'],
                'id_cotizacion' => $trabajo['id_cotizacion'],
                'fecha_inicio' => $trabajo['fecha_inicio'],
                'fecha_fin' => $trabajo['fecha_fin'],
                'estado' => $trabajo['estado'],
                'notas' => $trabajo['notas'],
                'fotos' => $trabajo['fotos']
            ]);
            
            $registro_stmt->execute([$trabajo['id_trabajos'], $id_usuario, $datos_eliminados, $datos_completos]);
        }
        
        // 2. Eliminar directamente todos los trabajos en papelera
        $stmt = $conex->prepare("DELETE FROM trabajos WHERE activo = 0");
        $stmt->execute();
        
        $conex->commit();
        
        $_SESSION['mensaje'] = "Se eliminaron permanentemente $totalTrabajos trabajos de la papelera";
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'No hay trabajos en la papelera para eliminar';
        $_SESSION['tipo_mensaje'] = 'info';
    }
    
} catch (PDOException $e) {
    $conex->rollBack();
    $_SESSION['mensaje'] = 'Error al vaciar la papelera: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

// Redirigir de vuelta al índice de trabajos
header('Location: index.php');
exit;
?>