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
        <div class="row mb-4">
            <div class="col-md-8 mx-auto text-center">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary filter-btn active" data-filter="all">Todos</button>
                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="tapizado">Tapizado Completo</button>
                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="reparaciones">Reparaciones</button>
                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="personalizacion">Personalización</button>
                </div>
            </div>
        </div>

        <!-- Galería de Trabajos -->
        <div class="row g-4" id="trabajos-container">
            <!-- Trabajo 1 - Tapizado -->
            <div class="col-md-4 trabajo-item" data-category="tapizado">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="assets/images/trabajo1.jpg" class="card-img-top" alt="Tapizado en piel - Mercedes-Benz">
                    <div class="card-body">
                        <h3 class="h5">Tapizado en Piel Premium</h3>
                        <p class="text-muted mb-2"><strong>Vehículo:</strong> Mercedes-Benz Clase S 2020</p>
                        <p class="card-text">Renovación completa de asientos y paneles de puertas en piel italiana de primera calidad, con costura francesa y detalles personalizados.</p>
                        <ul class="list-unstyled">
                            <li><strong>Materiales:</strong> Piel italiana premium, hilos reforzados</li>
                            <li><strong>Tiempo:</strong> 5 días laborales</li>
                            <li><strong>Garantía:</strong> 2 años</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Trabajo 2 - Reparaciones -->
            <div class="col-md-4 trabajo-item" data-category="reparaciones">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="assets/images/trabajo2.jpg" class="card-img-top" alt="Restauración clásico - Chevrolet Camaro">
                    <div class="card-body">
                        <h3 class="h5">Restauración Completa</h3>
                        <p class="text-muted mb-2"><strong>Vehículo:</strong> Chevrolet Camaro 1969</p>
                        <p class="card-text">Restauración fiel al diseño original con materiales modernos que mantienen el aspecto vintage pero con mayor durabilidad.</p>
                        <ul class="list-unstyled">
                            <li><strong>Materiales:</strong> Vinilo con textura similar al original</li>
                            <li><strong>Tiempo:</strong> 8 días laborales</li>
                            <li><strong>Garantía:</strong> 3 años</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Trabajo 3 - Personalización -->
            <div class="col-md-4 trabajo-item" data-category="personalizacion">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="assets/images/trabajo3.jpg" class="card-img-top" alt="Personalización - Ford Mustang">
                    <div class="card-body">
                        <h3 class="h5">Personalización Deportiva</h3>
                        <p class="text-muted mb-2"><strong>Vehículo:</strong> Ford Mustang GT 2018</p>
                        <p class="card-text">Diseño personalizado con combinación de piel alcántara y cuero perforado, con costuras contrastantes y logo bordado.</p>
                        <ul class="list-unstyled">
                            <li><strong>Materiales:</strong> Piel alcántara, cuero perforado</li>
                            <li><strong>Tiempo:</strong> 6 días laborales</li>
                            <li><strong>Garantía:</strong> 2 años</li>
                        </ul>
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

<?php
require_once __DIR__ . '/includes/footer.php';
?>