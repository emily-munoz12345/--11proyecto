<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin() && !isTechnician()) {
    header('Location: ../dashboard.php');
    exit;
}

// Verificar token CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['mensaje'] = 'Token de seguridad inválido';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: crear.php');
    exit;
}

if ($_POST['accion'] === 'crear') {
    try {
        // Obtener datos del formulario
        $id_cotizacion = $_POST['cotizacion'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'] ?: null;
        $estado = $_POST['estado'];
        $notas = $_POST['notas'] ?: '';
        
        // Validar datos requeridos
        if (empty($id_cotizacion) || empty($fecha_inicio) || empty($estado)) {
            throw new Exception('Todos los campos obligatorios deben ser completados');
        }
        
        // Verificar que la cotización existe y está pendiente
        $stmt_cotizacion = $conex->prepare("
            SELECT estado_cotizacion 
            FROM cotizaciones 
            WHERE id_cotizacion = ? AND activo = 1
        ");
        $stmt_cotizacion->execute([$id_cotizacion]);
        $cotizacion = $stmt_cotizacion->fetch(PDO::FETCH_ASSOC);
        
        if (!$cotizacion) {
            throw new Exception('La cotización seleccionada no existe o ha sido eliminada');
        }
        
        if ($cotizacion['estado_cotizacion'] !== 'Pendiente') {
            throw new Exception('Solo se pueden crear trabajos para cotizaciones en estado "Pendiente"');
        }
        
        // Verificar que la cotización no tenga ya un trabajo activo
        $stmt_verificar = $conex->prepare("
            SELECT COUNT(*) as count 
            FROM trabajos 
            WHERE id_cotizacion = ? AND activo = 1
        ");
        $stmt_verificar->execute([$id_cotizacion]);
        $resultado = $stmt_verificar->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado['count'] > 0) {
            throw new Exception('Esta cotización ya tiene un trabajo asociado');
        }
        
        // Procesar fotos
        $fotos_paths = [];
        if (!empty($_FILES['fotos']['name'][0])) {
            $upload_dir = __DIR__ . '/../../../uploads/trabajos/';
            
            // Crear directorio si no existe
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            foreach ($_FILES['fotos']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['fotos']['error'][$key] === UPLOAD_ERR_OK) {
                    // Validar tipo de archivo
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    $file_type = mime_content_type($tmp_name);
                    
                    if (!in_array($file_type, $allowed_types)) {
                        throw new Exception('Tipo de archivo no permitido: ' . $_FILES['fotos']['name'][$key]);
                    }
                    
                    // Validar tamaño del archivo (máximo 5MB)
                    if ($_FILES['fotos']['size'][$key] > 5 * 1024 * 1024) {
                        throw new Exception('El archivo es demasiado grande: ' . $_FILES['fotos']['name'][$key] . '. Máximo 5MB permitido.');
                    }
                    
                    $file_name = uniqid() . '_' . basename($_FILES['fotos']['name'][$key]);
                    $file_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($tmp_name, $file_path)) {
                        $fotos_paths[] = 'uploads/trabajos/' . $file_name;
                    } else {
                        throw new Exception('Error al subir la foto: ' . $_FILES['fotos']['name'][$key]);
                    }
                }
            }
        }
        
        $fotos_string = !empty($fotos_paths) ? implode(',', $fotos_paths) : '';
        
        // Iniciar transacción para asegurar consistencia
        $conex->beginTransaction();
        
        try {
            // Actualizar estado de la cotización a "Aprobado"
            $stmt_aprobar_cotizacion = $conex->prepare("
                UPDATE cotizaciones 
                SET estado_cotizacion = 'Aprobado' 
                WHERE id_cotizacion = ?
            ");
            $stmt_aprobar_cotizacion->execute([$id_cotizacion]);
            
            // Insertar trabajo en la base de datos
            $stmt = $conex->prepare("
                INSERT INTO trabajos 
                (id_cotizacion, fecha_inicio, fecha_fin, estado, notas, fotos, fecha_eliminacion, activo) 
                VALUES (?, ?, ?, ?, ?, ?, NULL, 1)
            ");
            
            $stmt->execute([
                $id_cotizacion,
                $fecha_inicio,
                $fecha_fin,
                $estado,
                $notas,
                $fotos_string
            ]);
            
            // Actualizar estado de la cotización a "Completada" si el trabajo se marca como "Entregado"
            if ($estado === 'Entregado') {
                $stmt_completar_cotizacion = $conex->prepare("
                    UPDATE cotizaciones 
                    SET estado_cotizacion = 'Completada' 
                    WHERE id_cotizacion = ?
                ");
                $stmt_completar_cotizacion->execute([$id_cotizacion]);
            }
            
            // Confirmar transacción
            $conex->commit();
            
            $_SESSION['mensaje'] = 'Trabajo creado exitosamente. La cotización ha sido aprobada automáticamente.';
            $_SESSION['tipo_mensaje'] = 'success';
            
            header('Location: index.php');
            exit;
            
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $conex->rollBack();
            throw $e;
        }
        
    } catch (Exception $e) {
        error_log("Error al crear trabajo: " . $e->getMessage());
        $_SESSION['mensaje'] = 'Error al crear el trabajo: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: crear.php');
        exit;
    }
}
?>