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

// Validar datos del formulario
$marca = trim($_POST['marca'] ?? '');
$modelo = trim($_POST['modelo'] ?? '');
$placa = trim($_POST['placa'] ?? '');
$notas = trim($_POST['notas'] ?? '');

// Validaciones básicas
$errores = [];

if (empty($marca)) $errores[] = 'La marca es obligatoria';
if (empty($modelo)) $errores[] = 'El modelo es obligatorio';
if (empty($placa)) $errores[] = 'La placa es obligatoria';

// Validar formato de placa (ejemplo: ABC-123 o ABC123)
if (!empty($placa) && !preg_match('/^[A-Za-z]{3}-?\d{3,4}$/', $placa)) {
    $errores[] = 'Formato de placa inválido (ejemplo: ABC-123 o ABC123)';
}

if (!empty($errores)) {
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id") . 
          '?error=' . urlencode(implode(', ', $errores)));
    exit;
}

try {
    if ($accion === 'crear') {
        // Crear nuevo vehículo
        $sql = "INSERT INTO vehiculos (marca_vehiculo, modelo_vehiculo, placa_vehiculo, nota_vehiculo) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conex->prepare($sql);
        $stmt->execute([$marca, $modelo, $placa, $notas]);
        
        $mensaje = 'Vehículo creado exitosamente';
    } elseif ($accion === 'editar' && $id > 0) {
        // Actualizar vehículo existente
        $sql = "UPDATE vehiculos SET 
                marca_vehiculo = ?, 
                modelo_vehiculo = ?, 
                placa_vehiculo = ?, 
                nota_vehiculo = ? 
                WHERE id_vehiculo = ?";
        $stmt = $conex->prepare($sql);
        $stmt->execute([$marca, $modelo, $placa, $notas, $id]);
        
        $mensaje = 'Vehículo actualizado exitosamente';
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