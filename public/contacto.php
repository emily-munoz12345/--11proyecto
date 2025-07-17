<?php
require_once __DIR__ . '/includes/head.php';
$title = 'Contacto - Nacional Tapizados';
?>

<?php
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="py-5">
    <div class="container">
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
                        <form id="contactForm">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre completo</label>
                                <input type="text" class="form-control" id="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="phone">
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Asunto</label>
                                <select class="form-select" id="subject" required>
                                    <option value="" selected disabled>Selecciona una opción</option>
                                    <option value="cotizacion">Solicitud de cotización</option>
                                    <option value="consulta">Consulta general</option>
                                    <option value="garantia">Soporte o garantía</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Mensaje</label>
                                <textarea class="form-control" id="message" rows="4" required></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Enviar mensaje</button>
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
                            <strong>Lunes a Viernes:</strong> 9:00 - 18:00<br>
                            <strong>Sábados:</strong> 9:00 - 14:00<br>
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