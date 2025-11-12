[file name]: materiales.php
[file content begin]
<?php
require_once __DIR__ . '/includes/head.php';
$title = 'Materiales - Nacional Tapizados';
?>

<?php
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h1 class="fw-bold">Materiales Premium</h1>
            <p class="lead text-muted">Calidad y durabilidad para cada necesidad</p>
        </div>

        <!-- Galería de Materiales -->
        <div class="row g-4" id="materiales-container">
            <!-- Material 1 - Cuero -->
            <div class="col-md-6 material-item" data-category="cuero">
                <div class="card floating-card border-0 shadow-lg h-100 card-cuero">
                    <div class="row g-0 h-100">
                        <div class="col-md-5">
                            <img src="assets/images/material-cuero.jpg" class="img-fluid w-100 h-100 object-fit-cover" alt="Piel natural" style="min-height: 250px;">
                        </div>
                        <div class="col-md-7 d-flex align-items-center">
                            <div class="card-body p-4">
                                <h3 class="h5 mb-3">Cuero Natural Premium</h3>
                                <p class="card-text mb-3">La opción más exclusiva para tu vehículo, disponible en múltiples texturas y colores.</p>
                                <ul class="list-unstyled mb-3">
                                    <li class="mb-2"><strong>Ventajas:</strong> Durabilidad extrema, confort superior</li>
                                    <li class="mb-2"><strong>Vida útil:</strong> 8-10 años</li>
                                    <li class="mb-0"><strong>Recomendado para:</strong> Vehículos de lujo y clásicos</li>
                                </ul>
                                <button class="btn btn-outline-dark btn-sm material-info-toggle" data-material="cuero">
                                    Ver cuidados <i class="fas fa-chevron-circle-down ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Material 2 - Alcántara -->
            <div class="col-md-6 material-item" data-category="alcantara">
                <div class="card floating-card border-0 shadow-lg h-100 card-alcantara">
                    <div class="row g-0 h-100">
                        <div class="col-md-5">
                            <img src="assets/images/material-alcantara.jpg" class="img-fluid w-100 h-100 object-fit-cover" alt="Alcántara" style="min-height: 250px;">
                        </div>
                        <div class="col-md-7 d-flex align-items-center">
                            <div class="card-body p-4">
                                <h3 class="h5 mb-3">Alcántara Deportiva</h3>
                                <p class="card-text mb-3">Material técnico que combina las ventajas de la piel con mayor resistencia al desgaste.</p>
                                <ul class="list-unstyled mb-3">
                                    <li class="mb-2"><strong>Ventajas:</strong> Antideslizante, resistente a manchas</li>
                                    <li class="mb-2"><strong>Vida útil:</strong> 6-8 años</li>
                                    <li class="mb-0"><strong>Recomendado para:</strong> Vehículos deportivos</li>
                                </ul>
                                <button class="btn btn-outline-dark btn-sm material-info-toggle" data-material="alcantara">
                                    Ver cuidados <i class="fas fa-chevron-circle-down ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Material 3 - Vinilcuero -->
            <div class="col-md-6 material-item" data-category="vinilcuero">
                <div class="card floating-card border-0 shadow-lg h-100 card-vinilcuero">
                    <div class="row g-0 h-100">
                        <div class="col-md-5">
                            <img src="assets/images/material-vinilcuero.jpg" class="img-fluid w-100 h-100 object-fit-cover" alt="Vinilo premium" style="min-height: 250px;">
                        </div>
                        <div class="col-md-7 d-flex align-items-center">
                            <div class="card-body p-4">
                                <h3 class="h5 mb-3">Vinilcuero Premium</h3>
                                <p class="card-text mb-3">Opción económica sin sacrificar calidad, con gran variedad de texturas y colores.</p>
                                <ul class="list-unstyled mb-3">
                                    <li class="mb-2"><strong>Ventajas:</strong> Resistente a líquidos, fácil limpieza</li>
                                    <li class="mb-2"><strong>Vida útil:</strong> 4-6 años</li>
                                    <li class="mb-0"><strong>Recomendado para:</strong> Vehículos familiares y trabajo</li>
                                </ul>
                                <button class="btn btn-outline-dark btn-sm material-info-toggle" data-material="vinilcuero">
                                    Ver cuidados <i class="fas fa-chevron-circle-down ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Material 4 - Tela -->
            <div class="col-md-6 material-item" data-category="tela">
                <div class="card floating-card border-0 shadow-lg h-100 card-tela">
                    <div class="row g-0 h-100">
                        <div class="col-md-5">
                            <img src="assets/images/material-tela.jpg" class="img-fluid w-100 h-100 object-fit-cover" alt="Tela técnica" style="min-height: 250px;">
                        </div>
                        <div class="col-md-7 d-flex align-items-center">
                            <div class="card-body p-4">
                                <h3 class="h5 mb-3">Tela Técnica</h3>
                                <p class="card-text mb-3">Materiales modernos con tratamientos especiales para máxima durabilidad.</p>
                                <ul class="list-unstyled mb-3">
                                    <li class="mb-2"><strong>Ventajas:</strong> Transpirable, antimanchas, resistente UV</li>
                                    <li class="mb-2"><strong>Vida útil:</strong> 5-7 años</li>
                                    <li class="mb-0"><strong>Recomendado para:</strong> Uso diario y climas cálidos</li>
                                </ul>
                                <button class="btn btn-outline-dark btn-sm material-info-toggle" data-material="tela">
                                    Ver cuidados <i class="fas fa-chevron-circle-down ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Tarjetas flotantes con información de cuidados -->
