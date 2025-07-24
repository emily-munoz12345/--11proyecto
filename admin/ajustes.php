<?php
// Incluir archivo de conexión
require_once __DIR__ . '/../php/conexion.php';

// Aquí podrías agregar lógica para procesar los ajustes guardados, si es necesario.
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajustes de Administración</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Condensed:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-dark: #2c3e50;
            --secondary-dark: #34495e;
            --gold-pastel: #d6a85e;
            --gold-cream: #f5d7a1;
            --gold-dark: #b88c3b;
            --neutral-light: #f8f9fa;
            --text-dark: #2d3436;
            --text-light: #f5f6fa;
            --accent-light: #e74c3c;
            --success-color: #8a9b6e;
            --error-color: #e74c3c;
            --sidebar-width: 250px;
            --space-xs: 0.25rem;
            --space-sm: 0.5rem;
            --space-md: 1rem;
            --space-lg: 1.5rem;
            --space-xl: 2rem;
            --border-radius: 6px;
            --border-soft: 1px solid rgba(214, 168, 94, 0.2);
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
            --transition-fast: all 0.2s ease;
            --transition-normal: all 0.3s ease;
            --gradient-gold: linear-gradient(135deg, var(--gold-pastel) 0%, var(--gold-cream) 100%);
            --z-sidebar: 1000;
        }

        /* Estructura principal */
        .admin-body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--neutral-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar (usando tus estilos) */
        .admin-sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--secondary-dark) 100%);
            color: var(--text-light);
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: var(--z-sidebar);
            box-shadow: var(--shadow-md);
            transition: var(--transition-normal);
        }

        /* Contenido principal */
        .content-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: var(--transition-normal);
        }

        .main-content {
            padding: var(--space-xl);
        }

        /* Header del dashboard */
        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--space-md) var(--space-xl);
            background: white;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .dashboard-title {
            font-family: 'Roboto Condensed', sans-serif;
            color: var(--primary-dark);
            font-weight: 700;
            margin: 0;
            font-size: 1.5rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: var(--space-md);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
            font-weight: 700;
            cursor: pointer;
        }

        /* Estilos para el formulario de ajustes */
        .settings-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--space-xl);
            margin-top: var(--space-md);
        }

        .settings-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            padding: var(--space-lg);
            transition: var(--transition-normal);
            border-top: 3px solid var(--gold-pastel);
        }

        .settings-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .settings-card h2 {
            font-family: 'Roboto Condensed', sans-serif;
            color: var(--primary-dark);
            margin-top: 0;
            margin-bottom: var(--space-lg);
            padding-bottom: var(--space-sm);
            border-bottom: 1px solid rgba(214, 168, 94, 0.3);
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .settings-card h2 i {
            color: var(--gold-dark);
        }

        .form-group {
            margin-bottom: var(--space-md);
        }

        .form-group label {
            display: block;
            margin-bottom: var(--space-xs);
            font-weight: 500;
            color: var(--text-dark);
        }

        .form-control {
            width: 100%;
            padding: var(--space-sm);
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-family: 'Roboto', sans-serif;
            transition: var(--transition-fast);
        }

        .form-control:focus {
            border-color: var(--gold-pastel);
            outline: none;
            box-shadow: 0 0 0 3px rgba(214, 168, 94, 0.2);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-sm);
            padding: var(--space-sm) var(--space-md);
            background: var(--gold-pastel);
            color: var(--primary-dark);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition-normal);
            box-shadow: var(--shadow-sm);
        }

        .btn:hover {
            background: var(--gold-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn i {
            font-size: 0.9em;
        }

        .btn-block {
            display: block;
            width: 100%;
            padding: var(--space-md);
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle .toggle-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
        }

        .password-toggle .toggle-icon:hover {
            color: var(--gold-dark);
        }

        /* Sección de notificaciones */
        .notification-options {
            display: flex;
            flex-direction: column;
            gap: var(--space-sm);
        }

        .notification-option {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        /* Footer */
        .admin-footer {
            margin-top: auto;
            padding: var(--space-md) var(--space-xl);
            background: var(--primary-dark);
            color: var(--text-light);
            text-align: center;
            font-size: 0.8rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .content-wrapper {
                margin-left: 0;
            }

            .settings-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: var(--space-md);
            }

            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: var(--space-md);
                padding: var(--space-md);
            }

            .user-info {
                margin-top: var(--space-md);
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body class="admin-body">
    <div class="admin-container">
        <!-- Sidebar (usando tus estilos) -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <i class="fas fa-crown"></i>
                    <span>Admin Panel</span>
                </div>
            </div>
            <nav class="sidebar-nav">
                <a href="#" class="nav-link"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                <a href="#" class="nav-link"><i class="fas fa-users"></i> <span>Usuarios</span></a>
                <a href="#" class="nav-link"><i class="fas fa-cog"></i> <span>Ajustes</span></a>
                <a href="#" class="nav-link"><i class="fas fa-chart-bar"></i> <span>Estadísticas</span></a>
                <a href="#" class="nav-link logout-link"><i class="fas fa-sign-out-alt"></i> <span>Cerrar Sesión</span></a>
            </nav>
        </aside>

        <div class="content-wrapper">
            <!-- Header -->
            <header class="dashboard-header">
                <h1 class="dashboard-title">Ajustes de Administración</h1>
                <div class="user-info">
                    <span class="user-greeting">Hola, Admin</span>
                    <div class="user-avatar">A</div>
                </div>
            </header>

            <!-- Contenido principal -->
            <main class="main-content">
                <div class="settings-container">
                    <!-- Sección de perfil -->
                    <section class="settings-card">
                        <h2><i class="fas fa-user-cog"></i> Perfil de Usuario</h2>
                        <form action="guardar_ajustes.php" method="post">
                            <div class="form-group">
                                <label for="nombre_usuario">Nombre Completo</label>
                                <input type="text" id="nombre_usuario" name="nombre_usuario" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="correo_usuario">Correo Electrónico</label>
                                <input type="email" id="correo_usuario" name="correo_usuario" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="telefono_usuario">Teléfono</label>
                                <input type="text" id="telefono_usuario" name="telefono_usuario" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-block"><i class="fas fa-save"></i> Guardar Cambios</button>
                        </form>
                    </section>

                    <!-- Sección de contraseña -->
                    <section class="settings-card">
                        <h2><i class="fas fa-lock"></i> Cambiar Contraseña</h2>
                        <form>
                            <div class="form-group password-toggle">
                                <label for="contrasena_actual">Contraseña Actual</label>
                                <input type="password" id="contrasena_actual" name="contrasena_actual" class="form-control" required>
                                <i class="fas fa-eye toggle-icon" onclick="togglePassword('contrasena_actual', this)"></i>
                            </div>
                            <div class="form-group password-toggle">
                                <label for="nueva_contrasena">Nueva Contraseña</label>
                                <input type="password" id="nueva_contrasena" name="nueva_contrasena" class="form-control" required>
                                <i class="fas fa-eye toggle-icon" onclick="togglePassword('nueva_contrasena', this)"></i>
                            </div>
                            <div class="form-group password-toggle">
                                <label for="confirmar_contrasena">Confirmar Nueva Contraseña</label>
                                <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" class="form-control" required>
                                <i class="fas fa-eye toggle-icon" onclick="togglePassword('confirmar_contrasena', this)"></i>
                            </div>
                            <button type="submit" class="btn btn-block"><i class="fas fa-key"></i> Actualizar Contraseña</button>
                        </form>
                    </section>

                    <!-- Sección de notificaciones -->
                    <section class="settings-card">
                        <h2><i class="fas fa-bell"></i> Preferencias de Notificación</h2>
                        <form>
                            <div class="form-group">
                                <label>Recibir Notificaciones</label>
                                <div class="notification-options">
                                    <label class="notification-option">
                                        <input type="radio" name="notificaciones" value="si" checked> Sí, deseo recibir notificaciones
                                    </label>
                                    <label class="notification-option">
                                        <input type="radio" name="notificaciones" value="no"> No, no deseo recibir notificaciones
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Tipo de Notificaciones</label>
                                <div class="notification-options">
                                    <label class="notification-option">
                                        <input type="checkbox" name="notif_alertas" checked> Alertas importantes
                                    </label>
                                    <label class="notification-option">
                                        <input type="checkbox" name="notif_actualizaciones" checked> Actualizaciones del sistema
                                    </label>
                                    <label class="notification-option">
                                        <input type="checkbox" name="notif_promociones"> Promociones y ofertas
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-block"><i class="fas fa-bell"></i> Guardar Preferencias</button>
                        </form>
                    </section>
                </div>
            </main>
    <?php require_once __DIR__ . '/includes/footer.php'; ?>

        </div>
    </div>

    <script>
        // Función para alternar la visibilidad de la contraseña
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Validación básica del formulario
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Validación de contraseña
                    if (form.querySelector('#nueva_contrasena') && form.querySelector('#confirmar_contrasena')) {
                        const nueva = form.querySelector('#nueva_contrasena').value;
                        const confirmar = form.querySelector('#confirmar_contrasena').value;
                        
                        if (nueva !== confirmar) {
                            alert('Las contraseñas no coinciden');
                            e.preventDefault();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>