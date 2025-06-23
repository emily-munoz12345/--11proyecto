<?php
require_once 'auth.php';
requireAuth();

// Verificar permisos
if (!isAdmin() && !isSeller()) {
    header('Location: ../admin/dashboard.php');
    exit;
}

require_once 'conexion.php';

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../clientes.php');
    exit;
}

// Recoger y validar datos
$accion = $_POST['accion'] ?? '';
$id = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;
$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$notas = trim($_POST['notas'] ?? '');

// Validaciones básicas
if (empty($nombre) || empty($correo) || empty($telefono) || empty($direccion)) {
    header('Location: ../clientes.php?error=Datos incompletos');
    exit;
}

try {
    if ($accion === 'agregar') {
        // Insertar nuevo cliente
        $sql = "INSERT INTO clientes (nombre_cliente, correo_cliente, telefono_cliente, direccion_cliente, notas_cliente) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conex->prepare($sql);
        $stmt->execute([$nombre, $correo, $telefono, $direccion, $notas]);
        
        $mensaje = 'Cliente agregado correctamente';
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
        
        $mensaje = 'Cliente actualizado correctamente';
    }
    
    header("Location: ../clientes.php?success=$mensaje");
} catch (PDOException $e) {
    $error = 'Error al procesar el cliente: ' . $e->getMessage();
    header("Location: ../clientes.php?error=$error");
}