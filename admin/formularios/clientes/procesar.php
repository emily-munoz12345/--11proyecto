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
    $_SESSION['mensaje'] = implode(', ', $errores);
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
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
        
        $_SESSION['mensaje'] = 'Cliente creado exitosamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } elseif ($accion === 'editar' && $id > 0) {
        // Obtener datos anteriores para registro de edición
        $stmt_old = $conex->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
        $stmt_old->execute([$id]);
        $cliente_anterior = $stmt_old->fetch(PDO::FETCH_ASSOC);
        
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
        
        // Registrar edición si existe la tabla
        if ($cliente_anterior) {
            try {
                $datos_anteriores = json_encode([
                    'nombre_cliente' => $cliente_anterior['nombre_cliente'],
                    'correo_cliente' => $cliente_anterior['correo_cliente'],
                    'telefono_cliente' => $cliente_anterior['telefono_cliente'],
                    'direccion_cliente' => $cliente_anterior['direccion_cliente'],
                    'notas_cliente' => $cliente_anterior['notas_cliente']
                ]);
                
                $datos_nuevos = json_encode([
                    'nombre_cliente' => $nombre,
                    'correo_cliente' => $correo,
                    'telefono_cliente' => $telefono,
                    'direccion_cliente' => $direccion,
                    'notas_cliente' => $notas
                ]);
                
                $stmt_log = $conex->prepare("INSERT INTO registro_ediciones 
                                          (tabla, id_registro, datos_anteriores, datos_nuevos, editado_por, fecha_edicion) 
                                          VALUES ('clientes', ?, ?, ?, ?, NOW())");
                $stmt_log->execute([$id, $datos_anteriores, $datos_nuevos, $_SESSION['id_usuario']]);
            } catch (Exception $e) {
                // No critical if logging fails
                error_log("Error al registrar edición: " . $e->getMessage());
            }
        }
        
        $_SESSION['mensaje'] = 'Cliente actualizado exitosamente';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        throw new Exception('Acción inválida');
    }
    
    header("Location: index.php");
    exit;
    
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error en la base de datos: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : "editar.php?id=$id"));
    exit;
}