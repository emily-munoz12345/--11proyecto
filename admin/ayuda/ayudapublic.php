<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda</title>
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

        /* Galería de imágenes */
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .gallery-item {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .gallery-item:hover {
            transform: scale(1.05);
        }

        .gallery-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            display: block;
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

            .image-gallery {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }
    </style>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="main-container">
        <div class="header-section">
            <h1 class="page-title"><i class="fas fa-question-circle"></i>Ayuda </h1>
            <div>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>Volver
                </a>
            </div>
        </div>

        <div class="help-content">
            <nav class="help-nav">
                <ul>
                    <li><a href="#introduccion" class="active">Introducción</a></li>
                    <li><a href="#navegacion">Navegación</a></li>
                    <li><a href="#servicios">Servicios</a></li>
                    <li><a href="#materiales">Materiales</a></li>
                    <li><a href="#trabajos">Trabajos Realizados</a></li>
                    <li><a href="#nosotros">Sobre Nosotros</a></li>
                    <li><a href="#contacto">Contacto</a></li>
                    <li><a href="#problemas-comunes">Problemas Comunes</a></li>
                </ul>
            </nav>

            <div class="help-sections">
                <section id="introduccion" class="help-section">
                    <h2><i class="fas fa-info-circle"></i>Introducción al Sitio Web</h2>
                    <p>Bienvenido al sitio web de Nacional Tapizados, tu destino para tapicería automotriz de calidad y servicios personalizados. Nuestra plataforma está diseñada para ofrecerte información completa sobre nuestros servicios y facilitar el contacto con nosotros.</p>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/public/inicio.png">
                        <div class="image-caption"></div>
                    </div>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <h4><i class="fas fa-couch"></i>Servicios Especializados</h4>
                            <p>Tapizado completo, reparaciones, limpieza y personalización de interiores automotrices.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-palette"></i>Materiales Premium</h4>
                            <p>Descubre nuestra amplia gama de materiales de alta calidad para tu vehículo.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-images"></i>Trabajos Realizados</h4>
                            <p>Conoce ejemplos de nuestra calidad artesanal en diferentes tipos de vehículos.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-headset"></i>Contacto Directo</h4>
                            <p>Nuestro equipo está listo para atender tus consultas y cotizaciones.</p>
                        </div>
                    </div>
                    
                    <p>En esta guía encontrarás información detallada sobre cómo utilizar todas las funciones de nuestro sitio web.</p>
                </section>

                <section id="navegacion" class="help-section">
                    <h2><i class="fas fa-compass"></i>Navegación en el Sitio</h2>
                    <p>Aprende a moverte eficientemente por nuestro sitio web:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Menú Principal</h3>
                            <p>Utiliza el menú superior para acceder a las principales secciones:</p>
                            <ul>
                                <li><strong>Inicio:</strong> Página principal con información general y servicios destacados.</li>
                                <li><strong>Servicios:</strong> Catálogo completo de nuestros servicios de tapicería.</li>
                                <li><strong>Materiales:</strong> Información sobre los materiales premium que utilizamos.</li>
                                <li><strong>Trabajos:</strong> Galería de trabajos realizados con ejemplos visuales.</li>
                                <li><strong>Nosotros:</strong> Información sobre nuestra historia, equipo y valores.</li>
                                <li><strong>Contacto:</strong> Formas de comunicarte con nosotros.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/public/menu.png">
                        <div class="image-caption"></div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Navegación por Secciones</h3>
                            <p>Cada sección tiene un diseño específico:</p>
                            <ul>
                                <li><strong>Tarjetas informativas:</strong> Haz clic en las tarjetas para obtener más detalles.</li>
                                <li><strong>Filtros:</strong> En algunas páginas puedes filtrar contenido por categorías.</li>
                                <li><strong>Botones de acción:</strong> Utiliza los botones para contactarnos o ver más información.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Asistente de Ayuda</h3>
                            <p>En la esquina inferior derecha encontrarás un botón de ayuda que te guiará en cada sección del sitio.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/public/ayuda.png">
                        <div class="image-caption"></div>
                    </div>
                </section>

                <section id="servicios" class="help-section">
                    <h2><i class="fas fa-concierge-bell"></i>Servicios</h2>
                    <p>En la sección de Servicios encontrarás información detallada sobre todos los trabajos que realizamos:</p>
                    
                    <div class="image-container">
                    <img src="../imagenes/ayuda/public/serv.png">
                        <div class="image-caption"></div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Filtrar Servicios</h3>
                            <p>Utiliza los botones de filtro para ver servicios específicos:</p>
                            <ul>
                                <li><strong>Todos:</strong> Muestra todos los servicios disponibles.</li>
                                <li><strong>Tapizado Completo:</strong> Renovación total de interiores.</li>
                                <li><strong>Reparaciones:</strong> Soluciones para daños y desgastes.</li>
                                <li><strong>Limpieza:</strong> Tratamientos profesionales de limpieza.</li>
                                <li><strong>Personalización:</strong> Diseños exclusivos para tu vehículo.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Información de Servicios</h3>
                            <p>Cada servicio incluye:</p>
                            <ul>
                                <li><strong>Descripción detallada:</strong> Explicación del servicio.</li>
                                <li><strong>Lista de beneficios:</strong> Ventajas y características.</li>
                                <li><strong>Tiempo estimado:</strong> Duración del trabajo.</li>
                                <li><strong>Precio referencial:</strong> Costo aproximado del servicio.</li>
                            </ul>
                        </div>
                    </div>
                    
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Proceso de Trabajo</h3>
                            <p>En la parte inferior de la página encontrarás nuestro proceso de trabajo:</p>
                            <ul>
                                <li><strong>Evaluación:</strong> Diagnóstico del vehículo.</li>
                                <li><strong>Cotización:</strong> Presupuesto transparente.</li>
                                <li><strong>Aprobación:</strong> Selección de materiales.</li>
                                <li><strong>Ejecución:</strong> Trabajo artesanal.</li>
                                <li><strong>Entrega:</strong> Con garantía documentada.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/public/procs.png">
                        <div class="image-caption"></div>
                    </div>
                </section>

                <section id="materiales" class="help-section">
                    <h2><i class="fas fa-palette"></i>Materiales</h2>
                    <p>En esta sección conocerás los materiales premium que utilizamos en nuestros trabajos:</p>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/public/mat.png">
                        <div class="image-caption"></div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Tipos de Materiales</h3>
                            <p>Explora los diferentes materiales disponibles:</p>
                            <ul>
                                <li><strong>Cuero Natural Premium:</strong> La opción más exclusiva con máxima durabilidad.</li>
                                <li><strong>Alcántara Deportiva:</strong> Material técnico antideslizante y resistente.</li>
                                <li><strong>Vinilcuero Premium:</strong> Opción económica sin sacrificar calidad.</li>
                                <li><strong>Tela Técnica:</strong> Materiales modernos con tratamientos especiales.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Información de Materiales</h3>
                            <p>Cada material incluye:</p>
                            <ul>
                                <li><strong>Ventajas:</strong> Beneficios específicos del material.</li>
                                <li><strong>Vida útil:</strong> Duración estimada.</li>
                                <li><strong>Recomendaciones:</strong> Para qué tipo de vehículos es ideal.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Cuidados Específicos</h3>
                            <p>Haz clic en "Ver cuidados" para cada material y obtén información detallada sobre:</p>
                            <ul>
                                <li><strong>Limpieza:</strong> Frecuencia y métodos recomendados.</li>
                                <li><strong>Mantenimiento:</strong> Cuidados específicos para cada material.</li>
                                <li><strong>Protección:</strong> Cómo preservar la calidad del material.</li>
                                <li><strong>Productos:</strong> Recomendaciones de productos de limpieza.</li>
                                <li><strong>Advertencias:</strong> Qué evitar para no dañar el material.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/public/formc.png">
                        <div class="image-caption"></div>
                    </div>
                </section>

                <section id="trabajos" class="help-section">
                    <h2><i class="fas fa-images"></i>Trabajos Realizados</h2>
                    <p>En esta sección podrás ver ejemplos de nuestro trabajo en diferentes vehículos:</p>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/public/trab.png">
                        <div class="image-caption"></div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Filtrar Trabajos</h3>
                            <p>Utiliza los botones de filtro para ver trabajos específicos:</p>
                            <ul>
                                <li><strong>Todos:</strong> Muestra todos los trabajos realizados.</li>
                                <li><strong>Tapizado Completo:</strong> Renovaciones totales de interiores.</li>
                                <li><strong>Reparaciones:</strong> Trabajos de restauración y reparación.</li>
                                <li><strong>Personalización:</strong> Diseños exclusivos y personalizados.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="image-gallery">
                        <div class="gallery-item">
                            <img src="../imagenes/ayuda/public/fil.png">
                        <div class="image-caption"></div>
                        </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Información de Trabajos</h3>
                            <p>Cada trabajo incluye:</p>
                            <ul>
                                <li><strong>Imágenes:</strong> Fotografías del antes y después.</li>
                                <li><strong>Descripción:</strong> Detalles del trabajo realizado.</li>
                                <li><strong>Vehículo:</strong> Marca y modelo del automóvil.</li>
                                <li><strong>Materiales utilizados:</strong> Especificaciones de los materiales.</li>
                                <li><strong>Tiempo de trabajo:</strong> Duración del proyecto.</li>
                                <li><strong>Garantía:</strong> Tiempo de garantía ofrecido.</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section id="nosotros" class="help-section">
                    <h2><i class="fas fa-users"></i>Sobre Nosotros</h2>
                    <p>Conoce más sobre Nacional Tapizados, nuestra historia y valores:</p>
                    
                    <div class="image-container">
                                        <div class="image-gallery">
                        <div class="gallery-item">
                            <img src="../imagenes/ayuda/public/nos.png">
                        <div class="image-caption"></div>
                        </div></div>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Nuestra Historia</h3>
                            <p>Descubre nuestra trayectoria desde 1995:</p>
                            <ul>
                                <li><strong>1995:</strong> Fundación como taller familiar.</li>
                                <li><strong>2002:</strong> Primera expansión de instalaciones.</li>
                                <li><strong>2010:</strong> Certificación de calidad e importación directa de materiales.</li>
                                <li><strong>2020:</strong> Modernización con tecnología de punta.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="image-container">
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Nuestro Equipo</h3>
                            <p>Conoce a los profesionales detrás de nuestro trabajo:</p>
                            <ul>
                                <li><strong>Juan Pérez:</strong> Fundador y maestro tapicero con 40+ años de experiencia.</li>
                                <li><strong>María Gómez:</strong> Diseñadora y especialista en materiales.</li>
                                <li><strong>Carlos Rojas:</strong> Jefe de taller supervisando cada proyecto.</li>
                            </ul>
                        </div>
                    </div>
                    
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Nuestros Valores</h3>
                            <p>Los principios que guían nuestro trabajo:</p>
                            <ul>
                                <li><strong>Calidad:</strong> Atención al detalle en cada pieza.</li>
                                <li><strong>Honestidad:</strong> Transparencia en cada proceso.</li>
                                <li><strong>Excelencia:</strong> Búsqueda constante de la perfección.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Nuestras Instalaciones</h3>
                            <p>Contamos con un espacio de 800m² diseñado específicamente para tapicería automotriz, con áreas especializadas para cada etapa del proceso.</p>
                        </div>
                    </div>
                    
                </section>

                <section id="contacto" class="help-section">
                    <h2><i class="fas fa-envelope"></i>Contacto</h2>
                    <p>Múltiples formas de contactarnos para resolver tus dudas o solicitar información:</p>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/public/formg.png">
                        <div class="image-caption">/div>
                    </div>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <h4><i class="fas fa-map-marker-alt"></i>Visítanos</h4>
                            <p>Cr 13 # 4.43, Ciudad, Estado 12345, Colombia</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-phone"></i>Llámanos</h4>
                            <p>Ventas: 57 1234 5678<br>Soporte: 57 9876 5432</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-envelope"></i>Escríbenos</h4>
                            <p>olfonsojose@gmail.com</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-clock"></i>Horario</h4>
                            <p>Lunes a Viernes: 8:00am - 6:00pm<br>Sábados: 8:00am - 2:00pm</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Formulario de Contacto</h3>
                            <p>Completa el formulario en línea con:</p>
                            <ul>
                                <li><strong>Nombre y Apellido:</strong> Información de contacto.</li>
                                <li><strong>Correo Electrónico:</strong> Para responder tu consulta.</li>
                                <li><strong>Teléfono:</strong> Número de contacto opcional.</li>
                                <li><strong>Asunto:</strong> Selecciona el motivo de tu consulta.</li>
                                <li><strong>Mensaje:</strong> Detalla tu consulta o solicitud.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/public/formc.png">
                        <div class="image-caption"></div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Confirmación de Envío</h3>
                            <p>Después de enviar el formulario:</p>
                            <ul>
                                <li>Verás un mensaje de confirmación en la parte superior de la página.</li>
                                <li>Si hay errores, recibirás indicaciones para corregirlos.</li>
                                <li>Nos pondremos en contacto contigo en un plazo máximo de 24 horas.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Ubicación</h3>
                            <p>En la parte inferior de la página de contacto encontrarás un mapa con nuestra ubicación exacta.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/public/map.png">
                        <div class="image-caption"></div>
                    </div>
                </section>

                <section id="problemas-comunes" class="help-section">
                    <h2><i class="fas fa-tools"></i>Solución de Problemas Comunes</h2>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No puedo ver las imágenes correctamente</h3>
                    <ul>
                        <li>Verifica tu conexión a internet.</li>
                        <li>Actualiza la página (F5 o Ctrl+R).</li>
                        <li>Prueba con un navegador diferente.</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>El formulario de contacto no se envía</h3>
                    <ul>
                        <li>Verifica que todos los campos obligatorios estén completos.</li>
                        <li>Asegúrate de que el correo electrónico tenga un formato válido.</li>
                        <li>Comprueba que no haya errores marcados en rojo en los campos.</li>
                    </ul>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/public/errr.png">
                        <div class="image-caption">Ejemplo de error en formulario con campos obligatorios</div>
                    </div>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No encuentro la información que busco</h3>
                    <ul>
                        <li>Utiliza el menú de navegación superior para acceder a las diferentes secciones.</li>
                        <li>Revisa la sección de "Servicios" para información detallada sobre nuestros trabajos.</li>
                        <li>Si no encuentras lo que buscas, contáctanos directamente.</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>Problemas con la visualización en móvil</h3>
                    <ul>
                        <li>Asegúrate de estar usando la versión más reciente de tu navegador.</li>
                        <li>Intenta rotar la pantalla de tu dispositivo para ver si mejora la visualización.</li>
                        <li>Si el problema persiste, contáctanos para reportarlo.</li>
                    </ul>
                    
                    <div class="step-container" style="background-color: rgba(25, 135, 84, 0.2); border-left-color: var(--success-color);">
                        <div class="step-content">
                            <h3><i class="fas fa-life-ring"></i>Contacto de Soporte</h3>
                            <p>Si continúas experimentando problemas, contacta a nuestro equipo de soporte:</p>
                            <ul>
                                <li><strong>Email:</strong> olfonsojose@gmail.com</li>
                                <li><strong>Teléfono:</strong> +57 1234 5678</li>
                                <li><strong>Horario de atención:</strong> Lunes a Viernes 8:00 AM - 6:00 PM</li>
                                <li><strong>Ubicación:</strong> Cr 13 # 4.43, Ciudad, Estado 12345, Colombia</li>
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