<div id="cuero-care" class="material-detail-card">
    <div class="card-header">
        <h2 class="card-title">Cuidados para Cuero Natural</h2>
        <button class="close-card" onclick="hideMaterialDetails()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="card-content">
        <div class="detail-item">
            <div class="detail-label">Limpieza</div>
            <div class="detail-value">Profesional cada 6 meses</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Hidratación</div>
            <div class="detail-value">Mensual con productos específicos</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Protección</div>
            <div class="detail-value">Evitar exposición solar directa</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Productos</div>
            <div class="detail-value">Usar solo limpiadores pH neutro</div>
        </div>
        <div class="notes-section">
            <div class="detail-label">Advertencias</div>
            <div class="detail-value">No usar productos con alcohol o siliconas</div>
        </div>
    </div>
</div>

<div id="alcantara-care" class="material-detail-card">
    <div class="card-header">
        <h2 class="card-title">Cuidados para Alcántara</h2>
        <button class="close-card" onclick="hideMaterialDetails()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="card-content">
        <div class="detail-item">
            <div class="detail-label">Limpieza</div>
            <div class="detail-value">Con productos específicos cada 2-4 meses</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Mantenimiento</div>
            <div class="detail-value">Usar cepillos suaves de cerdas naturales</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Protección</div>
            <div class="detail-value">Evitar contacto con productos aceitosos</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Productos</div>
            <div class="detail-value">Espumas limpiadoras especiales</div>
        </div>
        <div class="notes-section">
            <div class="detail-label">Advertencias</div>
            <div class="detail-value">Nunca limpiar en seco o con cepillos duros</div>
        </div>
    </div>
</div>

<div id="vinilcuero-care" class="material-detail-card">
    <div class="card-header">
        <h2 class="card-title">Cuidados para Vinilcuero</h2>
        <button class="close-card" onclick="hideMaterialDetails()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="card-content">
        <div class="detail-item">
            <div class="detail-label">Limpieza</div>
            <div class="detail-value">Mensual con paño húmedo</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Mantenimiento</div>
            <div class="detail-value">Aplicar protectores UV trimestralmente</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Protección</div>
            <div class="detail-value">Evitar objetos afilados</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Productos</div>
            <div class="detail-value">Jabón neutro diluido</div>
        </div>
        <div class="notes-section">
            <div class="detail-label">Advertencias</div>
            <div class="detail-value">No usar productos abrasivos o aceitosos</div>
        </div>
    </div>
</div>

<div id="tela-care" class="material-detail-card">
    <div class="card-header">
        <h2 class="card-title">Cuidados para Tela Técnica</h2>
        <button class="close-card" onclick="hideMaterialDetails()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="card-content">
        <div class="detail-item">
            <div class="detail-label">Limpieza</div>
            <div class="detail-value">Aspirado quincenal</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Mantenimiento</div>
            <div class="detail-value">Limpieza con espumas específicas</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Protección</div>
            <div class="detail-value">Aplicar repelente de líquidos</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Productos</div>
            <div class="detail-value">Espumas secas para tejidos</div>
        </div>
        <div class="notes-section">
            <div class="detail-label">Advertencias</div>
            <div class="detail-value">No lavar con agua ni frotar fuerte</div>
        </div>
    </div>
