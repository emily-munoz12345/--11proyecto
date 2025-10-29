<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ayuda - Gestión de Clientes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Biblioteca para generar PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.9);
            --primary-hover: rgba(140, 74, 63, 1);
            --secondary-color: rgba(108, 117, 125, 0.9);
            --text-color: #ffffff;
            --text-light: #f8f9fa;
            --text-muted: rgba(255, 255, 255, 0.85);
            --bg-transparent: rgba(30, 30, 30, 0.85);
            --bg-transparent-light: rgba(50, 50, 50, 0.7);
            --bg-input: rgba(0, 0, 0, 0.6);
            --border-color: rgba(255, 255, 255, 0.3);
            --success-color: rgba(25, 135, 84, 0.9);
            --danger-color: rgba(220, 53, 69, 0.9);
            --warning-color: rgba(255, 193, 7, 0.9);
            --info-color: rgba(13, 202, 240, 0.9);
        }

        body {
            background-image: url('https://pfst.cf2.poecdn.net/base/image/60ab54eef562f30f85a67bde31f924f078199dae0b7bc6c333dfb467a2c13471?w=1024&h=768&pmaid=442168253');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-light);
            min-height: 100vh;
            line-height: 1.6;
        }

        .main-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--bg-transparent);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
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
            font-size: 2.2rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
            color: var(--text-light);
        }

        .page-title i {
            margin-right: 12px;
            color: var(--primary-color);
        }

        .user-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .help-section {
            margin-bottom: 2.5rem;
            padding: 2rem;
            background-color: var(--bg-transparent-light);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .help-section h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
            font-weight: 600;
        }

        .help-section h3 {
            color: var(--text-light);
            margin-top: 1.8rem;
            margin-bottom: 1.2rem;
            font-weight: 600;
            border-left: 4px solid var(--primary-color);
            padding-left: 1rem;
        }

        .help-section h4 {
            color: var(--text-muted);
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .help-section p {
            color: var(--text-light);
            margin-bottom: 1rem;
            font-size: 1.05rem;
        }

        .feature-card {
            background: linear-gradient(135deg, var(--bg-transparent-light) 0%, rgba(70, 70, 70, 0.6) 100%);
            border-radius: 10px;
            padding: 1.8rem;
            margin-bottom: 1.8rem;
            border-left: 5px solid var(--primary-color);
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            border-left: 5px solid var(--primary-hover);
        }

        .feature-card h5 {
            color: var(--text-light);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .feature-card h5 i {
            margin-right: 12px;
            color: var(--primary-color);
            font-size: 1.3rem;
        }

        .step-list {
            list-style-type: none;
            padding-left: 0;
            counter-reset: step;
        }

        .step-list li {
            margin-bottom: 1.2rem;
            padding-left: 2.5rem;
            position: relative;
            color: var(--text-light);
            font-size: 1.05rem;
        }

        .step-list li:before {
            content: counter(step);
            counter-increment: step;
            position: absolute;
            left: 0;
            top: 0;
            background-color: var(--primary-color);
            color: white;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .permission-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .permission-table th,
        .permission-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-light);
        }

        .permission-table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
        }

        .permission-table tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.08);
        }

        .permission-table tr:hover {
            background-color: rgba(140, 74, 63, 0.2);
        }

        .btn-help {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            transition: all 0.3s ease;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        .btn-help:hover {
            background-color: var(--primary-hover);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .btn-pdf {
            background-color: var(--danger-color);
        }

        .btn-pdf:hover {
            background-color: rgba(220, 53, 69, 1);
        }

        .nav-tabs {
            border-bottom: 2px solid var(--border-color);
            margin-bottom: 2.5rem;
        }

        .nav-link {
            color: var(--text-muted);
            border: none;
            padding: 1rem 1.8rem;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 1.05rem;
            border-radius: 8px 8px 0 0;
        }

        .nav-link:hover {
            color: var(--text-light);
            background-color: var(--bg-transparent-light);
        }

        .nav-link.active {
            color: white;
            background-color: var(--primary-color);
            border-radius: 8px 8px 0 0;
            font-weight: 600;
        }

        .tab-content {
            padding: 1.5rem 0;
        }

        .simple-explanation {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-left: 4px solid var(--info-color);
        }

        .simple-explanation h5 {
            color: var(--info-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .warning-box {
            background-color: rgba(255, 193, 7, 0.2);
            border-left: 4px solid var(--warning-color);
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }

        .info-box {
            background-color: rgba(13, 202, 240, 0.2);
            border-left: 4px solid var(--info-color);
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }

        .quick-access {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .quick-card {
            background: linear-gradient(135deg, var(--bg-transparent-light) 0%, rgba(70, 70, 70, 0.6) 100%);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            cursor: pointer;
        }

        .quick-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            border-color: var(--primary-color);
        }

        .quick-card i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .quick-card h4 {
            color: var(--text-light);
            margin-bottom: 1rem;
        }

        .quick-card p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .role-restricted {
            display: none;
        }

        .admin-only {
            display: none;
        }

        .seller-only {
            display: none;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
                margin: 1rem;
            }

            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-title {
                font-size: 1.8rem;
            }

            .nav-link {
                padding: 0.8rem 1.2rem;
                font-size: 0.95rem;
            }

            .quick-access {
                grid-template-columns: 1fr;
            }
        }

        /* Estilos para el contenido del PDF */
        .pdf-content {
            background: white;
            color: #333;
            padding: 2rem;
            font-family: Arial, sans-serif;
        }

        .pdf-content h1, .pdf-content h2, .pdf-content h3 {
            color: #8c4a3f;
        }

        .pdf-content .feature-card {
            background: #f8f9fa;
            border-left: 5px solid #8c4a3f;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-question-circle"></i> Sistema de Ayuda
                </h1>
                <div class="user-badge mt-2">
                    <i class="fas fa-user"></i>
                    <span id="currentUser">Administrador</span>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="../dashboard.php" class="btn-help">
                    <i class="fas fa-arrow-left"></i> Volver al Sistema
                </a>
                <button class="btn-help btn-pdf" id="exportPdf">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </button>
            </div>
        </div>

        <!-- Selector de Rol (solo para demostración) -->
        <div class="help-section">
            <h3>Selecciona tu Rol en el Sistema</h3>
            <p>Esta selección determina qué información podrás ver en el sistema de ayuda:</p>
            <div class="d-flex gap-3 flex-wrap">
                <button class="btn-help" onclick="setUserRole('admin')">
                    <i class="fas fa-user-shield"></i> Soy Administrador
                </button>
                <button class="btn-help" onclick="setUserRole('seller')">
                    <i class="fas fa-user-tie"></i> Soy Vendedor
                </button>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="quick-access">
            <div class="quick-card" onclick="showTab('overview')">
                <i class="fas fa-home"></i>
                <h4>Resumen del Sistema</h4>
                <p>Conoce cómo funciona el sistema de gestión de clientes</p>
            </div>
            <div class="quick-card admin-only" onclick="showTab('admin')">
                <i class="fas fa-user-shield"></i>
                <h4>Guía del Administrador</h4>
                <p>Funciones completas de gestión del sistema</p>
            </div>
            <div class="quick-card seller-only" onclick="showTab('seller')">
                <i class="fas fa-user-tie"></i>
                <h4>Guía del Vendedor</h4>
                <p>Funciones para gestión de clientes</p>
            </div>
            <div class="quick-card" onclick="showTab('features')">
                <i class="fas fa-cogs"></i>
                <h4>Funcionalidades</h4>
                <p>Descubre todas las herramientas disponibles</p>
            </div>
            <div class="quick-card" onclick="showTab('troubleshooting')">
                <i class="fas fa-tools"></i>
                <h4>Solución de Problemas</h4>
                <p>Resuelve incidencias comunes del sistema</p>
            </div>
        </div>

        <!-- Pestañas de navegación -->
        <ul class="nav nav-tabs" id="helpTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                    <i class="fas fa-home"></i> Resumen
                </button>
            </li>
            <li class="nav-item admin-only" role="presentation">
                <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab">
                    <i class="fas fa-user-shield"></i> Administrador
                </button>
            </li>
            <li class="nav-item seller-only" role="presentation">
                <button class="nav-link" id="seller-tab" data-bs-toggle="tab" data-bs-target="#seller" type="button" role="tab">
                    <i class="fas fa-user-tie"></i> Vendedor
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="features-tab" data-bs-toggle="tab" data-bs-target="#features" type="button" role="tab">
                    <i class="fas fa-cogs"></i> Funcionalidades
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="troubleshooting-tab" data-bs-toggle="tab" data-bs-target="#troubleshooting" type="button" role="tab">
                    <i class="fas fa-tools"></i> Problemas
                </button>
            </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="helpTabsContent">
            <!-- Pestaña de Resumen -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="help-section">
                    <h2>Sistema de Gestión de Clientes - Nacional Tapizados</h2>
                    <p>Este sistema permite gestionar de manera eficiente la información de los clientes de <strong>Nacional Tapizados</strong>, empresa especializada en tapicería automotriz con más de 25 años de experiencia.</p>
                    
                    <div class="info-box">
                        <h4><i class="fas fa-info-circle"></i> Información Importante</h4>
                        <p>El sistema está diseñado específicamente para el negocio de tapicería automotriz, permitiendo gestionar clientes que solicitan servicios como tapizado completo, reparaciones, impermeabilización y personalización de vehículos.</p>
                    </div>
                    
                    <div class="feature-card">
                        <h5><i class="fas fa-users"></i> Perfiles de Usuario</h5>
                        <p>El sistema cuenta con dos tipos de usuarios principales con permisos diferenciados:</p>
                        
                        <table class="permission-table">
                            <thead>
                                <tr>
                                    <th>Función</th>
                                    <th>Administrador</th>
                                    <th>Vendedor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Crear clientes</td>
                                    <td><i class="fas fa-check text-success"></i></td>
                                    <td><i class="fas fa-check text-success"></i></td>
                                </tr>
                                <tr>
                                    <td>Editar todos los clientes</td>
                                    <td><i class="fas fa-check text-success"></i></td>
                                    <td><i class="fas fa-check text-success"></i></td>
                                </tr>
                                <tr>
                                    <td>Eliminar permanentemente</td>
                                    <td><i class="fas fa-check text-success"></i></td>
                                    <td><i class="fas fa-times text-danger"></i></td>
                                </tr>
                                <tr>
                                    <td>Gestionar papelera</td>
                                    <td><i class="fas fa-check text-success"></i></td>
                                    <td><i class="fas fa-times text-danger"></i></td>
                                </tr>
                                <tr>
                                    <td>Ver historial completo</td>
                                    <td><i class="fas fa-check text-success"></i></td>
                                    <td><i class="fas fa-times text-danger"></i></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <h3>¿Cómo Funciona el Sistema?</h3>
                    <div class="simple-explanation">
                        <h5><i class="fas fa-lightbulb"></i> Explicación Sencilla</h5>
                        <p>Imagina que el sistema es como un <strong>archivador digital</strong> donde guardas información de todos tus clientes. En lugar de tener papeles, tienes registros digitales que puedes:</p>
                        <ul>
                            <li><strong>Buscar</strong> rápidamente por nombre, teléfono o correo</li>
                            <li><strong>Agregar</strong> nuevos clientes fácilmente</li>
                            <li><strong>Actualizar</strong> información cuando cambien sus datos</li>
                            <li><strong>Archivar</strong> clientes que ya no están activos</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Pestaña de Administrador -->
            <div class="tab-pane fade admin-only" id="admin" role="tabpanel">
                <div class="help-section">
                    <h2>Funciones del Administrador</h2>
                    <p>Como administrador, tienes acceso completo a todas las funcionalidades del sistema.</p>
                    
                    <div class="info-box">
                        <h4><i class="fas fa-user-cog"></i> Acceso Completo</h4>
                        <p>Los administradores pueden realizar cualquier acción en el sistema, incluyendo la gestión de otros usuarios, configuración del sistema y eliminación permanente de registros.</p>
                    </div>
                    
                    <h3>Gestión Completa de Clientes</h3>
                    <ol class="step-list">
                        <li><strong>Ver todos los clientes:</strong> Accede a la pestaña "Buscar" para ver la lista completa de clientes activos.</li>
                        <li><strong>Crear nuevo cliente:</strong> Haz clic en "Nuevo Cliente" para agregar un cliente al sistema.</li>
                        <li><strong>Editar información:</strong> Selecciona un cliente y haz clic en "Editar" para modificar sus datos.</li>
                        <li><strong>Gestionar eliminaciones:</strong> Puedes mover clientes a la papelera o eliminarlos permanentemente.</li>
                    </ol>
                    
                    <h3>Gestión de la Papelera</h3>
                    <p>Como administrador, tienes control total sobre la papelera del sistema:</p>
                    
                    <div class="feature-card">
                        <h5><i class="fas fa-trash-restore"></i> Funciones de Papelera</h5>
                        <ul>
                            <li><strong>Restaurar clientes:</strong> Recupera clientes que han sido movidos a la papelera.</li>
                            <li><strong>Eliminar permanentemente:</strong> Borra definitivamente clientes de la papelera.</li>
                            <li><strong>Vaciar papelera:</strong> Elimina todos los clientes de la papelera de una vez.</li>
                        </ul>
                    </div>
                    
                    <div class="warning-box">
                        <h4><i class="fas fa-exclamation-triangle"></i> Advertencia Importante</h4>
                        <p>Las eliminaciones permanentes no se pueden deshacer. Asegúrate de verificar dos veces antes de eliminar registros permanentemente.</p>
                    </div>
                </div>
            </div>

            <!-- Pestaña de Vendedor -->
            <div class="tab-pane fade seller-only" id="seller" role="tabpanel">
                <div class="help-section">
                    <h2>Funciones del Vendedor</h2>
                    <p>Como vendedor, tienes acceso para gestionar la información de clientes en el sistema.</p>
                    
                    <div class="info-box">
                        <h4><i class="fas fa-user-check"></i> Acceso Específico</h4>
                        <p>Los vendedores pueden gestionar clientes pero con restricciones para proteger la integridad de los datos del sistema.</p>
                    </div>
                    
                    <h3>Gestión Básica de Clientes</h3>
                    <ol class="step-list">
                        <li><strong>Ver clientes activos:</strong> Accede a la pestaña "Buscar" para ver todos los clientes del sistema.</li>
                        <li><strong>Crear nuevo cliente:</strong> Usa el botón "Nuevo Cliente" para registrar clientes.</li>
                        <li><strong>Editar información:</strong> Modifica datos de clientes existentes según sea necesario.</li>
                        <li><strong>Buscar clientes:</strong> Utiliza la función de búsqueda en tiempo real.</li>
                        <li><strong>Mover a papelera:</strong> Puedes eliminar clientes moviéndolos a la papelera.</li>
                    </ol>
                    
                    <h3>Restricciones del Perfil Vendedor</h3>
                    <p>Por seguridad del sistema, como vendedor no puedes:</p>
                    
                    <div class="feature-card">
                        <h5><i class="fas fa-ban"></i> Acciones Restringidas</h5>
                        <ul>
                            <li>Acceder a configuraciones del sistema</li>
                            <li>Gestionar otros usuarios o vendedores</li>
                            <li>Eliminar registros permanentemente</li>
                            <li>Vaciar la papelera del sistema</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Pestaña de Funcionalidades -->
            <div class="tab-pane fade" id="features" role="tabpanel">
                <div class="help-section">
                    <h2>Funcionalidades Detalladas del Sistema</h2>
                    
                    <h3>Gestión de Clientes</h3>
                    
                    <div class="feature-card">
                        <h5><i class="fas fa-search-plus"></i> Búsqueda Avanzada</h5>
                        <p>Busca clientes por nombre, teléfono o correo electrónico.</p>
                        <div class="simple-explanation">
                            <h5><i class="fas fa-lightbulb"></i> ¿Cómo funciona?</h5>
                            <p>Es como buscar un contacto en tu teléfono: escribes parte del nombre, número o correo y el sistema te muestra los resultados que coinciden. Solo necesitas escribir al menos 2 letras para que empiece a buscar.</p>
                        </div>
                    </div>
                    
                    <div class="feature-card">
                        <h5><i class="fas fa-user-plus"></i> Crear Nuevo Cliente</h5>
                        <p>Agrega nuevos clientes al sistema completando un formulario sencillo.</p>
                        <div class="simple-explanation">
                            <h5><i class="fas fa-lightbulb"></i> ¿Cómo funciona?</h5>
                            <p>Es como llenar una ficha de cliente en papel, pero de forma digital. Solo necesitas completar los campos básicos como nombre, teléfono y correo. El sistema se encarga de guardar todo de forma segura.</p>
                        </div>
                    </div>
                    
                    <div class="feature-card">
                        <h5><i class="fas fa-edit"></i> Editar Información</h5>
                        <p>Modifica la información de clientes existentes cuando cambien sus datos.</p>
                        <div class="simple-explanation">
                            <h5><i class="fas fa-lightbulb"></i> ¿Cómo funciona?</h5>
                            <p>Si un cliente cambia de teléfono o correo, puedes actualizar su información fácilmente. El sistema recuerda quién hizo cada cambio y cuándo, como un historial de modificaciones.</p>
                        </div>
                    </div>
                    
                    <div class="feature-card">
                        <h5><i class="fas fa-trash-alt"></i> Sistema de Papelera</h5>
                        <p>Los clientes eliminados se envían a la papelera antes de borrarse permanentemente.</p>
                        <div class="simple-explanation">
                            <h5><i class="fas fa-lightbulb"></i> ¿Cómo funciona?</h5>
                            <p>Es como la papelera de reciclaje de tu computadora. Cuando eliminas un cliente, no se borra inmediatamente, sino que va a la papelera donde puedes recuperarlo si te equivocaste. Solo los administradores pueden vaciar la papelera definitivamente.</p>
                        </div>
                    </div>
                    
                    <h3>Estadísticas y Reportes</h3>
                    <div class="feature-card">
                        <h5><i class="fas fa-chart-bar"></i> Panel de Estadísticas</h5>
                        <p>Visualiza métricas importantes del negocio en tiempo real.</p>
                        <div class="simple-explanation">
                            <h5><i class="fas fa-lightbulb"></i> ¿Cómo funciona?</h5>
                            <p>El sistema te muestra números importantes como cuántos clientes tienes, cuántos se registraron hoy, etc. Es como un tablero de control que te da una vista rápida de cómo va el negocio.</p>
                        </div>
                    </div>
                    
                    <h3>Seguridad del Sistema</h3>
                    <div class="feature-card">
                        <h5><i class="fas fa-user-lock"></i> Control de Accesos</h5>
                        <p>Diferentes niveles de permisos según el rol del usuario.</p>
                        <div class="simple-explanation">
                            <h5><i class="fas fa-lightbulb"></i> ¿Cómo funciona?</h5>
                            <p>Es como tener llaves diferentes para diferentes puertas. Los vendedores tienen llaves para las funciones básicas, mientras que los administradores tienen llaves maestras para todo el sistema.</p>
                        </div>
                    </div>
                    
                    <div class="info-box admin-only">
                        <h4><i class="fas fa-shield-alt"></i> Información Técnica para Administradores</h4>
                        <p>El sistema utiliza una base de datos segura con tablas especializadas para clientes, usuarios y registro de actividades. Todas las eliminaciones se registran en la tabla <code>registro_eliminaciones</code> para mantener trazabilidad completa.</p>
                    </div>
                </div>
            </div>

            <!-- Pestaña de Solución de Problemas -->
            <div class="tab-pane fade" id="troubleshooting" role="tabpanel">
                <div class="help-section">
                    <h2>Solución de Problemas y Preguntas Frecuentes</h2>
                    
                    <h3>Problemas Comunes de Acceso</h3>
                    
                    <div class="feature-card">
                        <h5><i class="fas fa-sign-in-alt"></i> No puedo iniciar sesión</h5>
                        <p>Si tienes problemas para acceder al sistema:</p>
                        <ol class="step-list">
                            <li>Verifica que tu teclado no esté en mayúsculas</li>
                            <li>Asegúrate de estar usando las credenciales correctas</li>
                            <li>Comprueba que tu usuario esté activo en el sistema</li>
                            <li>Si persiste el problema, contacta al administrador</li>
                        </ol>
                    </div>
                    
                    <div class="feature-card">
                        <h5><i class="fas fa-exclamation-triangle"></i> Error al cargar una página</h5>
                        <p>Si una página no carga correctamente:</p>
                        <ol class="step-list">
                            <li>Actualiza la página (F5)</li>
                            <li>Limpia la caché de tu navegador</li>
                            <li>Verifica tu conexión a internet</li>
                            <li>Intenta acceder desde otro navegador</li>
                        </ol>
                    </div>
                    
                    <h3>Problemas con Datos de Clientes</h3>
                    
                    <div class="feature-card">
                        <h5><i class="fas fa-user-times"></i> No puedo encontrar un cliente</h5>
                        <p>Si un cliente no aparece en las búsquedas:</p>
                        <ol class="step-list">
                            <li>Verifica que estés buscando con el nombre correcto</li>
                            <li>Intenta buscar por teléfono o correo electrónico</li>
                            <li>Comprueba si el cliente fue movido a la papelera</li>
                            <li>Verifica que tengas permisos para ver ese cliente</li>
                        </ol>
                    </div>
                    
                    <h3>Contacto de Soporte Técnico</h3>
                    <p>Para problemas técnicos o preguntas sobre el sistema:</p>
                    
                    <div class="feature-card">
                        <h5><i class="fas fa-headset"></i> Canales de Soporte</h5>
                        <ul>
                            <li><strong>Email:</strong> soporte@nacionaltapizados.com</li>
                            <li><strong>Teléfono:</strong> +57 1 234 5678</li>
                            <li><strong>Horario de atención:</strong> Lunes a Viernes 8:00am - 6:00pm</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido para PDF (oculto) -->
    <div id="pdfContent" style="display: none;">
        <div class="pdf-content">
            <h1>Manual de Usuario - Sistema de Gestión de Clientes</h1>
            <h2>Nacional Tapizados</h2>
            <p><strong>Fecha de generación:</strong> <span id="pdfDate"></span></p>
            <p><strong>Usuario:</strong> <span id="pdfUser"></span></p>
            
            <div id="pdfSections">
                <!-- El contenido se generará dinámicamente según el rol -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Estado inicial - por defecto mostramos todo (rol admin)
        let currentRole = 'admin';
        
        // Función para establecer el rol del usuario
        function setUserRole(role) {
            currentRole = role;
            updateUIForRole();
            
            // Mostrar mensaje de confirmación
            const roleName = role === 'admin' ? 'Administrador' : 'Vendedor';
            alert(`Ahora estás viendo el sistema como: ${roleName}`);
        }
        
        // Función para actualizar la interfaz según el rol
        function updateUIForRole() {
            const isAdmin = currentRole === 'admin';
            const isSeller = currentRole === 'seller';
            
            // Actualizar badge de usuario
            document.getElementById('currentUser').textContent = isAdmin ? 'Administrador' : 'Vendedor';
            
            // Mostrar/ocultar elementos según el rol
            document.querySelectorAll('.admin-only').forEach(el => {
                el.style.display = isAdmin ? 'block' : 'none';
            });
            
            document.querySelectorAll('.seller-only').forEach(el => {
                el.style.display = isSeller ? 'block' : 'none';
            });
            
            // Si es vendedor, activar la pestaña de resumen por defecto
            if (isSeller) {
                document.getElementById('overview-tab').click();
            }
        }
        
        // Función para mostrar una pestaña específica
        function showTab(tabId) {
            document.getElementById(`${tabId}-tab`).click();
        }
        
        // Función para generar el PDF
        document.getElementById('exportPdf').addEventListener('click', function() {
            generatePdf();
        });
        
        function generatePdf() {
            // Configurar fecha actual
            const now = new Date();
            document.getElementById('pdfDate').textContent = now.toLocaleDateString('es-ES');
            document.getElementById('pdfUser').textContent = currentRole === 'admin' ? 'Administrador' : 'Vendedor';
            
            // Obtener el contenido según el rol
            const pdfSections = document.getElementById('pdfSections');
            pdfSections.innerHTML = '';
            
            // Agregar resumen (siempre visible)
            const overviewContent = document.getElementById('overview').cloneNode(true);
            cleanContentForPdf(overviewContent);
            pdfSections.appendChild(overviewContent);
            
            // Agregar contenido específico del rol
            if (currentRole === 'admin') {
                const adminContent = document.getElementById('admin').cloneNode(true);
                cleanContentForPdf(adminContent);
                pdfSections.appendChild(adminContent);
            } else {
                const sellerContent = document.getElementById('seller').cloneNode(true);
                cleanContentForPdf(sellerContent);
                pdfSections.appendChild(sellerContent);
            }
            
            // Agregar funcionalidades (siempre visible)
            const featuresContent = document.getElementById('features').cloneNode(true);
            cleanContentForPdf(featuresContent);
            pdfSections.appendChild(featuresContent);
            
            // Agregar solución de problemas (siempre visible)
            const troubleshootingContent = document.getElementById('troubleshooting').cloneNode(true);
            cleanContentForPdf(troubleshootingContent);
            pdfSections.appendChild(troubleshootingContent);
            
            // Configurar opciones del PDF
            const options = {
                margin: 10,
                filename: `manual-usuario-${currentRole}-${now.toISOString().split('T')[0]}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            
            // Generar el PDF
            html2pdf().set(options).from(document.getElementById('pdfContent')).save();
        }
        
        // Función para limpiar el contenido para PDF
        function cleanContentForPdf(element) {
            // Remover elementos interactivos
            element.querySelectorAll('button, .nav-tabs, .quick-access').forEach(el => {
                el.remove();
            });
            
            // Ajustar estilos para PDF
            element.querySelectorAll('.help-section').forEach(el => {
                el.style.marginBottom = '20px';
                el.style.padding = '15px';
                el.style.border = '1px solid #ddd';
            });
            
            // Asegurar que el contenido sea visible
            element.style.display = 'block';
        }
        
        // Inicializar la interfaz
        document.addEventListener('DOMContentLoaded', function() {
            updateUIForRole();
        });
    </script>
</body>
</html>