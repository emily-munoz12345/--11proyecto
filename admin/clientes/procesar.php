<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

// Verificar permisos
if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Obtener acción
$accion = $_POST['accion'] ?? '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Validar datos comunes
$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$notas = trim($_POST['notas'] ?? '');

// Validaciones básicas
$errores = [];

if (empty($nombre)) $errores[] = 'El nombre es obligatorio';
if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = 'Correo inválido';
if (empty($telefono)) $errores[] = 'El teléfono es obligatorio';
if (empty($direccion)) $errores[] = 'La dirección es obligatoria';

if (!empty($errores)) {
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id") . 
          '?error=' . urlencode(implode(', ', $errores)));
    exit;
}

try {
    if ($accion === 'crear') {
        // Crear nuevo cliente
        $sql = "INSERT INTO clientes (nombre_cliente, correo_cliente, telefono_cliente, 
                direccion_cliente, notas_cliente, fecha_registro) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conex->prepare($sql);
        $stmt->execute([$nombre, $correo, $telefono, $direccion, $notas]);
        
        $mensaje = 'Cliente creado exitosamente';
    } elseif ($accion === 'editar' && $id > 0) {
        // Actualizar cliente existente
        $sql = "UPDATE clientes SET 
                nombre_cliente = ?, 
                correo_cliente = ?, 
                telefono_cliente = ?, 
                direccion_cliente = ?, 
                notas_cliente = ? 
                WHERE id_cliente = ?";
        $stmt = $conex->prepare($sql);
        $stmt->execute([$nombre, $correo, $telefono, $direccion, $notas, $id]);
        
        $mensaje = 'Cliente actualizado exitosamente';
    } else {
        throw new Exception('Acción inválida');
    }
    
    header("Location: index.php?success=" . urlencode($mensaje));
} catch (PDOException $e) {
    $error = 'Error en la base de datos: ' . $e->getMessage();
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id") . 
          '?error=' . urlencode($error));
}