<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

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

$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$precio = trim($_POST['precio'] ?? '');
$stock = trim($_POST['stock'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$proveedor = trim($_POST['proveedor'] ?? '');

$errores = [];

if (empty($nombre)) $errores[] = 'El nombre es obligatorio';
if (empty($descripcion)) $errores[] = 'La descripción es obligatoria';
if (empty($precio) || !is_numeric($precio) || $precio < 0) $errores[] = 'Precio inválido';
if (empty($stock) || !is_numeric($stock) || $stock < 0) $errores[] = 'Stock inválido';
if (empty($categoria)) $errores[] = 'La categoría es obligatoria';
if (empty($proveedor)) $errores[] = 'El proveedor es obligatorio';

if (!empty($errores)) {
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id") . 
          '?error=' . urlencode(implode(', ', $errores)));
    exit;
}

try {
    if ($accion === 'crear') {
        $sql = "INSERT INTO materiales (nombre_material, descripcion_material, precio_metro, 
                stock_material, categoria_material, proveedor_material, fecha_actualizacion) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conex->prepare($sql);
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria, $proveedor]);
        
        $mensaje = 'Material creado exitosamente';
    } elseif ($accion === 'editar' && $id > 0) {
        $sql = "UPDATE materiales SET 
                nombre_material = ?, 
                descripcion_material = ?, 
                precio_metro = ?, 
                stock_material = ?, 
                categoria_material = ?, 
                proveedor_material = ?,
                fecha_actualizacion = NOW() 
                WHERE id_material = ?";
        $stmt = $conex->prepare($sql);
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria, $proveedor, $id]);
        
        $mensaje = 'Material actualizado exitosamente';
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