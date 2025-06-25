<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

if (!isAdmin()) {
    header('Location: ../dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$accion = $_POST['accion'] ?? '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

$id_cotizacion = intval($_POST['cotizacion'] ?? 0);
$id_tecnico = intval($_POST['tecnico'] ?? 0);
$fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
$fecha_fin_estimada = trim($_POST['fecha_fin_estimada'] ?? '');
$notas = trim($_POST['notas'] ?? '');

$errores = [];

// Validaciones básicas
if (empty($id_cotizacion)) $errores[] = 'Seleccione una cotización válida';
if (empty($id_tecnico)) $errores[] = 'Seleccione un técnico válido';
if (empty($fecha_inicio)) $errores[] = 'La fecha de inicio es obligatoria';

if (!empty($errores)) {
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id") . 
          '?error=' . urlencode(implode(', ', $errores)));
    exit;
}

try {
    $conex->beginTransaction();
    
    if ($accion === 'crear') {
        // Insertar trabajo
        $sql = "INSERT INTO trabajos (id_cotizacion, id_tecnico, id_creador, fecha_inicio, 
                fecha_fin_estimada, estado, notas) 
                VALUES (?, ?, ?, ?, ?, 'Pendiente', ?)";
        $stmt = $conex->prepare($sql);
        $stmt->execute([
            $id_cotizacion,
            $id_tecnico,
            $_SESSION['id_usuario'],
            $fecha_inicio,
            $fecha_fin_estimada ?: null,
            $notas
        ]);
        
        $id_trabajo = $conex->lastInsertId();
        $mensaje = 'Trabajo creado exitosamente';
        
        // Procesar fotos si se enviaron
        if (!empty($_FILES['fotos']['name'][0])) {
            $directorio = __DIR__ . '/../../uploads/trabajos/' . $id_trabajo . '/';
            
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }
            
            $sqlFoto = "INSERT INTO trabajo_fotos (id_trabajo, ruta) VALUES (?, ?)";
            $stmtFoto = $conex->prepare($sqlFoto);
            
            foreach ($_FILES['fotos']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['fotos']['error'][$key] === UPLOAD_ERR_OK) {
                    $nombre_archivo = uniqid() . '_' . basename($_FILES['fotos']['name'][$key]);
                    $ruta_completa = $directorio . $nombre_archivo;
                    
                    if (move_uploaded_file($tmp_name, $ruta_completa)) {
                        $ruta_relativa = '/uploads/trabajos/' . $id_trabajo . '/' . $nombre_archivo;
                        $stmtFoto->execute([$id_trabajo, $ruta_relativa]);
                    }
                }
            }
        }
    } elseif ($accion === 'editar' && $id > 0) {
        // Actualizar trabajo
        $sql = "UPDATE trabajos SET 
                id_tecnico = ?, 
                fecha_inicio = ?, 
                fecha_fin_estimada = ?, 
                notas = ? 
                WHERE id_trabajos = ?";
        $stmt = $conex->prepare($sql);
        $stmt->execute([
            $id_tecnico,
            $fecha_inicio,
            $fecha_fin_estimada ?: null,
            $notas,
            $id
        ]);
        
        $id_trabajo = $id;
        $mensaje = 'Trabajo actualizado exitosamente';
    } else {
        throw new Exception('Acción inválida');
    }
    
    $conex->commit();
    
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = 'success';
    header("Location: ver.php?id=$id_trabajo");
} catch (PDOException $e) {
    $conex->rollBack();
    $error = 'Error en la base de datos: ' . $e->getMessage();
    $_SESSION['mensaje'] = $error;
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
}