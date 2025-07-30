<?php
// Incluir archivo de conexión
require_once __DIR__ . '/../php/conexion.php';


require_once __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/sidebar.php';
?>
            <!-- Contenido principal -->
            <main class="main-content">
                <div class="settings-container">
                    <!-- Sección de perfil -->
                    <section class="settings-card">
                        <h2><i class="fas fa-user-cog"></i> Perfil de Usuario</h2>
                        <form action="guardar_ajustes.php" method="post">
                            <div class="form-group">
                                <label for="nombre_usuario">Nombre Completo</label>
                                <input type="text" id="nombre_usuario" name="nombre_usuario" class="form-control" value="Administrador" required>
                            </div>
                            <div class="form-group">
                                <label for="correo_usuario">Correo Electrónico</label>
                                <input type="email" id="correo_usuario" name="correo_usuario" class="form-control" value="admin@example.com" required>
                            </div>
                            <div class="form-group">
                                <label for="telefono_usuario">Teléfono</label>
                                <input type="text" id="telefono_usuario" name="telefono_usuario" class="form-control" value="+1234567890">
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

            <!-- Footer -->
            <footer class="admin-footer">
                <div class="footer-container">
                    <div class="footer-version">Versión 1.0.0</div>
                    <div class="footer-copyright">&copy; <?php echo date('Y'); ?> Sistema de Administración</div>
                    <div class="footer-links">
                        <a href="#" class="footer-link"><i class="fas fa-question-circle"></i> Ayuda</a>
                        <a href="#" class="footer-link"><i class="fas fa-file-alt"></i> Términos</a>
                    </div>
                </div>
            </footer>
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
                    if (form.querySelector('#nueva_contrasena')) {
                        const nueva = form.querySelector('#nueva_contrasena').value;
                        const confirmar = form.querySelector('#confirmar_contrasena')?.value;
                        
                        if (confirmar && nueva !== confirmar) {
                            alert('Las contraseñas no coinciden');
                            e.preventDefault();
                        }
                    }
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