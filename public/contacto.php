<?php
require_once __DIR__ . '/includes/head.php';
$title = 'Contacto - Nacional Tapizados';
?>

<?php
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="py-5">
    <div class="container">
        <!-- Contenedor para mensajes flotantes -->
        <div id="feedback-message" class="position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1100; display: none;">
            <div class="alert alert-dismissible fade show" role="alert">
                <span id="feedback-text"></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h1 class="fw-bold mb-4">Contáctanos</h1>
                <p class="lead mb-4">Estamos aquí para responder tus preguntas y brindarte el mejor servicio en tapicería automotriz.</p>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="h5 mb-3"><i class="fas fa-map-marker-alt text-primary me-2"></i> Visítanos</h3>
                        <address class="mb-0">
                            Cr 13 # 4.43<br>
                            Ciudad, Estado 12345<br>
                            Colombia<br>
                        </address>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="h5 mb-3"><i class="fas fa-phone-alt text-primary me-2"></i> Llámanos</h3>
                        <p class="mb-2"><strong>Ventas:</strong> <a href="tel:+525512345678">57 1234 5678</a></p>
                        <p class="mb-0"><strong>Soporte:</strong> <a href="tel:+525598765432">57 9876 5432</a></p>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h3 class="h5 mb-3"><i class="fas fa-envelope text-primary me-2"></i> Escríbenos</h3>
                        <p class="mb-2"><strong>General:</strong> <a href="mailto:info@nacionaltapizados.com">olfonsojose@gmail.com</a></p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h4 fw-bold mb-4">Formulario de Contacto</h2>
                        <form id="contactForm" action="procesar_contacto.php" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre completo</label>
                                <input type="text" class="form-control" id="name" name="nombre_completo" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="email" name="correo_electronico" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="phone" name="telefono">
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Asunto</label>
                                <select class="form-select" id="subject" name="asunto" required>
                                    <option value="" selected disabled>Selecciona una opción</option>
                                    <option value="cotizacion">Solicitud de cotización</option>
                                    <option value="consulta">Consulta general</option>
                                    <option value="garantia">Soporte o garantía</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Mensaje</label>
                                <textarea class="form-control" id="message" name="mensaje" rows="4" required></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <span id="btnText">Enviar mensaje</span>
                                    <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
             
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="ratio ratio-16x9">
                            <iframe 
                                src="https://maps.google.com/maps?q=Cra.+13+%23+4-43,+Tunja,+Boyac%C3%A1&output=embed"
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-md-6 mb-6 mb-md-0">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary mb-3">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <h3 class="h5">Horario de atención</h3>
                        <p class="mb-0">
                            <strong>Lunes a Viernes:</strong> 8:00am - 12:30am / 1:30pm - 6:00pm<br>
                            <strong>Sábados:</strong> 8:00am - 2:00pm<br>
                            <strong>Domingos:</strong> Cerrado
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-6 mb-md-0">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary mb-3">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                        <h3 class="h5">Citas</h3>
                        <p class="mb-0">Para mejor servicio, recomendamos agendar cita previa. Puedes hacerlo por teléfono o mediante nuestro formulario de contacto.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar mensaje de feedback si hay parámetros en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const message = urlParams.get('message');
    
    if (status) {
        const feedbackDiv = document.getElementById('feedback-message');
        const feedbackText = document.getElementById('feedback-text');
        const alertDiv = feedbackDiv.querySelector('.alert');
        
        // Configurar mensaje y estilo según el estado
        if (status === 'success') {
            feedbackText.textContent = '¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.';
            alertDiv.classList.add('alert-success');
        } else {
            feedbackText.textContent = message || 'Error al enviar el mensaje. Por favor inténtelo nuevamente.';
            alertDiv.classList.add('alert-danger');
        }
        
        // Mostrar el mensaje con animación
        feedbackDiv.style.display = 'block';
        setTimeout(() => {
            alertDiv.classList.add('show');
        }, 10);
        
        // Ocultar después de 5 segundos
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => {
                feedbackDiv.style.display = 'none';
                // Limpiar parámetros de la URL sin recargar
                history.replaceState(null, '', window.location.pathname);
            }, 300); // Tiempo para la animación de fade
        }, 5000);
    }

    // Prevenir envío duplicado del formulario
    const form = document.getElementById('contactForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            
            if (submitBtn && btnText && btnSpinner) {
                submitBtn.disabled = true;
                btnText.textContent = 'Enviando...';
                btnSpinner.classList.remove('d-none');
            }
        });
    }
});
</script>