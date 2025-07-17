<?php
require_once __DIR__ . '/includes/head.php';
$title = 'Nacional Tapizados - Expertos en Tapicería Automotriz';
?>

<!-- Navbar -->
<?php require_once __DIR__ . '/includes/navbar.php'; ?>

<!-- Hero Section -->
<!-- Hero Section -->
<section class="hero position-relative min-vh-100 d-flex align-items-center" style="background: linear-gradient(90deg, rgba(94, 48, 35, 0.9) 30%, rgba(94, 48, 35, 0.5) 100%), url('/--11proyecto/public/assets/images/images.jpg') center/cover no-repeat;">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <div class="hero-text text-white">
          <h1 class="display-3 fw-bold mb-4">Transformamos tu vehículo con <span class="text-gold">arte en tapicería</span></h1>
          <p class="lead fs-4 mb-5">Materiales premium y acabados perfectos que realzan el valor y estilo de tu automóvil</p>
        </div>
      </div>
    </div>
  </div>
  <a href="#servicios" class="scroll-down position-absolute bottom-0 start-50 translate-middle-x text-white fs-3 mb-4">
    <i class="fas fa-chevron-down"></i>
  </a>
</section>
<!-- Servicios -->
<section id="servicios" class="py-5 my-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="display-4 fw-bold mb-3">Nuestros Servicios</h2>
      <p class="lead text-muted">Calidad artesanal y atención personalizada</p>
    </div>

    <div class="row g-4">
      <!-- Servicio 1 -->
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
          <div class="card-body text-center p-4">
            <div class="icono-servicio bg-primary text-white rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
              <i class="fas fa-chair fs-3"></i>
            </div>
            <h3 class="h4 fw-bold mb-3">Tapizado Completo</h3>
            <p class="text-muted mb-4">Renovación total con materiales de primera calidad y diseño personalizado.</p>
          </div>
        </div>
      </div>

      <!-- Servicio 2 -->
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
          <div class="card-body text-center p-4">
            <div class="icono-servicio bg-primary text-white rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
              <i class="fas fa-couch fs-3"></i>
            </div>
            <h3 class="h4 fw-bold mb-3">Reparaciones</h3>
            <p class="text-muted mb-4">Solución profesional para roturas y desgastes en los asientos.</p>
          </div>
        </div>
      </div>

      <!-- Servicio 3 -->
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
          <div class="card-body text-center p-4">
            <div class="icono-servicio bg-primary text-white rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
              <i class="fas fa-tint fs-3"></i>
            </div>
            <h3 class="h4 fw-bold mb-3">Impermeabilización</h3>
            <p class="text-muted mb-4">Protección contra líquidos y manchas para mantener tus asientos.</p>
          </div>
        </div>
      </div>

      <!-- Servicio 4 -->
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
          <div class="card-body text-center p-4">
            <div class="icono-servicio bg-primary text-white rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
              <i class="fas fa-palette fs-3"></i>
            </div>
            <h3 class="h4 fw-bold mb-3">Personalización</h3>
            <p class="text-muted mb-4">Diseños exclusivos para darle un toque único a tu vehículo.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
      <div class="text-center mt-5">
      <a href="servicios.php" class="btn btn-outline-primary btn-lg px-4 py-2">Mas informacion</a>
    </div>
</section>

<!-- Sobre Nosotros -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 mb-4 mb-lg-0">
        <div class="position-relative">
          <img src="/--11proyecto/public/assets/images/taller.jpg" alt="Nuestro taller" class="img-fluid rounded shadow-lg">
          <div class="badge-trabajos position-absolute bottom-0 end-0 bg-primary text-white p-3 rounded m-3 shadow">
            <span class="fs-2 fw-bold">1500+</span> trabajos realizados
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <h2 class="display-4 fw-bold mb-4">Más de 25 años de experiencia</h2>
        <p class="lead mb-4">En Nacional Tapizados nos especializamos en la restauración y personalización de interiores automotrices.</p>
        
        <ul class="list-unstyled mb-5">
          <li class="mb-3"><i class="fas fa-check text-primary me-2"></i> Materiales de primera calidad</li>
          <li class="mb-3"><i class="fas fa-check text-primary me-2"></i> Técnicas artesanales y modernas</li>
          <li class="mb-3"><i class="fas fa-check text-primary me-2"></i> Garantía en nuestros trabajos</li>
          <li class="mb-3"><i class="fas fa-check text-primary me-2"></i> Atención personalizada</li>
        </ul>
        
        <a href="nosotros.php" class="btn btn-primary btn-lg px-4 py-2">Conoce nuestra historia</a>
      </div>
    </div>
  </div>
