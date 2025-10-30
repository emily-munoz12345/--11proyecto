<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda - Gestión de Materiales | Nacional Tapizados</title>
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
            <h1 class="page-title"><i class="fas fa-roll"></i>Ayuda - Gestión de Materiales</h1>
            <div>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>Volver
                </a>
                <a href="../materiales/index.php" class="btn btn-primary">
                    <i class="fas fa-roll"></i>Ir a Materiales
                </a>
            </div>
        </div>

        <div class="help-content">
            <nav class="help-nav">
                <ul>
                    <li><a href="#introduccion" class="active">Introducción</a></li>
                    <li><a href="#vista-general">Vista General</a></li>
                    <li><a href="#tipos-materiales">Tipos de Materiales</a></li>
                    <li><a href="#buscar-materiales">Buscar Materiales</a></li>
                    <li><a href="#nuevo-material">Crear Material</a></li>
                    <li><a href="#ver-detalles">Ver Detalles</a></li>
                    <li><a href="#editar-material">Editar Material</a></li>
                    <li><a href="#gestion-stock">Gestión de Stock</a></li>
                    <li><a href="#categorias">Categorías</a></li>
                    <li><a href="#proveedores">Proveedores</a></li>
                    <li><a href="#papelera">Papelera</a></li>
                    <li><a href="#problemas-comunes">Problemas Comunes</a></li>
                </ul>
            </nav>

            <div class="help-sections">
                <section id="introduccion" class="help-section">
                    <h2><i class="fas fa-info-circle"></i>Introducción a la Gestión de Materiales</h2>
                    <p>El módulo de Gestión de Materiales te permite administrar el inventario de todos los materiales de tapicería utilizados en Nacional Tapizados. Desde aquí podrás registrar nuevos materiales, controlar el stock disponible, gestionar precios y categorizar los diferentes tipos de materiales.</p>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (297).png" alt="Vista general de gestión de materiales">
                        <div class="image-caption">Figura 1: Vista general del módulo de Gestión de Materiales</div>
                    </div>
                    
                    <p><strong>Características principales:</strong></p>
                    <ul>
                        <li>Registro completo de materiales con precios y stock</li>
                        <li>Gestión de categorías y proveedores</li>
                        <li>Control de inventario en tiempo real</li>
                        <li>Integración con el módulo de cotizaciones y trabajos</li>
                        <li>Seguimiento de movimientos de stock</li>
                    </ul>
                </section>

                <section id="vista-general" class="help-section">
                    <h2><i class="fas fa-chart-bar"></i>Vista General y Estadísticas</h2>
                    <p>Al acceder al módulo de materiales, verás un resumen con las principales estadísticas:</p>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <h4><i class="fas fa-roll"></i>Total de Materiales</h4>
                            <p>Muestra el número total de materiales registrados en el sistema.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-calendar-plus"></i>Último Registro</h4>
                            <p>Indica la fecha del último material agregado al sistema.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-calendar-day"></i>Registros Hoy</h4>
                            <p>Muestra cuántos materiales se han registrado en el día actual.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-trash"></i>En Papelera</h4>
                            <p>Indica cuántos materiales han sido movidos a la papelera.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (301).png" alt="Estadísticas de materiales">
                        <div class="image-caption">Figura 2: Panel de estadísticas de materiales</div>
                    </div>
                    
                    <p>Además, el sistema muestra una lista de todos los materiales disponibles con información clave como nombre, categoría, precio y stock disponible.</p>
                </section>

                <section id="tipos-materiales" class="help-section">
                    <h2><i class="fas fa-tags"></i>Tipos de Materiales</h2>
                    <p>En Nacional Tapizados manejamos diferentes tipos de materiales para tapicería automotriz:</p>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <h4><i class="fas fa-leather"></i>Cuero Natural</h4>
                            <p>Material premium de alta durabilidad y confort. Ideal para vehículos de lujo y trabajos personalizados.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-layer-group"></i>Vinilouero</h4>
                            <p>Opción económica sin sacrificar calidad. Resistente a líquidos y fácil de limpiar.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-car"></i>Alcántara Deportiva</h4>
                            <p>Material técnico que combina las ventajas de la piel con mayor resistencia al desgaste.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-tshirt"></i>Tela Técnica</h4>
                            <p>Materiales modernos con tratamientos especiales para máxima durabilidad y transpirabilidad.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (302).png" alt="Tipos de materiales">
                        <div class="image-caption">Figura 3: Ejemplos de diferentes tipos de materiales</div>
                    </div>
                </section>

                <section id="buscar-materiales" class="help-section">
                    <h2><i class="fas fa-search"></i>Buscar Materiales</h2>
                    <p>Para encontrar rápidamente un material específico, utiliza la función de búsqueda:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a la búsqueda</h3>
                            <p>Localiza el campo de búsqueda en la parte superior de la lista de materiales.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Ingresar criterios</h3>
                            <p>Puedes buscar por:</p>
                            <ul>
                                <li>Nombre del material</li>
                                <li>Categoría</li>
                                <li>Proveedor</li>
                                <li>Descripción</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Filtrar resultados</h3>
                            <p>Utiliza las pestañas disponibles para filtrar por:</p>
                            <ul>
                                <li><strong>Recientes:</strong> Materiales agregados recientemente</li>
                                <li><strong>Por Categoría:</strong> Filtra por categoría específica</li>
                                <li><strong>Papelera:</strong> Materiales eliminados temporalmente</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (298).png" alt="Búsqueda de materiales">
                        <div class="image-caption">Figura 4: Campo de búsqueda y filtros de materiales</div>
                    </div>
                </section>

                <section id="nuevo-material" class="help-section">
                    <h2><i class="fas fa-plus-circle"></i>Crear Nuevo Material</h2>
                    <p>Para registrar un nuevo material en el sistema, sigue estos pasos:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder al formulario</h3>
                            <p>Haz clic en el botón "Nuevo Material" en la parte superior de la pantalla.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Completar información básica</h3>
                            <p>Llena todos los campos obligatorios:</p>
                            <ul>
                                <li><strong>Nombre del Material:</strong> Nombre descriptivo del material</li>
                                <li><strong>Categoría:</strong> Tipo de material (Cuero, Vinilo, Alcántara, etc.)</li>
                                <li><strong>Precio por Metro:</strong> Costo unitario del material</li>
                                <li><strong>Stock Disponible:</strong> Cantidad inicial en metros</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Información adicional</h3>
                            <p>Completa los campos opcionales si es necesario:</p>
                            <ul>
                                <li><strong>Proveedor:</strong> Empresa proveedora del material</li>
                                <li><strong>Descripción:</strong> Características específicas del material</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Guardar material</h3>
                            <p>Haz clic en "Guardar Material" para registrar la información en el sistema.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (303).png" alt="Formulario de nuevo material">
                        <div class="image-caption">Figura 5: Formulario para crear nuevo material</div>
                    </div>
                </section>

                <section id="ver-detalles" class="help-section">
                    <h2><i class="fas fa-eye"></i>Ver Detalles del Material</h2>
                    <p>Para ver la información completa de un material:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a detalles</h3>
                            <p>Haz clic en "Ver Detalles" desde la lista de materiales o en el menú de opciones.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Información completa</h3>
                            <p>Se mostrará una vista detallada con todos los datos del material:</p>
                            <ul>
                                <li><strong>ID del Material:</strong> Identificador único en el sistema</li>
                                <li><strong>Nombre y Descripción:</strong> Información descriptiva completa</li>
                                <li><strong>Precio por Metro:</strong> Costo actual del material</li>
                                <li><strong>Stock Disponible:</strong> Cantidad actual en inventario</li>
                                <li><strong>Categoría:</strong> Tipo de material</li>
                                <li><strong>Proveedor:</strong> Empresa proveedora</li>
                                <li><strong>Fechas:</strong> Registro y última actualización</li>
                                <li><strong>Estado:</strong> Estado actual del material</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (299).png" alt="Detalles del material">
                        <div class="image-caption">Figura 6: Vista de detalles de un material</div>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="#" class="action-button">
                            <i class="fas fa-edit"></i>Editar Material
                        </a>
                        <a href="#" class="action-button">
                            <i class="fas fa-arrow-left"></i>Volver a la Lista
                        </a>
                    </div>
                </section>

                <section id="editar-material" class="help-section">
                    <h2><i class="fas fa-edit"></i>Editar Información del Material</h2>
                    <p>Para modificar la información de un material existente:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Acceder a edición</h3>
                            <p>Desde la vista de detalles del material, haz clic en "Editar Material".</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Modificar información</h3>
                            <p>Actualiza los campos que necesites cambiar:</p>
                            <ul>
                                <li><strong>Nombre del Material:</strong> Si es necesario cambiar la descripción</li>
                                <li><strong>Categoría:</strong> Para reclasificar el material</li>
                                <li><strong>Precio por Metro:</strong> Actualizar costos</li>
                                <li><strong>Stock Disponible:</strong> Ajustar inventario</li>
                                <li><strong>Proveedor:</strong> Cambiar proveedor si es necesario</li>
                                <li><strong>Descripción:</strong> Actualizar características</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Guardar cambios</h3>
                            <p>Haz clic en "Actualizar Material" para guardar las modificaciones.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (300).png" alt="Editar material">
                        <div class="image-caption">Figura 7: Formulario de edición de material</div>
                    </div>
                    
                    <p><strong>Nota:</strong> Los cambios en el precio y stock afectarán automáticamente las cotizaciones y trabajos futuros que utilicen este material.</p>
                </section>

                <section id="gestion-stock" class="help-section">
                    <h2><i class="fas fa-boxes"></i>Gestión de Stock</h2>
                    <p>El control de inventario es fundamental para la gestión eficiente de materiales:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Monitoreo de Stock</h3>
                            <p>El sistema muestra en tiempo real:</p>
                            <ul>
                                <li>Stock disponible actual</li>
                                <li>Stock mínimo configurado</li>
                                <li>Alertas de stock bajo</li>
                                <li>Historial de movimientos</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Actualizar Stock</h3>
                            <p>Puedes actualizar el stock de dos formas:</p>
                            <ul>
                                <li><strong>Manual:</strong> Desde la edición del material</li>
                                <li><strong>Automática:</strong> Cuando se utilizan materiales en trabajos</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Alertas de Stock Bajo</h3>
                            <p>El sistema genera alertas automáticas cuando:</p>
                            <ul>
                                <li>El stock disponible está por debajo del mínimo</li>
                                <li>Un material se agota completamente</li>
                                <li>Hay materiales que no se han usado en mucho tiempo</li>
                            </ul>
                        </div>
                    </div>
                    
                    <table class="info-table">
                        <thead>
                            <tr>
                                <th>Estado de Stock</th>
                                <th>Indicador</th>
                                <th>Acción Recomendada</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Stock Normal</td>
                                <td><i class="fas fa-check-circle" style="color: var(--success-color);"></i> Verde</td>
                                <td>Continuar con operaciones normales</td>
                            </tr>
                            <tr>
                                <td>Stock Bajo</td>
                                <td><i class="fas fa-exclamation-triangle" style="color: var(--warning-color);"></i> Amarillo</td>
                                <td>Considerar reabastecimiento</td>
                            </tr>
                            <tr>
                                <td>Stock Crítico</td>
                                <td><i class="fas fa-times-circle" style="color: var(--danger-color);"></i> Rojo</td>
                                <td>Reabastecimiento urgente necesario</td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                <section id="categorias" class="help-section">
                    <h2><i class="fas fa-tags"></i>Gestión de Categorías</h2>
                    <p>Las categorías ayudan a organizar y clasificar los materiales:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Categorías Principales</h3>
                            <p>Las categorías predefinidas incluyen:</p>
                            <ul>
                                <li><strong>Cuero Natural:</strong> Materiales premium de cuero genuino</li>
                                <li><strong>Vinilouero:</strong> Materiales sintéticos de alta calidad</li>
                                <li><strong>Alcántara:</strong> Materiales técnicos deportivos</li>
                                <li><strong>Telas Técnicas:</strong> Materiales modernos especializados</li>
                                <li><strong>Accesorios:</strong> Complementos y acabados</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Beneficios de la Categorización</h3>
                            <ul>
                                <li>Búsqueda más eficiente de materiales</li>
                                <li>Reportes organizados por tipo de material</li>
                                <li>Mejor control de inventario</li>
                                <li>Optimización de compras por categoría</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section id="proveedores" class="help-section">
                    <h2><i class="fas fa-truck"></i>Gestión de Proveedores</h2>
                    <p>El registro de proveedores permite un mejor control de la cadena de suministro:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Información del Proveedor</h3>
                            <p>Para cada material puedes registrar:</p>
                            <ul>
                                <li>Nombre del proveedor</li>
                                <li>Información de contacto</li>
                                <li>Tiempos de entrega</li>
                                <li>Condiciones de pago</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Ventajas del Registro</h3>
                            <ul>
                                <li>Acceso rápido a información de contactos</li>
                                <li>Mejor negociación con proveedores</li>
                                <li>Control de calidad por proveedor</li>
                                <li>Optimización de costos de materiales</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section id="papelera" class="help-section">
                    <h2><i class="fas fa-trash"></i>Gestión de Papelera</h2>
                    <p>La papelera almacena temporalmente los materiales eliminados:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Mover a papelera</h3>
                            <p>Desde el menú de opciones de un material, haz clic en "Mover a papelera".</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Ver papelera</h3>
                            <p>Haz clic en la pestaña "Papelera" para ver todos los materiales eliminados.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Restaurar o eliminar</h3>
                            <p>Desde la papelera puedes:</p>
                            <ul>
                                <li><strong>Restaurar:</strong> Volver el material a su estado activo</li>
                                <li><strong>Eliminar permanentemente:</strong> Borrar definitivamente el material</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="Captura de pantalla (301).png" alt="Material movido a papelera">
                        <div class="image-caption">Figura 8: Confirmación de material movido a papelera</div>
                    </div>
                    
                    <p><strong>Importante:</strong> Los materiales en la papelera pueden ser restaurados dentro de los 30 días. No se pueden eliminar materiales que estén siendo utilizados en trabajos activos.</p>
                </section>

                <section id="problemas-comunes" class="help-section">
                    <h2><i class="fas fa-tools"></i>Solución de Problemas Comunes</h2>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No puedo encontrar un material</h3>
                    <ul>
                        <li>Verifica que estés escribiendo correctamente el nombre</li>
                        <li>Comprueba si el material ha sido movido a la papelera</li>
                        <li>Intenta buscar por categoría o proveedor</li>
                        <li>Verifica los filtros aplicados en la búsqueda</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>Error al guardar un material</h3>
                    <ul>
                        <li>Asegúrate de que todos los campos obligatorios estén completos</li>
                        <li>Verifica que el precio sea un número válido</li>
                        <li>Comprueba que el stock sea un número positivo</li>
                        <li>Confirma que no exista ya un material con el mismo nombre</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>Stock negativo</h3>
                    <ul>
                        <li>Revisa los trabajos recientes que utilizaron el material</li>
                        <li>Verifica que no haya errores en el registro inicial de stock</li>
                        <li>Actualiza manualmente el stock a un valor correcto</li>
                        <li>Consulta con el administrador si el problema persiste</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No puedo eliminar un material</h3>
                    <ul>
                        <li>Verifica que el material no esté siendo usado en trabajos activos</li>
                        <li>Comprueba que el material no esté asociado a cotizaciones pendientes</li>
                        <li>Si está en uso, espera a que se completen los trabajos o cambia el material</li>
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