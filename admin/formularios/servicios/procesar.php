<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos
if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Configurar zona horaria
date_default_timezone_set('America/Bogota');

// Función para redireccionar con mensaje de error
function redirigirConError($mensaje) {
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// Función para redireccionar con mensaje de éxito
function redirigirConExito($mensaje, $url = 'index.php') {
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = 'success';
    header('Location: ' . $url);
    exit;
}

// Procesar según la acción
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

try {
    switch ($accion) {
        case 'crear':
            procesarCreacion();
            break;
            
        case 'editar':
            procesarEdicion();
            break;
            
        case 'eliminar':
            procesarEliminacion();
            break;
            
        default:
            redirigirConError('Acción no válida');
    }
} catch (Exception $e) {
    redirigirConError('Error: ' . $e->getMessage());
}

function procesarCreacion() {
    global $conex;
    
    // Validar campos requeridos
    $camposRequeridos = ['nombre_servicio', 'categoria_servicio', 'precio_servicio', 'tiempo_estimado', 'descripcion_servicio'];
    foreach ($camposRequeridos as $campo) {
        if (empty($_POST[$campo])) {
            redirigirConError("El campo $campo es requerido");
        }
    }
    
    // Sanitizar datos
    $nombre = trim($_POST['nombre_servicio']);
    $categoria = trim($_POST['categoria_servicio']);
    $precio = floatval($_POST['precio_servicio']);
    $tiempo = trim($_POST['tiempo_estimado']);
    $descripcion = trim($_POST['descripcion_servicio']);
    
    // Validaciones adicionales
    if (strlen($nombre) < 3) {
        redirigirConError('El nombre del servicio debe tener al menos 3 caracteres');
    }
    
    if ($precio <= 0) {
        redirigirConError('El precio debe ser mayor a 0');
    }
    
    if (strlen($descripcion) < 10) {
        redirigirConError('La descripción debe tener al menos 10 caracteres');
    }
    
    // Verificar si ya existe un servicio con el mismo nombre
    $stmt = $conex->prepare("SELECT id_servicio FROM servicios WHERE nombre_servicio = ? AND activo = 1");
    $stmt->execute([$nombre]);
    
    if ($stmt->fetch()) {
        redirigirConError('Ya existe un servicio con ese nombre');
    }
    
    // Insertar nuevo servicio (usando la estructura correcta de la tabla)
    $stmt = $conex->prepare("INSERT INTO servicios (nombre_servicio, categoria_servicio, precio_servicio, tiempo_estimado, descripcion_servicio, fecha_registro) VALUES (?, ?, ?, ?, ?, NOW())");
    
    $stmt->execute([
        $nombre,
        $categoria,
        $precio,
        $tiempo,
        $descripcion
    ]);
    
    redirigirConExito('Servicio creado exitosamente');
}

function procesarEdicion() {
    global $conex;
    
    // Validar ID
    if (empty($_POST['id'])) {
        redirigirConError('ID de servicio no válido');
    }
    
    $id = intval($_POST['id']);
    
    // Validar campos requeridos
    $camposRequeridos = ['nombre_servicio', 'categoria_servicio', 'precio_servicio', 'tiempo_estimado', 'descripcion_servicio'];
    foreach ($camposRequeridos as $campo) {
        if (empty($_POST[$campo])) {
            redirigirConError("El campo $campo es requerido");
        }
    }
    
    // Sanitizar datos
    $nombre = trim($_POST['nombre_servicio']);
    $categoria = trim($_POST['categoria_servicio']);
    $precio = floatval($_POST['precio_servicio']);
    $tiempo = trim($_POST['tiempo_estimado']);
    $descripcion = trim($_POST['descripcion_servicio']);
    
    // Validaciones adicionales
    if (strlen($nombre) < 3) {
        redirigirConError('El nombre del servicio debe tener al menos 3 caracteres');
    }
    
    if ($precio <= 0) {
        redirigirConError('El precio debe ser mayor a 0');
    }
    
    if (strlen($descripcion) < 10) {
        redirigirConError('La descripción debe tener al menos 10 caracteres');
    }
    
    // Verificar si ya existe otro servicio con el mismo nombre
    $stmt = $conex->prepare("SELECT id_servicio FROM servicios WHERE nombre_servicio = ? AND id_servicio != ? AND activo = 1");
    $stmt->execute([$nombre, $id]);
    
    if ($stmt->fetch()) {
        redirigirConError('Ya existe otro servicio con ese nombre');
    }
    
    // Actualizar servicio (usando la estructura correcta de la tabla)
    $stmt = $conex->prepare("UPDATE servicios SET 
                            nombre_servicio = ?, 
                            categoria_servicio = ?, 
                            precio_servicio = ?, 
                            tiempo_estimado = ?, 
                            descripcion_servicio = ? 
                            WHERE id_servicio = ?");
    
    $resultado = $stmt->execute([
        $nombre,
        $categoria,
        $precio,
        $tiempo,
        $descripcion,
        $id
    ]);
    
    if ($resultado) {
        redirigirConExito('Servicio actualizado exitosamente');
    } else {
        redirigirConError('No se pudo actualizar el servicio');
    }
}

function procesarEliminacion() {
    global $conex;
    
    // Validar ID
    if (empty($_GET['id'])) {
        redirigirConError('ID de servicio no válido');
    }
    
    $id = intval($_GET['id']);
    
    // Verificar si el servicio existe
    $stmt = $conex->prepare("SELECT id_servicio FROM servicios WHERE id_servicio = ? AND activo = 1");
    $stmt->execute([$id]);
    
    if (!$stmt->fetch()) {
        redirigirConError('El servicio no existe');
    }
    
    // Verificar si el servicio está siendo usado en alguna cotización
    // CORRECCIÓN: El nombre correcto de la tabla es cotizacion_servicios (singular)
    $stmt = $conex->prepare("SELECT COUNT(*) FROM cotizacion_servicios WHERE id_servicio = ?");
    $stmt->execute([$id]);
    $usoEnCotizaciones = $stmt->fetchColumn();
    
    if ($usoEnCotizaciones > 0) {
        redirigirConError('No se puede eliminar el servicio porque está siendo usado en ' . $usoEnCotizaciones . ' cotización(es)');
    }
    
    // Eliminar servicio (eliminación lógica estableciendo activo = 0)
    $stmt = $conex->prepare("UPDATE servicios SET activo = 0, fecha_eliminacion = NOW() WHERE id_servicio = ?");
    $stmt->execute([$id]);
    
    redirigirConExito('Servicio eliminado exitosamente');
}
?>