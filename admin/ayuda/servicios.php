<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda - Gestión de Servicios | Nacional Tapizados</title>
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

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .action-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background-color: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .action-button:hover {
            background-color: rgba(140, 74, 63, 0.3);
            border-color: var(--primary-color);
        }

        /* Estilos para tablas de información */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            background-color: var(--bg-input);
            border-radius: 8px;
            overflow: hidden;
        }

        .info-table th, .info-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .info-table th {
            background-color: rgba(140, 74, 63, 0.3);
            color: var(--text-color);
            font-weight: 600;
        }

        .info-table td {
            color: var(--text-muted);
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

            .action-buttons {
                flex-direction: column;
            }

            .info-table {
                font-size: 0.9rem;
            }
        }
    </style>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="main-container">
        <div class="header-section">
            <h1 class="page-title"><i class="fas fa-concierge-bell"></i>Ayuda - Gestión de Servicios</h1>
            <div>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>Volver
                </a>
                <a href="../servicios/index.php" class="btn btn-primary">
                    <i class="fas fa-concierge-bell"></i>Ir a Servicios
                </a>
            </div>
        </div>

        <div class="help-content">
            <nav class="help-nav">
                <ul>
                    <li><a href="#introduccion" class="active">Introducción</a></li>
                    <li><a href="#vista-general">Vista General</a></li>
                    <li><a href="#tipos-servicios">Tipos de Servicios</a></li>
                    <li><a href="#buscar-servicios">Buscar Servicios</a></li>
                    <li><a href="#nuevo-servicio">Crear Servicio</a></li>
                    <li><a href="#ver-detalles">Ver Detalles</a></li>
                    <li><a href="#editar-servicio">Editar Servicio</a></li>
                    <li><a href="#precios-tiempos">Precios y Tiempos</a></li>
                    <li><a href="#categorias">Categorías</a></li>
                    <li><a href="#integracion">Integración con Cotizaciones</a></li>
                    <li><a href="#papelera">Papelera</a></li>
                    <li><a href="#problemas-comunes">Problemas Comunes</a></li>
                </ul>
            </nav>

            <div class="help-sections">
                <section id="introduccion" class="help-section">
                    <h2><i class="fas fa-info-circle"></i>Introducción a la Gestión de Servicios</h2>
                    <p>El módulo de Gestión de Servicios te permite administrar todos los servicios de tapicería automotriz ofrecidos por Nacional Tapizados. Desde aquí podrás crear, editar y organizar los diferentes servicios que se ofrecen a los clientes, incluyendo precios, tiempos de ejecución y descripciones detalladas.</p>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (304).png" alt="Vista general de gestión de servicios">
                        <div class="image-caption">Figura 1: Vista general del módulo de Gestión de Servicios</div>
                    </div>
                    
                    <p><strong>Características principales:</strong></p>
                    <ul>
                        <li>Catálogo completo de servicios de tapicería</li>
                        <li>Gestión de precios y tiempos de ejecución</li>
                        <li>Organización por categorías</li>
                        <li>Integración automática con cotizaciones</li>
                        <li>Control de servicios más populares</li>
                    </ul>
                </section>

                <section id="vista-general" class="help-section">
                    <h2><i class="fas fa-chart-bar"></i>Vista General y Estadísticas</h2>
                    <p>Al acceder al módulo de servicios, verás un resumen con las principales estadísticas:</p>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <h4><i class="fas fa-concierge-bell"></i>Total de Servicios</h4>
                            <p>Muestra el número total de servicios registrados en el sistema.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-calendar-plus"></i>Último Registro</h4>
                            <p>Indica la fecha del último servicio agregado al sistema.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-calendar-day"></i>Registros Hoy</h4>
                            <p>Muestra cuántos servicios se han registrado en el día actual.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-trash"></i>En Papelera</h4>
                            <p>Indica cuántos servicios han sido movidos a la papelera.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (304).png" alt="Estadísticas de servicios">
                        <div class="image-caption">Figura 2: Panel de estadísticas de servicios</div>
                    </div>
                    
                    <p>Además, el sistema muestra una lista de todos los servicios disponibles con información clave como nombre, precio, tiempo de ejecución y descripción breve.</p>
                </section>

                <section id="tipos-servicios" class="help-section">
                    <h2><i class="fas fa-tags"></i>Tipos de Servicios</h2>
                    <p>En Nacional Tapizados ofrecemos una amplia gama de servicios especializados en tapicería automotriz:</p>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <h4><i class="fas fa-couch"></i>Tapizado Completo</h4>
                            <p>Reemplazo completo de la tapicería de los asientos del vehículo. Incluye selección de materiales y acabados personalizados.</p>
                            <p><strong>Duración:</strong> 2-3 días</p>
                            <p><strong>Precio:</strong> Desde $500.000</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-tools"></i>Reparación de Asientos</h4>
                            <p>Reparación de daños específicos en asientos como roturas, desgarros o problemas en la estructura.</p>
                            <p><strong>Duración:</strong> 1-2 días</p>
                            <p><strong>Precio:</strong> Desde $100.000</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-spa"></i>Tratamiento de Cuero</h4>
                            <p>Limpieza profunda, hidratación y protección de superficies de cuero para mantener su aspecto y durabilidad.</p>
                            <p><strong>Duración:</strong> 1 día</p>
                            <p><strong>Precio:</strong> Desde $100.000</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-palette"></i>Personalización</h4>
                            <p>Servicios de personalización avanzada incluyendo costuras especiales, logos bordados y diseños únicos.</p>
                            <p><strong>Duración:</strong> Variable</p>
                            <p><strong>Precio:</strong> Personalizado</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (307).png" alt="Lista de servicios">
                        <div class="image-caption">Figura 3: Ejemplos de servicios disponibles</div>
                    </div>
                </section>

                <section id="buscar-servicios" class="help-section">
                    <h2><i class="fas fa-search"></i>Buscar Servicios</h2>
                    <p>Para encontrar rápidamente un servicio específico, utiliza la función de búsqueda:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a la búsqueda</h3>
                            <p>Localiza el campo de búsqueda en la parte superior de la lista de servicios.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Ingresar criterios</h3>
                            <p>Puedes buscar por:</p>
                            <ul>
                                <li>Nombre del servicio</li>
                                <li>Descripción</li>
                                <li>Categoría</li>
                                <li>Palabras clave</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Filtrar resultados</h3>
                            <p>Utiliza las pestañas disponibles para filtrar por:</p>
                            <ul>
                                <li><strong>Recientes:</strong> Servicios agregados recientemente</li>
                                <li><strong>Por Categoría:</strong> Filtra por categoría específica</li>
                                <li><strong>Papelera:</strong> Servicios eliminados temporalmente</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (305).png" alt="Búsqueda de servicios">
                        <div class="image-caption">Figura 4: Campo de búsqueda y opciones de servicios</div>
                    </div>
                </section>

                <section id="nuevo-servicio" class="help-section">
                    <h2><i class="fas fa-plus-circle"></i>Crear Nuevo Servicio</h2>
                    <p>Para registrar un nuevo servicio en el sistema, sigue estos pasos:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder al formulario</h3>
                            <p>Haz clic en el botón "Nuevo Servicio" en la parte superior de la pantalla.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Completar información básica</h3>
                            <p>Llena todos los campos obligatorios:</p>
                            <ul>
                                <li><strong>Nombre del Servicio:</strong> Nombre descriptivo y claro</li>
                                <li><strong>Categoría:</strong> Tipo de servicio (Tapizado, Reparación, etc.)</li>
                                <li><strong>Precio:</strong> Costo base del servicio</li>
                                <li><strong>Tiempo de Ejecución:</strong> Duración estimada en días</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Información adicional</h3>
                            <p>Completa los campos opcionales para mejor descripción:</p>
                            <ul>
                                <li><strong>Descripción Detallada:</strong> Explicación completa del servicio</li>
                                <li><strong>Materiales Incluidos:</strong> Materiales que incluye el servicio</li>
                                <li><strong>Requisitos Especiales:</strong> Condiciones o requisitos específicos</li>
                                <li><strong>Garantía:</strong> Tiempo de garantía del servicio</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Guardar servicio</h3>
                            <p>Haz clic en "Guardar Servicio" para registrar la información en el sistema.</p>
                        </div>
                    </div>
                    
                    <p><strong>Consejo:</strong> Usa descripciones claras y detalladas para que los clientes entiendan exactamente qué incluye cada servicio.</p>
                </section>

                <section id="ver-detalles" class="help-section">
                    <h2><i class="fas fa-eye"></i>Ver Detalles del Servicio</h2>
                    <p>Para ver la información completa de un servicio:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a detalles</h3>
                            <p>Haz clic en "Ver Detalles" desde la lista de servicios o en el menú de opciones.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Información completa</h3>
                            <p>Se mostrará una vista detallada con todos los datos del servicio:</p>
                            <ul>
                                <li><strong>ID del Servicio:</strong> Identificador único en el sistema</li>
                                <li><strong>Nombre y Descripción:</strong> Información descriptiva completa</li>
                                <li><strong>Precio Base:</strong> Costo actual del servicio</li>
                                <li><strong>Tiempo de Ejecución:</strong> Duración estimada</li>
                                <li><strong>Categoría:</strong> Tipo de servicio</li>
                                <li><strong>Materiales Incluidos:</strong> Lista de materiales que incluye</li>
                                <li><strong>Garantía:</strong> Tiempo de garantía ofrecido</li>
                                <li><strong>Fechas:</strong> Registro y última actualización</li>
                                <li><strong>Estado:</strong> Estado actual del servicio</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="#" class="action-button">
                            <i class="fas fa-edit"></i>Editar Servicio
                        </a>
                        <a href="#" class="action-button">
                            <i class="fas fa-arrow-left"></i>Volver a la Lista
                        </a>
                    </div>
                </section>

                <section id="editar-servicio" class="help-section">
                    <h2><i class="fas fa-edit"></i>Editar Información del Servicio</h2>
                    <p>Para modificar la información de un servicio existente:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a edición</h3>
                            <p>Desde la vista de detalles del servicio, haz clic en "Editar Servicio".</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Modificar información</h3>
                            <p>Actualiza los campos que necesites cambiar:</p>
                            <ul>
                                <li><strong>Nombre del Servicio:</strong> Para mejorar la claridad</li>
                                <li><strong>Categoría:</strong> Para reclasificar el servicio</li>
                                <li><strong>Precio:</strong> Actualizar costos según el mercado</li>
                                <li><strong>Tiempo de Ejecución:</strong> Ajustar según experiencia real</li>
                                <li><strong>Descripción:</strong> Mejorar la información para clientes</li>
                                <li><strong>Materiales Incluidos:</strong> Actualizar lista de materiales</li>
                                <li><strong>Garantía:</strong> Modificar términos de garantía</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Guardar cambios</h3>
                            <p>Haz clic en "Actualizar Servicio" para guardar las modificaciones.</p>
                        </div>
                    </div>
                    
                    <p><strong>Nota:</strong> Los cambios en el precio y descripción afectarán automáticamente las cotizaciones futuras que incluyan este servicio.</p>
                </section>

                <section id="precios-tiempos" class="help-section">
                    <h2><i class="fas fa-dollar-sign"></i>Gestión de Precios y Tiempos</h2>
                    <p>La configuración adecuada de precios y tiempos es crucial para la rentabilidad y satisfacción del cliente:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Estructura de Precios</h3>
                            <p>Considera estos factores al establecer precios:</p>
                            <ul>
                                <li><strong>Costos de Materiales:</strong> Incluye el costo real de los materiales</li>
                                <li><strong>Mano de Obra:</strong> Tiempo del técnico especializado</li>
                                <li><strong>Gastos Generales:</strong> Costos indirectos del taller</li>
                                <li><strong>Margen de Ganancia:</strong> Porcentaje de utilidad</li>
                                <li><strong>Competencia:</strong> Precios del mercado local</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Tiempos de Ejecución</h3>
                            <p>Establece tiempos realistas considerando:</p>
                            <ul>
                                <li><strong>Experiencia Histórica:</strong> Tiempos reales de trabajos anteriores</li>
                                <li><strong>Complejidad:</strong> Dificultad técnica del servicio</li>
                                <li><strong>Disponibilidad de Materiales:</strong> Tiempos de entrega</li>
                                <li><strong>Capacidad del Taller:</strong> Recursos humanos disponibles</li>
                            </ul>
                        </div>
                    </div>
                    
                    <table class="info-table">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th>Precio Base</th>
                                <th>Tiempo Estimado</th>
                                <th>Factores que Afectan el Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Tapizado Completo</td>
                                <td>$500.000 - $1.500.000</td>
                                <td>2-3 días</td>
                                <td>Material seleccionado, tipo de vehículo, complejidad del diseño</td>
                            </tr>
                            <tr>
                                <td>Reparación de Asientos</td>
                                <td>$100.000 - $300.000</td>
                                <td>1-2 días</td>
                                <td>Extensión del daño, tipo de material, necesidad de refuerzos estructurales</td>
                            </tr>
                            <tr>
                                <td>Tratamiento de Cuero</td>
                                <td>$100.000 - $200.000</td>
                                <td>1 día</td>
                                <td>Condición actual del cuero, productos utilizados, tamaño del vehículo</td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                <section id="categorias" class="help-section">
                    <h2><i class="fas fa-tags"></i>Gestión de Categorías</h2>
                    <p>Las categorías ayudan a organizar y clasificar los servicios:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Categorías Principales</h3>
                            <p>Las categorías predefinidas incluyen:</p>
                            <ul>
                                <li><strong>Tapizado Completo:</strong> Servicios de reemplazo total</li>
                                <li><strong>Reparaciones:</strong> Servicios correctivos y de mantenimiento</li>
                                <li><strong>Tratamientos Especiales:</strong> Cuidado y protección de materiales</li>
                                <li><strong>Personalización:</strong> Servicios de modificación estética</li>
                                <li><strong>Instalación de Accesorios:</strong> Adición de complementos</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Beneficios de la Categorización</h3>
                            <ul>
                                <li>Navegación más intuitiva para los clientes</li>
                                <li>Organización eficiente del catálogo</li>
                                <li>Reportes de ventas por categoría</li>
                                <li>Identificación de servicios más populares</li>
                                <li>Mejor planificación de recursos por tipo de servicio</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section id="integracion" class="help-section">
                    <h2><i class="fas fa-link"></i>Integración con Cotizaciones</h2>
                    <p>Los servicios están completamente integrados con el módulo de cotizaciones:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Selección en Cotizaciones</h3>
                            <p>Al crear una cotización:</p>
                            <ul>
                                <li>Los servicios aparecen en menús desplegables organizados por categoría</li>
                                <li>Los precios se cargan automáticamente</li>
                                <li>Las descripciones ayudan al cliente a entender cada servicio</li>
                                <li>Los tiempos de ejecución se suman para el cálculo total</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Actualizaciones Automáticas</h3>
                            <p>Cuando modificas un servicio:</p>
                            <ul>
                                <li>Los cambios en precios afectan las nuevas cotizaciones</li>
                                <li>Las cotizaciones existentes mantienen los precios originales</li>
                                <li>Las descripciones actualizadas se reflejan inmediatamente</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Reportes y Análisis</h3>
                            <p>El sistema permite:</p>
                            <ul>
                                <li>Identificar servicios más solicitados</li>
                                <li>Analizar rentabilidad por servicio</li>
                                <li>Optimizar el catálogo según demanda</li>
                                <li>Planificar promociones estratégicas</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section id="papelera" class="help-section">
                    <h2><i class="fas fa-trash"></i>Gestión de Papelera</h2>
                    <p>La papelera almacena temporalmente los servicios eliminados:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Mover a papelera</h3>
                            <p>Desde el menú de opciones de un servicio, haz clic en "Mover a papelera".</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Ver papelera</h3>
                            <p>Haz clic en la pestaña "Papelera" para ver todos los servicios eliminados.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Restaurar o eliminar</h3>
                            <p>Desde la papelera puedes:</p>
                            <ul>
                                <li><strong>Restaurar:</strong> Volver el servicio a su estado activo</li>
                                <li><strong>Eliminar permanentemente:</strong> Borrar definitivamente el servicio</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (307).png" alt="Servicio movido a papelera">
                        <div class="image-caption">Figura 5: Confirmación de servicio movido a papelera</div>
                    </div>
                    
                    <p><strong>Importante:</strong> Los servicios en la papelera pueden ser restaurados dentro de los 30 días. No se pueden eliminar servicios que estén siendo utilizados en cotizaciones o trabajos activos.</p>
                </section>

                <section id="problemas-comunes" class="help-section">
                    <h2><i class="fas fa-tools"></i>Solución de Problemas Comunes</h2>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No puedo encontrar un servicio</h3>
                    <ul>
                        <li>Verifica que estés escribiendo correctamente el nombre</li>
                        <li>Comprueba si el servicio ha sido movido a la papelera</li>
                        <li>Intenta buscar por categoría o palabras clave</li>
                        <li>Verifica los filtros aplicados en la búsqueda</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>Error al guardar un servicio</h3>
                    <ul>
                        <li>Asegúrate de que todos los campos obligatorios estén completos</li>
                        <li>Verifica que el precio sea un número válido</li>
                        <li>Comprueba que el tiempo de ejecución sea un número positivo</li>
                        <li>Confirma que no exista ya un servicio con el mismo nombre</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>El servicio no aparece en cotizaciones</h3>
                    <ul>
                        <li>Verifica que el servicio esté en estado "Activo"</li>
                        <li>Comprueba que no esté en la papelera</li>
                        <li>Actualiza la página de cotizaciones</li>
                        <li>Contacta al administrador si el problema persiste</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No puedo eliminar un servicio</h3>
                    <ul>
                        <li>Verifica que el servicio no esté siendo usado en cotizaciones activas</li>
                        <li>Comprueba que no esté asociado a trabajos en proceso</li>
                        <li>Si está en uso, espera a que se completen las cotizaciones o cambia el servicio</li>
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