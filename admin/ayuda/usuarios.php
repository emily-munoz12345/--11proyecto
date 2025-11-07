<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda - Gestión de Usuarios | Nacional Tapizados</title>
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

        .help-nav a:hover,
        .help-nav a.active {
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

        .help-section p,
        .help-section ul {
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
            <h1 class="page-title"><i class="fas fa-question-circle"></i>Ayuda - Gestión de Usuarios</h1>
            <div>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>Volver
                </a>
                <a href="../formularios/usuarios/index.php" class="btn btn-primary">
                    <i class="fas fa-users-cog"></i>Ir a Usuarios
                </a>
            </div>
        </div>

        <div class="help-content">
            <nav class="help-nav">
                <ul>
                    <li><a href="#introduccion" class="active">Introducción</a></li>
                    <li><a href="#acceso-administrador">Acceso Administrador</a></li>
                    <li><a href="#vista-general">Vista General</a></li>
                    <li><a href="#buscar-usuarios">Buscar Usuarios</a></li>
                    <li><a href="#nuevo-usuario">Agregar Usuario</a></li>
                    <li><a href="#ver-detalles">Ver Detalles</a></li>
                    <li><a href="#editar-usuario">Editar Usuario</a></li>
                    <li><a href="#activar-desactivar">Activar/Desactivar</a></li>
                    <li><a href="#eliminar-usuario">Eliminar Usuario</a></li>
                    <li><a href="#problemas-comunes">Problemas Comunes</a></li>
                </ul>
            </nav>

            <div class="help-sections">
                <section id="introduccion" class="help-section">
                    <h2><i class="fas fa-info-circle"></i>Introducción a la Gestión de Usuarios</h2>
                    <p>El módulo de Gestión de Usuarios permite administrar todos los usuarios del sistema de Nacional Tapizados. Desde aquí podrás agregar nuevos usuarios, gestionar roles, activar o desactivar cuentas y eliminar usuarios.</p>

                    <div class="image-container">
                        <img src="../imagenes/ayuda/usuarios/inicio.png">
                        <div class="image-caption"></div>
                    </div>

                    <p><strong>IMPORTANTE:</strong> Este módulo está disponible exclusivamente para usuarios con rol de Administrador. Los usuarios con otros roles no tendrán acceso a esta sección.</p>
                </section>

                <section id="acceso-administrador" class="help-section">
                    <h2><i class="fas fa-user-shield"></i>Acceso Exclusivo para Administradores</h2>
                    <p>La gestión de usuarios es una función crítica que solo debe ser manejada por administradores del sistema:</p>

                    <div class="feature-grid">
                        <div class="feature-card">
                            <h4><i class="fas fa-lock"></i>Control de Acceso</h4>
                            <p>Solo los usuarios con rol de Administrador pueden acceder a esta sección.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-user-tag"></i>Gestión de Roles</h4>
                            <p>Los administradores pueden asignar y modificar roles de usuario (Administrador, Técnico, Vendedor).</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-user-slash"></i>Control de Estado</h4>
                            <p>Los administradores pueden activar o desactivar cuentas de usuario.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-trash-alt"></i>Eliminación Definitiva</h4>
                            <p>Los usuarios eliminados se borran permanentemente del sistema sin pasar por papelera.</p>
                        </div>
                    </div>

                    <div class="image-container">
                        <img src="../imagenes/ayuda/usuarios/accus.png">
                        <div class="image-caption"></div>
                    </div>
                </section>

                <section id="vista-general" class="help-section">
                    <h2><i class="fas fa-chart-bar"></i>Vista General y Estadísticas</h2>
                    <p>Al acceder al módulo de usuarios, verás un resumen con las principales estadísticas:</p>

                    <div class="feature-grid">
                        <div class="feature-card">
                            <h4><i class="fas fa-users"></i>Total de Usuarios</h4>
                            <p>Muestra el número total de usuarios registrados en el sistema.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-user-check"></i>Usuarios Activos</h4>
                            <p>Indica cuántos usuarios tienen su cuenta activa en el sistema.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-user-times"></i>Usuarios Inactivos</h4>
                            <p>Muestra cuántos usuarios tienen su cuenta desactivada.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-calendar-day"></i>Registros Hoy</h4>
                            <p>Indica cuántos usuarios se han registrado en el día actual.</p>
                        </div>
                    </div>

                    <div class="image-container">
                        <img src="../imagenes/ayuda/usuarios/estd.png">
                        <div class="image-caption"></div>
                    </div>
                </section>

                <section id="buscar-usuarios" class="help-section">
                    <h2><i class="fas fa-search"></i>Buscar Usuarios</h2>
                    <p>Para encontrar rápidamente un usuario específico, utiliza la función de búsqueda:</p>

                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a la búsqueda</h3>
                            <p>Localiza el campo de búsqueda en la parte superior de la lista de usuarios.</p>
                        </div>
                    </div>

                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Ingresar criterios</h3>
                            <p>Escribe el nombre, nombre de usuario o correo del usuario que deseas encontrar.</p>
                        </div>
                    </div>

                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Filtrar resultados</h3>
                            <p>Utiliza las pestañas "Recientes" e "Inactivos" para filtrar los resultados de búsqueda.</p>
                        </div>
                    </div>

                    <div class="image-container">
                        <img src="../imagenes/ayuda/usuarios/busq.png">
                        <div class="image-caption"></div>
                    </div>
                </section>

                <section id="nuevo-usuario" class="help-section">
                    <h2><i class="fas fa-user-plus"></i>Agregar Nuevo Usuario</h2>
                    <p>Para registrar un nuevo usuario en el sistema, sigue estos pasos:</p>

                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder al formulario</h3>
                            <p>Haz clic en el botón "Nuevo Usuario" en la parte superior de la pantalla.</p>
                        </div>
                    </div>

                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Completar información</h3>
                            <p>Llena todos los campos obligatorios:</p>
                            <ul>
                                <li><strong>Nombre Completo:</strong> Nombre y apellidos del usuario.</li>
                                <li><strong>Nombre de Usuario:</strong> Identificador único para el sistema.</li>
                                <li><strong>Correo Electrónico:</strong> Dirección de email válida.</li>
                                <li><strong>Correo Electrónico:</strong> Dirección de email válida.</li>
                                <li><strong>Contraseña:</strong> Clave de acceso para el usuario.</li>
                                <li><strong>Rol:</strong> Selecciona el tipo de usuario (Administrador, Técnico, Vendedor).</li>
                            </ul>
                        </div>
                    </div>

                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Información adicional</h3>
                            <p>Completa los campos opcionales si es necesario:</p>
                            <ul>
                                <li><strong>Teléfono:</strong> Número de contacto del usuario.</li>
                                <li><strong>Estado:</strong> Define si el usuario estará activo o inactivo.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="step-container">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Guardar usuario</h3>
                            <p>Haz clic en "Guardar Usuario" para registrar la información en el sistema.</p>
                        </div>
                    </div>

                    <div class="image-container">
                        <img src="../imagenes/ayuda/usuarios/crear.png">
                        <div class="image-caption"></div>
                    </div>

                    <p><strong>Nota importante:</strong> La contraseña se almacena en texto plano en el sistema. Se recomienda utilizar contraseñas seguras y cambiarlas periódicamente.</p>
                </section>

                <section id="ver-detalles" class="help-section">
                    <h2><i class="fas fa-eye"></i>Ver Detalles del Usuario</h2>
                    <p>Para ver la información completa de un usuario:</p>

                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Seleccionar usuario</h3>
                            <p>Haz clic en cualquier usuario de la lista para ver sus detalles.</p>
                        </div>
                    </div>

                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Revisar información</h3>
                            <p>Se mostrará una tarjeta con todos los datos del usuario:</p>
                            <ul>
                                <li><strong>ID del Usuario:</strong> Identificador único en el sistema.</li>
                                <li><strong>Información de contacto:</strong> Nombre, teléfono, correo.</li>
                                <li><strong>Rol:</strong> Tipo de usuario (Administrador, Técnico, Vendedor).</li>
                                <li><strong>Fecha de Creación:</strong> Cuándo fue agregado al sistema.</li>
                                <li><strong>Última Actividad:</strong> Cuándo fue la última vez que inició sesión.</li>
                                <li><strong>Estado:</strong> Si está activo o inactivo.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="image-container">
                        <img src="../imagenes/ayuda/usuarios/ver.png">
                        <div class="image-caption"></div>
                    </div>
                </section>

                <section id="editar-usuario" class="help-section">
                    <h2><i class="fas fa-edit"></i>Editar Información del Usuario</h2>
                    <p>Para modificar la información de un usuario existente:</p>

                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a edición</h3>
                            <p>Desde la vista de detalles del usuario, haz clic en "Editar Usuario".</p>
                        </div>
                    </div>

                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Modificar información</h3>
                            <p>Actualiza los campos que necesites cambiar en el formulario de edición.</p>
                        </div>
                    </div>

                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Guardar cambios</h3>
                            <p>Haz clic en "Guardar Cambios" para actualizar la información del usuario.</p>
                        </div>
                    </div>

                    <div class="image-container">
                        <img src="../imagenes/ayuda/usuarios/edit.png">
                        <div class="image-caption"></div>
                    </div>

                    <p><strong>Nota:</strong> Puedes modificar todos los campos del usuario incluyendo su rol, que determinará los permisos que tendrá en el sistema.</p>
                </section>

                <section id="activar-desactivar" class="help-section">
                    <h2><i class="fas fa-power-off"></i>Activar y Desactivar Usuarios</h2>
                    <p>Puedes controlar el acceso al sistema activando o desactivando usuarios:</p>

                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Seleccionar acción</h3>
                            <p>Desde la vista de detalles del usuario, haz clic en "Desactivar" o "Activar".</p>
                        </div>
                    </div>

                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Confirmar acción</h3>
                            <p>El sistema mostrará una confirmación antes de cambiar el estado del usuario.</p>
                        </div>
                    </div>

                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Verificar cambio</h3>
                            <p>El estado del usuario se actualizará inmediatamente en el sistema.</p>
                        </div>
                    </div>
<!--
                    <div class="image-container">
                        <img src="../imagenes/ayuda/usuarios/config.png">
                        <div class="image-caption">Figura 8: Confirmación para cambiar estado de usuario</div>
                    </div>
    -->
                    <p><strong>Importante:</strong> Los usuarios inactivos no pueden iniciar sesión en el sistema, aunque sus datos permanecen almacenados.</p>
                </section>

                <section id="eliminar-usuario" class="help-section">
                    <h2><i class="fas fa-trash-alt"></i>Eliminar Usuario Definitivamente</h2>
                    <p><strong>ADVERTENCIA:</strong> La eliminación de usuarios es permanente. No existe papelera de reciclaje para usuarios.</p>

                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Seleccionar eliminación</h3>
                            <p>Desde la vista de detalles del usuario, haz clic en "Eliminar Usuario".</p>
                        </div>
                    </div>

                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Confirmar eliminación</h3>
                            <p>El sistema solicitará confirmación antes de proceder con la eliminación.</p>
                        </div>
                    </div>

                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Eliminación permanente</h3>
                            <p>Una vez confirmado, el usuario será eliminado permanentemente del sistema.</p>
                        </div>
                    </div>

                    <div class="image-container">
                        <img src="../imagenes/ayuda/usuarios/elim.png">
                        <div class="image-caption"></div>
                    </div>

                    <div class="step-container" style="background-color: rgba(220, 53, 69, 0.2); border-left-color: var(--danger-color);">
                        <div class="step-content">
                            <h3><i class="fas fa-exclamation-triangle"></i>Precaución</h3>
                            <p>La eliminación de usuarios es irreversible. Una vez eliminado:</p>
                            <ul>
                                <li>No podrá recuperarse la información del usuario</li>
                                <li>Se perderán todos los datos asociados al usuario</li>
                                <li>El usuario no podrá volver a acceder al sistema</li>
                            </ul>
                            <p>Se recomienda desactivar usuarios en lugar de eliminarlos, a menos que sea estrictamente necesario.</p>
                        </div>
                    </div>
                </section>

                <section id="problemas-comunes" class="help-section">
                    <h2><i class="fas fa-tools"></i>Solución de Problemas Comunes</h2>

                    <h3><i class="fas fa-exclamation-triangle"></i>No puedo acceder a la gestión de usuarios</h3>
                    <ul>
                        <li>Verifica que tu usuario tenga rol de Administrador.</li>
                        <li>Contacta al administrador principal del sistema.</li>
                        <li>Comprueba que tu cuenta esté activa.</li>
                    </ul>

                    <h3><i class="fas fa-exclamation-triangle"></i>Error al guardar un usuario</h3>
                    <ul>
                        <li>Asegúrate de que todos los campos obligatorios estén completos.</li>
                        <li>Verifica que el nombre de usuario no esté en uso por otro usuario.</li>
                        <li>Comprueba que el formato del correo electrónico sea válido.</li>
                        <li>Confirma que las contraseñas coincidan si estás cambiando la contraseña.</li>
                    </ul>

                    <h3><i class="fas fa-exclamation-triangle"></i>Un usuario no puede iniciar sesión</h3>
                    <ul>
                        <li>Verifica que el usuario esté activo en el sistema.</li>
                        <li>Comprueba que el nombre de usuario y contraseña sean correctos.</li>
                        <li>Confirma que el usuario tenga un rol asignado.</li>
                    </ul>

                    <h3><i class="fas fa-exclamation-triangle"></i>No aparecen todas las opciones de administrador</h3>
                    <ul>
                        <li>Asegúrate de que tu usuario tenga permisos de Administrador completos.</li>
                        <li>Actualiza la página (F5) para recargar los permisos.</li>
                        <li>Cierra sesión y vuelve a iniciar sesión.</li>
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