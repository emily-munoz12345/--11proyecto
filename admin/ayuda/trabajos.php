<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda - Gestión de Trabajos | Nacional Tapizados</title>
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

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin: 0.25rem;
        }

        .status-pendiente {
            background-color: var(--warning-color);
            color: #000;
        }

        .status-proceso {
            background-color: var(--info-color);
            color: #000;
        }

        .status-completado {
            background-color: var(--success-color);
            color: white;
        }

        .status-cancelado {
            background-color: var(--danger-color);
            color: white;
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
        }
    </style>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="main-container">
        <div class="header-section">
            <h1 class="page-title"><i class="fas fa-tools"></i>Ayuda - Gestión de Trabajos</h1>
            <div>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>Volver
                </a>
                <a href="../formularios/trabajos/index.php" class="btn btn-primary">
                    <i class="fas fa-tools"></i>Ir a Trabajos
                </a>
            </div>
        </div>

        <div class="help-content">
            <nav class="help-nav">
                <ul>
                    <li><a href="#introduccion" class="active">Introducción</a></li>
                    <li><a href="#vista-general">Vista General</a></li>
                    <li><a href="#estados-trabajos">Estados de Trabajos</a></li>
                    <li><a href="#buscar-trabajos">Buscar Trabajos</a></li>
                    <li><a href="#nuevo-trabajo">Crear Trabajo</a></li>
                    <li><a href="#relacion-cotizaciones">Relación con Cotizaciones</a></li>
                    <li><a href="#ver-detalles">Ver Detalles</a></li>
                    <li><a href="#gestion-imagenes">Gestión de Imágenes</a></li>
                    <li><a href="#editar-trabajo">Editar Trabajo</a></li>
                    <li><a href="#cambiar-estado">Cambiar Estado</a></li>
                    <li><a href="#papelera">Papelera</a></li>
                    <li><a href="#problemas-comunes">Problemas Comunes</a></li>
                </ul>
            </nav>

            <div class="help-sections">
                <section id="introduccion" class="help-section">
                    <h2><i class="fas fa-info-circle"></i>Introducción a la Gestión de Trabajos</h2>
                    <p>El módulo de Gestión de Trabajos te permite administrar todo el proceso de producción de tapicería automotriz, desde la creación del trabajo hasta su finalización. Este módulo está completamente integrado con el sistema de cotizaciones, clientes y vehículos.</p>
                    
                    <p><strong>Características principales:</strong></p>
                    <ul>
                        <li>Creación de trabajos a partir de cotizaciones aprobadas</li>
                        <li>Seguimiento del estado del trabajo en tiempo real</li>
                        <li>Gestión de imágenes del antes, durante y después</li>
                        <li>Asignación de técnicos y fechas de entrega</li>
                        <li>Cálculo automático de costos y materiales</li>
                    </ul>
                </section>

                <section id="vista-general" class="help-section">
                    <h2><i class="fas fa-chart-bar"></i>Vista General y Estadísticas</h2>
                    <p>Al acceder al módulo de trabajos, verás un resumen con las principales estadísticas:</p>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <h4><i class="fas fa-tools"></i>Total de Trabajos</h4>
                            <p>Muestra el número total de trabajos registrados en el sistema.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-calendar-plus"></i>Último Trabajo</h4>
                            <p>Indica el último trabajo creado con información del cliente y vehículo.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-calendar-day"></i>Trabajos de Hoy</h4>
                            <p>Muestra cuántos trabajos se han creado en el día actual.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-trash"></i>En Papelera</h4>
                            <p>Indica cuántos trabajos han sido movidos a la papelera.</p>
                        </div>
                    </div>
                    
                    <p>Además, el sistema muestra una lista de todos los trabajos activos con información clave como cliente, vehículo, estado y fechas importantes.</p>
                </section>

                <section id="estados-trabajos" class="help-section">
                    <h2><i class="fas fa-tasks"></i>Estados de los Trabajos</h2>
                    <p>Los trabajos pueden tener los siguientes estados durante su ciclo de vida:</p>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <h4><span class="status-badge status-pendiente">Pendiente</span></h4>
                            <p>El trabajo ha sido creado pero aún no ha iniciado su ejecución. Está esperando a ser asignado a un técnico o a que comience la producción.</p>
                        </div>
                        <div class="feature-card">
                            <h4><span class="status-badge status-proceso">En Proceso</span></h4>
                            <p>El trabajo está actualmente en ejecución. El técnico asignado está realizando las labores de tapicería.</p>
                        </div>
                        <div class="feature-card">
                            <h4><span class="status-badge status-completado">Completado</span></h4>
                            <p>El trabajo ha sido finalizado exitosamente y está listo para ser entregado al cliente.</p>
                        </div>
                        <div class="feature-card">
                            <h4><span class="status-badge status-cancelado">Cancelado</span></h4>
                            <p>El trabajo ha sido cancelado por alguna razón específica (cliente desistió, problemas técnicos, etc.).</p>
                        </div>
                    </div>
                    
                    <p><strong>Nota:</strong> El sistema permite cambiar el estado del trabajo en cualquier momento para reflejar su progreso real.</p>
                </section>

                <section id="buscar-trabajos" class="help-section">
                    <h2><i class="fas fa-search"></i>Buscar Trabajos</h2>
                    <p>Para encontrar rápidamente un trabajo específico, utiliza la función de búsqueda:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a la búsqueda</h3>
                            <p>Localiza el campo de búsqueda en la parte superior de la lista de trabajos.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Ingresar criterios</h3>
                            <p>Puedes buscar por:</p>
                            <ul>
                                <li>Nombre del cliente</li>
                                <li>Placa del vehículo</li>
                                <li>ID del trabajo</li>
                                <li>Marca o modelo del vehículo</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Filtrar resultados</h3>
                            <p>Utiliza las pestañas disponibles para filtrar por:</p>
                            <ul>
                                <li><strong>Recientes:</strong> Trabajos creados recientemente</li>
                                <li><strong>Por Estado:</strong> Filtra por estado específico (Pendiente, En Proceso, etc.)</li>
                                <li><strong>Papelera:</strong> Trabajos eliminados temporalmente</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section id="nuevo-trabajo" class="help-section">
                    <h2><i class="fas fa-plus-circle"></i>Crear Nuevo Trabajo</h2>
                    <p>Existen dos formas principales de crear un nuevo trabajo:</p>
                    
                    <h3><i class="fas fa-file-invoice"></i>Desde una Cotización Aprobada</h3>
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a cotizaciones</h3>
                            <p>Navega al módulo de cotizaciones y localiza una cotización con estado "Aprobada".</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Crear trabajo</h3>
                            <p>Haz clic en el botón "Crear Trabajo" en la vista de detalles de la cotización.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Información precargada</h3>
                            <p>El sistema transferirá automáticamente:</p>
                            <ul>
                                <li>Información del cliente y vehículo</li>
                                <li>Servicios a realizar</li>
                                <li>Precios y cálculos</li>
                                <li>Notas adicionales</li>
                            </ul>
                        </div>
                    </div>
                    
                    <h3><i class="fas fa-plus"></i>Creación Manual</h3>
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder al formulario</h3>
                            <p>Haz clic en el botón "Nuevo Trabajo" en el módulo de trabajos.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Seleccionar cliente y vehículo</h3>
                            <p>Elige el cliente y su vehículo de los menús desplegables.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Definir servicios</h3>
                            <p>Agrega los servicios de tapicería a realizar manualmente.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Configurar fechas y técnico</h3>
                            <p>Establece fechas importantes y asigna un técnico responsable.</p>
                        </div>
                    </div>
                </section>

                <section id="relacion-cotizaciones" class="help-section">
                    <h2><i class="fas fa-link"></i>Relación con Cotizaciones</h2>
                    <p>El sistema mantiene una relación estrecha entre cotizaciones y trabajos:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Cambio Automático de Estado</h3>
                            <p><strong>Cuando creas un trabajo desde una cotización:</strong></p>
                            <ul>
                                <li>La cotización cambia automáticamente su estado a "En Proceso"</li>
                                <li>Se establece un vínculo permanente entre la cotización y el trabajo</li>
                                <li>El cliente no puede modificar la cotización una vez convertida en trabajo</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Sin Cotizaciones Disponibles</h3>
                            <p><strong>Si no hay cotizaciones aprobadas disponibles:</strong></p>
                            <ul>
                                <li>El sistema mostrará un mensaje indicando que no hay cotizaciones libres</li>
                                <li>Aparecerá la opción "Ir a Crear Cotización" para redirigirte al módulo correspondiente</li>
                                <li>Podrás crear el trabajo manualmente si es necesario</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Seguimiento Integrado</h3>
                            <p>Desde la vista de una cotización puedes ver si ya tiene un trabajo asociado y acceder directamente a él.</p>
                        </div>
                    </div>
                    
                    <p><strong>Beneficio:</strong> Esta integración evita duplicación de información y mantiene un flujo coherente desde la cotización hasta la finalización del trabajo.</p>
                </section>

                <section id="ver-detalles" class="help-section">
                    <h2><i class="fas fa-eye"></i>Ver Detalles del Trabajo</h2>
                    <p>La vista de detalles muestra información completa sobre un trabajo:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Información General</h3>
                            <ul>
                                <li><strong>ID del Trabajo:</strong> Identificador único</li>
                                <li><strong>Cliente y Vehículo:</strong> Información completa</li>
                                <li><strong>Estado Actual:</strong> Estado actual del trabajo</li>
                                <li><strong>Técnico Asignado:</strong> Persona responsable</li>
                                <li><strong>Fechas:</strong> Creación, inicio, fin estimado y real</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Servicios y Costos</h3>
                            <ul>
                                <li>Lista detallada de servicios a realizar</li>
                                <li>Precios individuales y totales</li>
                                <li>Materiales utilizados</li>
                                <li>Costos adicionales</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Galería de Imágenes</h3>
                            <p>Sección especial para gestionar imágenes del trabajo:</p>
                            <ul>
                                <li>Imágenes del estado inicial (antes)</li>
                                <li>Fotos del proceso (durante)</li>
                                <li>Imágenes del resultado final (después)</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Notas y Observaciones</h3>
                            <p>Comentarios del técnico, observaciones especiales y detalles relevantes para el trabajo.</p>
                        </div>
                    </div>
                </section>

                <section id="gestion-imagenes" class="help-section">
                    <h2><i class="fas fa-images"></i>Gestión de Imágenes del Trabajo</h2>
                    <p>Una de las características más importantes del módulo de trabajos es la capacidad de gestionar imágenes:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Agregar Imágenes</h3>
                            <p>Desde la vista de detalles del trabajo:</p>
                            <ul>
                                <li>Haz clic en "Agregar Imágenes"</li>
                                <li>Selecciona las imágenes desde tu dispositivo</li>
                                <li>Clasifícalas por tipo (Antes, Durante, Después)</li>
                                <li>Agrega descripciones opcionales</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Tipos de Imágenes</h3>
                            <p><strong>Antes:</strong> Estado inicial del vehículo antes de cualquier trabajo</p>
                            <p><strong>Durante:</strong> Proceso de tapicería en ejecución</p>
                            <p><strong>Después:</strong> Resultado final del trabajo completado</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Visualizar Imágenes</h3>
                            <ul>
                                <li>Galería organizada por categorías</li>
                                <li>Vista en miniatura con opción de ampliación</li>
                                <li>Navegación entre imágenes</li>
                                <li>Descarga de imágenes individuales</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Gestionar Imágenes</h3>
                            <ul>
                                <li>Eliminar imágenes no deseadas</li>
                                <li>Reorganizar el orden de visualización</li>
                                <li>Actualizar descripciones</li>
                                <li>Cambiar categoría de las imágenes</li>
                            </ul>
                        </div>
                    </div>
                    
                    <p><strong>Importante:</strong> Las imágenes son fundamentales para documentar el trabajo, mostrar el progreso al cliente y como evidencia del antes y después.</p>
                </section>

                <section id="editar-trabajo" class="help-section">
                    <h2><i class="fas fa-edit"></i>Editar Información del Trabajo</h2>
                    <p>Para modificar la información de un trabajo existente:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a edición</h3>
                            <p>Desde la vista de detalles del trabajo, haz clic en "Editar Trabajo".</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Campos editables</h3>
                            <p>Puedes modificar:</p>
                            <ul>
                                <li>Información del técnico asignado</li>
                                <li>Fechas estimadas de finalización</li>
                                <li>Servicios adicionales</li>
                                <li>Costos y materiales</li>
                                <li>Notas y observaciones</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Guardar cambios</h3>
                            <p>Haz clic en "Actualizar Trabajo" para guardar las modificaciones.</p>
                        </div>
                    </div>
                    
                    <p><strong>Nota:</strong> Algunos campos pueden tener restricciones de edición dependiendo del estado actual del trabajo.</p>
                </section>

                <section id="cambiar-estado" class="help-section">
                    <h2><i class="fas fa-sync-alt"></i>Cambiar Estado del Trabajo</h2>
                    <p>Actualizar el estado del trabajo es fundamental para el seguimiento:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder al cambio de estado</h3>
                            <p>Desde la vista de detalles, busca la sección de estado y haz clic en "Cambiar Estado".</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Seleccionar nuevo estado</h3>
                            <p>Elige el estado apropiado según el progreso real del trabajo.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Agregar comentario (opcional)</h3>
                            <p>Puedes incluir un comentario explicando el cambio de estado.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Confirmar cambio</h3>
                            <p>Haz clic en "Actualizar Estado" para confirmar el cambio.</p>
                        </div>
                    </div>
                    
                    <p><strong>Flujo recomendado:</strong> Pendiente → En Proceso → Completado</p>
                </section>

                <section id="papelera" class="help-section">
                    <h2><i class="fas fa-trash"></i>Gestión de Papelera</h2>
                    <p>La papelera almacena temporalmente los trabajos eliminados:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Mover a papelera</h3>
                            <p>Desde el menú de opciones de un trabajo, haz clic en "Mover a papelera".</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Ver papelera</h3>
                            <p>Haz clic en la pestaña "Papelera" para ver todos los trabajos eliminados.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Restaurar o eliminar</h3>
                            <p>Desde la papelera puedes:</p>
                            <ul>
                                <li><strong>Restaurar:</strong> Volver el trabajo a su estado activo</li>
                                <li><strong>Eliminar permanentemente:</strong> Borrar definitivamente el trabajo</li>
                            </ul>
                        </div>
                    </div>
                    
                    <p><strong>Importante:</strong> Los trabajos en la papelera pueden ser restaurados dentro de los 30 días. Después de este período, se eliminarán automáticamente del sistema.</p>
                </section>

                <section id="problemas-comunes" class="help-section">
                    <h2><i class="fas fa-tools"></i>Solución de Problemas Comunes</h2>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No puedo crear trabajo desde cotización</h3>
                    <ul>
                        <li>Verifica que la cotización esté en estado "Aprobada"</li>
                        <li>Comprueba que no exista ya un trabajo asociado a esa cotización</li>
                        <li>Asegúrate de tener permisos para crear trabajos</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No aparecen cotizaciones disponibles</h3>
                    <ul>
                        <li>Verifica que existan cotizaciones con estado "Aprobada"</li>
                        <li>Comprueba que las cotizaciones no estén ya asociadas a trabajos</li>
                        <li>Si no hay cotizaciones, ve al módulo de cotizaciones para crear una nueva</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>Error al subir imágenes</h3>
                    <ul>
                        <li>Verifica que los archivos sean formatos de imagen válidos (JPG, PNG, etc.)</li>
                        <li>Comprueba que el tamaño de cada imagen no exceda el límite permitido</li>
                        <li>Asegúrate de tener conexión a internet estable</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No puedo cambiar el estado del trabajo</h3>
                    <ul>
                        <li>Verifica que tengas permisos para modificar estados</li>
                        <li>Comprueba que el trabajo no esté en un estado final (Completado/Cancelado)</li>
                        <li>Contacta al administrador si necesitas permisos adicionales</li>
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