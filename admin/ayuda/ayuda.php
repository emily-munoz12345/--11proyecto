<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ayuda - Nacional Tapizados</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.9);
            --primary-hover: rgba(140, 74, 63, 1);
            --secondary-color: rgba(108, 117, 125, 0.9);
            --text-color: #ffffff;
            --text-light: #f8f9fa;
            --text-muted: rgba(255, 255, 255, 0.85);
            --bg-transparent: rgba(30, 30, 30, 0.85);
            --bg-transparent-light: rgba(50, 50, 50, 0.7);
            --bg-input: rgba(0, 0, 0, 0.6);
            --border-color: rgba(255, 255, 255, 0.3);
            --success-color: rgba(25, 135, 84, 0.9);
            --danger-color: rgba(220, 53, 69, 0.9);
            --warning-color: rgba(255, 193, 7, 0.9);
            --info-color: rgba(13, 202, 240, 0.9);
        }

        body {
            background-image: url('https://pfst.cf2.poecdn.net/base/image/60ab54eef562f30f85a67bde31f924f078199dae0b7bc6c333dfb467a2c13471?w=1024&h=768&pmaid=442168253');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-light);
            min-height: 100vh;
            line-height: 1.6;
        }

        .main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--bg-transparent);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
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
            font-size: 2.2rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
            color: var(--text-light);
        }

        .page-title i {
            margin-right: 12px;
            color: var(--primary-color);
        }

        .btn-help {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            transition: all 0.3s ease;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        .btn-help:hover {
            background-color: var(--primary-hover);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .btn-pdf {
            background-color: var(--danger-color);
        }

        .btn-pdf:hover {
            background-color: rgba(220, 53, 69, 1);
        }

        .help-section {
            margin-bottom: 2.5rem;
            padding: 2rem;
            background-color: var(--bg-transparent-light);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .help-section h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
            font-weight: 600;
        }

        .help-section h3 {
            color: var(--text-light);
            margin-top: 1.8rem;
            margin-bottom: 1.2rem;
            font-weight: 600;
            border-left: 4px solid var(--primary-color);
            padding-left: 1rem;
        }

        .help-section p {
            color: var(--text-light);
            margin-bottom: 1rem;
            font-size: 1.05rem;
        }

        .feature-card {
            background: linear-gradient(135deg, var(--bg-transparent-light) 0%, rgba(70, 70, 70, 0.6) 100%);
            border-radius: 10px;
            padding: 1.8rem;
            margin-bottom: 1.8rem;
            border-left: 5px solid var(--primary-color);
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            border-left: 5px solid var(--primary-hover);
        }

        .feature-card h4 {
            color: var(--text-light);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .feature-card h4 i {
            margin-right: 12px;
            color: var(--primary-color);
            font-size: 1.3rem;
        }

        .step-list {
            list-style-type: none;
            padding-left: 0;
            counter-reset: step;
        }

        .step-list li {
            margin-bottom: 1.2rem;
            padding-left: 2.5rem;
            position: relative;
            color: var(--text-light);
            font-size: 1.05rem;
        }

        .step-list li:before {
            content: counter(step);
            counter-increment: step;
            position: absolute;
            left: 0;
            top: 0;
            background-color: var(--primary-color);
            color: white;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
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
            color: var(--text-light);
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

        .warning-box {
            background-color: rgba(255, 193, 7, 0.2);
            border-left: 4px solid var(--warning-color);
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }

        .info-box {
            background-color: rgba(13, 202, 240, 0.2);
            border-left: 4px solid var(--info-color);
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }

        .faq-item {
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .faq-question {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .faq-answer {
            color: var(--text-light);
            padding-left: 1.8rem;
        }

        .quick-access {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .quick-card {
            background: linear-gradient(135deg, var(--bg-transparent-light) 0%, rgba(70, 70, 70, 0.6) 100%);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            cursor: pointer;
        }

        .quick-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            border-color: var(--primary-color);
        }

        .quick-card i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .quick-card h4 {
            color: var(--text-light);
            margin-bottom: 1rem;
        }

        .quick-card p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        /* Estilos para la sección de botones de navegación */
        .navigation-section {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }

        .navigation-title {
            color: var(--text-light);
            margin-bottom: 1.5rem;
            font-weight: 600;
            text-align: center;
        }

        .navigation-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .nav-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            text-align: center;
            min-height: 100px;
        }

        .nav-btn:hover {
            background-color: var(--primary-hover);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .nav-btn i {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .nav-btn span {
            font-size: 0.9rem;
        }

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
                font-size: 1.8rem;
            }

            .quick-access {
                grid-template-columns: 1fr;
            }

            .navigation-buttons {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Estilos para el contenido del PDF */
        .pdf-content {
            background: white;
            color: #333;
            padding: 2rem;
            font-family: Arial, sans-serif;
        }

        .pdf-content h1, .pdf-content h2, .pdf-content h3 {
            color: #8c4a3f;
        }

        .pdf-content .feature-card {
            background: #f8f9fa;
            border-left: 5px solid #8c4a3f;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-question-circle"></i> Sistema de Ayuda General
                </h1>
                <p class="text-muted mt-2">Nacional Tapizados - Sistema de Gestión</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="../dashboard.php" class="btn-help">
                    <i class="fas fa-arrow-left"></i> Volver al Sistema
                </a>
                <button class="btn-help btn-pdf" id="exportPdf">
                    <i class="fas fa-file-pdf"></i> Descargar Manual
                </button>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="quick-access">
            <div class="quick-card" onclick="scrollToSection('introduccion')">
                <i class="fas fa-info-circle"></i>
                <h4>Introducción</h4>
                <p>Conoce el sistema de gestión</p>
            </div>
            <div class="quick-card" onclick="scrollToSection('roles')">
                <i class="fas fa-user-shield"></i>
                <h4>Roles y Permisos</h4>
                <p>Entiende los niveles de acceso</p>
            </div>
            <div class="quick-card" onclick="scrollToSection('modulos')">
                <i class="fas fa-cubes"></i>
                <h4>Módulos Principales</h4>
                <p>Descubre las funcionalidades</p>
            </div>
            <div class="quick-card" onclick="scrollToSection('faq')">
                <i class="fas fa-question"></i>
                <h4>Preguntas Frecuentes</h4>
                <p>Resuelve tus dudas rápidamente</p>
            </div>
            <div class="quick-card" onclick="scrollToSection('soporte')">
                <i class="fas fa-headset"></i>
                <h4>Soporte Técnico</h4>
                <p>Contacta con nuestro equipo</p>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div id="introduccion" class="help-section">
            <h2>Introducción al Sistema</h2>
            <p>Bienvenido al sistema de gestión integral de <strong>Nacional Tapizados</strong>, diseñado específicamente para optimizar los procesos de nuestra empresa especializada en tapicería automotriz.</p>
            
            <div class="info-box">
                <h4><i class="fas fa-info-circle"></i> Propósito del Sistema</h4>
                <p>Este sistema centraliza la gestión de clientes, servicios, usuarios y reportes, permitiendo un control completo del negocio desde una única plataforma.</p>
            </div>
            
            <div class="feature-card">
                <h4><i class="fas fa-bullseye"></i> Objetivos Principales</h4>
                <ul>
                    <li>Gestionar eficientemente la información de clientes</li>
                    <li>Controlar el inventario de materiales y productos</li>
                    <li>Seguimiento de servicios y reparaciones</li>
                    <li>Generación de reportes y estadísticas</li>
                    <li>Optimizar los procesos administrativos</li>
                </ul>
            </div>
        </div>

        <div id="roles" class="help-section">
            <h2>Roles y Permisos del Sistema</h2>
            <p>El sistema cuenta con diferentes niveles de acceso según el rol del usuario, garantizando la seguridad de la información.</p>
            
            <div class="warning-box">
                <h4><i class="fas fa-exclamation-triangle"></i> Importante</h4>
                <p>Solo los usuarios con rol de <strong>Administrador</strong> tienen acceso completo a todas las funcionalidades del sistema.</p>
            </div>
            
            <table class="permission-table">
                <thead>
                    <tr>
                        <th>Función / Módulo</th>
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
                        <td>Gestión de Clientes</td>
                        <td><i class="fas fa-check text-success"></i></td>
                        <td><i class="fas fa-check text-success"></i></td>
                        <td><i class="fas fa-check text-success"></i></td>
                    </tr>
                    <tr>
                        <td>Gestión de Servicios</td>
                        <td><i class="fas fa-check text-success"></i></td>
                        <td><i class="fas fa-check text-success"></i></td>
                        <td><i class="fas fa-check text-success"></i></td>
                    </tr>
                    <tr>
                        <td>Reportes y Estadísticas</td>
                        <td><i class="fas fa-check text-success"></i></td>
                        <td><i class="fas fa-times text-danger"></i></td>
                        <td><i class="fas fa-times text-danger"></i></td>
                    </tr>
                    <tr>
                        <td>Configuración del Sistema</td>
                        <td><i class="fas fa-check text-success"></i></td>
                        <td><i class="fas fa-times text-danger"></i></td>
                        <td><i class="fas fa-times text-danger"></i></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="feature-card">
                <h4><i class="fas fa-user-cog"></i> Cambio de Roles</h4>
                <p>El cambio de roles de usuario solo puede ser realizado por un administrador del sistema. Para solicitar un cambio de permisos:</p>
                <ol class="step-list">
                    <li>Contacta al administrador del sistema</li>
                    <li>Solicita el cambio especificando el nuevo rol requerido</li>
                    <li>El administrador verificará y realizará el cambio</li>
                    <li>Recibirás confirmación una vez realizado el cambio</li>
                </ol>
            </div>
        </div>

        <div id="modulos" class="help-section">
            <h2>Módulos Principales del Sistema</h2>
            
            <div class="feature-card">
                <h4><i class="fas fa-users"></i> Gestión de Clientes</h4>
                <p>Módulo para administrar toda la información de los clientes de Nacional Tapizados.</p>
                <ul>
                    <li>Registro de nuevos clientes</li>
                    <li>Búsqueda y filtrado avanzado</li>
                    <li>Historial de servicios por cliente</li>
                    <li>Gestión de contactos y preferencias</li>
                </ul>
            </div>
            
            <div class="feature-card">
                <h4><i class="fas fa-user-cog"></i> Gestión de Usuarios</h4>
                <p>Módulo exclusivo para administradores para gestionar usuarios del sistema.</p>
                <ul>
                    <li>Creación y edición de usuarios</li>
                    <li>Asignación y cambio de roles</li>
                    <li>Activación y desactivación de cuentas</li>
                    <li>Control de accesos y permisos</li>
                </ul>
            </div>
            
            <div class="feature-card">
                <h4><i class="fas fa-tools"></i> Gestión de Servicios</h4>
                <p>Módulo para administrar los servicios de tapicería automotriz.</p>
                <ul>
                    <li>Registro de servicios solicitados</li>
                    <li>Seguimiento de estado de trabajos</li>
                    <li>Gestión de cotizaciones</li>
                    <li>Control de materiales utilizados</li>
                </ul>
            </div>
            
            <div class="feature-card">
                <h4><i class="fas fa-chart-bar"></i> Reportes y Estadísticas</h4>
                <p>Módulo para generar reportes y visualizar métricas del negocio.</p>
                <ul>
                    <li>Reportes de ventas y servicios</li>
                    <li>Estadísticas de clientes</li>
                    <li>Métricas de productividad</li>
                    <li>Indicadores de rendimiento</li>
                </ul>
            </div>
        </div>

        <div id="faq" class="help-section">
            <h2>Preguntas Frecuentes</h2>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <i class="fas fa-chevron-down"></i>
                    ¿Cómo restablezco mi contraseña?
                </div>
                <div class="faq-answer">
                    <p>Si has olvidado tu contraseña, contacta al administrador del sistema para que pueda restablecerla. Por seguridad, los usuarios no pueden restablecer sus propias contraseñas.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <i class="fas fa-chevron-down"></i>
                    ¿Por qué no puedo acceder a ciertas funciones?
                </div>
                <div class="faq-answer">
                    <p>El acceso a las funciones del sistema está determinado por tu rol. Si necesitas acceso a funciones específicas, solicita al administrador que revise y ajuste tus permisos.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <i class="fas fa-chevron-down"></i>
                    ¿Cómo busco un cliente específico?
                </div>
                <div class="faq-answer">
                    <p>En el módulo de Gestión de Clientes, utiliza el campo de búsqueda en la parte superior. Puedes buscar por nombre, teléfono, correo electrónico o cualquier dato del cliente.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <i class="fas fa-chevron-down"></i>
                    ¿Qué hago si el sistema no responde?
                </div>
                <div class="faq-answer">
                    <p>Si experimentas problemas de rendimiento o el sistema no responde:
                    <ol class="step-list">
                        <li>Verifica tu conexión a internet</li>
                        <li>Actualiza la página (F5)</li>
                        <li>Limpia la caché de tu navegador</li>
                        <li>Si persiste, contacta al soporte técnico</li>
                    </ol>
                    </p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <i class="fas fa-chevron-down"></i>
                    ¿Cómo genero un reporte?
                </div>
                <div class="faq-answer">
                    <p>Los reportes están disponibles en el módulo correspondiente. Selecciona el tipo de reporte, define los parámetros (fechas, filtros) y haz clic en "Generar Reporte". Algunos reportes requieren permisos de administrador.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <i class="fas fa-chevron-down"></i>
                    ¿Puedo usar el sistema desde mi teléfono?
                </div>
                <div class="faq-answer">
                    <p>Sí, el sistema es responsive y se adapta a diferentes tamaños de pantalla. Puedes acceder desde tu teléfono o tablet usando cualquier navegador moderno.</p>
                </div>
            </div>
        </div>

        <div id="soporte" class="help-section">
            <h2>Soporte Técnico</h2>
            <p>Si necesitas ayuda adicional o has encontrado un problema en el sistema, nuestro equipo de soporte está disponible para asistirte.</p>
            
            <div class="feature-card">
                <h4><i class="fas fa-headset"></i> Canales de Contacto</h4>
                <ul>
                    <li><strong>Email:</strong> soporte@nacionaltapizados.com</li>
                    <li><strong>Teléfono:</strong> +57 1 234 5678</li>
                    <li><strong>Horario de atención:</strong> Lunes a Viernes 8:00am - 6:00pm</li>
                    <li><strong>Dirección:</strong> Cra 45 # 26-85, Bogotá, Colombia</li>
                </ul>
            </div>
            
            <div class="info-box">
                <h4><i class="fas fa-lightbulb"></i> Recomendaciones para Reportar Problemas</h4>
                <p>Para agilizar la solución de problemas, por favor incluye esta información cuando contactes al soporte:</p>
                <ul>
                    <li>Tu nombre de usuario y rol en el sistema</li>
                    <li>Descripción detallada del problema</li>
                    <li>Pasos para reproducir el problema</li>
                    <li>Capturas de pantalla si es posible</li>
                    <li>Navegador y sistema operativo que usas</li>
                </ul>
            </div>
        </div>

        <!-- Sección de Navegación -->
        <div class="navigation-section">
            <h3 class="navigation-title">Acceso Rápido a Módulos</h3>
            <div class="navigation-buttons">
                <a href="inicio.php" class="nav-btn">
                    <i class="fas fa-home"></i>
                    <span>Ayuda de Inicio</span>
                </a>
                <a href="clientes.php" class="nav-btn">
                    <i class="fas fa-users"></i>
                    <span>Ayuda de Clientes</span>
                </a>
                <a href="servicios.php" class="nav-btn">
                    <i class="fas fa-tools"></i>
                    <span>Ayuda de Servicios</span>
                </a>
                <a href="vehiculos.php" class="nav-btn">
                    <i class="fas fa-boxes"></i>
                    <span>Ayuda de Vehiculos</span>
                </a>
                <a href="usuarios.php" class="nav-btn">
                    <i class="fas fa-chart-bar"></i>
                    <span>Ayuda de Usuarios</span>
                </a>
                <a href="materiales.php" class="nav-btn">
                    <i class="fas fa-user-cog"></i>
                    <span>Ayuda de Materiales</span>
                </a>
                <a href="trabajos.php" class="nav-btn">
                    <i class="fas fa-cog"></i>
                    <span>Ayuda de Trabajos</span>
                </a>
                
                <a href="cotizaciones.php" class="nav-btn">
                    <i class="fas fa-cog"></i>
                    <span>Ayuda de Cotizaciones</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Contenido para PDF (oculto) -->
    <div id="pdfContent" style="display: none;">
        <div class="pdf-content">
            <h1>Manual de Usuario - Sistema de Gestión</h1>
            <h2>Nacional Tapizados</h2>
            <p><strong>Fecha de generación:</strong> <span id="pdfDate"></span></p>
            
            <div id="pdfSections">
                <!-- El contenido se generará dinámicamente -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para desplazarse a una sección
        function scrollToSection(sectionId) {
            document.getElementById(sectionId).scrollIntoView({ 
                behavior: 'smooth' 
            });
        }

        // Función para alternar preguntas FAQ
        function toggleFaq(element) {
            const answer = element.nextElementSibling;
            const icon = element.querySelector('i');
            
            if (answer.style.display === 'block') {
                answer.style.display = 'none';
                icon.className = 'fas fa-chevron-down';
            } else {
                answer.style.display = 'block';
                icon.className = 'fas fa-chevron-up';
            }
        }

        // Inicializar FAQ
        document.addEventListener('DOMContentLoaded', function() {
            // Ocultar todas las respuestas al cargar
            document.querySelectorAll('.faq-answer').forEach(answer => {
                answer.style.display = 'none';
            });
        });

        // Función para generar el PDF
        document.getElementById('exportPdf').addEventListener('click', function() {
            generatePdf();
        });

        function generatePdf() {
            // Configurar fecha actual
            const now = new Date();
            document.getElementById('pdfDate').textContent = now.toLocaleDateString('es-ES');
            
            // Obtener el contenido
            const pdfSections = document.getElementById('pdfSections');
            pdfSections.innerHTML = '';
            
            // Agregar todas las secciones al PDF
            const sections = [
                'introduccion', 'roles', 'modulos', 'faq', 'soporte'
            ];
            
            sections.forEach(sectionId => {
                const section = document.getElementById(sectionId).cloneNode(true);
                cleanContentForPdf(section);
                pdfSections.appendChild(section);
            });
            
            // Configurar opciones del PDF
            const options = {
                margin: 10,
                filename: `manual-usuario-nacional-tapizados-${now.toISOString().split('T')[0]}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            
            // Generar el PDF
            html2pdf().set(options).from(document.getElementById('pdfContent')).save();
        }
        
        // Función para limpiar el contenido para PDF
        function cleanContentForPdf(element) {
            // Remover elementos interactivos
            element.querySelectorAll('button, .quick-access, .navigation-section').forEach(el => {
                el.remove();
            });
            
            // Ajustar estilos para PDF
            element.querySelectorAll('.help-section').forEach(el => {
                el.style.marginBottom = '20px';
                el.style.padding = '15px';
                el.style.border = '1px solid #ddd';
            });
            
            // Mostrar todas las respuestas FAQ en el PDF
            element.querySelectorAll('.faq-answer').forEach(answer => {
                answer.style.display = 'block';
            });
            
            // Asegurar que el contenido sea visible
            element.style.display = 'block';
        }
    </script>
</body>
</html>