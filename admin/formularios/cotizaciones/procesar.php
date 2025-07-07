<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../../php/auth.php';

if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

session_start();

// Validar CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: index.php?error=Token de seguridad inválido');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Validar y sanitizar entradas
$accion = isset($_POST['accion']) && in_array($_POST['accion'], ['crear', 'editar']) ? $_POST['accion'] : '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Validar datos requeridos
$errores = [];
$camposRequeridos = ['cliente', 'vehiculo', 'subtotal', 'iva', 'total', 'servicios_json'];
foreach ($camposRequeridos as $campo) {
    if (empty($_POST[$campo])) {
        $errores[] = "El campo $campo es requerido";
    }
}

if (!empty($errores)) {
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id") .
    '?error=' . urlencode(implode(', ', $errores)));
    exit;
}

// Procesar servicios y valor adicional
$servicios_data = json_decode($_POST['servicios_json'], true);
if (json_last_error() !== JSON_ERROR_NONE || !is_array($servicios_data['servicios'])) {
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id") .
    '?error=Error en los servicios seleccionados');
    exit;
}

$valor_adicional = isset($servicios_data['valor_adicional']) ? floatval($servicios_data['valor_adicional']) : 0;

// Asignar valores
$id_cliente = intval($_POST['cliente']);
$id_vehiculo = intval($_POST['vehiculo']);
$subtotal = floatval($_POST['subtotal']);
$iva = floatval($_POST['iva']);
$total = floatval($_POST['total']);
$notas = trim($_POST['notas'] ?? '');

try {
    $conex->beginTransaction();

    if ($accion === 'crear') {
        // Insertar cotización
        $sql = "INSERT INTO cotizaciones (
                id_usuario, 
                id_cliente, 
                id_vehiculo, 
                fecha_cotizacion, 
                subtotal_cotizacion, 
                iva, 
                total_cotizacion, 
                valor_adicional,
                estado_cotizacion, 
                notas_cotizacion
            ) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, 'Pendiente', ?)";
        $stmt = $conex->prepare($sql);
        $stmt->execute([
            $_SESSION['id_usuario'],
            $id_cliente,
            $id_vehiculo,
            $subtotal,
            $iva,
            $total,
            $valor_adicional,
            $notas
        ]);
        
        $id_cotizacion = $conex->lastInsertId();
        $mensaje = 'Cotización creada exitosamente';
    } elseif ($accion === 'editar' && $id > 0) {
        // Actualizar cotización
        $sql = "UPDATE cotizaciones SET 
                id_cliente = ?, 
                id_vehiculo = ?, 
                subtotal_cotizacion = ?, 
                iva = ?, 
                total_cotizacion = ?, 
                valor_adicional = ?,
                notas_cotizacion = ? 
                WHERE id_cotizacion = ?";
        $stmt = $conex->prepare($sql);
        $stmt->execute([
            $id_cliente,
            $id_vehiculo,
            $subtotal,
            $iva,
            $total,
            $valor_adicional,
            $notas,
            $id
        ]);
        
        $id_cotizacion = $id;
        $mensaje = 'Cotización actualizada exitosamente';
    } else {
        throw new Exception('Acción inválida');
    }

    // Insertar servicios de la cotización (eliminar los anteriores si es edición)
    if ($accion === 'editar') {
        $conex->prepare("DELETE FROM cotizacion_servicios WHERE id_cotizacion = ?")->execute([$id_cotizacion]);
    }

    $sqlServicios = "INSERT INTO cotizacion_servicios (id_cotizacion, id_servicio, precio) VALUES (?, ?, ?)";
    $stmtServicios = $conex->prepare($sqlServicios);

    foreach ($servicios_data['servicios'] as $servicio) {
        if (!isset($servicio['id']) || !isset($servicio['precio'])) {
            throw new Exception('Datos de servicio inválidos');
        }
        $stmtServicios->execute([
            $id_cotizacion,
            intval($servicio['id']),
            floatval($servicio['precio'])
        ]);
    }

    $conex->commit();

    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = 'success';
    header("Location: ver.php?id=$id_cotizacion");
    exit;
} catch (PDOException $e) {
    $conex->rollBack();
    error_log("Error en la base de datos: " . $e->getMessage());
    $_SESSION['mensaje'] = 'Error al procesar la cotización';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
    exit;
} catch (Exception $e) {
    $conex->rollBack();
    error_log("Error general: " . $e->getMessage());
    $_SESSION['mensaje'] = $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
    exit;
}