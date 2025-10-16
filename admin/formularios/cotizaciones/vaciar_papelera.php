<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__, 3));
require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos (solo Admin)
if (!isAdmin()) {
    $_SESSION['mensaje'] = 'No tienes permisos para realizar esta acción';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

try {
    // Contar cuántas cotizaciones hay en la papelera antes de eliminar
    $stmt = $conex->prepare("SELECT COUNT(*) FROM cotizaciones WHERE activo = 0");
    $stmt->execute();
    $totalCotizaciones = $stmt->fetchColumn();
    
    if ($totalCotizaciones > 0) {
        // Eliminar directamente todas las cotizaciones en papelera
        $stmt = $conex->prepare("DELETE FROM cotizaciones WHERE activo = 0");
        $stmt->execute();
        
        $_SESSION['mensaje'] = "Se eliminaron permanentemente $totalCotizaciones cotizaciones de la papelera";
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'No hay cotizaciones en la papelera para eliminar';
        $_SESSION['tipo_mensaje'] = 'info';
    }
    
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al vaciar la papelera: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
}

// Redirigir de vuelta al índice de cotizaciones
header('Location: index.php');
exit;
?>