<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda - Gestión de Cotizaciones | Nacional Tapizados</title>
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
            <h1 class="page-title"><i class="fas fa-file-invoice-dollar"></i>Ayuda - Gestión de Cotizaciones</h1>
            <div>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>Volver
                </a>
                <a href="../cotizaciones/index.php" class="btn btn-primary">
                    <i class="fas fa-file-invoice"></i>Ir a Cotizaciones
                </a>
            </div>
        </div>

        <div class="help-content">
            <nav class="help-nav">
                <ul>
                    <li><a href="#introduccion" class="active">Introducción</a></li>
                    <li><a href="#vista-general">Vista General</a></li>
                    <li><a href="#buscar-cotizaciones">Buscar Cotizaciones</a></li>
                    <li><a href="#nueva-cotizacion">Crear Cotización</a></li>
                    <li><a href="#ver-detalles">Ver Detalles</a></li>
                    <li><a href="#editar-cotizacion">Editar Cotización</a></li>
                    <li><a href="#pdf-imprimir">PDF e Imprimir</a></li>
                    <li><a href="#crear-trabajo">Crear Trabajo</a></li>
                    <li><a href="#papelera">Papelera</a></li>
                    <li><a href="#problemas-comunes">Problemas Comunes</a></li>
                </ul>
            </nav>

            <div class="help-sections">
                <section id="introduccion" class="help-section">
                    <h2><i class="fas fa-info-circle"></i>Introducción a la Gestión de Cotizaciones</h2>
                    <p>El módulo de Gestión de Cotizaciones te permite crear, administrar y dar seguimiento a todas las cotizaciones de servicios de tapicería automotriz. Desde aquí podrás generar propuestas detalladas para los clientes, incluyendo servicios, costos y cálculos automáticos.</p>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/cotizaciones/inicio.PNG">
                        <div class="image-caption"></div>
                    </div>
                    
                    <p>Las cotizaciones están integradas con los módulos de clientes y vehículos, permitiendo crear propuestas personalizadas para cada cliente y su vehículo específico.</p>
                </section>

                <section id="vista-general" class="help-section">
                    <h2><i class="fas fa-chart-bar"></i>Vista General y Estadísticas</h2>
                    <p>Al acceder al módulo de cotizaciones, verás un resumen con las principales estadísticas:</p>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <h4><i class="fas fa-file-invoice"></i>Total de Cotizaciones</h4>
                            <p>Muestra el número total de cotizaciones registradas en el sistema.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-calendar-plus"></i>Última Cotización</h4>
                            <p>Indica la última cotización creada con información del cliente y vehículo.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-calendar-day"></i>Cotizaciones de Hoy</h4>
                            <p>Muestra cuántas cotizaciones se han creado en el día actual.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-trash"></i>En Papelera</h4>
                            <p>Indica cuántas cotizaciones han sido movidas a la papelera.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/cotizaciones/estadisticas.PNG">
                        <div class="image-caption"></div>
                    </div>
                </section>

                <section id="buscar-cotizaciones" class="help-section">
                    <h2><i class="fas fa-search"></i>Buscar Cotizaciones</h2>
                    <p>Para encontrar rápidamente una cotización específica, utiliza la función de búsqueda:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a la búsqueda</h3>
                            <p>Localiza el campo de búsqueda en la parte superior de la lista de cotizaciones.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Ingresar criterios</h3>
                            <p>Escribe el nombre del cliente, placa del vehículo o ID de cotización.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Filtrar resultados</h3>
                            <p>Utiliza las pestañas "Recientes" y "Papelera" para filtrar los resultados.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/cotizaciones/filtro.PNG">
                        <div class="image-caption"></div>
                    </div>
                </section>

                <section id="nueva-cotizacion" class="help-section">
                    <h2><i class="fas fa-plus-circle"></i>Crear Nueva Cotización</h2>
                    <p>Para crear una nueva cotización en el sistema, sigue estos pasos:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder al formulario</h3>
                            <p>Haz clic en el botón "Nueva Cotización" en la parte superior de la pantalla.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Seleccionar cliente</h3>
                            <p>Elige el cliente del menú desplegable. El sistema cargará automáticamente sus vehículos.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Seleccionar vehículo</h3>
                            <p>Una vez seleccionado el cliente, elige el vehículo específico para la cotización.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Agregar servicios</h3>
                            <p>Selecciona los servicios de tapicería a incluir en la cotización:</p>
                            <ul>
                                <li>Selecciona cada servicio del menú desplegable</li>
                                <li>El sistema mostrará el precio automáticamente</li>
                                <li>Usa el botón "+ Agregar" para añadir más servicios</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">5</div>
                        <div class="step-content">
                            <h3>Revisar cálculos automáticos</h3>
                            <p>El sistema calcula automáticamente:</p>
                            <ul>
                                <li><strong>Subtotal:</strong> Suma de todos los servicios</li>
                                <li><strong>IVA (19%):</strong> Impuesto calculado sobre el subtotal</li>
                                <li><strong>Valor Adicional:</strong> Costos extras manuales</li>
                                <li><strong>Total:</strong> Suma total de la cotización</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">6</div>
                        <div class="step-content">
                            <h3>Agregar notas</h3>
                            <p>Incluye observaciones importantes para el cliente o detalles específicos del trabajo.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">7</div>
                        <div class="step-content">
                            <h3>Guardar cotización</h3>
                            <p>Haz clic en "Crear Cotización" para guardar la propuesta en el sistema.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/cotizaciones/crear.PNG">
                        <div class="image-caption"></div>
                    </div>
                </section>

                <section id="ver-detalles" class="help-section">
                    <h2><i class="fas fa-eye"></i>Ver Detalles de la Cotización</h2>
                    <p>La vista de detalles muestra toda la información completa de una cotización:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a detalles</h3>
                            <p>Haz clic en "Ver Detalles" desde la lista de cotizaciones o en el menú de opciones.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Información general</h3>
                            <p>En la parte superior encontrarás:</p>
                            <ul>
                                <li><strong>Número de cotización:</strong> Identificador único</li>
                                <li><strong>Información del cliente:</strong> Nombre, teléfono, correo</li>
                                <li><strong>Información del vehículo:</strong> Marca, modelo, placa</li>
                                <li><strong>Vendedor:</strong> Persona que creó la cotización</li>
                                <li><strong>Fecha:</strong> Fecha y hora de creación</li>
                                <li><strong>Estado:</strong> Actual estado de la cotización</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Servicios y costos</h3>
                            <p>Tabla detallada con todos los servicios incluidos:</p>
                            <ul>
                                <li>Lista de servicios con sus precios individuales</li>
                                <li>Cálculo de subtotal de servicios</li>
                                <li>IVA aplicado (19%)</li>
                                <li>Valores adicionales si los hay</li>
                                <li>Total final de la cotización</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Notas adicionales</h3>
                            <p>Observaciones específicas sobre la cotización o condiciones especiales.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/cotizaciones/det1.PNG">>
                        <div class="image-caption"></div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/cotizaciones/det2.PNG">
                        <div class="image-caption"></div>
                    </div>
                </section>

                <section id="editar-cotizacion" class="help-section">
                    <h2><i class="fas fa-edit"></i>Editar Cotización</h2>
                    <p>Para modificar una cotización existente:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a edición</h3>
                            <p>Desde la vista de detalles de la cotización, haz clic en "Editar".</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Modificar información</h3>
                            <p>Puedes actualizar:</p>
                            <ul>
                                <li>Cliente y vehículo (si es necesario)</li>
                                <li>Lista de servicios y sus precios</li>
                                <li>Valores adicionales</li>
                                <li>Notas para el cliente</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Guardar cambios</h3>
                            <p>Haz clic en "Actualizar Cotización" para guardar los cambios.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/cotizaciones/editar.PNG">
                        <div class="image-caption"></div>
                    </div>
                </section>

                <section id="pdf-imprimir" class="help-section">
                    <h2><i class="fas fa-file-pdf"></i>Generar PDF e Imprimir</h2>
                    <p>Desde la vista de detalles de una cotización, puedes generar documentos profesionales:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a funciones de exportación</h3>
                            <p>En la vista de detalles, busca los botones de acción:</p>
                            <div class="action-buttons">
                                <a href="#" class="action-button">
                                    <i class="fas fa-file-pdf"></i>Generar PDF
                                </a>
                                <a href="#" class="action-button">
                                    <i class="fas fa-print"></i>Imprimir
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Generar PDF</h3>
                            <p>Al hacer clic en "Generar PDF", el sistema crea un documento profesional que incluye:</p>
                            <ul>
                                <li>Logo e información de Nacional Tapizados</li>
                                <li>Número y fecha de la cotización</li>
                                <li>Información completa del cliente y vehículo</li>
                                <li>Detalle de servicios con precios</li>
                                <li>Cálculos de subtotal, IVA y total</li>
                                <li>Notas adicionales</li>
                                <li>Información de contacto de la empresa</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Imprimir cotización</h3>
                            <p>La función de impresión genera una versión optimizada para impresión con el mismo formato profesional del PDF.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/cotizaciones/pdf.PNG">
                        <div class="image-caption"></div>
                    </div>
                    
                    <p><strong>Nota:</strong> Tanto el PDF como la versión imprimible mantienen el branding profesional de Nacional Tapizados y son ideales para enviar a clientes o archivar.</p>
                </section>

                <section id="crear-trabajo" class="help-section">
                    <h2><i class="fas fa-wrench"></i>Crear Trabajo desde Cotización</h2>
                    <p>Cuando un cliente aprueba una cotización, puedes convertirla directamente en un trabajo:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a la función</h3>
                            <p>Desde la vista de detalles de una cotización aprobada, busca el botón:</p>
                            <div class="action-buttons">
                                <a href="#" class="action-button">
                                    <i class="fas fa-wrench"></i>Crear Trabajo
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Confirmar creación</h3>
                            <p>El sistema te redirigirá al formulario de creación de trabajo con toda la información de la cotización pre-cargada:</p>
                            <ul>
                                <li>Cliente y vehículo automáticamente seleccionados</li>
                                <li>Servicios de la cotización incluidos</li>
                                <li>Precios y cálculos transferidos</li>
                                <li>Notas adicionales conservadas</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Completar información del trabajo</h3>
                            <p>Agrega información específica del trabajo:</p>
                            <ul>
                                <li>Fecha de inicio programada</li>
                                <li>Técnico asignado</li>
                                <li>Plazo estimado de entrega</li>
                                <li>Observaciones adicionales para el taller</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Guardar trabajo</h3>
                            <p>Haz clic en "Crear Trabajo" para iniciar el proceso de producción.</p>
                        </div>
                    </div>
                    
                    <p><strong>Beneficio:</strong> Esta función ahorra tiempo y evita errores al transferir automáticamente toda la información de la cotización aprobada al módulo de trabajos.</p>
                </section>

                <section id="papelera" class="help-section">
                    <h2><i class="fas fa-trash"></i>Gestión de Papelera</h2>
                    <p>La papelera almacena temporalmente las cotizaciones eliminadas:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Mover a papelera</h3>
                            <p>Desde el menú de opciones de una cotización, haz clic en "Mover a papelera".</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Ver papelera</h3>
                            <p>Haz clic en la pestaña "Papelera" para ver todas las cotizaciones eliminadas.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Restaurar o eliminar</h3>
                            <p>Desde la papelera puedes restaurar cotizaciones o eliminarlas permanentemente.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/cotizaciones/paper.PNG">
                        <div class="image-caption"></div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/cotizaciones/paperv.PNG">
                        <div class="image-caption">   </div>
                    </div>
                    
                    <p><strong>Importante:</strong> Las cotizaciones en la papelera pueden ser restauradas dentro de los 30 días. Después de este período, se eliminarán automáticamente del sistema.</p>
                </section>

                <section id="problemas-comunes" class="help-section">
                    <h2><i class="fas fa-tools"></i>Solución de Problemas Comunes</h2>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No puedo encontrar una cotización</h3>
                    <ul>
                        <li>Verifica que estés escribiendo correctamente el nombre del cliente o placa.</li>
                        <li>Comprueba si la cotización ha sido movida a la papelera.</li>
                        <li>Intenta buscar por partes del nombre o por ID de cotización.</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>Error al guardar una cotización</h3>
                    <ul>
                        <li>Asegúrate de que todos los campos obligatorios estén completos.</li>
                        <li>Verifica que hayas seleccionado al menos un servicio.</li>
                        <li>Comprueba que los precios de servicios sean válidos (números positivos).</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No aparecen vehículos al seleccionar cliente</h3>
                    <ul>
                        <li>Verifica que el cliente seleccionado tenga vehículos registrados.</li>
                        <li>Comprueba que los vehículos no estén en estado inactivo.</li>
                        <li>Si el cliente no tiene vehículos, regístralos primero en el módulo de vehículos.</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>Problemas al generar PDF</h3>
                    <ul>
                        <li>Verifica que tu navegador permita ventanas emergentes.</li>
                        <li>Asegúrate de tener un lector de PDF instalado.</li>
                        <li>Comprueba tu conexión a internet si el PDF se genera en el servidor.</li>
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