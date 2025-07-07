<?php
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

if (!isAdmin() && !isTecnico()) {
    header('Location: ../dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['accion'])) {
    header('Location: index.php');
    exit;
}

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
$id = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

try {
    $conex->beginTransaction();

    if ($accion === 'crear') {
        // Validar datos para crear nuevo trabajo
        $id_cotizacion = intval($_POST['id_cotizacion']);
        $fecha_inicio = $_POST['fecha_inicio'];
        $estado = $_POST['estado'];
        $notas = trim($_POST['notas'] ?? '');

        // Validar que la cotización existe y está aprobada
        $stmt = $conex->prepare("SELECT estado_cotizacion FROM cotizaciones WHERE id_cotizacion = ?");
        $stmt->execute([$id_cotizacion]);
        $cotizacion = $stmt->fetch();

        if (!$cotizacion || $cotizacion['estado_cotizacion'] !== 'Aprobado') {
            throw new Exception('La cotización no existe o no está aprobada');
        }

        // Validar que no existe ya un trabajo para esta cotización
        $stmt = $conex->prepare("SELECT 1 FROM trabajos WHERE id_cotizacion = ?");
        $stmt->execute([$id_cotizacion]);
        if ($stmt->fetch()) {
            throw new Exception('Ya existe un trabajo para esta cotización');
        }

        // Procesar fotos si se subieron
        $fotos = [];
        $maxFotos = 5;
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!empty($_FILES['fotos']['name'][0])) {
            $uploadDir = __DIR__ . '/../../uploads/trabajos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileCount = 0;
            foreach ($_FILES['fotos']['tmp_name'] as $key => $tmp_name) {
                if ($fileCount >= $maxFotos) break;
                
                // Validar tipo y tamaño de archivo
                $fileType = strtolower(pathinfo($_FILES['fotos']['name'][$key], PATHINFO_EXTENSION));
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (!in_array($fileType, $allowedTypes)) continue;
                if ($_FILES['fotos']['size'][$key] > $maxSize) continue;
                if ($_FILES['fotos']['error'][$key] !== UPLOAD_ERR_OK) continue;
                
                // Generar nombre único
                $fileName = 'trabajo_' . uniqid() . '.' . $fileType;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($tmp_name, $targetPath)) {
                    $fotos[] = '/uploads/trabajos/' . $fileName;
                    $fileCount++;
                }
            }
        }

        // Insertar nuevo trabajo
        $sql = "INSERT INTO trabajos (id_cotizacion, fecha_inicio, fecha_fin, estado, notas, fotos) 
                VALUES (?, ?, NULL, ?, ?, ?)";
        $stmt = $conex->prepare($sql);
        $stmt->execute([
            $id_cotizacion,
            $fecha_inicio,
            $estado,
            $notas,
            !empty($fotos) ? implode(',', $fotos) : null
        ]);

        $id_trabajo = $conex->lastInsertId();
        $mensaje = 'Trabajo creado exitosamente';

    } elseif ($accion === 'editar' && $id > 0) {
        // Validar datos para editar trabajo
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
        $estado = $_POST['estado'];
        $notas = trim($_POST['notas'] ?? '');

        // Obtener fotos existentes
        $stmt = $conex->prepare("SELECT fotos FROM trabajos WHERE id_trabajos = ?");
        $stmt->execute([$id]);
        $trabajo = $stmt->fetch();
        $fotos = [];
        
        if (!empty($trabajo['fotos'])) {
            $fotos = is_array($trabajo['fotos']) ? $trabajo['fotos'] : explode(',', $trabajo['fotos']);
            $fotos = array_filter($fotos); // Eliminar elementos vacíos
        }

        // Procesar nuevas fotos si se subieron
        $maxFotos = 5;
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!empty($_FILES['fotos']['name'][0])) {
            $uploadDir = __DIR__ . '/../../uploads/trabajos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileCount = 0;
            foreach ($_FILES['fotos']['tmp_name'] as $key => $tmp_name) {
                if (count($fotos) + $fileCount >= $maxFotos) break;
                
                // Validar tipo y tamaño de archivo
                $fileType = strtolower(pathinfo($_FILES['fotos']['name'][$key], PATHINFO_EXTENSION));
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (!in_array($fileType, $allowedTypes)) continue;
                if ($_FILES['fotos']['size'][$key] > $maxSize) continue;
                if ($_FILES['fotos']['error'][$key] !== UPLOAD_ERR_OK) continue;
                
                // Generar nombre único
                $fileName = 'trabajo_' . $id . '_' . uniqid() . '.' . $fileType;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($tmp_name, $targetPath)) {
                    $fotos[] = '/uploads/trabajos/' . $fileName;
                    $fileCount++;
                }
            }
        }

        // Actualizar trabajo
        $sql = "UPDATE trabajos SET 
                fecha_inicio = ?, 
                fecha_fin = ?, 
                estado = ?, 
                notas = ?, 
                fotos = ?
                WHERE id_trabajos = ?";
        $stmt = $conex->prepare($sql);
        $stmt->execute([
            $fecha_inicio,
            $fecha_fin,
            $estado,
            $notas,
            !empty($fotos) ? implode(',', $fotos) : null,
            $id
        ]);

        $mensaje = 'Trabajo actualizado exitosamente';

    } elseif ($accion === 'eliminar_foto' && $id > 0 && isset($_GET['foto'])) {
        // Eliminar una foto específica de un trabajo
        $foto = urldecode($_GET['foto']);
        
        $stmt = $conex->prepare("SELECT fotos FROM trabajos WHERE id_trabajos = ?");
        $stmt->execute([$id]);
        $trabajo = $stmt->fetch();
        
        if ($trabajo) {
            $fotos = [];
            if (!empty($trabajo['fotos'])) {
                $fotos = is_array($trabajo['fotos']) ? $trabajo['fotos'] : explode(',', $trabajo['fotos']);
                $fotos = array_filter($fotos); // Eliminar elementos vacíos
            }
            
            // Buscar y eliminar la foto específica
            $key = array_search($foto, $fotos);
            if ($key !== false) {
                // Eliminar archivo físico
                $ruta_foto = __DIR__ . '/../..' . $fotos[$key];
                if (file_exists($ruta_foto)) {
                    unlink($ruta_foto);
                }
                
                // Eliminar del array
                unset($fotos[$key]);
                $fotos = array_values($fotos); // Reindexar array
                
                // Actualizar base de datos
                $stmt = $conex->prepare("UPDATE trabajos SET fotos = ? WHERE id_trabajos = ?");
                $stmt->execute([!empty($fotos) ? implode(',', $fotos) : null, $id]);
            }
        }
        
        header("Location: editar.php?id=$id");
        exit;

    } elseif ($accion === 'cambiar_estado' && $id > 0 && isset($_GET['estado'])) {
        // Cambiar estado del trabajo
        $nuevo_estado = $_GET['estado'];
        $fecha_fin = ($nuevo_estado == 'Entregado') ? date('Y-m-d') : null;
        
        $sql = "UPDATE trabajos SET estado = ?, fecha_fin = ? WHERE id_trabajos = ?";
        $stmt = $conex->prepare($sql);
        $stmt->execute([$nuevo_estado, $fecha_fin, $id]);
        
        $mensaje = 'Estado del trabajo actualizado';
    } else {
        throw new Exception('Acción no válida');
    }

    $conex->commit();
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = 'success';
    header("Location: index.php?id=$id");
    exit;

} catch (Exception $e) {
    $conex->rollBack();
    $_SESSION['mensaje'] = 'Error: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    
    if ($accion === 'crear') {
        header('Location: crear.php?error=' . urlencode($e->getMessage()));
    } elseif ($accion === 'editar' || $accion === 'eliminar_foto') {
        header("Location: editar.php?id=$id&error=" . urlencode($e->getMessage()));
    } else {
        header('Location: index.php');
    }
    exit;
}