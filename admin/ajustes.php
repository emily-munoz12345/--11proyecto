<?php
// Incluir archivo de conexión
require_once __DIR__ . '/../php/conexion.php';
require_once __DIR__ . '/../php/auth.php';

// Verificar autenticación
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/includes/head.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajustes del Sistema | Nacional Tapizados</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.8);
            --primary-hover: rgba(140, 74, 63, 1);
            --text-color: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --bg-transparent: rgba(255, 255, 255, 0.1);
            --bg-transparent-light: rgba(255, 255, 255, 0.15);
            --border-color: rgba(255, 255, 255, 0.2);
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --sidebar-width: 250px;
        }
        
        body {
            background-image: url('https://pfst.cf2.poecdn.net/base/image/fe72e5f0bf336b4faca086bc6a42c20a45e904d165e796b52eca655a143283b8?w=1024&h=768&pmaid=426747789');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            color: var(--text-color);
            min-height: 100vh;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .admin-sidebar {
            width: var(--sidebar-width);
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .admin-sidebar.active {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        .sidebar-header {
            padding: 0 1rem 1rem;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1rem;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 0.8rem 1.5rem;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .sidebar-menu li a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .sidebar-menu li.active a {
            background-color: var(--primary-color);
        }

        /* Main Content Styles */
        .admin-main {
            flex: 1;
            padding: 2rem;
            background-color: var(--bg-transparent);
            backdrop-filter: blur(12px);
        }

        .admin-header {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .admin-header h1 {
            margin: 0;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .admin-header h1 i {
            color: var(--primary-color);
        }

        .admin-breadcrumb {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        /* Settings Panel Styles */
        .settings-wrapper {
            display: flex;
            gap: 2rem;
        }

        .settings-nav {
            width: 220px;
            flex-shrink: 0;
        }

        .settings-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            background-color: var(--bg-transparent-light);
            border-radius: 8px;
            overflow: hidden;
        }

        .settings-nav li {
            border-bottom: 1px solid var(--border-color);
        }

        .settings-nav li:last-child {
            border-bottom: none;
        }

        .settings-nav li a {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .settings-nav li a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .settings-nav li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .settings-nav li.active a {
            background-color: var(--primary-color);
        }

        .settings-content {
            flex: 1;
        }

        .settings-section {
            display: none;
            background-color: var(--bg-transparent-light);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .settings-section.active {
            display: block;
        }

        .settings-section h2 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .settings-section h2 i {
            color: var(--primary-color);
        }

        /* Form Styles */
        .settings-form {
            max-width: 800px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem 1rem;
            background-color: var(--bg-transparent);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-color);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(140, 74, 63, 0.3);
            background-color: rgba(255, 255, 255, 0.2);
        }

        .radio-group,
        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .radio-group label,
        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: normal;
            cursor: pointer;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .description {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }

        /* Button Styles */
        .button-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.8rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            gap: 0.5rem;
        }

        .button-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .form-actions {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        /* System Status Styles */
        .system-status {
            background-color: var(--bg-transparent-light);
            border-radius: 8px;
            padding: 1.5rem;
        }

        .system-status h3 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .system-status h3 i {
            color: var(--primary-color);
        }

        .status-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .status-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            background-color: var(--bg-transparent);
            border-radius: 8px;
            padding: 1rem;
        }

        .status-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .status-icon.success {
            background-color: rgba(40, 167, 69, 0.2);
            color: var(--success-color);
        }

        .status-icon.warning {
            background-color: rgba(255, 193, 7, 0.2);
            color: var(--warning-color);
        }

        .status-info h4 {
            margin: 0 0 0.3rem;
            font-size: 1rem;
        }

        .status-info p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Switch Styles */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
            margin-left: auto;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: var(--primary-color);
        }

        input:focus + .slider {
            box-shadow: 0 0 1px var(--primary-color);
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .system-footer {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .settings-wrapper {
                flex-direction: column;
            }
            
            .settings-nav {
                width: 100%;
            }
            
            .admin-main {
                padding: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .admin-sidebar {
                width: 100%;
                position: fixed;
                bottom: 0;
                left: 0;
                z-index: 1000;
                padding: 0;
                margin-left: 0;
            }
            
            .admin-sidebar.active {
                margin-left: 0;
                transform: translateY(100%);
            }
            
            .sidebar-menu {
                display: flex;
                overflow-x: auto;
            }
            
            .sidebar-menu li {
                flex: 1;
                min-width: max-content;
                border-bottom: none;
                border-right: 1px solid var(--border-color);
            }
            
            .sidebar-menu li:last-child {
                border-right: none;
            }
            
            .sidebar-menu li a {
                padding: 0.8rem 1rem;
                justify-content: center;
                flex-direction: column;
                text-align: center;
                font-size: 0.8rem;
            }
            
            .sidebar-menu li a i {
                margin-right: 0;
                margin-bottom: 0.3rem;
                font-size: 1.2rem;
            }
            
            .admin-main {
                padding-bottom: 80px; /* Espacio para el menú móvil */
            }
        }
    </style>
</head>
<body class="admin-body">
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <!-- Contenido principal -->
        <main class="admin-main">
            <div class="admin-header">
                <h1><i class="fas fa-cog"></i> Ajustes del Sistema</h1>
                <div class="admin-breadcrumb">
                    <span>Escritorio</span> &raquo; <span>Ajustes</span>
                </div>
            </div>

            <div class="settings-wrapper">
                <!-- Menú de navegación de ajustes -->
                <nav class="settings-nav">
                    <ul>
                        <li class="active"><a href="#general"><i class="fas fa-sliders-h"></i> Generales</a></li>
                        <li><a href="#lectura"><i class="fas fa-book-reader"></i> Lectura</a></li>
                        <li><a href="#escritura"><i class="fas fa-edit"></i> Escritura</a></li>
                        <li><a href="#comentarios"><i class="fas fa-comments"></i> Comentarios</a></li>
                        <li><a href="#medios"><i class="fas fa-images"></i> Medios</a></li>
                        <li><a href="#enlaces"><i class="fas fa-link"></i> Enlaces permanentes</a></li>
                        <li><a href="#privacidad"><i class="fas fa-user-shield"></i> Privacidad</a></li>
                    </ul>
                </nav>

                <!-- Contenido de ajustes -->
                <div class="settings-content">
                    <!-- Sección General -->
                    <section id="general" class="settings-section active">
                        <h2><i class="fas fa-sliders-h"></i> Ajustes Generales</h2>
                        <form class="settings-form">
                            <div class="form-group">
                                <label for="site_title">Título del Sitio</label>
                                <input type="text" id="site_title" name="site_title" value="Nacional Tapizados" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="site_description">Descripción Corta</label>
                                <textarea id="site_description" name="site_description">Expertos en tapizados y decoración de interiores</textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="admin_email">Correo Electrónico del Administrador</label>
                                <input type="email" id="admin_email" name="admin_email" value="admin@nacionaltapizados.com" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="timezone">Zona Horaria</label>
                                <select id="timezone" name="timezone">
                                    <option value="America/Bogota" selected>Bogotá (UTC-5)</option>
                                    <option value="America/New_York">Nueva York (UTC-4)</option>
                                    <option value="America/Los_Angeles">Los Ángeles (UTC-7)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="date_format">Formato de Fecha</label>
                                <div class="radio-group">
                                    <label><input type="radio" name="date_format" value="d/m/Y" checked> dd/mm/aaaa</label>
                                    <label><input type="radio" name="date_format" value="m/d/Y"> mm/dd/aaaa</label>
                                    <label><input type="radio" name="date_format" value="Y-m-d"> aaaa-mm-dd</label>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="button-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
                            </div>
                        </form>
                    </section>

                    <!-- Sección Lectura -->
                    <section id="lectura" class="settings-section">
                        <h2><i class="fas fa-book-reader"></i> Ajustes de Lectura</h2>
                        <form class="settings-form">
                            <div class="form-group">
                                <label>Tu página de inicio muestra</label>
                                <div class="radio-group">
                                    <label><input type="radio" name="front_page" value="posts" checked> Tus últimas entradas</label>
                                    <label><input type="radio" name="front_page" value="page"> Una página estática</label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="front_page_select">Portada</label>
                                <select id="front_page_select" name="front_page_select" disabled>
                                    <option value="1">Home - Portada</option>
                                    <option value="2">Inicio</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="posts_page_select">Página de entradas</label>
                                <select id="posts_page_select" name="posts_page_select" disabled>
                                    <option value="3">Blog</option>
                                    <option value="4">Noticias</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="posts_per_page">Número máximo de entradas a mostrar</label>
                                <input type="number" id="posts_per_page" name="posts_per_page" value="10" min="1" max="50">
                            </div>
                            
                            <div class="form-group">
                                <label>Para cada entrada en el feed, incluir</label>
                                <div class="radio-group">
                                    <label><input type="radio" name="feed_content" value="full" checked> Texto completo</label>
                                    <label><input type="radio" name="feed_content" value="excerpt"> Extracto</label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="discourage_search"> Pedir a los motores de búsqueda que no indexen este sitio
                                </label>
                                <p class="description">Los motores de búsqueda podrían ignorar esta solicitud.</p>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="button-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
                            </div>
                        </form>
                    </section>

                    <!-- Sección de estado del sistema -->
                    <section class="system-status">
                        <h3><i class="fas fa-info-circle"></i> Estado del Sistema</h3>
                        <div class="status-cards">
                            <div class="status-card">
                                <div class="status-icon success">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="status-info">
                                    <h4>Versión del Sistema</h4>
                                    <p>1.5.2</p>
                                </div>
                            </div>
                            
                            <div class="status-card">
                                <div class="status-icon warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="status-info">
                                    <h4>Modo Mantenimiento</h4>
                                    <label class="switch">
                                        <input type="checkbox">
                                        <span class="slider round"></span>
                                    </label>
                                    <p>Actívalo si tu sitio está "en desarrollo"</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="system-footer">
                            <p>Gracias por usar nuestro sistema de administración</p>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Mostrar/ocultar secciones de ajustes
        document.addEventListener('DOMContentLoaded', function() {
            // Navegación entre pestañas
            const navLinks = document.querySelectorAll('.settings-nav a');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Ocultar todas las secciones
                    document.querySelectorAll('.settings-section').forEach(section => {
                        section.classList.remove('active');
                    });
                    
                    // Desactivar todos los enlaces
                    navLinks.forEach(navLink => {
                        navLink.parentNode.classList.remove('active');
                    });
                    
                    // Activar sección seleccionada
                    const target = this.getAttribute('href');
                    document.querySelector(target).classList.add('active');
                    this.parentNode.classList.add('active');
                });
            });
            
            // Habilitar selects cuando se selecciona página estática
            const frontPageRadios = document.querySelectorAll('input[name="front_page"]');
            frontPageRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const selects = document.querySelectorAll('#front_page_select, #posts_page_select');
                    selects.forEach(select => {
                        select.disabled = this.value !== 'page';
                    });
                });
            });

            // Toggle sidebar en móvil
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const sidebar = document.querySelector('.admin-sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>