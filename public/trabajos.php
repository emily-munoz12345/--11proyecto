<?php
require_once __DIR__ . '/includes/head.php';
$title = 'Trabajos Realizados - Nacional Tapizados';
?>

<?php
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h1 class="fw-bold">Nuestros Trabajos Realizados</h1>
            <p class="lead text-muted">Ejemplos de nuestra calidad artesanal en tapicería automotriz</p>
        </div>

        <!-- Filtros -->
        <div class="row mb-5">
            <div class="col-md-8 mx-auto text-center">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-dark filter-btn active" data-filter="all">Todos</button>
                    <button type="button" class="btn btn-outline-dark filter-btn" data-filter="tapizado">Tapizado Completo</button>
                    <button type="button" class="btn btn-outline-dark filter-btn" data-filter="reparaciones">Reparaciones</button>
                    <button type="button" class="btn btn-outline-dark filter-btn" data-filter="personalizacion">Personalización</button>
                </div>
            </div>
        </div>

        <!-- Galería de Trabajos -->
        <div class="row" id="trabajos-container">
            <!-- Trabajo 1 - Tapizado (Imagen izquierda, texto derecha) -->
            <div class="col-12 trabajo-item mb-5" data-category="tapizado">
                <div class="card floating-card border-0 shadow-lg h-100 card-tapizado">
                    <div class="row g-0 h-100">
                        <div class="col-md-6">
                            <img src="assets/images/trabajo1.jpg" class="img-fluid w-100 h-100 object-fit-cover" alt="Tapizado en piel - Mercedes-Benz" style="min-height: 350px;">
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="card-body p-5">
                                <h3 class="h4 mb-3">Tapizado en Piel Premium</h3>
                                <p class="text-muted mb-3"><strong>Vehículo:</strong> Mercedes-Benz Clase S 2020</p>
                                <p class="card-text mb-4">Renovación completa de asientos y paneles de puertas en piel italiana de primera calidad, con costura francesa y detalles personalizados.</p>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2"><strong>Materiales:</strong> Piel italiana premium, hilos reforzados</li>
                                    <li class="mb-2"><strong>Tiempo:</strong> 5 días laborales</li>
                                    <li class="mb-0"><strong>Garantía:</strong> 2 años</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trabajo 2 - Reparaciones (Texto izquierda, imagen derecha) -->
            <div class="col-12 trabajo-item mb-5" data-category="reparaciones">
                <div class="card floating-card border-0 shadow-lg h-100 card-reparaciones">
                    <div class="row g-0 h-100">
                        <div class="col-md-6 order-md-2">
                            <img src="assets/images/trabajo2.jpg" class="img-fluid w-100 h-100 object-fit-cover" alt="Restauración clásico - Chevrolet Camaro" style="min-height: 350px;">
                        </div>
                        <div class="col-md-6 order-md-1 d-flex align-items-center">
                            <div class="card-body p-5">
                                <h3 class="h4 mb-3">Restauración Completa</h3>
                                <p class="text-muted mb-3"><strong>Vehículo:</strong> Chevrolet Camaro 1969</p>
                                <p class="card-text mb-4">Restauración fiel al diseño original con materiales modernos que mantienen el aspecto vintage pero con mayor durabilidad.</p>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2"><strong>Materiales:</strong> Vinilo con textura similar al original</li>
                                    <li class="mb-2"><strong>Tiempo:</strong> 8 días laborales</li>
                                    <li class="mb-0"><strong>Garantía:</strong> 3 años</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trabajo 3 - Personalización (Imagen izquierda, texto derecha) -->
            <div class="col-12 trabajo-item mb-5" data-category="personalizacion">
                <div class="card floating-card border-0 shadow-lg h-100 card-personalizacion">
                    <div class="row g-0 h-100">
                        <div class="col-md-6">
                            <img src="assets/images/trabajo3.jpg" class="img-fluid w-100 h-100 object-fit-cover" alt="Personalización - Ford Mustang" style="min-height: 350px;">
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="card-body p-5">
                                <h3 class="h4 mb-3">Personalización Deportiva</h3>
                                <p class="text-muted mb-3"><strong>Vehículo:</strong> Ford Mustang GT 2018</p>
                                <p class="card-text mb-4">Diseño personalizado con combinación de piel alcántara y cuero perforado, con costuras contrastantes y logo bordado.</p>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2"><strong>Materiales:</strong> Piel alcántara, cuero perforado</li>
                                    <li class="mb-2"><strong>Tiempo:</strong> 6 días laborales</li>
                                    <li class="mb-0"><strong>Garantía:</strong> 2 años</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Agrega jQuery si no lo tienes ya -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Manejar el clic en los botones de filtro
    $('.filter-btn').click(function() {
        // Remover la clase active de todos los botones
        $('.filter-btn').removeClass('active');
        // Agregar la clase active al botón clickeado
        $(this).addClass('active');
        
        // Obtener el valor del filtro
        var filter = $(this).data('filter');
        
        // Mostrar todos los elementos si el filtro es 'all'
        if (filter === 'all') {
            $('.trabajo-item').show();
        } else {
            // Ocultar todos los elementos
            $('.trabajo-item').hide();
            // Mostrar solo los elementos con la categoría correspondiente
            $('.trabajo-item[data-category="' + filter + '"]').show();
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

/* Colores para cada categoría */
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
.trabajo-item {
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

.card-personalizacion:hover {
    box-shadow: 0 12px 35px rgba(155, 89, 182, 0.2) !important;
}
</style>

<?php
require_once __DIR__ . '/includes/footer.php';
    include '../admin/includes/bot.php'; ?>
    <script>
 setHelpModule('Inicio');
</script>