</div>

<!-- Overlay para fondo oscuro -->
<div class="overlay" id="overlay" onclick="hideMaterialDetails()"></div>

<!-- Agrega jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Manejar clic en los botones de información de materiales
    $('.material-info-toggle').click(function() {
        const material = $(this).data('material');
        const careCard = $('#' + material + '-care');
        const icon = $(this).find('i');
        
        // Preparar la tarjeta para animación desde abajo
        careCard.css({
            'transform': 'translate(-50%, 100vh)',
            'opacity': '0'
        });
        
        // Mostrar tarjeta y overlay
        careCard.show();
        $('#overlay').show();
        
        // Animar la tarjeta desde abajo
        setTimeout(function() {
            careCard.css({
                'transform': 'translate(-50%, -50%)',
                'opacity': '1'
            });
        }, 10);
        
        // Cambiar ícono
        icon.removeClass('fa-chevron-circle-down');
        icon.addClass('fa-chevron-circle-up');
        
        // Deshabilitar scroll del body
        $('body').css('overflow', 'hidden');
    });
    
    // Cerrar con tecla ESC
    $(document).keydown(function(event) {
        if (event.key === 'Escape') {
            hideMaterialDetails();
        }
    });
});

// Función para ocultar detalles del material
function hideMaterialDetails() {
    const cards = $('.material-detail-card');
    
    // Animar hacia abajo antes de ocultar
    cards.css({
        'transform': 'translate(-50%, 100vh)',
        'opacity': '0'
    });
    
    // Ocultar después de la animación
    setTimeout(function() {
        cards.hide();
        $('#overlay').hide();
        
        // Restaurar todos los íconos
        $('.material-info-toggle i').removeClass('fa-chevron-circle-up').addClass('fa-chevron-circle-down');
        
        // Habilitar scroll del body
        $('body').css('overflow', 'auto');
    }, 300);
}
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

/* Colores para cada categoría de materiales */
.card-cuero:hover::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #8B4513, #A0522D);
    z-index: 1;
}

.card-alcantara:hover::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #2C3E50, #34495E);
    z-index: 1;
}

.card-vinilcuero:hover::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #7F8C8D, #95A5A6);
    z-index: 1;
}

.card-tela:hover::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #16A085, #1ABC9C);
    z-index: 1;
}

/* Animación suave al cargar */
.material-item {
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
.card-cuero .card-body h3 {
    color: #8B4513;
}

.card-alcantara .card-body h3 {
    color: #2C3E50;
}

.card-vinilcuero .card-body h3 {
    color: #7F8C8D;
}

.card-tela .card-body h3 {
    color: #16A085;
}

/* Efectos de sombra específicos para cada categoría */
.card-cuero:hover {
    box-shadow: 0 12px 35px rgba(139, 69, 19, 0.2) !important;
}

.card-alcantara:hover {
    box-shadow: 0 12px 35px rgba(44, 62, 80, 0.2) !important;
}

.card-vinilcuero:hover {
    box-shadow: 0 12px 35px rgba(127, 140, 141, 0.2) !important;
}

.card-tela:hover {
    box-shadow: 0 12px 35px rgba(22, 160, 133, 0.2) !important;
}

/* Estilos para el overlay */
.overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 1000;
    backdrop-filter: blur(5px);
}

/* Estilos para las tarjetas de detalles de materiales */
.material-detail-card {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    max-width: 700px;
    max-height: 90vh;
    background-color: rgba(50, 50, 50, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    z-index: 1001;
    overflow-y: auto;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.card-title {
    margin: 0;
    font-size: 1.8rem;
    color: #fff;
}

.close-card {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0.5rem;
    transition: all 0.3s ease;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.close-card:hover {
    color: white;
    background-color: rgba(255, 255, 255, 0.1);
}

.card-content {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
}

.detail-item {
    margin-bottom: 1rem;
}

.detail-label {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 0.25rem;
}

.detail-value {
    font-size: 1.1rem;
    word-break: break-word;
    color: #fff;
}

.notes-section {
    grid-column: 1 / -1;
    background-color: rgba(0, 0, 0, 0.2);
    padding: 1.5rem;
    border-radius: 8px;
    margin-top: 1rem;
}

/* Estilos responsivos */
@media (max-width: 768px) {
    .material-detail-card {
        width: 95%;
        padding: 1.5rem;
    }
    
    .card-content {
        grid-template-columns: 1fr;
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
[file content end]