<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin()) {
    header('Location: ../dashboard.php');
    exit;
}

// Inicializar variables de sesión para mensajes
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
    $_SESSION['tipo_mensaje'] = '';
}

// Procesar eliminación (solo si no es el propio usuario)
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    
    // No permitir auto-eliminación
    if ($id != $_SESSION['usuario_id']) {
        try {
            // Verificar si el usuario tiene trabajos asociados
            $stmt = $conex->prepare("SELECT COUNT(*) FROM trabajos WHERE id_usuario = ?");
            $stmt->execute([$id]);
            $tieneTrabajos = $stmt->fetchColumn();
            
            if ($tieneTrabajos > 0) {
                $_SESSION['mensaje'] = 'No se puede eliminar: usuario tiene trabajos asociados';
                $_SESSION['tipo_mensaje'] = 'danger';
            } else {
                // Marcar como inactivo en lugar de eliminar
                $stmt = $conex->prepare("UPDATE usuarios SET activo = 0 WHERE id_usuario = ?");
                if ($stmt->execute([$id])) {
                    $_SESSION['mensaje'] = 'Usuario desactivado correctamente';
                    $_SESSION['tipo_mensaje'] = 'success';
                }
            }
        } catch (PDOException $e) {
            $_SESSION['mensaje'] = 'Error al eliminar: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }
    } else {
        $_SESSION['mensaje'] = 'No puedes desactivar tu propio usuario';
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    header('Location: index.php');
    exit;
}

// Procesar activación/desactivación
if (isset($_GET['cambiar_estado'])) {
    $id = intval($_GET['cambiar_estado']);
    
    // No permitir auto-desactivación
    if ($id != $_SESSION['usuario_id']) {
        try {
            // Obtener estado actual
            $stmt = $conex->prepare("SELECT activo, nombre_completo FROM usuarios WHERE id_usuario = ?");
            $stmt->execute([$id]);
            $usuario = $stmt->fetch();
            
            if ($usuario) {
                $nuevoEstado = $usuario['activo'] ? 0 : 1;
                $accion = $nuevoEstado ? 'activado' : 'desactivado';
                
                $stmt = $conex->prepare("UPDATE usuarios SET activo = ? WHERE id_usuario = ?");
                if ($stmt->execute([$nuevoEstado, $id])) {
                    $_SESSION['mensaje'] = "Usuario {$usuario['nombre_completo']} {$accion} correctamente";
                    $_SESSION['tipo_mensaje'] = 'success';
                }
            }
        } catch (PDOException $e) {
            $_SESSION['mensaje'] = 'Error al cambiar estado: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }
    } else {
        $_SESSION['mensaje'] = 'No puedes cambiar tu propio estado';
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    header('Location: index.php');
    exit;
}

// Obtener estadísticas generales
$stats = $conex->query("SELECT 
    COUNT(*) as total_usuarios,
    (SELECT COUNT(*) FROM usuarios WHERE activo = 1) as usuarios_activos,
    (SELECT COUNT(*) FROM usuarios WHERE activo = 0) as usuarios_inactivos,
    (SELECT COUNT(*) FROM usuarios WHERE activo = 0) as usuarios_papelera,
    (SELECT COUNT(*) FROM usuarios WHERE DATE(fecha_creacion) = CURDATE()) as registros_hoy
FROM usuarios")->fetch(PDO::FETCH_ASSOC);

// Obtener los 8 usuarios más recientes (activos e inactivos)
$usuariosRecientes = $conex->query("SELECT u.*, r.nombre_rol FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol ORDER BY u.fecha_creacion DESC LIMIT 4")->fetchAll();

// Obtener todos los usuarios activos para las pestañas
$todosUsuarios = $conex->query("SELECT u.*, r.nombre_rol FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol WHERE u.activo = 1 ORDER BY u.nombre_completo ASC")->fetchAll();

// Obtener usuarios inactivos (activo = 0)
$usuariosInactivos = $conex->query("SELECT u.*, r.nombre_rol FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol WHERE u.activo = 0 ORDER BY u.nombre_completo ASC")->fetchAll();

// Obtener usuarios en papelera (activo = 0)
$usuariosPapelera = $conex->query("SELECT u.*, r.nombre_rol FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol WHERE u.activo = 0 ORDER BY u.fecha_eliminacion DESC")->fetchAll();

// Procesar búsqueda si es una solicitud AJAX
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    if (!isset($_GET['q']) || strlen($_GET['q']) < 2) {
        echo json_encode([]);
        exit;
    }

    $searchTerm = '%' . $_GET['q'] . '%';
    $stmt = $conex->prepare("SELECT u.*, r.nombre_rol FROM usuarios u 
                            JOIN roles r ON u.id_rol = r.id_rol
                            WHERE u.activo = 1 
                            AND (u.nombre_completo LIKE :search 
                            OR u.username_usuario LIKE :search 
                            OR u.correo_usuario LIKE :search
                            OR r.nombre_rol LIKE :search)
                            ORDER BY u.nombre_completo ASC 
                            LIMIT 10");
    $stmt->bindParam(':search', $searchTerm);
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

$title = 'Gestión de Usuarios';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.8);
            --primary-hover: rgba(140, 74, 63, 1);
            --secondary-color: rgba(108, 117, 125, 0.8);
            --text-color: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --bg-transparent: rgba(0, 0, 0, 0.5);
            --bg-transparent-light: rgba(0, 0, 0, 0.4);
            --bg-input: rgba(0, 0, 0, 0.6);
            --border-color: rgba(255, 255, 255, 0.2);
            --success-color: rgba(25, 135, 84, 0.8);
            --danger-color: rgba(220, 53, 69, 0.8);
            --warning-color: rgba(255, 193, 7, 0.8);
            --info-color: rgba(13, 202, 240, 0.8);
        }

        body {
            background-image: url('https://pfst.cf2.poecdn.net/base/image/60ab54eef562f30f85a67bde31f924f078199dae0b7bc6c333dfb467a2c13471?w=1024&h=768&pmaid=442168253');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            min-height: 100vh;
        }

        .main-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--bg-transparent);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            color: var(--text-color);
        }

        .page-title i {
            margin-right: 12px;
            color: var(--primary-color);
        }

        /* Estilos para pestañas */
        .nav-tabs {
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 2rem;
        }

        .nav-link {
            color: var(--text-muted);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--text-color);
            background-color: var(--bg-transparent-light);
        }

        .nav-link.active {
            color: white;
            background-color: var(--primary-color);
            border-radius: 8px 8px 0 0;
        }

        .tab-content {
            padding: 1.5rem 0;
        }

        /* Estilos para el buscador */
        .search-container {
            position: relative;
            margin-bottom: 2rem;
        }

        .search-input {
            width: 100%;
            padding: 1rem;
            border-radius: 8px;
            border: none;
            background-color: var(--bg-input);
            color: var(--text-color);
            font-size: 1rem;
            backdrop-filter: blur(5px);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--primary-color);
            background-color: rgba(0, 0, 0, 0.7);
        }

        .search-input::placeholder {
            color: var(--text-muted);
        }

        .search-results {
            position: absolute;
            width: 100%;
            z-index: 1000;
            background-color: rgba(30, 30, 30, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 0 0 8px 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            border: 1px solid var(--border-color);
            border-top: none;
            max-height: 400px;
            overflow-y: auto;
            display: none;
        }

        .search-result-item {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .search-result-item:hover {
            background-color: var(--primary-color);
        }

        .search-client-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .search-client-info {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .search-client-date {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.5);
        }

        /* Estilos para la lista de usuarios */
        .client-list {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .client-item {
            padding: 1.2rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .client-item:hover {
            background-color: rgba(140, 74, 63, 0.3);
        }

        .client-name {
            font-weight: 500;
            font-size: 1.1rem;
            color: var(--text-color);
        }

        .client-description {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 0.3rem;
        }

        .client-arrow {
            margin-left: 1rem;
            opacity: 0.7;
            transition: all 0.3s ease;
            color: var(--text-color);
        }

        .client-item:hover .client-arrow {
            opacity: 1;
            transform: translateX(3px);
        }

        /* Estilos para acciones en la lista */
        .client-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        /* Estilos para botones */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            gap: 0.5rem;
        }

        .btn i {
            font-size: 0.9rem;
        }

        .btn-sm {
            padding: 0.35rem 0.5rem;
            font-size: 0.8rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-secondary:hover {
            background-color: rgba(108, 117, 125, 1);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: rgba(220, 53, 69, 1);
        }

        .btn-info {
            background-color: var(--info-color);
            color: white;
        }

        .btn-info:hover {
            background-color: rgba(13, 202, 240, 1);
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: rgba(25, 135, 84, 1);
        }

        .btn-warning {
            background-color: var(--warning-color);
            color: black;
        }

        .btn-warning:hover {
            background-color: rgba(255, 193, 7, 1);
        }

        /* Estilos para tarjetas de resumen */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            background-color: var(--bg-transparent-light);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .summary-card:hover {
            transform: translateY(-5px);
            background-color: rgba(140, 74, 63, 0.3);
        }

        .summary-card h3 {
            margin-top: 0;
            font-size: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .summary-card p {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0;
            color: var(--text-color);
        }

        /* Estilos para alertas */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(5px);
            border-left: 4px solid var(--info-color);
            background-color: rgba(13, 202, 240, 0.2);
            color: var(--text-color);
        }

        .alert-success {
            background-color: rgba(25, 135, 84, 0.2);
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            border-left: 4px solid var(--danger-color);
        }

        .alert-warning {
            background-color: rgba(255, 193, 7, 0.2);
            border-left: 4px solid var(--warning-color);
        }

        .alert-info {
            background-color: rgba(13, 202, 240, 0.2);
            border-left: 4px solid var(--info-color);
        }

        /* Estilos para elementos inactivos */
        .inactive-item {
            opacity: 0.8;
            background-color: rgba(255, 193, 7, 0.1);
        }
        
        .inactive-item:hover {
            background-color: rgba(255, 193, 7, 0.2);
        }
        
        .inactive-badge {
            background-color: var(--warning-color);
            color: black;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-left: 0.5rem;
        }

        .active-badge {
            background-color: var(--success-color);
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-left: 0.5rem;
        }

        /* Estilos para tarjetas de usuarios */
        .client-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .client-card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
        }

        .client-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            background-color: rgba(140, 74, 63, 0.2);
        }

        .client-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .client-card-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .client-card-badge {
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .client-card-body {
            margin-bottom: 1.5rem;
        }

        .client-card-detail {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
            color: var(--text-color);
        }

        .client-card-detail i {
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }

        /* Flecha para tarjetas */
        .edit-arrow {
            position: absolute;
            bottom: 15px;
            right: 15px;
            color: var(--primary-color);
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .edit-arrow:hover {
            transform: translateX(3px);
            color: var(--text-color);
        }

        /* Nueva tarjeta flotante para opciones */
        .options-floating-card {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            background-color: rgba(40, 40, 40, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 0;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            z-index: 1001;
            animation: fadeInUp 0.4s ease;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .options-card-header {
            background-color: var(--primary-color);
            padding: 1.2rem;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }

        .options-card-title {
            margin: 0;
            font-size: 1.2rem;
            color: white;
            font-weight: 500;
        }

        .options-card-body {
            padding: 1.5rem;
        }

        .option-item {
            display: flex;
            align-items: center;
            padding: 0.9rem 1rem;
            margin-bottom: 0.8rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
            color: var(--text-color);
            text-decoration: none;
            background-color: rgba(255, 255, 255, 0.08);
        }

        .option-item:last-child {
            margin-bottom: 0;
        }

        .option-item:hover {
            background-color: var(--primary-color);
            transform: translateX(5px);
        }

        .option-item i {
            margin-right: 0.8rem;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .option-close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.3rem;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .option-close:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Estilos para tarjetas flotantes */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate(-50%, -40%);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
                margin: 1rem;
            }

            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .nav-tabs .nav-link {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .summary-cards {
                grid-template-columns: 1fr;
            }

            .client-cards {
                grid-template-columns: 1fr;
            }

            .options-floating-card {
                width: 90%;
                max-width: 300px;
            }
        }
    </style>   
</head>

<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-users-cog"></i> Gesti&oacute;n de Usuarios
            </h1>
            <div class="d-flex gap-2">
                <a href="crear.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                </a>
                <a href="../../dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if (!empty($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
                <div>
                    <i class="fas fa-<?=
                                        $_SESSION['tipo_mensaje'] === 'success' ? 'check-circle' : 
                                        ($_SESSION['tipo_mensaje'] === 'danger' ? 'times-circle' : 
                                        ($_SESSION['tipo_mensaje'] === 'warning' ? 'exclamation-triangle' : 'info-circle'))
                                        ?> me-2"></i>
                    <?= $_SESSION['mensaje'] ?>
                </div>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
            </div>
            <?php 
            $_SESSION['mensaje'] = '';
            $_SESSION['tipo_mensaje'] = '';
            ?>
        <?php endif; ?>

        <!-- Estadísticas rápidas -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total de Usuarios</h3>
                <p><?= $stats['total_usuarios'] ?></p>
            </div>
            <div class="summary-card">
                <h3>Usuarios Activos</h3>
                <p><?= $stats['usuarios_activos'] ?></p>
            </div>
            <div class="summary-card">
                <h3>Usuarios Inactivos</h3>
                <p><?= $stats['usuarios_inactivos'] ?></p>
            </div>
            <div class="summary-card">
                <h3>Registros Hoy</h3>
                <p><?= $stats['registros_hoy'] ?></p>
            </div>
        </div>

        <!-- Pestañas de navegación -->
        <ul class="nav nav-tabs" id="clientTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="search-tab" data-bs-toggle="tab" data-bs-target="#search" type="button" role="tab">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="recent-tab" data-bs-toggle="tab" data-bs-target="#recent" type="button" role="tab">
                    <i class="fas fa-clock"></i> Recientes
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="inactive-tab" data-bs-toggle="tab" data-bs-target="#inactive" type="button" role="tab">
                    <i class="fas fa-user-slash"></i> Inactivos
                </button>
            </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="clientTabsContent">
            <!-- Pestaña de búsqueda -->
            <div class="tab-pane fade show active" id="search" role="tabpanel">
                <div class="search-container">
                    <input type="text" class="search-input" id="searchInput" placeholder="Buscar usuario por nombre, usuario, correo o rol..." autocomplete="off">
                    <div class="search-results" id="searchResults"></div>
                </div>

                <!-- Resultados iniciales (todos los usuarios activos) -->
                <div class="client-list" id="allClientsList">
                    <?php foreach ($todosUsuarios as $usuario): ?>
                        <div class="client-item" data-client-id="<?= $usuario['id_usuario'] ?>" onclick="showOptionsCard(<?= $usuario['id_usuario'] ?>, '<?= htmlspecialchars(addslashes($usuario['nombre_completo'])) ?>', <?= $usuario['activo'] ?>)">
                            <div class="client-info">
                                <div class="client-name">
                                    <?= htmlspecialchars($usuario['nombre_completo']) ?>
                                </div>
                                <div class="client-description">
                                    <?= htmlspecialchars($usuario['username_usuario']) ?> · 
                                    <?= htmlspecialchars($usuario['correo_usuario']) ?> · 
                                    <?= htmlspecialchars($usuario['nombre_rol']) ?>
                                </div>
                            </div>
                            <div class="client-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Pestaña de usuarios recientes -->
            <div class="tab-pane fade" id="recent" role="tabpanel">
                <div class="client-cards">
                    <?php foreach ($usuariosRecientes as $usuario): ?>
                        <div class="client-card" onclick="showOptionsCard(<?= $usuario['id_usuario'] ?>, '<?= htmlspecialchars(addslashes($usuario['nombre_completo'])) ?>', <?= $usuario['activo'] ?>)">
                            <div class="client-card-header">
                                <h3 class="client-card-title"><?= htmlspecialchars($usuario['nombre_completo']) ?></h3>
                                <div>
                                    <span class="client-card-badge"><?= htmlspecialchars($usuario['nombre_rol']) ?></span>
                                </div>
                            </div>
                            <div class="client-card-body">
                                <div class="client-card-detail">
                                    <i class="fas fa-user"></i>
                                    <span><?= htmlspecialchars($usuario['username_usuario']) ?></span>
                                </div>
                                <div class="client-card-detail">
                                    <i class="fas fa-toggle-<?= $usuario['activo'] ? 'on' : 'off' ?>"></i>
                                    <span>Estado: <?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?></span>
                                </div>
                            </div>
                            <div class="edit-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Pestaña de usuarios inactivos -->
            <div class="tab-pane fade" id="inactive" role="tabpanel">
                <?php if (!empty($usuariosInactivos)): ?>
                    <div class="client-list">
                        <?php foreach ($usuariosInactivos as $usuario): ?>
                            <div class="client-item inactive-item">
                                <div class="client-info">
                                    <div class="client-name">
                                        <?= htmlspecialchars($usuario['nombre_completo']) ?>
                                    </div>
                                    <div class="client-description">
                                        <?= htmlspecialchars($usuario['username_usuario']) ?> · 
                                        <?= htmlspecialchars($usuario['correo_usuario']) ?> · 
                                        <?= htmlspecialchars($usuario['nombre_rol']) ?>
                                        <br>
                                        <small>Última actividad: <?= $usuario['ultima_actividad'] ? date('d/m/Y H:i', strtotime($usuario['ultima_actividad'])) : 'Nunca' ?></small>
                                    </div>
                                </div>
                                <div class="client-actions">
                                    <a href="index.php?cambiar_estado=<?= $usuario['id_usuario'] ?>" class="btn btn-success btn-sm" onclick="return confirm('¿Activar a <?= htmlspecialchars(addslashes($usuario['nombre_completo'])) ?>?')">
                                        <i class="fas fa-check"></i> Activar
                                    </a>
                                    <a href="eliminar_permanentemente.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-primary btn-sm" onclick="return confirm('¿Mover a papelera a <?= htmlspecialchars(addslashes($usuario['nombre_completo'])) ?>?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-user-slash fa-3x mb-3" style="color: var(--text-muted);"></i>
                        <h4 style="color: var(--text-muted);">No hay usuarios inactivos</h4>
                        <p style="color: var(--text-muted);">Todos los usuarios están activos en el sistema</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tarjeta flotante para opciones -->
        <div class="overlay" id="overlay"></div>
        <div class="options-floating-card" id="optionsCard">
            <button class="option-close" id="closeOptionsCard">
                <i class="fas fa-times"></i>
            </button>
            <div class="options-card-header">
                <h3 class="options-card-title" id="optionsCardTitle">Opciones</h3>
            </div>
            <div class="options-card-body">
                <a href="#" class="option-item" id="viewOption">
                    <i class="fas fa-eye"></i> Ver Detalles
                </a>
                <a href="#" class="option-item" id="editOption">
                    <i class="fas fa-edit"></i> Editar Usuario
                </a>
                <a href="#" class="option-item" id="stateOption">
                    <i class="fas fa-toggle-on"></i> Cambiar Estado
                </a>
                <a href="#" class="option-item " id="deleteOption">
                    <i class="fas fa-trash-alt"></i> Eliminar Usuario
                </a>
            </div>
        </div>
    </div>

<?php include '../../includes/bot.php'; ?>
    <script>setHelpModule('Usuarios');</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables globales
        let currentClientId = null;
        let currentClientName = '';
        let currentClientActive = true;

        // Función para mostrar la tarjeta de opciones
        function showOptionsCard(clientId, clientName, isActive) {
            currentClientId = clientId;
            currentClientName = clientName;
            currentClientActive = isActive;
            
            // Actualizar título de la tarjeta
            document.getElementById('optionsCardTitle').textContent = clientName;
            
            // Actualizar enlaces
            document.getElementById('viewOption').href = `ver.php?id=${clientId}`;
            document.getElementById('editOption').href = `editar.php?id=${clientId}`;
            document.getElementById('stateOption').href = `index.php?cambiar_estado=${clientId}`;
            document.getElementById('deleteOption').href = `eliminar_permanentemente.php?id=${clientId}`;
            
            // Actualizar texto del botón de estado
            const stateIcon = document.querySelector('#stateOption i');
            const stateText = document.querySelector('#stateOption');
            if (currentClientActive) {
                stateIcon.className = 'fas fa-toggle-off';
                stateText.innerHTML = '<i class="fas fa-toggle-off"></i> Desactivar';
            } else {
                stateIcon.className = 'fas fa-toggle-on';
                stateText.innerHTML = '<i class="fas fa-toggle-on"></i> Activar';
            }
            
            // Actualizar texto del botón de eliminar/mover a papelera
            const deleteText = document.querySelector('#deleteOption');
            if (currentClientActive) {
                deleteText.innerHTML = '<i class="fas fa-trash-alt"></i> Eliminar Usuario';
            } else {
                deleteText.innerHTML = '<i class="fas fa-trash-alt"></i> Eliminar Usuario';
            }
            
            // Mostrar overlay y tarjeta
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('optionsCard').style.display = 'block';
        }

        // Función para ocultar la tarjeta de opciones
        function hideOptionsCard() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('optionsCard').style.display = 'none';
            currentClientId = null;
            currentClientName = '';
            currentClientActive = true;
        }

        // Event listeners para cerrar la tarjeta
        document.getElementById('closeOptionsCard').addEventListener('click', hideOptionsCard);
        document.getElementById('overlay').addEventListener('click', hideOptionsCard);

        // Confirmaciones para acciones
        document.addEventListener('DOMContentLoaded', function() {
            // Confirmación para cambio de estado
            const stateLinks = document.querySelectorAll('a[href*="cambiar_estado"]');
            stateLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const clientItem = this.closest('.client-item');
                    const clientName = clientItem.querySelector('.client-name').textContent.split('Inactivo')[0].split('Activo')[0].trim();
                    const isActive = !clientItem.classList.contains('inactive-item');
                    const action = isActive ? 'desactivar' : 'activar';
                    
                    if (!confirm(`¿Estás seguro de ${action} a "${clientName}"?`)) {
                        e.preventDefault();
                    }
                });
            });

            // Confirmación para eliminación permanente
            const permanentDeleteLinks = document.querySelectorAll('a[href*="eliminar_permanentemente"]');
            permanentDeleteLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const clientName = this.closest('.client-item').querySelector('.client-name').textContent.split('Inactivo')[0].split('Activo')[0].trim();
                    if (!confirm(`¿Estás seguro de eliminar permanentemente a "${clientName}"? Esta acción no se puede deshacer.`)) {
                        e.preventDefault();
                    }
                });
            });
        });

        // Manejo de búsqueda en tiempo real
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.trim();
            const resultsContainer = document.getElementById('searchResults');
            const allClientsList = document.getElementById('allClientsList');
            
            if (searchTerm.length < 2) {
                resultsContainer.style.display = 'none';
                allClientsList.style.display = 'block';
                return;
            }
            
            // Mostrar loading
            resultsContainer.innerHTML = '<div class="search-result-item">Buscando...</div>';
            resultsContainer.style.display = 'block';
            allClientsList.style.display = 'none';
            
            // Realizar búsqueda AJAX
            fetch(`index.php?ajax=1&q=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = '';
                    
                    if (data.length === 0) {
                        resultsContainer.innerHTML = '<div class="search-result-item">No se encontraron resultados</div>';
                        return;
                    }
                    
                    data.forEach(user => {
                        const resultItem = document.createElement('div');
                        resultItem.className = 'search-result-item';
                        resultItem.innerHTML = `
                            <div>
                                <div class="search-client-name">${escapeHtml(user.nombre_completo)}</div>
                                <div class="search-client-info">
                                    ${escapeHtml(user.username_usuario)} · ${escapeHtml(user.correo_usuario)} · ${escapeHtml(user.nombre_rol)}
                                </div>
                            </div>
                            <div class="client-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        `;
                        resultItem.addEventListener('click', () => {
                            showOptionsCard(user.id_usuario, user.nombre_completo, user.activo);
                            resultsContainer.style.display = 'none';
                            document.getElementById('searchInput').value = '';
                            allClientsList.style.display = 'block';
                        });
                        resultsContainer.appendChild(resultItem);
                    });
                })
                .catch(error => {
                    console.error('Error en la búsqueda:', error);
                    resultsContainer.innerHTML = '<div class="search-result-item">Error al buscar</div>';
                });
        });

        // Función para escapar HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Cerrar resultados al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                document.getElementById('searchResults').style.display = 'none';
            }
        });
    </script>
</body>

</html>