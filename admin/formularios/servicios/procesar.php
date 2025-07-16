<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$accion = $_POST['accion'] ?? '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$precio = trim($_POST['precio'] ?? '');
$tiempo = trim($_POST['tiempo'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');

$errores = [];

if (empty($nombre)) $errores[] = 'El nombre es obligatorio';
if (empty($descripcion)) $errores[] = 'La descripción es obligatoria';
if (empty($precio) || !is_numeric($precio) || $precio < 0) $errores[] = 'Precio inválido';
if (empty($tiempo)) $errores[] = 'El tiempo estimado es obligatorio';
if (empty($categoria)) $errores[] = 'La categoría es obligatoria';

if (!empty($errores)) {
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id") . 
          '?error=' . urlencode(implode(', ', $errores)));
    exit;
}

try {
    if ($accion === 'crear') {
        $sql = "INSERT INTO servicios (nombre_servicio, descripcion_servicio, precio_servicio, 
                tiempo_estimado, categoria_servicio) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conex->prepare($sql);
        $stmt->execute([$nombre, $descripcion, $precio, $tiempo, $categoria]);
        
        $mensaje = 'Servicio creado exitosamente';
    } elseif ($accion === 'editar' && $id > 0) {
        $sql = "UPDATE servicios SET 
                nombre_servicio = ?, 
                descripcion_servicio = ?, 
                precio_servicio = ?, 
                tiempo_estimado = ?, 
                categoria_servicio = ? 
                WHERE id_servicio = ?";
        $stmt = $conex->prepare($sql);
        $stmt->execute([$nombre, $descripcion, $precio, $tiempo, $categoria, $id]);
        
        $mensaje = 'Servicio actualizado exitosamente';
    } else {
        throw new Exception('Acción inválida');
    }
    
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = 'success';
    header("Location: index.php");
} catch (PDOException $e) {
    $error = 'Error en la base de datos: ' . $e->getMessage();
    $_SESSION['mensaje'] = $error;
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
}