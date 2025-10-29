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
    $_SESSION['mensaje'] = 'No tienes permisos para realizar esta acción';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

try {
    $conex->beginTransaction();
    
    $id_usuario = $_SESSION['usuario_id'];

    // 1. Contar y obtener los materiales en la papelera antes de eliminar
    $stmt = $conex->prepare("SELECT COUNT(*), GROUP_CONCAT(id_material) as ids_materiales FROM materiales WHERE activo = 0");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalMateriales = $result['COUNT(*)'];
    $ids_materiales = $result['ids_materiales'];
    
    if ($totalMateriales > 0) {
        // 2. Guardar registro de eliminación permanente para cada material
        $stmt_select = $conex->prepare("SELECT * FROM materiales WHERE activo = 0");
        $stmt_select->execute();
        $materiales = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt_insert = $conex->prepare("
            INSERT INTO registro_eliminaciones 
            (tabla, id_registro, eliminado_por, accion, datos, datos_anteriores) 
            VALUES ('materiales', ?, ?, 'ELIMINACION_PERMANENTE', ?, ?)
        ");
        
        foreach ($materiales as $material) {
            $datos_eliminados = "Material eliminado permanentemente: " . $material['nombre_material'];
            $datos_completos = json_encode([
                'id_material' => $material['id_material'],
                'nombre_material' => $material['nombre_material'],
                'descripcion_material' => $material['descripcion_material'],
                'precio_metro' => $material['precio_metro'],
                'stock_material' => $material['stock_material'],
                'categoria_material' => $material['categoria_material'],
                'proveedor_material' => $material['proveedor_material'],
                'fecha_registro' => $material['fecha_registro'],
                'fecha_actualizacion' => $material['fecha_actualizacion']
            ]);
            
            $stmt_insert->execute([
                $material['id_material'], 
                $id_usuario, 
                $datos_eliminados, 
                $datos_completos
            ]);
        }
        
        // 3. Eliminar permanentemente todos los materiales en papelera
        $stmt_delete = $conex->prepare("DELETE FROM materiales WHERE activo = 0");
        $stmt_delete->execute();
        
        $conex->commit();
        
        $_SESSION['mensaje'] = "Se eliminaron permanentemente $totalMateriales materiales de la papelera";
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'No hay materiales en la papelera para eliminar';
        $_SESSION['tipo_mensaje'] = 'info';
    }
    
} catch (PDOException $e) {
    $conex->rollBack();
    $_SESSION['mensaje'] = 'Error al vaciar la papelera: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

// Redirigir de vuelta al índice de materiales
header('Location: index.php');
exit;
?>