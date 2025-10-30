<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda - Gestión de Vehículos | Nacional Tapizados</title>
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
            <h1 class="page-title"><i class="fas fa-car"></i>Ayuda - Gestión de Vehículos</h1>
            <div>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>Volver
                </a>
                <a href="../vehiculos/index.php" class="btn btn-primary">
                    <i class="fas fa-car"></i>Ir a Vehículos
                </a>
            </div>
        </div>

        <div class="help-content">
            <nav class="help-nav">
                <ul>
                    <li><a href="#introduccion" class="active">Introducción</a></li>
                    <li><a href="#vista-general">Vista General</a></li>
                    <li><a href="#buscar-vehiculos">Buscar Vehículos</a></li>
                    <li><a href="#nuevo-vehiculo">Agregar Vehículo</a></li>
                    <li><a href="#ver-detalles">Ver Detalles</a></li>
                    <li><a href="#editar-vehiculo">Editar Vehículo</a></li>
                    <li><a href="#papelera">Papelera</a></li>
                    <li><a href="#problemas-comunes">Problemas Comunes</a></li>
                </ul>
            </nav>

            <div class="help-sections">
                <section id="introduccion" class="help-section">
                    <h2><i class="fas fa-info-circle"></i>Introducción a la Gestión de Vehículos</h2>
                    <p>El módulo de Gestión de Vehículos te permite administrar toda la información relacionada con los vehículos de los clientes de Nacional Tapizados. Desde aquí podrás registrar nuevos vehículos, buscar información existente, editar datos y gestionar registros eliminados.</p>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (261).png" alt="Vista general de gestión de vehículos">
                        <div class="image-caption">Figura 1: Vista general del módulo de Gestión de Vehículos</div>
                    </div>
                    
                    <p>Este módulo está integrado con el sistema de clientes, permitiendo asociar cada vehículo a su propietario correspondiente para un mejor control y seguimiento.</p>
                </section>

                <section id="vista-general" class="help-section">
                    <h2><i class="fas fa-chart-bar"></i>Vista General y Estadísticas</h2>
                    <p>Al acceder al módulo de vehículos, verás un resumen con las principales estadísticas:</p>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <h4><i class="fas fa-car"></i>Total de Vehículos</h4>
                            <p>Muestra el número total de vehículos registrados en el sistema.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-calendar-plus"></i>Último Registro</h4>
                            <p>Indica la fecha del último vehículo agregado al sistema.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-calendar-day"></i>Registros Hoy</h4>
                            <p>Muestra cuántos vehículos se han registrado en el día actual.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-trash"></i>En Papelera</h4>
                            <p>Indica cuántos vehículos han sido movidos a la papelera.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (267).png" alt="Estadísticas de vehículos">
                        <div class="image-caption">Figura 2: Panel de estadísticas de vehículos</div>
                    </div>
                </section>

                <section id="buscar-vehiculos" class="help-section">
                    <h2><i class="fas fa-search"></i>Buscar Vehículos</h2>
                    <p>Para encontrar rápidamente un vehículo específico, utiliza la función de búsqueda:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a la búsqueda</h3>
                            <p>Localiza el campo de búsqueda en la parte superior de la lista de vehículos.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Ingresar criterios</h3>
                            <p>Escribe la marca, modelo o placa del vehículo que deseas encontrar.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Filtrar resultados</h3>
                            <p>Utiliza las pestañas "Recientes" y "Papelera" para filtrar los resultados de búsqueda.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (261).png" alt="Búsqueda de vehículos">
                        <div class="image-caption">Figura 3: Campo de búsqueda y filtros de vehículos</div>
                    </div>
                </section>

                <section id="nuevo-vehiculo" class="help-section">
                    <h2><i class="fas fa-plus-circle"></i>Agregar Nuevo Vehículo</h2>
                    <p>Para registrar un nuevo vehículo en el sistema, sigue estos pasos:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder al formulario</h3>
                            <p>Haz clic en el botón "Nuevo Vehículo" en la parte superior de la pantalla.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Seleccionar cliente</h3>
                            <p>Elige el cliente propietario del vehículo del menú desplegable.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Completar información básica</h3>
                            <p>Llena todos los campos obligatorios marcados con asterisco (*):</p>
                            <ul>
                                <li><strong>Marca:</strong> Fabricante del vehículo (Toyota, Ford, Chevrolet, etc.)</li>
                                <li><strong>Modelo:</strong> Modelo específico del vehículo (Corolla, F-150, Spark, etc.)</li>
                                <li><strong>Placa:</strong> Número de placa con formato ABC123</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Información adicional</h3>
                            <p>Completa los campos opcionales si es necesario:</p>
                            <ul>
                                <li><strong>Color:</strong> Color principal del vehículo</li>
                                <li><strong>Notas Adicionales:</strong> Observaciones o detalles importantes</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">5</div>
                        <div class="step-content">
                            <h3>Guardar vehículo</h3>
                            <p>Haz clic en "Guardar Vehículo" para registrar la información en el sistema.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (263).png" alt="Formulario de nuevo vehículo">
                        <div class="image-caption">Figura 4: Formulario para agregar nuevo vehículo</div>
                    </div>
                </section>

                <section id="ver-detalles" class="help-section">
                    <h2><i class="fas fa-eye"></i>Ver Detalles del Vehículo</h2>
                    <p>Para ver la información completa de un vehículo:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Seleccionar vehículo</h3>
                            <p>Haz clic en cualquier vehículo de la lista para ver sus detalles.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Revisar información</h3>
                            <p>Se mostrará una vista detallada con todos los datos del vehículo:</p>
                            <ul>
                                <li><strong>Marca y Modelo:</strong> Información básica del vehículo</li>
                                <li><strong>Placa:</strong> Número de identificación</li>
                                <li><strong>Propietario:</strong> Cliente dueño del vehículo</li>
                                <li><strong>Información de contacto:</strong> Teléfono y correo del propietario</li>
                                <li><strong>Notas:</strong> Información adicional sobre el vehículo</li>
                                <li><strong>Estado:</strong> Si está activo o en papelera</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (264).png" alt="Detalles del vehículo">
                        <div class="image-caption">Figura 5: Vista de detalles de un vehículo</div>
                    </div>
                </section>

                <section id="editar-vehiculo" class="help-section">
                    <h2><i class="fas fa-edit"></i>Editar Información del Vehículo</h2>
                    <p>Para modificar la información de un vehículo existente:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a edición</h3>
                            <p>Desde la vista de detalles del vehículo, haz clic en "Editar".</p>
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
                            <p>Haz clic en "Actualizar Vehículo" para guardar los cambios.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (265).png" alt="Editar vehículo">
                        <div class="image-caption">Figura 6: Formulario de edición de vehículo</div>
                    </div>
                    
                    <p><strong>Nota:</strong> Los campos marcados con asterisco (*) son obligatorios y deben completarse para guardar los cambios.</p>
                </section>

                <section id="papelera" class="help-section">
                    <h2><i class="fas fa-trash"></i>Gestión de Papelera</h2>
                    <p>La papelera almacena temporalmente los vehículos eliminados antes de su eliminación permanente:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Mover a papelera</h3>
                            <p>Desde la vista de detalles del vehículo, haz clic en "Mover a papelera".</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Ver papelera</h3>
                            <p>Haz clic en la pestaña "Papelera" para ver todos los vehículos eliminados.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Restaurar o eliminar</h3>
                            <p>Desde la papelera puedes restaurar vehículos o eliminarlos permanentemente.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (268).png" alt="Vehículo en papelera">
                        <div class="image-caption">Figura 7: Vehículo movido a la papelera</div>
                    </div>
                    
                    <p><strong>Importante:</strong> Los vehículos en la papelera pueden ser restaurados dentro de los 30 días. Después de este período, se eliminarán automáticamente del sistema.</p>
                </section>

                <section id="problemas-comunes" class="help-section">
                    <h2><i class="fas fa-tools"></i>Solución de Problemas Comunes</h2>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No puedo encontrar un vehículo</h3>
                    <ul>
                        <li>Verifica que estés escribiendo correctamente la placa en el buscador.</li>
                        <li>Comprueba si el vehículo ha sido movido a la papelera.</li>
                        <li>Intenta buscar por marca o modelo en lugar de la placa.</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>Error al guardar un vehículo</h3>
                    <ul>
                        <li>Asegúrate de que todos los campos obligatorios estén completos.</li>
                        <li>Verifica que el formato de la placa sea correcto (3 letras + 3 números).</li>
                        <li>Comprueba que el cliente seleccionado exista en el sistema.</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No aparece el cliente en la lista</h3>
                    <ul>
                        <li>Verifica que el cliente esté registrado en el sistema.</li>
                        <li>Comprueba que el cliente no esté en estado inactivo.</li>
                        <li>Si el cliente no existe, regístralo primero en el módulo de clientes.</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No tengo permisos para algunas acciones</h3>
                    <ul>
                        <li>Algunas funciones pueden estar restringidas según tu perfil de usuario.</li>
                        <li>Contacta al administrador del sistema si necesitas permisos adicionales.</li>
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