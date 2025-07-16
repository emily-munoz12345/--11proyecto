<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos
if (!isAdmin() && !isSeller()) {
    $_SESSION['mensaje'] = 'No tienes permisos para realizar esta acción';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ../dashboard.php');
    exit;
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['mensaje'] = 'Método no permitido';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Obtener acción y datos del formulario
$accion = $_POST['accion'] ?? '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$id_cliente = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;

// Validar y limpiar datos
$marca = trim($_POST['marca'] ?? '');
$modelo = trim($_POST['modelo'] ?? '');
$placa = strtoupper(str_replace(['-', ' '], '', trim($_POST['placa'] ?? '')));
$notas = trim($_POST['notas'] ?? '');

// Validaciones
$errores = [];

// Validar campos obligatorios
if (empty($marca)) $errores[] = 'La marca es obligatoria';
if (empty($modelo)) $errores[] = 'El modelo es obligatorio';
if (empty($placa)) $errores[] = 'La placa es obligatoria';
if ($accion === 'crear' && $id_cliente <= 0) $errores[] = 'Debe seleccionar un cliente';

// Validar formato de placa (3 letras + 3-4 números)
if (!empty($placa) && !preg_match('/^[A-Z]{3}\d{3,4}$/', $placa)) {
    $errores[] = 'Formato de placa inválido. Debe ser 3 letras seguidas de 3-4 números (ej: ABC123)';
}

// Validar longitud máxima de campos
if (strlen($marca) > 50) $errores[] = 'La marca no puede exceder los 50 caracteres';
if (strlen($modelo) > 50) $errores[] = 'El modelo no puede exceder los 50 caracteres';
if (strlen($placa) > 20) $errores[] = 'La placa no puede exceder los 20 caracteres';
if (strlen($notas) > 500) $errores[] = 'Las notas no pueden exceder los 500 caracteres';

// Si hay errores, redireccionar con mensajes
if (!empty($errores)) {
    $_SESSION['mensaje'] = implode('<br>', $errores);
    $_SESSION['tipo_mensaje'] = 'danger';
    $_SESSION['form_data'] = [
        'marca' => $marca,
        'modelo' => $modelo,
        'placa' => $placa,
        'notas' => $notas,
        'id_cliente' => $id_cliente
    ];
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
    exit;
}

try {
    $conex->beginTransaction();

    if ($accion === 'crear') {
        // Verificar si la placa ya existe
        $stmt = $conex->prepare("SELECT id_vehiculo FROM vehiculos WHERE placa_vehiculo = ?");
        $stmt->execute([$placa]);
        if ($stmt->fetch()) {
            throw new Exception('Ya existe un vehículo con esta placa');
        }

        // Verificar que el cliente existe
        $stmt = $conex->prepare("SELECT 1 FROM clientes WHERE id_cliente = ?");
        $stmt->execute([$id_cliente]);
        if (!$stmt->fetch()) {
            throw new Exception('El cliente seleccionado no existe');
        }

        // Insertar nuevo vehículo
        $sql = "INSERT INTO vehiculos (marca_vehiculo, modelo_vehiculo, placa_vehiculo, notas_vehiculo) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conex->prepare($sql);
        $stmt->execute([$marca, $modelo, $placa, $notas]);
        
        $id_vehiculo = $conex->lastInsertId();
        
        // Asociar vehículo al cliente
        $sql = "INSERT INTO cliente_vehiculo (id_cliente, id_vehiculo) VALUES (?, ?)";
        $stmt = $conex->prepare($sql);
        $stmt->execute([$id_cliente, $id_vehiculo]);
        
        $mensaje = 'Vehículo creado exitosamente';
        $redireccion = "index.php?id=$id_vehiculo";

    } elseif ($accion === 'editar' && $id > 0) {
        // Verificar si el vehículo existe
        $stmt = $conex->prepare("SELECT 1 FROM vehiculos WHERE id_vehiculo = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            throw new Exception('El vehículo no existe');
        }

        // Verificar si la placa ya existe en otro vehículo
        $stmt = $conex->prepare("SELECT id_vehiculo FROM vehiculos WHERE placa_vehiculo = ? AND id_vehiculo != ?");
        $stmt->execute([$placa, $id]);
        if ($stmt->fetch()) {
            throw new Exception('Ya existe otro vehículo con esta placa');
        }

        // Actualizar vehículo
        $sql = "UPDATE vehiculos SET 
                marca_vehiculo = ?, 
                modelo_vehiculo = ?, 
                placa_vehiculo = ?, 
                notas_vehiculo = ? 
                WHERE id_vehiculo = ?";
        $stmt = $conex->prepare($sql);
        $stmt->execute([$marca, $modelo, $placa, $notas, $id]);
        
        $mensaje = 'Vehículo actualizado exitosamente';
        $redireccion = "index.php?id=$id";

    } else {
        throw new Exception('Acción no válida');
    }

    $conex->commit();
    
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = 'success';
    header("Location: $redireccion");
    exit;

} catch (Exception $e) {
    $conex->rollBack();
    
    $_SESSION['mensaje'] = 'Error: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    $_SESSION['form_data'] = [
        'marca' => $marca,
        'modelo' => $modelo,
        'placa' => $placa,
        'notas' => $notas,
        'id_cliente' => $id_cliente
    ];
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
    exit;
}