<?php
require_once __DIR__ . '/../includes/head.php';
$title = 'Trabajos Realizados - Nacional Tapizados';
?>

<?php
require_once __DIR__ . '/../includes/navbar.php';
?>

<main class="py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h1 class="fw-bold">Nuestros Trabajos Realizados</h1>
            <p class="lead text-muted">Ejemplos de nuestra calidad artesanal en tapicería automotriz</p>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-6 mx-auto text-center">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary active">Todos</button>
                    <button type="button" class="btn btn-outline-primary">Tapizado Completo</button>
                    <button type="button" class="btn btn-outline-primary">Reparaciones</button>
                    <button type="button" class="btn btn-outline-primary">Personalización</button>
                </div>
            </div>
        </div>

        <!-- Galería de Trabajos -->
        <div class="row g-4">
            <!-- Trabajo 1 -->
            <div class="col-md-4">
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

            <!-- Trabajo 2 -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="assets/images/trabajo2.jpg" class="card-img-top" alt="Restauración clásico - Chevrolet Camaro">
                    <div class="card-body">
                        <h3 class="h5">Restauración Completa</h3>
                        <p class="text-muted mb-2"><strong>Vehículo:</strong> Chevrolet Camaro 1969</p>
                        <p class="card-text">Restauración fiel al diseño original con materiales modernos que mantienen el aspecto vintage pero con mayor durabilidad.</p>
                        <ul class="list-unstyled">
                            <li><strong>Materiales:</strong> Vinilo premium con textura similar al original</li>
                            <li><strong>Tiempo:</strong> 8 días laborales</li>
                            <li><strong>Garantía:</strong> 3 años</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Trabajo 3 -->
            <div class="col-md-4">
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

            <!-- Más trabajos... -->
        </div>

        <div class="text-center mt-5">
            <a href="contacto.php" class="btn btn-primary btn-lg">Solicitar Cotización</a>
        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>