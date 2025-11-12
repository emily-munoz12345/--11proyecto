<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda | Nacional Tapizados</title>
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
            <h1 class="page-title"><i class="fas fa-question-circle"></i>Ayuda - Sitio Web Nacional Tapizados</h1>
            <div>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>Volver
                </a>
                <a href="../index.html" class="btn btn-primary">
                    <i class="fas fa-home"></i>Inicio
                </a>
            </div>
        </div>

        <div class="help-content">
            <nav class="help-nav">
                <ul>
                    <li><a href="#introduccion" class="active">Introducción</a></li>
                    <li><a href="#navegacion">Navegación</a></li>
                    <li><a href="#productos">Productos</a></li>
                    <li><a href="#contacto">Contacto</a></li>
                    <li><a href="#cuenta">Mi Cuenta</a></li>
                    <li><a href="#pedidos">Pedidos</a></li>
                    <li><a href="#pagos">Pagos</a></li>
                    <li><a href="#problemas-comunes">Problemas Comunes</a></li>
                </ul>
            </nav>

            <div class="help-sections">
                <section id="introduccion" class="help-section">
                    <h2><i class="fas fa-info-circle"></i>Introducción al Sitio Web</h2>
                    <p>Bienvenido al sitio web de Nacional Tapizados, tu destino para muebles de calidad y tapizados personalizados. Nuestra plataforma está diseñada para ofrecerte una experiencia de compra fácil y agradable.</p>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <h4><i class="fas fa-shopping-cart"></i>Compras Fáciles</h4>
                            <p>Navega por nuestro catálogo y realiza compras de forma segura.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-couch"></i>Productos de Calidad</h4>
                            <p>Descubre nuestra amplia gama de muebles y servicios de tapizado.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-truck"></i>Envío Rápido</h4>
                            <p>Recibe tus productos en la comodidad de tu hogar.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-headset"></i>Soporte Personalizado</h4>
                            <p>Nuestro equipo está listo para ayudarte en lo que necesites.</p>
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
                                <li><strong>Inicio:</strong> Página principal con productos destacados.</li>
                                <li><strong>Productos:</strong> Catálogo completo de muebles y servicios.</li>
                                <li><strong>Servicios:</strong> Información sobre nuestros servicios de tapizado.</li>
                                <li><strong>Contacto:</strong> Formas de comunicarte con nosotros.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Búsqueda</h3>
                            <p>Utiliza la barra de búsqueda para encontrar productos específicos por nombre, categoría o características.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Filtros</h3>
                            <p>En la página de productos, utiliza los filtros para refinar tu búsqueda por categoría, precio, color, etc.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/publica/navegacion.png" alt="Navegación del sitio">
                        <div class="image-caption">Ejemplo de navegación en el sitio web</div>
                    </div>
                </section>

                <section id="productos" class="help-section">
                    <h2><i class="fas fa-couch"></i>Productos y Catálogo</h2>
                    <p>Descubre cómo explorar y seleccionar productos en nuestro catálogo:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Explorar Categorías</h3>
                            <p>Navega por nuestras categorías principales:</p>
                            <ul>
                                <li><strong>Sofás y Seccionales:</strong> Diversos estilos y tamaños.</li>
                                <li><strong>Sillas y Butacas:</strong> Para interior y exterior.</li>
                                <li><strong>Taburetes y Bancos:</strong> Complementos para tu hogar.</li>
                                <li><strong>Tapizados Personalizados:</strong> Servicio a medida.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Ver Detalles del Producto</h3>
                            <p>Haz clic en cualquier producto para ver información detallada:</p>
                            <ul>
                                <li><strong>Imágenes:</strong> Vista múltiple del producto.</li>
                                <li><strong>Descripción:</strong> Características y materiales.</li>
                                <li><strong>Dimensiones:</strong> Medidas exactas del producto.</li>
                                <li><strong>Precio y Disponibilidad:</strong> Información de compra.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Personalización</h3>
                            <p>Para productos personalizables, selecciona:</p>
                            <ul>
                                <li><strong>Color:</strong> Diferentes opciones de tela.</li>
                                <li><strong>Material:</strong> Variedad de materiales disponibles.</li>
                                <li><strong>Medidas:</strong> Especificaciones personalizadas.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/publica/productos.png" alt="Página de productos">
                        <div class="image-caption">Vista de productos en el catálogo</div>
                    </div>
                </section>

                <section id="contacto" class="help-section">
                    <h2><i class="fas fa-envelope"></i>Contacto y Soporte</h2>
                    <p>Múltiples formas de contactarnos para resolver tus dudas o solicitar información:</p>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <h4><i class="fas fa-map-marker-alt"></i>Visítanos</h4>
                            <p>Ven a nuestro showroom para ver nuestros productos en persona.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-phone"></i>Llámanos</h4>
                            <p>Habla directamente con nuestro equipo de atención al cliente.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-envelope"></i>Escríbenos</h4>
                            <p>Envía tus consultas por correo electrónico.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-comments"></i>Chat en Vivo</h4>
                            <p>Conversa en tiempo real con nuestros asesores.</p>
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
                                <li><strong>Mensaje:</strong> Detalla tu consulta o solicitud.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Solicitud de Cotización</h3>
                            <p>Para productos personalizados, utiliza el formulario específico que incluye:</p>
                            <ul>
                                <li><strong>Tipo de Producto:</strong> Sofá, silla, etc.</li>
                                <li><strong>Medidas Específicas:</strong> Dimensiones requeridas.</li>
                                <li><strong>Material Preferido:</strong> Tipo de tela o material.</li>
                                <li><strong>Presupuesto Aproximado:</strong> Rango de inversión.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/publica/contacto.png" alt="Formulario de contacto">
                        <div class="image-caption">Formulario de contacto en línea</div>
                    </div>
                </section>

                <section id="cuenta" class="help-section">
                    <h2><i class="fas fa-user"></i>Mi Cuenta</h2>
                    <p>Gestiona tu información personal y preferencias:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Crear Cuenta</h3>
                            <p>Regístrate para acceder a funciones exclusivas:</p>
                            <ul>
                                <li><strong>Nombre Completo:</strong> Tu nombre y apellidos.</li>
                                <li><strong>Correo Electrónico:</strong> Será tu usuario.</li>
                                <li><strong>Contraseña:</strong> Crea una contraseña segura.</li>
                                <li><strong>Teléfono:</strong> Número de contacto.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Iniciar Sesión</h3>
                            <p>Accede a tu cuenta con tu correo y contraseña.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Gestionar Perfil</h3>
                            <p>Desde tu cuenta puedes:</p>
                            <ul>
                                <li><strong>Actualizar Información:</strong> Modificar datos personales.</li>
                                <li><strong>Cambiar Contraseña:</strong> Actualizar credenciales de acceso.</li>
                                <li><strong>Gestionar Direcciones:</strong> Agregar o editar direcciones de envío.</li>
                                <li><strong>Preferencias:</strong> Configurar notificaciones y preferencias.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/publica/cuenta.png" alt="Panel de usuario">
                        <div class="image-caption">Panel de control de usuario</div>
                    </div>
                </section>

                <section id="pedidos" class="help-section">
                    <h2><i class="fas fa-shopping-bag"></i>Realizar Pedidos</h2>
                    <p>Proceso completo para realizar tus compras en línea:</p>
                    
                    <div class="step-container">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Agregar al Carrito</h3>
                            <p>Selecciona los productos que deseas comprar y agrégalos a tu carrito.</p>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Revisar Carrito</h3>
                            <p>Verifica los productos en tu carrito antes de proceder al pago:</p>
                            <ul>
                                <li><strong>Cantidad:</strong> Ajusta las cantidades si es necesario.</li>
                                <li><strong>Precio:</strong> Revisa el costo total de tu pedido.</li>
                                <li><strong>Opciones de Envío:</strong> Selecciona el método de entrega.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Proceso de Checkout</h3>
                            <p>Completa la información necesaria para finalizar tu compra:</p>
                            <ul>
                                <li><strong>Información de Envío:</strong> Dirección de entrega.</li>
                                <li><strong>Método de Pago:</strong> Selecciona forma de pago.</li>
                                <li><strong>Revisión Final:</strong> Confirma los detalles del pedido.</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step-container">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Confirmación</h3>
                            <p>Recibirás una confirmación por correo electrónico con los detalles de tu pedido y número de seguimiento.</p>
                        </div>
                    </div>
                    
                    <div class="image-container">
                        <img src="../imagenes/ayuda/publica/carrito.png" alt="Carrito de compras">
                        <div class="image-caption">Carrito de compras y proceso de checkout</div>
                    </div>
                </section>

                <section id="pagos" class="help-section">
                    <h2><i class="fas fa-credit-card"></i>Métodos de Pago</h2>
                    <p>Opciones seguras para realizar tus pagos:</p>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <h4><i class="fas fa-credit-card"></i>Tarjetas de Crédito</h4>
                            <p>Aceptamos todas las tarjetas principales: Visa, MasterCard, American Express.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-university"></i>Transferencias Bancarias</h4>
                            <p>Realiza transferencias desde tu banco directamente.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-mobile-alt"></i>Pagos Móviles</h4>
                            <p>Utiliza aplicaciones de pago móvil compatibles.</p>
                        </div>
                        <div class="feature-card">
                            <h4><i class="fas fa-money-bill-wave"></i>Efectivo</h4>
                            <p>Pago en efectivo al momento de la entrega (sujeto a condiciones).</p>
                        </div>
                    </div>
                    
                    <h3><i class="fas fa-shield-alt"></i>Seguridad en Pagos</h3>
                    <p>Tu seguridad es nuestra prioridad:</p>
                    <ul>
                        <li><strong>Cifrado SSL:</strong> Todos los datos se transmiten de forma segura.</li>
                        <li><strong>Protección de Datos:</strong> No almacenamos información sensible de pago.</li>
                        <li><strong>Verificación:</strong> Procesos de autenticación para transacciones.</li>
                    </ul>
                    
                    <div class="step-container">
                        <div class="step-content">
                            <h3><i class="fas fa-lock"></i>Garantía de Seguridad</h3>
                            <p>Todas las transacciones están protegidas con los más altos estándares de seguridad. Si experimentas algún problema con tu pago, contacta inmediatamente a nuestro equipo de soporte.</p>
                        </div>
                    </div>
                </section>

                <section id="problemas-comunes" class="help-section">
                    <h2><i class="fas fa-tools"></i>Solución de Problemas Comunes</h2>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No puedo crear una cuenta</h3>
                    <ul>
                        <li>Verifica que tu correo electrónico no esté ya registrado.</li>
                        <li>Asegúrate de que la contraseña cumpla con los requisitos mínimos.</li>
                        <li>Comprueba que todos los campos obligatorios estén completos.</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>Problemas al iniciar sesión</h3>
                    <ul>
                        <li>Verifica que estés usando el correo correcto.</li>
                        <li>Confirma que tu contraseña sea la correcta (distingue mayúsculas y minúsculas).</li>
                        <li>Utiliza la opción "¿Olvidaste tu contraseña?" si es necesario.</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>Error al realizar un pedido</h3>
                    <ul>
                        <li>Verifica que todos los productos estén disponibles.</li>
                        <li>Comprueba que la información de envío sea correcta.</li>
                        <li>Asegúrate de que el método de pago seleccionado sea válido.</li>
                    </ul>
                    
                    <h3><i class="fas fa-exclamation-triangle"></i>No recibo el correo de confirmación</h3>
                    <ul>
                        <li>Revisa tu carpeta de spam o correo no deseado.</li>
                        <li>Verifica que hayas ingresado correctamente tu dirección de correo.</li>
                        <li>Espera unos minutos, a veces hay demoras en el envío.</li>
                    </ul>
                    
                    <div class="step-container" style="background-color: rgba(25, 135, 84, 0.2); border-left-color: var(--success-color);">
                        <div class="step-content">
                            <h3><i class="fas fa-life-ring"></i>Contacto de Soporte</h3>
                            <p>Si continúas experimentando problemas, contacta a nuestro equipo de soporte:</p>
                            <ul>
                                <li><strong>Email:</strong> soporte@nacionaltapizados.com</li>
                                <li><strong>Teléfono:</strong> +57 123 456 7890</li>
                                <li><strong>Horario de atención:</strong> Lunes a Viernes 8:00 AM - 6:00 PM</li>
                                <li><strong>Chat en Vivo:</strong> Disponible en horario comercial</li>
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