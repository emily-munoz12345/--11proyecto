<footer class="footer">
  <div class="footer-top">
    <div class="footer-container">
      <div class="footer-col">
        <div class="footer-logo">
          <i class="fas fa-car-alt me-2"></i>
          <span>Nacional</span>Tapizados
        </div>
        <p class="footer-about">Más de 25 años transformando vehículos con trabajos de tapicería de alta calidad y diseño personalizado.</p>
        <div class="social-icons">
          <a href="(aca se pega el link del perfil del usuario)" target="_blank">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="https://instagram.com/nacionalde_tapizados/" target="_blank">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="https://wa.me/573204948958?text=Hola%2C%20quiero%20más%20información" target="_blank">
            <i class="fab fa-whatsapp"></i>
          </a>
        </div>
      </div>

      <div class="footer-col">
        <h3 class="footer-title">Contacto</h3>
        <ul class="footer-contact">
          <li><i class="fas fa-map-marker-alt"></i> Av. Principal 123, Ciudad</li>
          <li><i class="fas fa-phone"></i> +1 234 567 890</li>
          <li><i class="fas fa-envelope"></i> info@nacionaltapizados.com</li>
          <li><i class="fas fa-clock"></i> Lunes a Viernes: 9am - 6pm</li>
        </ul>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="container">
      <p>&copy; <?php echo date('Y'); ?> Nacional Tapizados. Todos los derechos reservados.</p>
      <div class="footer-legal">
        <a href="#" onclick="openModal('privacy')">Política de privacidad</a>
        <a href="#" onclick="openModal('terms')">Términos de servicio</a>
      </div>
    </div>
  </div>
</footer>

<!-- Modal Structure -->
<div id="modal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <div id="modal-text"></div>
  </div>
</div>

<script>
  function openModal(type) {
    const modal = document.getElementById('modal');
    const modalText = document.getElementById('modal-text');

    if (type === 'privacy') {
      modalText.innerHTML = '<h2>Política de Privacidad</h2><p><h5>1. Introducción</h5> Esta política de privacidad describe cómo recopilamos, usamos y protegemos la información personal de nuestros usuarios en Nacional De Tapizados</p> <h5>2. Información que Recopilamos Información Personal </h5> Recopilamos datos como nombre, dirección de correo electrónico, número de teléfono y dirección física cuando los usuarios se registran o realizan una compra. Información no Personal  También recopilamos datos que no identifican personalmente a los usuarios, como la dirección IP y el tipo de navegador.</p> <h5>3. Uso de la Información Utilizamos la información recopilada para: </h5>Procesar pedidos y gestionar cuentas de usuario. Enviar comunicaciones sobre productos, promociones y actualizaciones. Mejorar nuestros servicios y la experiencia del usuario.</p> <h5>4. Compartición de la Información</h5> No vendemos, intercambiamos ni transferimos a terceros la información personal de los usuarios, excepto en los siguientes casos: Proveedores de servicios que nos ayudan a operar nuestro sitio web y llevar a cabo nuestras operaciones, siempre que estos mantengan la confidencialidad de la información. Cuando la ley lo requiera o para proteger nuestros derechos.</p> <h5>5. Seguridad de la Información:</h5>Implementamos medidas de seguridad adecuadas para proteger la información personal contra el acceso no autorizado, la alteración, divulgación o destrucción.</p><h5>6.Derechos de los Usuarios Los usuarios tienen derecho a: </h5> Acceder a la información personal que tenemos sobre ellos. </p> Solicitar la corrección de datos inexactos.</p> Solicitar la eliminación de su información personal.</p><h5>7. Cambios a esta Política </h5>Nos reservamos el derecho de modificar esta política de privacidad. Cualquier cambio será publicado en esta página. <h5>8. Contacto </h5> Si tienes preguntas sobre esta política de privacidad, contáctanos en olfonsojose@gmail.com.';
    } else if (type === 'terms') {
      modalText.innerHTML = '<h2>Términos de Servicio</h2><p><h5>1. Aceptación de los Términos </h5>Al acceder y utilizar Nacional De Tapizados, aceptas cumplir con estos términos de servicio. Si no estás de acuerdo, no debes utilizar el sitio.</p> <h5>2. Modificaciones </h5>Nos reservamos el derecho de modificar estos términos en cualquier momento. Las modificaciones se publicarán en esta página y se considerarán efectivas inmediatamente. Es tu responsabilidad revisar estos términos periódicamente.</p><h5> 3. Uso del Sitio </h5> <h6>Elegibilidad:</h6> Debes tener al menos 18 años para utilizar este sitio. <h6>Conducta:</h6> Te comprometes a no usar el sitio para actividades ilegales o no autorizadas.</p><h5>4. Propiedad Intelectual </h5>Todo el contenido del sitio, incluyendo textos, imágenes, gráficos y logotipos, está protegido por derechos de autor y otras leyes de propiedad intelectual. No puedes reproducir, distribuir o modificar este contenido sin nuestro consentimiento.</p><h5>5. Productos y Servicios </h5> <h6>Descripción:</h6> Hacemos todo lo posible para garantizar que la descripción de nuestros productos sea precisa. Sin embargo, no garantizamos que la información sea siempre libre de errores. <h6>Precios:</h6> Los precios están sujetos a cambios sin previo aviso.</p> <h6>6. Limitación de Responsabilidad </h5>No seremos responsables de ningún daño indirecto, incidental o consecuente que resulte del uso o la incapacidad de usar el sitio. Esto incluye, pero no se limita a, pérdidas de ganancias, datos o uso.</p><h5>7. Enlaces a Terceros </h5>Nuestro sitio puede contener enlaces a sitios de terceros. No somos responsables del contenido de esos sitios y no asumimos ninguna obligación por ellos.</p><h5> 8. Legislación Aplicable </h5>Estos términos se regirán e interpretarán de acuerdo con las leyes de [país o estado]. Cualquier disputa se resolverá en los tribunales de [localidad].';
    }

    modal.style.display = "block";
  }

  function closeModal() {
    document.getElementById('modal').style.display = "none";
  }

  window.onclick = function(event) {
    const modal = document.getElementById('modal');
    if (event.target === modal) {
      modal.style.display = "none";
    }
  }
</script>

<style>
  /* Estilos del footer */
  .footer {
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
      url('textura-cuero.jpg') center/cover;
    color: var(--neutral-light);
    position: relative;
  }

  .footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 10px;
    background: var(--gradient-leather);
  }

  .footer-top {
    padding: var(--space-xxl) 0;
  }

  .footer-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--space-xl);
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--space-lg);
  }

  .footer-logo {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-light);
    display: flex;
    align-items: center;
    margin-bottom: var(--space-md);
  }

  .footer-about {
    margin-bottom: var(--space-md);
    line-height: 1.6;
  }

  .social-icons {
    display: flex;
    gap: var(--space-md);
  }

  .footer-title {
    color: var(--accent-light);
    font-size: 1.3rem;
    margin-bottom: var(--space-lg);
    position: relative;
    padding-bottom: var(--space-sm);
  }

  .footer-bottom {
    background: rgba(0, 0, 0, 0.3);
    padding: var(--space-md) 0;
    text-align: center;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
  }

  .footer-legal {
    display: flex;
    gap: var(--space-lg);
  }

  .footer-legal a {
    color: var(--neutral-dark);
    transition: var(--transition-fast);
  }

  /* Estilos del modal */
  .modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
  }

  .modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
  }

  .close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }

  .close:hover,
  .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }

  @media (max-width: 768px) {
    .footer-bottom .container {
      flex-direction: column;
      gap: var(--space-sm);
    }
  }
</style>