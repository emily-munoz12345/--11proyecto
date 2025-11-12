<?php
require_once __DIR__ . '/includes/head.php';
$title = 'Nuestros Servicios - Nacional Tapizados';
?>

<?php
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h1 class="fw-bold">Nuestros Servicios Especializados</h1>
            <p class="lead text-muted">Soluciones integrales para la tapicería de tu vehículo</p>
        </div>

        <!-- Filtros de Servicios -->
        <div class="row mb-5">
            <div class="col-md-10 mx-auto text-center">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-dark service-filter-btn active" data-filter="all">Todos</button>
                    <button type="button" class="btn btn-outline-dark service-filter-btn" data-filter="tapizado">Tapizado Completo</button>
                    <button type="button" class="btn btn-outline-dark service-filter-btn" data-filter="reparaciones">Reparaciones</button>
                    <button type="button" class="btn btn-outline-dark service-filter-btn" data-filter="limpieza">Limpieza</button>
                    <button type="button" class="btn btn-outline-dark service-filter-btn" data-filter="personalizacion">Personalización</button>
                </div>
            </div>
        </div>

        <!-- Galería de Servicios -->
        <div class="row g-4 mb-5" id="servicios-container">
            <!-- Servicio 1 - Tapizado -->
            <div class="col-lg-6 service-item" data-category="tapizado">
                <div class="card floating-card border-0 shadow-lg h-100 card-tapizado">
                    <div class="row g-0 h-100">
                        <div class="col-md-5">
                            <img src="assets/images/servicio-tapizado.jpg" class="img-fluid w-100 h-100 object-fit-cover" alt="Tapizado completo" style="min-height: 250px;">
                        </div>
                        <div class="col-md-7 d-flex align-items-center">
                            <div class="card-body p-4">
                                <h3 class="h5 mb-3">Tapizado Completo</h3>
                                <p class="card-text mb-3">Renovación total de los asientos, puertas, techo y demás superficies textiles de tu vehículo.</p>
                                <ul class="list-unstyled mb-3">
                                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Reemplazo completo de materiales</li>
                                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Diseño personalizado</li>
                                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Materiales a elección</li>
                                    <li class="mb-0"><i class="fas fa-check text-primary me-2"></i> Garantía de 2 años</li>
                                </ul>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-primary">3-7 días</span>
                                    <small class="text-muted">Desde $500.000</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Servicio 2 - Reparaciones -->
            <div class="col-lg-6 service-item" data-category="reparaciones">
                <div class="card floating-card border-0 shadow-lg h-100 card-reparaciones">
                    <div class="row g-0 h-100">
                        <div class="col-md-5">
                            <img src="assets/images/servicio-reparacion.jpg" class="img-fluid w-100 h-100 object-fit-cover" alt="Reparaciones" style="min-height: 250px;">
                        </div>
                        <div class="col-md-7 d-flex align-items-center">
                            <div class="card-body p-4">
                                <h3 class="h5 mb-3">Reparaciones Especializadas</h3>
                                <p class="card-text mb-3">Soluciones profesionales para roturas, desgastes y daños en la tapicería.</p>
                                <ul class="list-unstyled mb-3">
                                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Parches invisibles</li>
                                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Reconstrucción de espumas</li>
                                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Reemplazo de secciones</li>
                                    <li class="mb-0"><i class="fas fa-check text-primary me-2"></i> Garantía de 1 año</li>
                                </ul>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-primary">1-3 días</span>
                                    <small class="text-muted">Desde $80.000</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Servicio 3 - Limpieza -->
            <div class="col-lg-6 service-item" data-category="limpieza">
                <div class="card floating-card border-0 shadow-lg h-100 card-limpieza">
                    <div class="row g-0 h-100">
                        <div class="col-md-5">
                            <img src="assets/images/servicio-limpieza.jpg" class="img-fluid w-100 h-100 object-fit-cover" alt="Limpieza profesional" style="min-height: 250px;">
                        </div>
                        <div class="col-md-7 d-flex align-items-center">
                            <div class="card-body p-4">
                                <h3 class="h5 mb-3">Limpieza Profesional</h3>
                                <p class="card-text mb-3">Eliminación de manchas, olores y recuperación del color original.</p>
                                <ul class="list-unstyled mb-3">
                                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Tratamiento anti-manchas</li>
                                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Eliminación de olores</li>
                                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Hidratación de pieles</li>
                                    <li class="mb-0"><i class="fas fa-check text-primary me-2"></i> Protección UV</li>
                                </ul>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-primary">4-8 horas</span>
                                    <small class="text-muted">Desde $50.000</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Servicio 4 - Personalización -->
            <div class="col-lg-6 service-item" data-category="personalizacion">
                <div class="card floating-card border-0 shadow-lg h-100 card-personalizacion">
                    <div class="row g-0 h-100">
                        <div class="col-md-5">
                            <img src="assets/images/servicio-personalizacion.jpg" class="img-fluid w-100 h-100 object-fit-cover" alt="Personalización" style="min-height: 250px;">
                        </div>
                        <div class="col-md-7 d-flex align-items-center">
                            <div class="card-body p-4">
                                <h3 class="h5 mb-3">Personalización Premium</h3>
                                <p class="card-text mb-3">Diseños exclusivos para darle un toque único a tu vehículo.</p>
                                <ul class="list-unstyled mb-3">
                                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Costuras contrastantes</li>
                                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Logos bordados</li>
                                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Combinación de materiales</li>
                                    <li class="mb-0"><i class="fas fa-check text-primary me-2"></i> Diseño exclusivo</li>
                                </ul>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-primary">5-10 días</span>
                                    <small class="text-muted">Desde $300.000</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Proceso de Trabajo - Diseño Creativo -->
    <div class="process-wrapper py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-uppercase">Nuestro Proceso de Trabajo</h2>
                <p class="lead">Nuestra cadena de valor en tapicería automotriz</p>
            </div>

            <!-- Proceso Visual -->
            <div class="process-chain d-flex justify-content-between align-items-center">
                <!-- Paso 1 -->
                <div class="process-step process-tangible">
                    <div class="process-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="process-content">
                        <h4>Evaluación</h4>
                        <p class="small text-muted">Diagnóstico detallado del vehículo</p>
                    </div>
                    <div class="process-connector"></div>
                </div>

                <!-- Paso 2 -->
                <div class="process-step process-intangible">
                    <div class="process-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="process-content">
                        <h4>Cotización</h4>
                        <p class="small text-muted">Presupuesto transparente</p>
                    </div>
                    <div class="process-connector"></div>
                </div>

                <!-- Paso 3 -->
                <div class="process-step process-tangible">
                    <div class="process-icon">
                        <i class="fas fa-thumbs-up"></i>
                    </div>
                    <div class="process-content">
                        <h4>Aprobación</h4>
                        <p class="small text-muted">Selección de materiales</p>
                    </div>
                    <div class="process-connector"></div>
                </div>

                <!-- Paso 4 -->
                <div class="process-step process-intangible">
                    <div class="process-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="process-content">
                        <h4>Ejecución</h4>
                        <p class="small text-muted">Trabajo artesanal</p>
                    </div>
                    <div class="process-connector"></div>
                </div>

                <!-- Paso 5 -->
                <div class="process-step process-tangible">
                    <div class="process-icon">
                        <i class="fas fa-car-side"></i>
                    </div>
                    <div class="process-content">
                        <h4>Entrega</h4>
                        <p class="small text-muted">Con garantía documentada</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Agrega jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Manejar el clic en los botones de filtro de servicios
    $('.service-filter-btn').click(function() {
        // Remover la clase active de todos los botones
        $('.service-filter-btn').removeClass('active');
        // Agregar la clase active al botón clickeado
        $(this).addClass('active');
        
        // Obtener el valor del filtro
        var filter = $(this).data('filter');
        
        // Mostrar todos los elementos si el filtro es 'all'
        if (filter === 'all') {
            $('.service-item').show();
        } else {
            // Ocultar todos los elementos
            $('.service-item').hide();
            // Mostrar solo los elementos con la categoría correspondiente
            $('.service-item[data-category="' + filter + '"]').show();
        }
    });
});
</script>

