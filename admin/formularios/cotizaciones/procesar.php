<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos
if (!isAdmin() && !isSeller()) {
    $_SESSION['mensaje'] = 'No tiene permisos para realizar esta acción';
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

// Obtener acción
$accion = $_POST['accion'] ?? '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Validar datos
$errores = [];

$id_cliente = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;
$id_vehiculo = isset($_POST['id_vehiculo']) ? intval($_POST['id_vehiculo']) : 0;
$servicios = $_POST['servicios'] ?? [];
$precios = $_POST['precios'] ?? [];
$subtotal = isset($_POST['subtotal_cotizacion']) ? floatval($_POST['subtotal_cotizacion']) : 0;
$valor_adicional = isset($_POST['valor_adicional']) ? floatval($_POST['valor_adicional']) : 0;
$iva = isset($_POST['iva']) ? floatval($_POST['iva']) : 0;
$total = isset($_POST['total_cotizacion']) ? floatval($_POST['total_cotizacion']) : 0;
$notas = trim($_POST['notas_cotizacion'] ?? '');

// Validaciones
if ($id_cliente <= 0) $errores[] = 'Cliente inválido';
if ($id_vehiculo <= 0) $errores[] = 'Vehículo inválido';
if (empty($servicios)) $errores[] = 'Debe seleccionar al menos un servicio';
if ($iva < 0 || $iva > 100) $errores[] = 'IVA debe estar entre 0 y 100';

if (!empty($errores)) {
    $_SESSION['mensaje'] = implode(', ', $errores);
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
    exit;
}

try {
    $conex->beginTransaction();
    
    if ($accion === 'crear') {
        // Crear nueva cotización
        $sql = "INSERT INTO cotizaciones (id_usuario, id_cliente, id_vehiculo, subtotal_cotizacion, 
                valor_adicional, iva, total_cotizacion, estado_cotizacion, notas_cotizacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Pendiente', ?)";
        $stmt = $conex->prepare($sql);
        $stmt->execute([
            $_SESSION['usuario_id'], 
            $id_cliente, 
            $id_vehiculo, 
            $subtotal, 
            $valor_adicional, 
            $iva, 
            $total, 
            $notas
        ]);
        
        $id_cotizacion = $conex->lastInsertId();
        
        // Insertar servicios de la cotización
        $sql_servicios = "INSERT INTO cotizacion_servicios (id_cotizacion, id_servicio, precio) VALUES (?, ?, ?)";
        $stmt_servicios = $conex->prepare($sql_servicios);
        
        foreach ($servicios as $index => $id_servicio) {
            $precio = $precios[$index];
            $stmt_servicios->execute([$id_cotizacion, $id_servicio, $precio]);
        }
        
        $_SESSION['mensaje'] = 'Cotización creada exitosamente';
        $_SESSION['tipo_mensaje'] = 'success';
        
    } elseif ($accion === 'editar' && $id > 0) {
        // Actualizar cotización existente
        $sql = "UPDATE cotizaciones SET 
                id_cliente = ?, 
                id_vehiculo = ?, 
                subtotal_cotizacion = ?, 
                valor_adicional = ?, 
                iva = ?, 
                total_cotizacion = ?, 
                notas_cotizacion = ? 
                WHERE id_cotizacion = ?";
        $stmt = $conex->prepare($sql);
        $stmt->execute([
            $id_cliente, 
            $id_vehiculo, 
            $subtotal, 
            $valor_adicional, 
            $iva, 
            $total, 
            $notas, 
            $id
        ]);
        
        // Eliminar servicios anteriores
        $sql_delete = "DELETE FROM cotizacion_servicios WHERE id_cotizacion = ?";
        $stmt_delete = $conex->prepare($sql_delete);
        $stmt_delete->execute([$id]);
        
        // Insertar nuevos servicios
        $sql_servicios = "INSERT INTO cotizacion_servicios (id_cotizacion, id_servicio, precio) VALUES (?, ?, ?)";
        $stmt_servicios = $conex->prepare($sql_servicios);
        
        foreach ($servicios as $index => $id_servicio) {
            $precio = $precios[$index];
            $stmt_servicios->execute([$id, $id_servicio, $precio]);
        }
        
        $_SESSION['mensaje'] = 'Cotización actualizada exitosamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        throw new Exception('Acción inválida');
    }
    
    $conex->commit();
    header("Location: index.php");
    exit;
    
} catch (PDOException $e) {
    $conex->rollBack();
    $_SESSION['mensaje'] = 'Error en la base de datos: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
    exit;
} catch (Exception $e) {
    $conex->rollBack();
    $_SESSION['mensaje'] = $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
    exit;
}