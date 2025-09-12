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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            background-image: url('https://pfst.cf2.poecdn.net/base/image/60ab54eef562f30f85a67bde31f924f078199dae0b7bc6c333dfb467a2c13471?w=1024&h=768&pmaid=442168253');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            color: var(--text-color);
            min-height: 100vh;
        }

        .main-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--bg-transparent);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
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
        }

        .page-title i {
            margin-right: 12px;
            color: var(--primary-color);
        }

        /* Estilos para pestañas */
        .nav-tabs {
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 2rem;
        }

        .nav-link {
            color: var(--text-muted);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--text-color);
            background-color: var(--bg-transparent-light);
        }

        .nav-link.active {
            color: white;
            background-color: var(--primary-color);
            border-radius: 8px 8px 0 0;
        }

        .tab-content {
            padding: 1.5rem 0;
        }

        /* Estilos para tarjetas */
        .help-card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            height: 100%;
        }

        .help-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .help-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .help-card-icon {
            width: 50px;
            height: 50px;
            background-color: rgba(140, 74, 63, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 1.5rem;
        }

        .help-card-title {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 600;
        }

        .help-card-body {
            margin-bottom: 1rem;
        }

        /* FAQ */
        .faq-item {
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            background-color: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            background-color: rgba(255, 255, 255, 0.12);
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
        .resource-card {
            background-color: var(--bg-transparent-light);
            border-radius: 10px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            height: 100%;
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

        .resource-card p {
            flex-grow: 1;
            margin-bottom: 1rem;
            color: var(--text-muted);
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
        }

        .resource-card a:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        /* Botones */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            gap: 0.5rem;
        }

        .btn i {
            font-size: 0.9rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: rgba(108, 117, 125, 0.8);
            color: white;
        }

        .btn-secondary:hover {
            background-color: rgba(108, 117, 125, 1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .nav-tabs {
                flex-wrap: wrap;
            }
            
            .nav-link {
                border-radius: 8px;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body class="admin-body">
    <div class="admin-container-center">
        <!-- Sidebar -->
        <?php include __DIR__ . '../../includes/sidebar.php'; ?>
        
        <!-- Contenido principal -->
        <main class="admin-main">
            <div class="main-container">
                <!-- Encabezado -->
                <div class="header-section">
                    <h1 class="page-title">
                        <i class="fas fa-headset"></i> Soporte Administrativo
                    </h1>
                    <div class="d-flex gap-2">
                        <a href="javascript:history.back()" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <p class="mb-4">Recursos y asistencia técnica para el personal administrativo de Nacional Tapizados. Aquí encontrarás guías, contactos de soporte y respuestas a preguntas frecuentes sobre el sistema interno.</p>

                <!-- Pestañas de navegación -->
                <ul class="nav nav-tabs" id="helpTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="faq-tab" data-bs-toggle="tab" data-bs-target="#faq" type="button" role="tab">
                            <i class="fas fa-question-circle"></i> Preguntas Frecuentes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="support-tab" data-bs-toggle="tab" data-bs-target="#support" type="button" role="tab">
                            <i class="fas fa-life-ring"></i> Soporte Técnico
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="resources-tab" data-bs-toggle="tab" data-bs-target="#resources" type="button" role="tab">
                            <i class="fas fa-tools"></i> Recursos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="procedures-tab" data-bs-toggle="tab" data-bs-target="#procedures" type="button" role="tab">
                            <i class="fas fa-list-check"></i> Procedimientos
                        </button>
                    </li>
                </ul>

                <!-- Contenido de las pestañas -->
                <div class="tab-content" id="helpTabsContent">
                    <!-- Pestaña de FAQ -->
                    <div class="tab-pane fade show active" id="faq" role="tabpanel">
                        <div class="help-card">
                            <div class="help-card-header">
                                <div class="help-card-icon">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <h2 class="help-card-title">Preguntas Frecuentes - Área Administrativa</h2>
                            </div>
                            <div class="help-card-body">
                                <div class="faq-item">
                                    <h3><span>1</span> ¿Cómo gestionar usuarios y permisos?</h3>
                                    <p>Desde el módulo de Usuarios en el panel de administración puedes crear, editar y desactivar cuentas. Los roles y permisos se asignan en la pestaña "Configuración de acceso".</p>
                                </div>
                                
                                <div class="faq-item">
                                    <h3><span>2</span> ¿Qué hacer si el sistema muestra errores?</h3>
                                    <p>Primero verifica tu conexión a internet. Si el problema persiste, registra el código de error y contacta a soporte técnico. No intentes soluciones que puedan afectar la integridad de los datos.</p>
                                </div>
                                
                                <div class="faq-item">
                                    <h3><span>3</span> ¿Cómo actualizar inventario?</h3>
                                    <p>Dirígete al módulo de Inventario, selecciona "Actualizar existencias". Puedes hacer ajustes manuales o cargar un archivo CSV con las nuevas cantidades.</p>
                                </div>
                                
                                <div class="faq-item">
                                    <h3><span>4</span> ¿Cómo configurar alertas del sistema?</h3>
                                    <p>En Configuración > Notificaciones puedes activar alertas para niveles bajos de inventario, pagos pendientes, citas próximas y otros eventos importantes.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pestaña de Soporte Técnico -->
                    <div class="tab-pane fade" id="support" role="tabpanel">
                        <div class="help-card">
                            <div class="help-card-header">
                                <div class="help-card-icon">
                                    <i class="fas fa-life-ring"></i>
                                </div>
                                <h2 class="help-card-title">Soporte Técnico</h2>
                            </div>
                            <div class="help-card-body">
                                <p>Para problemas técnicos urgentes o consultas sobre el sistema administrativo, contacta a nuestro equipo de soporte interno.</p>
                                
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
                        </div>
                    </div>

                    <!-- Pestaña de Recursos -->
                    <div class="tab-pane fade" id="resources" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 col-lg-3 mb-4">
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
                            </div>
                            
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="resource-card">
                                    <div class="resource-icon">
                                        <i class="fas fa-video"></i>
                                    </div>
                                    <h3>Tutoriales en Video</h3>
                                    <p>Videotutoriales paso a paso para aprender a usar el sistema.</p>
                                    <a href="https://intranet.nacionaltapizados.com/tutoriales" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> Acceder
                                    </a>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="resource-card">
                                    <div class="resource-icon">
                                        <i class="fas fa-list-check"></i>
                                    </div>
                                    <h3>Checklist Diario</h3>
                                    <p>Lista de verificación para las tareas administrativas diarias.</p>
                                    <a href="checklist_diario.pdf" target="_blank">
                                        <i class="fas fa-download"></i> Descargar
                                    </a>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="resource-card">
                                    <div class="resource-icon">
                                        <i class="fas fa-headset"></i>
                                    </div>
                                    <h3>Soporte Remoto</h3>
                                    <p>Acceso a asistencia técnica remota para resolver problemas rápidamente.</p>
                                    <a href="https://soporte.nacionaltapizados.com" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> Conectar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pestaña de Procedimientos -->
                    <div class="tab-pane fade" id="procedures" role="tabpanel">
                        <div class="help-card">
                            <div class="help-card-header">
                                <div class="help-card-icon">
                                    <i class="fas fa-list-check"></i>
                                </div>
                                <h2 class="help-card-title">Procedimientos Administrativos</h2>
                            </div>
                            <div class="help-card-body">
                                <div class="faq-item">
                                    <h3><span>1</span> Cierre de Caja Diario</h3>
                                    <p>Realizar el corte de caja diario antes de las 7:00 PM. Verificar que los totales coincidan con el sistema y reportar cualquier discrepancia al departamento de contabilidad.</p>
                                </div>
                                
                                <div class="faq-item">
                                    <h3><span>2</span> Actualización de Inventario</h3>
                                    <p>Los martes y viernes se deben actualizar los niveles de inventario. Registrar productos con menos de 5 unidades en el reporte de reposición urgente.</p>
                                </div>
                                
                                <div class="faq-item">
                                    <h3><span>3</span> Respaldo de Información</h3>
                                    <p>El sistema realiza respaldos automáticos cada 24 horas. Verificar el reporte de respaldo exitoso cada mañana a las 8:00 AM.</p>
                                </div>
                                
                                <div class="faq-item">
                                    <h3><span>4</span> Reporte de Incidentes</h3>
                                    <p>Cualquier falla o problema técnico debe ser reportado inmediatamente usando el formulario de incidentes en la intranet, indicando el código de error si está disponible.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Activar tooltips de Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
</body>
</html>