<style>
.object-fit-cover {
    object-fit: cover;
}

.floating-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
}

.floating-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15) !important;
}

.card-body {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
}

/* Colores para cada categoría de servicios */
.card-tapizado:hover::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #3498db, #2980b9);
    z-index: 1;
}

.card-reparaciones:hover::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #e74c3c, #c0392b);
    z-index: 1;
}

.card-limpieza:hover::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #27ae60, #229954);
    z-index: 1;
}

.card-personalizacion:hover::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #9b59b6, #8e44ad);
    z-index: 1;
}

/* Animación suave al cargar */
.service-item {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Efecto de escalado suave en la imagen */
.floating-card img {
    transition: transform 0.3s ease;
}

.floating-card:hover img {
    transform: scale(1.03);
}

/* Mejorar el espaciado interno */
.card-body {
    position: relative;
    z-index: 2;
}

/* Estilos adicionales para destacar las categorías */
.card-tapizado .card-body h3 {
    color: #2980b9;
}

.card-reparaciones .card-body h3 {
    color: #c0392b;
}

.card-limpieza .card-body h3 {
    color: #229954;
}

.card-personalizacion .card-body h3 {
    color: #8e44ad;
}

/* Efectos de sombra específicos para cada categoría */
.card-tapizado:hover {
    box-shadow: 0 12px 35px rgba(52, 152, 219, 0.2) !important;
}

.card-reparaciones:hover {
    box-shadow: 0 12px 35px rgba(231, 76, 60, 0.2) !important;
}

.card-limpieza:hover {
    box-shadow: 0 12px 35px rgba(39, 174, 96, 0.2) !important;
}

.card-personalizacion:hover {
    box-shadow: 0 12px 35px rgba(155, 89, 182, 0.2) !important;
}

/* Estilos para el proceso de trabajo */
.process-step {
    text-align: center;
    position: relative;
    flex: 1;
    padding: 20px;
}

.process-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 15px;
    background: linear-gradient(135deg, #8c4a3f, #a05a4f);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
    box-shadow: 0 5px 15px rgba(140, 74, 63, 0.3);
    transition: all 0.3s ease;
}

.process-step:hover .process-icon {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(140, 74, 63, 0.4);
}

.process-content h4 {
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
}

.process-connector {
    position: absolute;
    top: 40px;
    right: -30px;
    width: 60px;
    height: 2px;
    background: linear-gradient(90deg, #8c4a3f, #a05a4f);
    z-index: 1;
}

.process-step:last-child .process-connector {
    display: none;
}

.process-tangible .process-icon {
    background: linear-gradient(135deg, #8c4a3f, #a05a4f);
}

.process-intangible .process-icon {
    background: linear-gradient(135deg, #2c3e50, #34495e);
}

/* Responsive para el proceso */
@media (max-width: 768px) {
    .process-chain {
        flex-direction: column;
        gap: 30px;
    }
    
    .process-connector {
        display: none;
    }
    
    .process-step {
        width: 100%;
    }
    
    .floating-card .row {
        flex-direction: column;
    }
    
    .floating-card .col-md-5 {
        height: 200px;
    }
}
</style>

<?php
require_once __DIR__ . '/includes/footer.php';
    include '../admin/includes/bot.php'; ?>
    <script>
 setHelpModule('Inicio');
</script>