<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda - Página de Inicio | Nacional Tapizados</title>
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
            margin: 0;
            padding: 0;
        }

        .main-container {
            max-width: 1200px;
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

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            gap: 0.5rem;
        }

        .btn-outline-secondary {
            background-color: transparent;
            border: 1px solid var(--secondary-color);
            color: var(--text-color);
        }

        .btn-outline-secondary:hover {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        /* Estilos para el contenido de ayuda */
        .help-content {
            display: grid;
            grid-template-columns: 1fr 3fr;
            gap: 2rem;
        }

        .help-nav {
            background-color: var(--bg-transparent-light);
            border-radius: 12px;
            padding: 1.5rem;
            height: fit-content;
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
        }

        .help-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .help-nav li {
            margin-bottom: 0.75rem;
        }

        .help-nav a {
            display: block;
            padding: 0.75rem 1rem;
            color: var(--text-color);
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .help-nav a:hover, .help-nav a.active {
            background-color: rgba(140, 74, 63, 0.3);
            border-left: 3px solid var(--primary-color);
        }

        .help-sections {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .help-section {
            background-color: var(--bg-transparent-light);
            border-radius: 12px;
            padding: 1.5rem;
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
        }

        .help-section h2 {
            margin-top: 0;
            color: var(--primary-color);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .help-section h2 i {
            font-size: 1.5rem;
        }

        .help-section h3 {
            color: var(--text-color);
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .help-section h3 i {
            color: var(--primary-color);
        }

        .help-section p, .help-section ul {
            color: var(--text-muted);
            line-height: 1.6;
        }

        .help-section ul {
            padding-left: 1.5rem;
        }

        .help-section li {
            margin-bottom: 0.5rem;
        }

        .image-container {
            margin: 1.5rem 0;
            text-align: center;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
        }

        .image-container img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .image-caption {
            padding: 0.75rem;
            background-color: var(--bg-input);
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .step-container {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: var(--bg-input);
            border-radius: 8px;
            border-left: 4px solid var(--primary-color);
        }

        .step-number {
            background-color: var(--primary-color);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .step-content {
            flex-grow: 1;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .feature-card {
            background-color: var(--bg-input);
            border-radius: 8px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-color);
        }

        .feature-card h4 {
            margin-top: 0;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .feature-card h4 i {
            color: var(--primary-color);
        }

        .permission-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .permission-table th,
        .permission-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-color);
        }

        .permission-table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
        }

        .permission-table tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.08);
        }

        .permission-table tr:hover {
            background-color: rgba(140, 74, 63, 0.2);
        }

        /* Estilos responsivos */
        @media (max-width: 768px) {
            .main-container {
                margin: 1rem;
                padding: 1.5rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .help-content {
                grid-template-columns: 1fr;
            }

            .help-nav {
                order: 2;
            }

            .help-sections {
                order: 1;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="main-container">
        <div class="header-section">
            <h1 class="page-title"><i class="fas fa-home"></i>Ayuda - Página de Inicio</h1>
            <div>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>Volver
                </a>
                <a href="../dashboard.php" class="btn btn-primary">
                    <i class="fas fa-roll"></i>Ir a Clientes
                </a>
            </div>
        </div>

        <div class="help-content">
            <nav class="help-nav">
                <ul>
                    <li><a href="#introduccion" class="active">Introducción</a></li>
                    <li><a href="#bienvenida">Pantalla de Bienvenida</a></li>
                    <li><a href="#menu-navegacion">Menú de Navegación</a></li>
                    <li><a href="#resumen-sistema">Resumen del Sistema</a></li>
                    <li><a href="#perfil-usuario">Perfil de Usuario</a></li>
                    <li><a href="#roles-permisos">Roles y Permisos</a></li>
                    <li><a href="#navegacion-rapida">Navegación Rápida</a></li>
                    <li><a href="#problemas-comunes">Problemas Comunes</a></li>
                </ul>
            </nav>

            <div class="help-sections">
                <section id="introduccion" class="help-section">
                    <h2><i class="fas fa-info-circle"></i>Introducción a la página de Inicio</h2>
                    <p>La página de inicio o Dashboard es el centro de control principal del sistema de gestión de <strong>Nacional Tapizados</strong>. Desde aquí puedes acceder a todas las funcionalidades del sistema y ver un resumen general del estado del negocio.</p>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/inicio/inicio.PNG">
                        <div class="image-caption"></div>
                    </div>
                    
                    <p>El Dashboard se adapta automáticamente según tu rol de usuario, mostrando únicamente las opciones y estadísticas a las que tienes acceso.</p>
                </section>

                <section id="bienvenida" class="help-section">
                    <h2><i class="fas fa-hand-wave"></i>Pantalla de Bienvenida</h2>
                    <p>Al iniciar sesión, verás una pantalla de bienvenida personalizada con información importante:</p>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <h4><i class="fas fa-user"></i>Saludo Personalizado</h4>
                            <p>El sistema te da la bienvenida usando tu nombre completo.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-user-tag"></i>Rol del Usuario</h4>
                            <p>Muestra claramente tu rol actual en el sistema (Administrador, Vendedor, Técnico).</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-info-circle"></i>Descripción del Acceso</h4>
                            <p>Explica brevemente las capacidades de tu rol en el sistema.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/inicio/bienvenida.PNG">
                        <div class="image-caption"></div>
                    </div>
                </section>

                <section id="menu-navegacion" class="help-section">
                    <h2><i class="fas fa-bars"></i>Menú de Navegación</h2>
                    <p>El menú lateral te permite acceder a todos los módulos del sistema. Las opciones disponibles varían según tu rol:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Navegación Principal</h3>
                            <p>El menú incluye las siguientes secciones principales:</p>
                            <ul>
                                <li><strong>Inicio:</strong> Regresa al dashboard en cualquier momento</li>
                                <li><strong>Clientes:</strong> Gestión de información de clientes</li>
                                <li><strong>Vehículos:</strong> Registro y seguimiento de vehículos</li>
                                <li><strong>Cotizaciones:</strong> Creación y gestión de presupuestos</li>
                                <li><strong>Trabajos:</strong> Control de trabajos en curso y finalizados</li>
                                <li><strong>Materiales:</strong> Gestión de inventario y materiales</li>
                                <li><strong>Servicios:</strong> Catálogo de servicios ofrecidos</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Opciones de Administración</h3>
                            <p>Para usuarios con rol de Administrador:</p>
                            <ul>
                                <li><strong>Usuarios:</strong> Gestión de usuarios del sistema</li>
                                <li><strong>Reportes:</strong> Generación de reportes y estadísticas</li>
                                <li><strong>Configuración:</strong> Ajustes del sistema</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Acceso Rápido</h3>
                            <p>En la parte superior del menú encontrarás:</p>
                            <ul>
                                <li><strong>Botón de Perfil:</strong> Acceso rápido a tu perfil de usuario</li>
                                <li><strong>Cerrar Sesión:</strong> Salir seguramente del sistema</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/inicio/menu.PNG">
                        <div class="image-caption"></div>
                    </div>
                </section>

                <section id="resumen-sistema" class="help-section">
                    <h2><i class="fas fa-chart-bar"></i>Resumen del Sistema</h2>
                    <p>El inicio muestra un resumen visual con las métricas más importantes del negocio:</p>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <h4><i class="fas fa-users"></i>Clientes</h4>
                            <p>Número total de clientes registrados en el sistema.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-car"></i>Vehículos</h4>
                            <p>Cantidad de vehículos registrados para servicios.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-file-invoice-dollar"></i>Cotizaciones</h4>
                            <p>Presupuestos generados en el sistema.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-boxes"></i>Materiales</h4>
                            <p>Tipos de materiales disponibles en inventario.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-concierge-bell"></i>Servicios</h4>
                            <p>Servicios de tapicería disponibles.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-tools"></i>Trabajos</h4>
                            <p>Trabajos activos o en progreso.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-user-cog"></i>Usuarios</h4>
                            <p>Usuarios registrados en el sistema.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/inicio/resumen.PNG">
                        <div class="image-caption"></div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/inicio/dresum1.PNG">
                        <div class="image-caption"></div>
                    </div>
                                        
                    <div class="image-container">
                        <img src="../imagenes/ayuda/inicio/dresum2.PNG">
                        <div class="image-caption"></div>
                    </div>
                </section>

                <section id="perfil-usuario" class="help-section">
                    <h2><i class="fas fa-user-circle"></i>Perfil de Usuario</h2>
                    <p>Gestiona tu información personal y preferencias del sistema:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder al Perfil</h3>
                            <p>Haz clic en tu nombre de usuario o avatar en la esquina superior derecha del menú.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Información Disponible</h3>
                            <p>En tu perfil podrás ver y editar:</p>
                            <ul>
                                <li><strong>Información Personal:</strong> Nombre, correo, teléfono</li>
                                <li><strong>Datos de Cuenta:</strong> Nombre de usuario, fecha de registro</li>
                                <li><strong>Preferencias:</strong> Configuración personal del sistema</li>
                                <li><strong>Actividad Reciente:</strong> Historial de tus acciones</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Gestión de Contraseña</h3>
                            <p>Desde el perfil puedes solicitar el cambio de contraseña (debes contactar al administrador).</p>
                        </div>
                    </div>
                    
                    <div class="info-box">
                        <h4><i class="fas fa-info-circle"></i> Nota Importante</h4>
                        <p>Algunas opciones de perfil pueden estar limitadas según tu rol. Los administradores tienen acceso completo a todas las funciones de perfil.</p>
                    </div>
                </section>

                <section id="roles-permisos" class="help-section">
                    <h2><i class="fas fa-user-shield"></i>Roles y Permisos en el inicio</h2>
                    <p>El contenido del dashboard se adapta dinámicamente según tu rol de usuario:</p>
                    
                    <table class="permission-table">
                        <thead>
                            <tr>
                                <th>Función / Elemento</th>
                                <th>Administrador</th>
                                <th>Vendedor</th>
                                <th>Técnico</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Gestión de Usuarios</td>
                                <td><i class="fas fa-check text-success"></i></td>
                                <td><i class="fas fa-times text-danger"></i></td>
                                <td><i class="fas fa-times text-danger"></i></td>
                            </tr>
                            <tr>
                                <td>Reportes Completos</td>
                                <td><i class="fas fa-check text-success"></i></td>
                                <td><i class="fas fa-times text-danger"></i></td>
                                <td><i class="fas fa-times text-danger"></i></td>
                            </tr>
                            <tr>
                                <td>Gestión de Materiales</td>
                                <td><i class="fas fa-check text-success"></i></td>
                                <td><i class="fas fa-check text-success"></i></td>
                                <td><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td>Gestión de Clientes</td>
                                <td><i class="fas fa-check text-success"></i></td>
                                <td><i class="fas fa-check text-success"></i></td>
                                <td><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td>Gestión de Trabajos</td>
                                <td><i class="fas fa-check text-success"></i></td>
                                <td><i class="fas fa-check text-success"></i></td>
                                <td><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td>Configuración del Sistema</td>
                                <td><i class="fas fa-check text-success"></i></td>
                                <td><i class="fas fa-times text-danger"></i></td>
                                <td><i class="fas fa-times text-danger"></i></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="warning-box">
                        <h4><i class="fas fa-exclamation-triangle"></i> Importante</h4>
                        <p>Si necesitas acceso a funciones que no ves en tu dashboard, contacta al administrador del sistema para que revise y ajuste tus permisos.</p>
                    </div>
                </section>

                <section id="navegacion-rapida" class="help-section">
                    <h2><i class="fas fa-bolt"></i>Navegación Rápida</h2>
                    <p>Consejos para moverte eficientemente por el sistema:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceso Directo al Inicio</h3>
                            <p>Siempre puedes hacer clic en "Inicio" en el menú lateral para regresar al dashboard principal.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Búsqueda Rápida</h3>
                            <p>Utiliza la función de búsqueda (cuando esté disponible) para encontrar clientes, vehículos o trabajos específicos.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Atajos de Teclado</h3>
                            <p>Algunas pantallas pueden tener atajos de teclado para acciones frecuentes.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Acceso a Perfil</h3>
                            <p>Haz clic en tu nombre en la esquina superior derecha para acceder rápidamente a tu perfil de usuario.</p>
                        </div>
                    </div>
                </section>

                <section id="problemas-comunes" class="help-section">
                    <h2><i class="fas fa-tools"></i>Solución de Problemas Comunes</h2>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No veo todas las opciones del menú</h3>
                    <ul>
                        <li>Verifica que hayas iniciado sesión con el usuario correcto</li>
                        <li>Tu rol de usuario puede tener restricciones de acceso</li>
                        <li>Contacta al administrador si necesitas acceso adicional</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>Las estadísticas no se actualizan</h3>
                    <ul>
                        <li>Actualiza la página (F5) para cargar la información más reciente</li>
                        <li>Verifica tu conexión a internet</li>
                        <li>Espera unos minutos ya que algunas actualizaciones pueden tener delay</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No puedo acceder a mi perfil</h3>
                    <ul>
                        <li>Verifica que estés haciendo clic en el área correcta del menú</li>
                        <li>Comprueba que tu sesión esté activa</li>
                        <li>Intenta cerrar sesión y volver a iniciar</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>La página carga lentamente</h3>
                    <ul>
                        <li>Verifica tu conexión a internet</li>
                        <li>Limpia la caché de tu navegador</li>
                        <li>Intenta acceder desde otro navegador</li>
                    </ul>
                    
                    <div class="step-container" style="background-color: rgba(25, 135, 84, 0.2); border-left-color: var(--success-color);">
                        <div class="step-content">
                            <h3><i class="fas fa-life-ring"></i>Contacto de Soporte</h3>
                            <p>Si continúas experimentando problemas, contacta al equipo de soporte:</p>
                            <ul>
                                <li><strong>Email:</strong> soporte@nacionaltapizados.com</li>
                                <li><strong>Teléfono:</strong> +57 123 456 7890</li>
                                <li><strong>Horario de atención:</strong> Lunes a Viernes 8:00 AM - 6:00 PM</li>
                            </ul>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script>
        // Navegación suave entre secciones
        document.querySelectorAll('.help-nav a').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remover clase active de todos los enlaces
                document.querySelectorAll('.help-nav a').forEach(link => {
                    link.classList.remove('active');
                });
                
                // Agregar clase active al enlace clickeado
                this.classList.add('active');
                
                // Desplazarse a la sección
                const targetId = this.getAttribute('href');
                const targetSection = document.querySelector(targetId);
                
                if (targetSection) {
                    window.scrollTo({
                        top: targetSection.offsetTop - 20,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Actualizar enlace activo al hacer scroll
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('.help-section');
            const navLinks = document.querySelectorAll('.help-nav a');
            
            let currentSection = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                
                if (window.scrollY >= (sectionTop - 100)) {
                    currentSection = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${currentSection}`) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>