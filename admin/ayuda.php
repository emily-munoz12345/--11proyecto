<?php
require_once __DIR__ . '/../php/conexion.php';
require_once __DIR__ . '/../php/auth.php';



require_once __DIR__ . '/includes/head.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda y Soporte | Nacional Tapizados</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.8);
            --primary-hover: rgba(140, 74, 63, 1);
            --text-color: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --bg-transparent: rgba(255, 255, 255, 0.1);
            --bg-transparent-light: rgba(255, 255, 255, 0.15);
            --border-color: rgba(255, 255, 255, 0.2);
            --success-color: #28a745;
            --link-color: #4CAF50;
        }
        
        body {
            background-image: url('https://pfst.cf2.poecdn.net/base/image/fe72e5f0bf336b4faca086bc6a42c20a45e904d165e796b52eca655a143283b8?w=1024&h=768&pmaid=426747789');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            color: var(--text-color);
            min-height: 100vh;
        }

        .help-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--bg-transparent);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
        }

        .help-header {
            text-align: center;
            margin-bottom: 3rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .help-header h1 {
            margin: 0;
            font-size: 2.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .help-header h1 i {
            color: var(--primary-color);
        }

        .help-header p {
            color: var(--text-muted);
            max-width: 700px;
            margin: 1rem auto 0;
            line-height: 1.6;
        }

        /* Secciones de ayuda */
        .help-section {
            margin-bottom: 3rem;
        }

        .help-section h2 {
            font-size: 1.6rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .help-section h2 i {
            color: var(--primary-color);
        }

        /* FAQ */
        .faq-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .faq-item {
            background-color: var(--bg-transparent-light);
            border-radius: 10px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .faq-item h3 {
            margin-top: 0;
            margin-bottom: 1rem;
            font-size: 1.2rem;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .faq-item h3 span {
            background-color: var(--primary-color);
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .faq-item p {
            margin: 0;
            line-height: 1.6;
            color: var(--text-muted);
        }

        /* Contacto */
        .contact-card {
            background-color: var(--bg-transparent-light);
            border-radius: 10px;
            padding: 2rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .contact-card p {
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .contact-method {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        .contact-method i {
            font-size: 1.5rem;
            color: var(--primary-color);
            width: 40px;
            height: 40px;
            background-color: rgba(140, 74, 63, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .contact-method div h4 {
            margin: 0 0 0.3rem;
            font-size: 1.1rem;
        }

        .contact-method div p {
            margin: 0;
            color: var(--text-muted);
        }

        .contact-method a {
            color: var(--link-color);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .contact-method a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        /* Recursos */
        .resources-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .resource-card {
            background-color: var(--bg-transparent-light);
            border-radius: 10px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .resource-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .resource-icon {
            width: 60px;
            height: 60px;
            background-color: rgba(76, 175, 80, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            color: var(--link-color);
            font-size: 1.5rem;
        }

        .resource-card h3 {
            margin: 0 0 1rem;
            font-size: 1.1rem;
        }

        .resource-card a {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background-color: var(--primary-color);
            color: white;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-top: auto;
        }

        .resource-card a:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .help-container {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            .faq-container {
                grid-template-columns: 1fr;
            }
            
            .help-header h1 {
                font-size: 1.8rem;
            }
            
            .help-section h2 {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body class="admin-body">
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include __DIR__ . '/includes/sidebar.php'; ?>
        <a href="javascript:history.back()" class="back-button">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        <!-- Contenido principal -->
        <main class="admin-main">
            <div class="help-container">
                <header class="help-header">
                    <h1>
                        <i class="fas fa-question-circle"></i> Ayuda y Soporte
                    </h1>
                    <p>Encuentra respuestas a tus preguntas, información de contacto y recursos útiles para sacar el máximo provecho de nuestro sistema.</p>
                </header>

                <!-- Preguntas Frecuentes -->
                <section class="help-section">
                    <h2>
                        <i class="fas fa-question"></i> Preguntas Frecuentes
                    </h2>
                    <div class="faq-container">
                        <article class="faq-item">
                            <h3><span>1</span> ¿Cómo puedo crear una cuenta?</h3>
                            <p>Para crear una cuenta, dirígete a la página de registro y completa el formulario con tus datos personales y de contacto. Recibirás un correo electrónico de confirmación para activar tu cuenta.</p>
                        </article>
                        
                        <article class="faq-item">
                            <h3><span>2</span> ¿Cómo puedo restablecer mi contraseña?</h3>
                            <p>Si olvidaste tu contraseña, haz clic en "¿Olvidaste tu contraseña?" en la página de inicio de sesión. Ingresa tu correo electrónico y sigue las instrucciones que recibirás para crear una nueva contraseña segura.</p>
                        </article>
                        
                        <article class="faq-item">
                            <h3><span>3</span> ¿Qué métodos de pago aceptan?</h3>
                            <p>Aceptamos diversos métodos de pago incluyendo tarjetas de crédito (Visa, MasterCard, American Express), transferencias bancarias y pagos en efectivo en nuestras instalaciones.</p>
                        </article>
                        
                        <article class="faq-item">
                            <h3><span>4</span> ¿Cómo programo una cita?</h3>
                            <p>Puedes programar una cita desde tu panel de control seleccionando el servicio que necesitas, la fecha y hora disponibles que mejor se ajusten a tu agenda.</p>
                        </article>
                        
                        <article class="faq-item">
                            <h3><span>5</span> ¿Cuál es el tiempo de entrega?</h3>
                            <p>El tiempo de entrega varía según el tipo de trabajo. Para tapicería estándar, el tiempo promedio es de 5-7 días hábiles. Trabajos complejos pueden requerir más tiempo.</p>
                        </article>
                        
                        <article class="faq-item">
                            <h3><span>6</span> ¿Ofrecen garantía?</h3>
                            <p>Sí, todos nuestros trabajos tienen una garantía de 6 meses contra defectos de fabricación. La garantía no cubre daños por mal uso o desgaste normal.</p>
                        </article>
                    </div>
                </section>

                <!-- Contacto -->
                <section class="help-section">
                    <h2>
                        <i class="fas fa-envelope"></i> Contacto
                    </h2>
                    <div class="contact-card">
                        <p>Si no encuentras respuesta a tu pregunta en nuestra sección de ayuda, nuestro equipo de soporte está disponible para ayudarte.</p>
                        
                        <div class="contact-method">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <h4>Correo Electrónico</h4>
                                <p><a href="mailto:soporte@nacionaltapizados.com">soporte@nacionaltapizados.com</a></p>
                                <p>Respuesta en menos de 24 horas</p>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <i class="fas fa-phone"></i>
                            <div>
                                <h4>Teléfono</h4>
                                <p><a href="tel:+573001234567">+57 300 123 4567</a></p>
                                <p>Lunes a Viernes: 8:00 AM - 6:00 PM</p>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <h4>Dirección</h4>
                                <p>Calle 123 #45-67, Bogotá, Colombia</p>
                                <p>Horario de atención: Lunes a Sábado 9:00 AM - 5:00 PM</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Recursos -->
                <section class="help-section">
                    <h2>
                        <i class="fas fa-book"></i> Recursos Útiles
                    </h2>
                    <div class="resources-grid">
                        <div class="resource-card">
                            <div class="resource-icon">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <h3>Guía del Usuario</h3>
                            <p>Manual completo con instrucciones detalladas para usar todas las funciones del sistema.</p>
                            <a href="guia_usuario.pdf" target="_blank">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                        </div>
                        
                        <div class="resource-card">
                            <div class="resource-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h3>Política de Privacidad</h3>
                            <p>Información sobre cómo protegemos y manejamos tus datos personales.</p>
                            <a href="politica_privacidad.pdf" target="_blank">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                        </div>
                        
                        <div class="resource-card">
                            <div class="resource-icon">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <h3>Términos de Servicio</h3>
                            <p>Condiciones y términos que rigen el uso de nuestro sistema y servicios.</p>
                            <a href="terminos_servicio.pdf" target="_blank">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                        </div>
                        
                        <div class="resource-card">
                            <div class="resource-icon">
                                <i class="fas fa-video"></i>
                            </div>
                            <h3>Video Tutoriales</h3>
                            <p>Guías en video para aprender a usar las principales funciones del sistema.</p>
                            <a href="tutoriales.html" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Ver
                            </a>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script>
        // Funcionalidad adicional si es necesaria
        document.addEventListener('DOMContentLoaded', function() {
            // Puedes añadir aquí interacciones como acordeones para las FAQs
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Implementar funcionalidad de acordeón si se desea
                });
            });
        });
    </script>
</body>
</html>