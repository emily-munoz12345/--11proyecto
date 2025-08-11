<?php
require_once __DIR__ . '../../../php/conexion.php';
require_once __DIR__ . '../../../php/auth.php';

require_once __DIR__ . '../../includes/head.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda Administrativa | Nacional Tapizados</title>
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
        <?php include __DIR__ . '../../includes/sidebar.php'; ?>
        <a href="javascript:history.back()" class="back-button">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        <!-- Contenido principal -->
        <main class="admin-main">
            <div class="help-container">
                <header class="help-header">
                    <h1>
                        <i class="fas fa-headset"></i> Soporte Administrativo
                    </h1>
                    <p>Recursos y asistencia técnica para el personal administrativo de Nacional Tapizados. Aquí encontrarás guías, contactos de soporte y respuestas a preguntas frecuentes sobre el sistema interno.</p>
                </header>

                <!-- Preguntas Frecuentes -->
                <section class="help-section">
                    <h2>
                        <i class="fas fa-question-circle"></i> Preguntas Frecuentes - Área Administrativa
                    </h2>
                    <div class="faq-container">
                        <article class="faq-item">
                            <h3><span>1</span> ¿Cómo gestionar usuarios y permisos?</h3>
                            <p>Desde el módulo de Usuarios en el panel de administración puedes crear, editar y desactivar cuentas. Los roles y permisos se asignan en la pestaña "Configuración de acceso".</p>
                        </article>
                        
                        <article class="faq-item">
                            <h3><span>2</span> ¿Cómo generar reportes personalizados?</h3>
                            <p>En la sección de Reportes, selecciona el tipo de informe, ajusta los filtros y fechas según necesites. Puedes exportar a PDF, Excel o programar envíos automáticos.</p>
                        </article>
                        
                        <article class="faq-item">
                            <h3><span>3</span> ¿Qué hacer si el sistema muestra errores?</h3>
                            <p>Primero verifica tu conexión a internet. Si el problema persiste, registra el código de error y contacta a soporte técnico. No intentes soluciones que puedan afectar la integridad de los datos.</p>
                        </article>
                        
                        <article class="faq-item">
                            <h3><span>4</span> ¿Cómo actualizar inventario?</h3>
                            <p>Dirígete al módulo de Inventario, selecciona "Actualizar existencias". Puedes hacer ajustes manuales o cargar un archivo CSV con las nuevas cantidades.</p>
                        </article>
                        
                        <article class="faq-item">
                            <h3><span>5</span> ¿Cómo procesar devoluciones?</h3>
                            <p>En el módulo de Ventas, busca la transacción original, selecciona "Procesar devolución" y sigue el asistente. El sistema ajustará automáticamente el inventario y los registros contables.</p>
                        </article>
                        
                        <article class="faq-item">
                            <h3><span>6</span> ¿Cómo configurar alertas del sistema?</h3>
                            <p>En Configuración > Notificaciones puedes activar alertas para niveles bajos de inventario, pagos pendientes, citas próximas y otros eventos importantes.</p>
                        </article>
                    </div>
                </section>

                <!-- Contacto -->
                <section class="help-section">
                    <h2>
                        <i class="fas fa-life-ring"></i> Soporte Técnico
                    </h2>
                    <div class="contact-card">
                        <p>Para problemas técnicos urgentes o consultas sobre el sistema administrativo, contacta a nuestro equipo de soporte interno.</p>
                        
                        <div class="contact-method">
                            <i class="fas fa-ticket-alt"></i>
                            <div>
                                <h4>Sistema de Tickets</h4>
                                <p><a href="https://soporte.nacionaltapizados.com" target="_blank">Abrir un ticket de soporte</a></p>
                                <p>Seguimiento prioritario para problemas del sistema</p>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <i class="fas fa-phone"></i>
                            <div>
                                <h4>Soporte Urgente</h4>
                                <p><a href="tel:+573001234567">Ext. 101 (Interno)</a></p>
                                <p>Disponible 24/7 para emergencias del sistema</p>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <i class="fas fa-users"></i>
                            <div>
                                <h4>Capacitación</h4>
                                <p><a href="mailto:capacitacion@nacionaltapizados.com">capacitacion@nacionaltapizados.com</a></p>
                                <p>Solicita sesiones de entrenamiento para tu equipo</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Recursos -->
                <section class="help-section">
                    <h2>
                        <i class="fas fa-tools"></i> Recursos Administrativos
                    </h2>
                    <div class="resources-grid">
                        <div class="resource-card">
                            <div class="resource-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h3>Manual Administrativo</h3>
                            <p>Guía completa con todos los procesos y políticas internas de la empresa.</p>
                            <a href="manual_administrativo.pdf" target="_blank">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                        </div>
                        
                        <div class="resource-card">
                            <div class="resource-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3>Plantillas de Reportes</h3>
                            <p>Colección de plantillas predefinidas para análisis de ventas e inventario.</p>
                            <a href="plantillas_reportes.zip" target="_blank">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                        </div>
                        
                        <div class="resource-card">
                            <div class="resource-icon">
                                <i class="fas fa-database"></i>
                            </div>
                            <h3>Protocolo de Respaldo</h3>
                            <p>Instrucciones para realizar copias de seguridad de la información crítica.</p>
                            <a href="protocolo_respaldo.pdf" target="_blank">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                        </div>
                        
                        <div class="resource-card">
                            <div class="resource-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <h3>Seguridad de Datos</h3>
                            <p>Políticas y mejores prácticas para proteger la información sensible.</p>
                            <a href="seguridad_datos.pdf" target="_blank">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                        </div>
                        
                        <div class="resource-card">
                            <div class="resource-icon">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <h3>Flujos de Trabajo</h3>
                            <p>Diagramas de los procesos clave de aprobación y autorizaciones.</p>
                            <a href="flujos_trabajo.pdf" target="_blank">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                        </div>
                        
                        <div class="resource-card">
                            <div class="resource-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <h3>Calendario Fiscal</h3>
                            <p>Fechas importantes y vencimientos para el año en curso.</p>
                            <a href="calendario_fiscal.ics" target="_blank">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                        </div>
                    </div>
                </section>
                
                <!-- Procedimientos -->
                <section class="help-section">
                    <h2>
                        <i class="fas fa-list-check"></i> Procedimientos Clave
                    </h2>
                    <div class="faq-container">
                        <article class="faq-item">
                            <h3><i class="fas fa-user-plus"></i> Alta de Nuevo Empleado</h3>
                            <p>1. Recopilar documentación requerida<br>2. Registrar en sistema de RRHH<br>3. Asignar equipos y accesos<br>4. Programar inducción</p>
                        </article>
                        
                        <article class="faq-item">
                            <h3><i class="fas fa-file-invoice-dollar"></i> Cierre Mensual</h3>
                            <p>1. Verificar conciliaciones bancarias<br>2. Revisar cuentas por cobrar/pagar<br>3. Generar reportes financieros<br>4. Realizar respaldo completo</p>
                        </article>
                        
                        <article class="faq-item">
                            <h3><i class="fas fa-box-open"></i> Recepción de Mercancía</h3>
                            <p>1. Verificar factura vs pedido<br>2. Inspeccionar estado de productos<br>3. Registrar en sistema<br>4. Almacenar con código QR</p>
                        </article>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script>
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