</section>

<!-- Trabajos Destacados -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="display-4 fw-bold mb-3">Trabajos Recientes</h2>
      <p class="lead text-muted">Algunos ejemplos de nuestro trabajo</p>
    </div>

    <div class="row g-4">
      <!-- Trabajo 1 -->
      <div class="col-md-6 col-lg-4">
        <div class="card-trabajo position-relative overflow-hidden rounded shadow-lg">
          <img src="/--11proyecto/public/assets/images/trabajo1.jpg" class="img-fluid w-100" alt="Tapicería Chevrolet Spark" style="height: 300px; object-fit: cover;">
          <div class="trabajo-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-75 opacity-0 transition-all">
            <div class="text-center text-white p-4">
              <h3 class="h4 fw-bold mb-2">Chevrolet Spark</h3>
              <p class="mb-3">Tapizado completo en piel</p>
              <a href="trabajos.php" class="btn btn-outline-light">Ver detalles</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Trabajo 2 -->
      <div class="col-md-6 col-lg-4">
        <div class="card-trabajo position-relative overflow-hidden rounded shadow-lg">
          <img src="/--11proyecto/public/assets/images/trabajo2.jpg" class="img-fluid w-100" alt="Tapicería Ford Mustang" style="height: 300px; object-fit: cover;">
          <div class="trabajo-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-75 opacity-0 transition-all">
            <div class="text-center text-white p-4">
              <h3 class="h4 fw-bold mb-2">Ford Mustang</h3>
              <p class="mb-3">Restauración clásica</p>
              <a href="trabajos.php" class="btn btn-outline-light">Ver detalles</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Trabajo 3 -->
      <div class="col-md-6 col-lg-4">
        <div class="card-trabajo position-relative overflow-hidden rounded shadow-lg">
          <img src="/--11proyecto/public/assets/images/trabajo3.jpg" class="img-fluid w-100" alt="Tapicería Volkswagen Kombi" style="height: 300px; object-fit: cover;">
          <div class="trabajo-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-75 opacity-0 transition-all">
            <div class="text-center text-white p-4">
              <h3 class="h4 fw-bold mb-2">Volkswagen Kombi</h3>
              <p class="mb-3">Personalización vintage</p>
              <a href="trabajos.php" class="btn btn-outline-light">Ver detalles</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="text-center mt-5">
      <a href="trabajos.php" class="btn btn-outline-primary btn-lg px-4 py-2">Ver más trabajos</a>
    </div>
  </div>
</section>

<!-- Testimonios -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="display-4 fw-bold mb-3">Lo que dicen nuestros clientes</h2>
      <p class="lead text-muted">Experiencias reales</p>
    </div>

    <div class="row g-4">
      <!-- Testimonio 1 -->
      <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body p-4">
            <div class="testimonio-icon text-primary fs-1 mb-4">
              <i class="fas fa-quote-left"></i>
            </div>
            <blockquote class="testimonio-texto mb-4">
              "Excelente trabajo en el tapizado de mi camioneta. Quedó como nueva y el trato fue impecable."
            </blockquote>
            <div class="testimonio-autor d-flex align-items-center">
            </div>
          </div>
        </div>
      </div>

      <!-- Testimonio 2 -->
      <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body p-4">
            <div class="testimonio-icon text-primary fs-1 mb-4">
              <i class="fas fa-quote-left"></i>
            </div>
            <blockquote class="testimonio-texto mb-4">
              "La personalización que hicieron a mi auto superó todas mis expectativas. ¡Recomendados!"
            </blockquote>
            <div class="testimonio-autor d-flex align-items-center">
            </div>
          </div>
        </div>
      </div>

      <!-- Testimonio 3 -->
      <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body p-4">
            <div class="testimonio-icon text-primary fs-1 mb-4">
              <i class="fas fa-quote-left"></i>
            </div>
            <blockquote class="testimonio-texto mb-4">
              "Profesionales desde el primer contacto. El resultado final fue exactamente lo que quería."
            </blockquote>
            <div class="testimonio-autor d-flex align-items-center">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="py-5 bg-dark text-white">
  <div class="container text-center py-4">
    <h2 class="display-4 fw-bold mb-3">¿Listo para transformar tu vehículo?</h2>
    <p class="lead mb-4">Contáctanos hoy mismo para una cotización sin compromiso</p>
    <a href="contacto.php" class="btn btn-light btn-lg px-4 py-2">Contactar ahora</a>
  </div>
</section>

<!-- Footer -->
<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/--11proyecto/public/assets/js/main.js" defer></script>