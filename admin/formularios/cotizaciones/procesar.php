<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar CSRF
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['mensaje'] = 'Token de seguridad inválido';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: crear.php');
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
    $_SESSION['mensaje'] = implode(', ', $errores);
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
    exit;
}

// Procesar servicios y valor adicional
$servicios_data = json_decode($_POST['servicios_json'], true);
if (json_last_error() !== JSON_ERROR_NONE || !isset($servicios_data['servicios']) || !is_array($servicios_data['servicios'])) {
    $_SESSION['mensaje'] = 'Error en los servicios seleccionados: ' . json_last_error_msg();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
    exit;
}

// Validar que haya al menos un servicio
if (count($servicios_data['servicios']) === 0) {
    $_SESSION['mensaje'] = 'Debe seleccionar al menos un servicio';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
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

// Verificar que el usuario está en sesión
if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['mensaje'] = 'Error: Usuario no autenticado';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

try {
    $conex->beginTransaction();

    if ($accion === 'crear') {
        // Verificar que el cliente y vehículo existen y están activos
        $stmt_check = $conex->prepare("SELECT id_cliente FROM clientes WHERE id_cliente = ? AND activo = 1");
        $stmt_check->execute([$id_cliente]);
        if (!$stmt_check->fetch()) {
            throw new Exception('El cliente seleccionado no existe o está inactivo');
        }

        $stmt_check = $conex->prepare("SELECT id_vehiculo FROM vehiculos WHERE id_vehiculo = ? AND activo = 1");
        $stmt_check->execute([$id_vehiculo]);
        if (!$stmt_check->fetch()) {
            throw new Exception('El vehículo seleccionado no existe o está inactivo');
        }

        // Verificar relación cliente-vehículo
        $stmt_check = $conex->prepare("SELECT id_cliente FROM cliente_vehiculo WHERE id_cliente = ? AND id_vehiculo = ? AND activo = 1");
        $stmt_check->execute([$id_cliente, $id_vehiculo]);
        if (!$stmt_check->fetch()) {
            throw new Exception('El vehículo seleccionado no pertenece al cliente');
        }

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
            $id_usuario,
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
        // Verificar que la cotización existe
        $stmt_check = $conex->prepare("SELECT id_cotizacion FROM cotizaciones WHERE id_cotizacion = ? AND activo = 1");
        $stmt_check->execute([$id]);
        if (!$stmt_check->fetch()) {
            throw new Exception('La cotización no existe');
        }

        // Verificar que el cliente y vehículo existen y están activos
        $stmt_check = $conex->prepare("SELECT id_cliente FROM clientes WHERE id_cliente = ? AND activo = 1");
        $stmt_check->execute([$id_cliente]);
        if (!$stmt_check->fetch()) {
            throw new Exception('El cliente seleccionado no existe o está inactivo');
        }

        $stmt_check = $conex->prepare("SELECT id_vehiculo FROM vehiculos WHERE id_vehiculo = ? AND activo = 1");
        $stmt_check->execute([$id_vehiculo]);
        if (!$stmt_check->fetch()) {
            throw new Exception('El vehículo seleccionado no existe o está inactivo');
        }

        // Verificar relación cliente-vehículo
        $stmt_check = $conex->prepare("SELECT id_cliente FROM cliente_vehiculo WHERE id_cliente = ? AND id_vehiculo = ? AND activo = 1");
        $stmt_check->execute([$id_cliente, $id_vehiculo]);
        if (!$stmt_check->fetch()) {
            throw new Exception('El vehículo seleccionado no pertenece al cliente');
        }

        // Actualizar cotización
        $sql = "UPDATE cotizaciones SET 
                id_cliente = ?, 
                id_vehiculo = ?, 
                subtotal_cotizacion = ?, 
                iva = ?, 
                total_cotizacion = ?, 
                valor_adicional = ?,
                notas_cotizacion = ?,
                fecha_cotizacion = NOW()
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
        
        // Eliminar servicios anteriores
        $delete_stmt = $conex->prepare("DELETE FROM cotizacion_servicios WHERE id_cotizacion = ?");
        $delete_stmt->execute([$id_cotizacion]);
    } else {
        throw new Exception('Acción inválida');
    }

    // Insertar servicios de la cotización
    $sqlServicios = "INSERT INTO cotizacion_servicios (id_cotizacion, id_servicio, precio) VALUES (?, ?, ?)";
    $stmtServicios = $conex->prepare($sqlServicios);

    foreach ($servicios_data['servicios'] as $servicio) {
        if (!isset($servicio['id']) || !isset($servicio['precio'])) {
            throw new Exception('Datos de servicio inválidos');
        }
        
        $id_servicio = intval($servicio['id']);
        $precio_servicio = floatval($servicio['precio']);
        
        // Verificar que el servicio existe
        $stmt_check_servicio = $conex->prepare("SELECT id_servicio FROM servicios WHERE id_servicio = ? AND activo = 1");
        $stmt_check_servicio->execute([$id_servicio]);
        if (!$stmt_check_servicio->fetch()) {
            throw new Exception('El servicio seleccionado no existe o está inactivo');
        }
        
        $stmtServicios->execute([
            $id_cotizacion,
            $id_servicio,
            $precio_servicio
        ]);
    }

    $conex->commit();

    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = 'success';
    header("Location: ver.php?id=$id_cotizacion");
    exit;
    
} catch (PDOException $e) {
    if (isset($conex)) {
        $conex->rollBack();
    }
    error_log("Error en la base de datos: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    
    $_SESSION['mensaje'] = 'Error al procesar la cotización: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
    exit;
} catch (Exception $e) {
    if (isset($conex)) {
        $conex->rollBack();
    }
    error_log("Error general: " . $e->getMessage());
    $_SESSION['mensaje'] = $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
    exit;
}