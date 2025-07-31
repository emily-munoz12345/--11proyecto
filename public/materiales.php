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
            <h1 class="fw-bold">Materiales </h1>
            <p class="lead text-muted">Calidad y durabilidad para cada necesidad</p>
        </div>

        <div class="row g-4">
            <!-- Material 1 -->
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="row g-0">
                        <div class="col-md-5">
                            <img src="assets/images/material-cuero.jpg" class="img-fluid rounded-start h-100" alt="Piel natural">
                        </div>
                        <div class="col-md-7">
                            <div class="card-body position-relative">
                                <h3 class="h5">Cuero Natural 
                                    <button class="btn btn-link p-0 ms-2 material-info-toggle" type="button" data-material="cuero">
                                        <i class="fas fa-chevron-circle-down"></i>
                                    </button>
                                </h3>
                                <p class="card-text">La opción más exclusiva para tu vehículo, disponible en múltiples texturas y colores.</p>
                                <ul class="list-unstyled">
                                    <li><strong>Ventajas:</strong> Durabilidad extrema, confort superior, aspecto lujoso</li>
                                    <li><strong>Vida útil:</strong> 8-10 años con mantenimiento adecuado</li>
                                    <li><strong>Recomendado para:</strong> Vehículos de lujo, clásicos, entusiastas</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Material 2 -->
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="row g-0">
                        <div class="col-md-5">
                            <img src="assets/images/material-alcantara.jpg" class="img-fluid rounded-start h-100" alt="Alcántara">
                        </div>
                        <div class="col-md-7">
                            <div class="card-body position-relative">
                                <h3 class="h5">Alcántara Deportiva
                                    <button class="btn btn-link p-0 ms-2 material-info-toggle" type="button" data-material="alcantara">
                                        <i class="fas fa-chevron-circle-down"></i>
                                    </button>
                                </h3>
                                <p class="card-text">Material técnico que combina las ventajas de la piel con mayor resistencia al desgaste.</p>
                                <ul class="list-unstyled">
                                    <li><strong>Ventajas:</strong> Antideslizante, resistente a manchas, transpirable</li>
                                    <li><strong>Vida útil:</strong> 6-8 años con uso normal</li>
                                    <li><strong>Recomendado para:</strong> Vehículos deportivos, asientos de conductor</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Material 3 -->
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="row g-0">
                        <div class="col-md-5">
                            <img src="assets/images/material-vinilcuero.jpg" class="img-fluid rounded-start h-100" alt="Vinilo premium">
                        </div>
                        <div class="col-md-7">
                            <div class="card-body position-relative">
                                <h3 class="h5">Vinilcuero
                                    <button class="btn btn-link p-0 ms-2 material-info-toggle" type="button" data-material="vinilcuero">
                                        <i class="fas fa-chevron-circle-down"></i>
                                    </button>
                                </h3>
                                <p class="card-text">Opción económica sin sacrificar calidad, con gran variedad de texturas y colores.</p>
                                <ul class="list-unstyled">
                                    <li><strong>Ventajas:</strong> Resistente a líquidos, fácil limpieza, económico</li>
                                    <li><strong>Vida útil:</strong> 4-6 años</li>
                                    <li><strong>Recomendado para:</strong> Vehículos familiares, trabajo, restauraciones</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Material 4 -->
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="row g-0">
                        <div class="col-md-5">
                            <img src="assets/images/material-tela.jpg" class="img-fluid rounded-start h-100" alt="Tela técnica">
                        </div>
                        <div class="col-md-7">
                            <div class="card-body position-relative">
                                <h3 class="h5">Tela Técnica
                                    <button class="btn btn-link p-0 ms-2 material-info-toggle" type="button" data-material="tela">
                                        <i class="fas fa-chevron-circle-down"></i>
                                    </button>
                                </h3>
                                <p class="card-text">Materiales modernos con tratamientos especiales para máxima durabilidad.</p>
                                <ul class="list-unstyled">
                                    <li><strong>Ventajas:</strong> Transpirable, antimanchas, resistente a UV</li>
                                    <li><strong>Vida útil:</strong> 5-7 años</li>
                                    <li><strong>Recomendado para:</strong> Vehículos de uso diario, climas cálidos</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Tarjetas flotantes con información de cuidados (inicialmente ocultas) -->
<div id="cuero-care" class="floating-card">
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

<div id="alcantara-care" class="floating-card">
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

<div id="vinilcuero-care" class="floating-card">
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

<div id="tela-care" class="floating-card">
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

<style>
    body {
        font-family: 'Roboto', sans-serif;
    }
    
    .material-info-toggle {
        color: #8c4a3f;
        transition: all 0.3s ease;
    }
    
    .material-info-toggle:hover {
        color: #6d3a32;
        transform: scale(1.1);
    }
    
    .material-info-toggle.active {
        transform: rotate(180deg);
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
    
    /* Estilos para las tarjetas flotantes */
    .floating-card {
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
        animation: fadeInUp 0.4s ease;
        overflow-y: auto;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translate(-50%, -40%);
        }
        to {
            opacity: 1;
            transform: translate(-50%, -50%);
        }
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
        .floating-card {
            width: 95%;
            padding: 1.5rem;
        }
        
        .card-content {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clic en los botones de información
    const toggleButtons = document.querySelectorAll('.material-info-toggle');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const material = this.getAttribute('data-material');
            const careCard = document.getElementById(`${material}-care`);
            const icon = this.querySelector('i');
            
            // Mostrar tarjeta y overlay
            careCard.style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
            
            // Rotar el ícono
            this.classList.add('active');
            icon.classList.remove('fa-chevron-circle-down');
            icon.classList.add('fa-chevron-circle-up');
            
            // Deshabilitar scroll del body
            document.body.style.overflow = 'hidden';
        });
    });
    
    // Cerrar con tecla ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideMaterialDetails();
        }
    });
});

// Función para ocultar detalles del material
function hideMaterialDetails() {
    document.querySelectorAll('.floating-card').forEach(card => {
        card.style.display = 'none';
    });
    document.getElementById('overlay').style.display = 'none';
    
    // Restaurar todos los íconos
    document.querySelectorAll('.material-info-toggle').forEach(button => {
        button.classList.remove('active');
        const icon = button.querySelector('i');
        icon.classList.remove('fa-chevron-circle-up');
        icon.classList.add('fa-chevron-circle-down');
    });
    
    // Habilitar scroll del body
    document.body.style.overflow = 'auto';
}
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>