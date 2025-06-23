<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nacional Tapizados - Expertos en Tapicería Automotriz</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animaciones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Navbar --> 
<?php include '../--11proyecto/includes/navbar.php'; ?>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="container h-100">
            <div class="row h-100 align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-3 fw-bold text-white mb-4 animate__animated animate__fadeInDown">Expertos en Tapicería Automotriz</h1>
                    <p class="lead text-white mb-4 animate__animated animate__fadeIn animate__delay-1s">Transformamos tu vehículo con materiales premium y acabados perfectos que realzan su valor y estilo.</p>
                    <div class="animate__animated animate__fadeIn animate__delay-2s">
                        <a href="cotizaciones.html" class="btn btn-primary btn-lg me-3">Solicitar Cotización</a>
                        <a href="#servicios" class="btn btn-outline-light btn-lg">Nuestros Servicios</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Servicios -->
    <section id="servicios" class="py-5 bg-light">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="fw-bold">Nuestros Servicios</h2>
                <p class="text-muted">Calidad artesanal y atención personalizada</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm service-card">
                        <div class="card-body text-center p-4">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary mb-4">
                                <i class="fas fa-chair fa-2x"></i>
                            </div>
                            <h5 class="card-title">Tapizado Completo</h5>
                            <p class="card-text">Renovación total de los asientos con materiales de primera calidad y diseño personalizado.</p>
                            <a href="servicios.html" class="btn btn-outline-primary">Más información</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm service-card">
                        <div class="card-body text-center p-4">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary mb-4">
                                <i class="fas fa-tools fa-2x"></i>
                            </div>
                            <h5 class="card-title">Reparaciones</h5>
                            <p class="card-text">Soluciones profesionales para roturas, desgastes y daños en la tapicería de tu vehículo.</p>
                            <a href="servicios.html" class="btn btn-outline-primary">Más información</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm service-card">
                        <div class="card-body text-center p-4">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary mb-4">
                                <i class="fas fa-spray-can fa-2x"></i>
                            </div>
                            <h5 class="card-title">Limpieza Profesional</h5>
                            <p class="card-text">Eliminación de manchas, olores y recuperación del color original de tus asientos.</p>
                            <a href="servicios.html" class="btn btn-outline-primary">Más información</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm service-card">
                        <div class="card-body text-center p-4">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary mb-4">
                                <i class="fas fa-palette fa-2x"></i>
                            </div>
                            <h5 class="card-title">Personalización</h5>
                            <p class="card-text">Diseños exclusivos y modificaciones para darle un toque único a tu vehículo.</p>
                            <a href="servicios.html" class="btn btn-outline-primary">Más información</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sobre Nosotros -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="assets/images/taller.jpg" alt="Taller de tapicería" class="img-fluid rounded shadow">
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">Más de 25 años de experiencia</h2>
                    <p class="lead mb-4">En Nacional Tapizados nos especializamos en la restauración y personalización de interiores automotrices con los más altos estándares de calidad.</p>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Materiales importados de primera calidad</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Técnicas artesanales combinadas con tecnología moderna</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Garantía en todos nuestros trabajos</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Atención personalizada a cada cliente</li>
                    </ul>
                    <a href="nosotros.html" class="btn btn-primary mt-3">Conoce nuestra historia</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Trabajos Destacados -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="fw-bold">Trabajos Recientes</h2>
                <p class="text-muted">Algunos ejemplos de nuestro trabajo</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="portfolio-item">
                        <img src="assets/images/trabajo1.jpg" alt="Tapizado en piel" class="img-fluid rounded">
                        <div class="portfolio-overlay">
                            <h5>Tapizado en piel</h5>
                            <p>Mercedes-Benz Clase S</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="portfolio-item">
                        <img src="assets/images/trabajo2.jpg" alt="Restauración completa" class="img-fluid rounded">
                        <div class="portfolio-overlay">
                            <h5>Restauración completa</h5>
                            <p>Chevrolet Camaro 1969</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="portfolio-item">
                        <img src="assets/images/trabajo3.jpg" alt="Personalización" class="img-fluid rounded">
                        <div class="portfolio-overlay">
                            <h5>Personalización</h5>
                            <p>Ford Mustang GT</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="trabajos.html" class="btn btn-outline-primary">Ver más trabajos</a>
            </div>
        </div>
    </section>

    <!-- Testimonios -->
    <section class="py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="fw-bold">Lo que dicen nuestros clientes</h2>
                <p class="text-muted">Experiencias reales</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="card-text mb-4">"Quedé impresionado con la calidad del trabajo en mi BMW. Los asientos quedaron como nuevos y el trato fue excelente."</p>
                            <div class="d-flex align-items-center">
                                <img src="assets/images/cliente1.jpg" alt="Carlos Méndez" class="rounded-circle me-3" width="50">
                                <div>
                                    <h6 class="mb-0">Carlos Méndez</h6>
                                    <small class="text-muted">Dueño de BMW Serie 5</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="card-text mb-4">"Excelente servicio y atención. Recuperaron los asientos de mi clásico como si fueran nuevos."</p>
                            <div class="d-flex align-items-center">
                                <img src="assets/images/cliente2.jpg" alt="Ana Rodríguez" class="rounded-circle me-3" width="50">
                                <div>
                                    <h6 class="mb-0">Ana Rodríguez</h6>
                                    <small class="text-muted">Dueña de Volkswagen Escarabajo 1972</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <p class="card-text mb-4">"El personal fue muy profesional y el resultado superó mis expectativas. Totalmente recomendado."</p>
                            <div class="d-flex align-items-center">
                                <img src="assets/images/cliente3.jpg" alt="Luis Fernández" class="rounded-circle me-3" width="50">
                                <div>
                                    <h6 class="mb-0">Luis Fernández</h6>
                                    <small class="text-muted">Dueño de Ford F-150</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="fw-bold mb-4">¿Listo para transformar tu vehículo?</h2>
            <p class="lead mb-4">Contáctanos hoy mismo para una cotización sin compromiso</p>
            <a href="contacto.html" class="btn btn-light btn-lg">Contactar ahora</a>
        </div>
    </section>

    <!-- Footer -->
<?php include '../--11proyecto/includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>