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
    header('Location: index.php');
    exit;
}

if ($_POST['accion'] === 'editar') {
    try {
        // Obtener datos del formulario
        $id_trabajo = $_POST['id_trabajo'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'] ?: null;
        $estado = $_POST['estado'];
        $notas = $_POST['notas'] ?: '';
        $fotos_existentes = $_POST['fotos_existentes'] ?: '';
        
        // Validar datos requeridos
        if (empty($id_trabajo) || empty($fecha_inicio) || empty($estado)) {
            throw new Exception('Todos los campos obligatorios deben ser completados');
        }
        
        // Verificar que el trabajo existe
        $stmt_verificar = $conex->prepare("
            SELECT id_cotizacion, fotos 
            FROM trabajos 
            WHERE id_trabajos = ? AND activo = 1
        ");
        $stmt_verificar->execute([$id_trabajo]);
        $trabajo_actual = $stmt_verificar->fetch(PDO::FETCH_ASSOC);
        
        if (!$trabajo_actual) {
            throw new Exception('El trabajo no existe o ha sido eliminado');
        }
        
        $id_cotizacion = $trabajo_actual['id_cotizacion'];
        
        // Procesar eliminación de fotos
        $fotos_actualizadas = $fotos_existentes;
        if (!empty($_POST['fotos_eliminar'])) {
            $fotos_eliminar = json_decode($_POST['fotos_eliminar'], true);
            if (is_array($fotos_eliminar) && !empty($fotos_eliminar)) {
                $fotos_array = !empty($fotos_actualizadas) ? explode(',', $fotos_actualizadas) : [];
                $fotos_array = array_filter($fotos_array, function($foto) use ($fotos_eliminar) {
                    return !in_array($foto, $fotos_eliminar);
                });
                $fotos_actualizadas = implode(',', $fotos_array);
                
                // Eliminar archivos físicos
                foreach ($fotos_eliminar as $foto_path) {
                    $file_path = __DIR__ . '/../../../' . $foto_path;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }
        }
        
        // Procesar nuevas fotos
        $nuevas_fotos_paths = [];
        if (!empty($_FILES['nuevas_fotos']['name'][0])) {
            $upload_dir = __DIR__ . '/../../../uploads/trabajos/';
            
            // Crear directorio si no existe
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            foreach ($_FILES['nuevas_fotos']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['nuevas_fotos']['error'][$key] === UPLOAD_ERR_OK) {
                    // Validar tipo de archivo
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    $file_type = mime_content_type($tmp_name);
                    
                    if (!in_array($file_type, $allowed_types)) {
                        throw new Exception('Tipo de archivo no permitido: ' . $_FILES['nuevas_fotos']['name'][$key]);
                    }
                    
                    // Validar tamaño del archivo (máximo 5MB)
                    if ($_FILES['nuevas_fotos']['size'][$key] > 5 * 1024 * 1024) {
                        throw new Exception('El archivo es demasiado grande: ' . $_FILES['nuevas_fotos']['name'][$key] . '. Máximo 5MB permitido.');
                    }
                    
                    $file_name = uniqid() . '_' . basename($_FILES['nuevas_fotos']['name'][$key]);
                    $file_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($tmp_name, $file_path)) {
                        $nuevas_fotos_paths[] = 'uploads/trabajos/' . $file_name;
                    } else {
                        throw new Exception('Error al subir la foto: ' . $_FILES['nuevas_fotos']['name'][$key]);
                    }
                }
            }
        }
        
        // Combinar fotos existentes con nuevas
        $fotos_final = $fotos_actualizadas;
        if (!empty($nuevas_fotos_paths)) {
            if (!empty($fotos_final)) {
                $fotos_final .= ',' . implode(',', $nuevas_fotos_paths);
            } else {
                $fotos_final = implode(',', $nuevas_fotos_paths);
            }
        }
        
        // Iniciar transacción
        $conex->beginTransaction();
        
        try {
            // Actualizar trabajo en la base de datos
            $stmt = $conex->prepare("
                UPDATE trabajos 
                SET fecha_inicio = ?, fecha_fin = ?, estado = ?, notas = ?, fotos = ?
                WHERE id_trabajos = ?
            ");
            
            $stmt->execute([
                $fecha_inicio,
                $fecha_fin,
                $estado,
                $notas,
                $fotos_final,
                $id_trabajo
            ]);
            
            // Actualizar estado de la cotización según el estado del trabajo
            if ($estado === 'Entregado') {
                $stmt_cotizacion = $conex->prepare("
                    UPDATE cotizaciones 
                    SET estado_cotizacion = 'Completada' 
                    WHERE id_cotizacion = ?
                ");
                $stmt_cotizacion->execute([$id_cotizacion]);
            } elseif ($estado === 'Cancelado') {
                $stmt_cotizacion = $conex->prepare("
                    UPDATE cotizaciones 
                    SET estado_cotizacion = 'Rechazada' 
                    WHERE id_cotizacion = ?
                ");
                $stmt_cotizacion->execute([$id_cotizacion]);
            } else {
                // Si el trabajo está en progreso o pendiente, mantener la cotización como aprobada
                $stmt_cotizacion = $conex->prepare("
                    UPDATE cotizaciones 
                    SET estado_cotizacion = 'Aprobado' 
                    WHERE id_cotizacion = ?
                ");
                $stmt_cotizacion->execute([$id_cotizacion]);
            }
            
            // Confirmar transacción
            $conex->commit();
            
            $_SESSION['mensaje'] = 'Trabajo actualizado exitosamente';
            $_SESSION['tipo_mensaje'] = 'success';
            
            header('Location: index.php');
            exit;
            
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $conex->rollBack();
            throw $e;
        }
        
    } catch (Exception $e) {
        error_log("Error al actualizar trabajo: " . $e->getMessage());
        $_SESSION['mensaje'] = 'Error al actualizar el trabajo: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: editar.php?id=' . $id_trabajo);
        exit;
    }
}
?>