<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nacional Tapizados</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* ESTILOS GENERALES */
    :root {
      --dorado-oscuro: #996515;
      --dorado: #B8860B;
      --dorado-claro: #d4af37;
      --dorado-hover: #FFD700;
      --vinotinto: #a83a3a;       /* Color principal más claro */
      --vinotinto-claro: #c45e5e; /* Versión más clara */
      --vinotinto-suave: #d88a8a; /* Para textos secundarios */
      --vinotinto-scroll: #d88a8a;/* Color barra scroll */
      --texto-claro: #f8f9fa;
      --fondo-oscuro: rgba(0, 0, 0, 0.85);
      --fondo-semioscuro: rgba(0, 0, 0, 0.7);
      --space-xs: 0.5rem;
      --space-sm: 1rem;
      --space-md: 1.5rem;
      --space-lg: 2rem;
      --space-xl: 3rem;
      --space-xxl: 4rem;
      --transition-fast: 0.2s ease;
    }

    /* RESET Y ESTILOS BASE */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Arial', sans-serif;
      line-height: 1.6;
    }

    /* ESTILOS DEL FOOTER (COLORES DORADOS) */
    .footer {
      background: linear-gradient(var(--fondo-semioscuro), var(--fondo-semioscuro)),
                  url('textura-cuero.jpg') center/cover;
      color: var(--texto-claro);
      position: relative;
      margin-top: var(--space-xxl);
    }

    .footer::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 10px;
      background: linear-gradient(to right, var(--dorado-oscuro), var(--dorado));
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
      color: var(--texto-claro);
      display: flex;
      align-items: center;
      margin-bottom: var(--space-md);
    }

    .footer-logo span {
      color: var(--dorado-claro);
      font-weight: 800;
    }

    .footer-logo i {
      color: var(--dorado);
      font-size: 1.5rem;
      margin-right: var(--space-xs);
    }

    .footer-about {
      margin-bottom: var(--space-md);
      line-height: 1.6;
      color: rgba(255, 255, 255, 0.8);
      font-size: 0.95rem;
    }

    .footer-title {
      color: var(--dorado);
      font-size: 1.3rem;
      margin-bottom: var(--space-lg);
      position: relative;
      padding-bottom: var(--space-sm);
      font-weight: 600;
    }

    .footer-title::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: 0;
      width: 50px;
      height: 2px;
      background: var(--dorado-claro);
    }

    .footer-contact li, 
    .footer-list li {
      margin-bottom: var(--space-sm);
      display: flex;
      align-items: center;
      gap: var(--space-sm);
      font-size: 0.95rem;
    }

    .footer-contact i, 
    .footer-list i {
      color: var(--dorado);
      width: 20px;
      text-align: center;
      font-size: 1rem;
    }

    .footer-bottom {
      background: rgba(0, 0, 0, 0.3);
      padding: var(--space-md) 0;
      text-align: center;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .footer-bottom .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 var(--space-lg);
    }

    .footer-bottom p {
      margin-bottom: var(--space-sm);
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.7);
    }

    .footer-legal {
      display: flex;
      justify-content: center;
      gap: var(--space-lg);
      flex-wrap: wrap;
    }

    .footer-legal a {
      color: var(--dorado-claro);
      transition: var(--transition-fast);
      text-decoration: none;
      font-size: 0.9rem;
    }

    .footer-legal a:hover {
      color: var(--dorado-hover);
      text-decoration: underline;
    }

    /* REDES SOCIALES */
    .social-icons {
      display: flex;
      gap: var(--space-md);
      margin-top: var(--space-lg);
    }

    .social-icon {
      position: relative;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 255, 255, 0.1);
      transition: all 0.3s ease;
    }

    .social-icon i {
      color: var(--dorado);
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .social-icon:hover {
      background: rgba(184, 134, 11, 0.2);
      transform: translateY(-3px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .social-icon:hover i {
      color: var(--dorado-hover);
    }

    .social-tooltip {
      position: absolute;
      top: -30px;
      left: 50%;
      transform: translateX(-50%);
      background: var(--dorado-oscuro);
      color: #fff;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.75rem;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
      white-space: nowrap;
    }

    .social-icon:hover .social-tooltip {
      opacity: 1;
      visibility: visible;
      top: -40px;
    }

    /* MODAL (ESTILO VINOTINTO) */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.7);
    }

    .modal-content {
      background-color: var(--fondo-oscuro);
      margin: 5% auto;
      padding: 30px;
      border: 1px solid var(--vinotinto);
      width: 80%;
      max-width: 800px;
      border-radius: 5px;
      color: var(--texto-claro);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
      max-height: 80vh;
      overflow-y: auto;
      position: relative;
      
      /* Estilos personalizados para la barra de scroll */
      scrollbar-width: thin;
      scrollbar-color: var(--vinotinto-scroll) var(--fondo-oscuro);
    }

    /* Personalización de scroll para WebKit (Chrome, Safari) */
    .modal-content::-webkit-scrollbar {
      width: 10px;
    }

    .modal-content::-webkit-scrollbar-track {
      background: var(--fondo-oscuro);
      border-radius: 5px;
    }

    .modal-content::-webkit-scrollbar-thumb {
      background-color: var(--vinotinto-scroll);
      border-radius: 5px;
      border: 2px solid var(--fondo-oscuro);
    }

    .modal-content::-webkit-scrollbar-thumb:hover {
      background-color: var(--vinotinto-claro);
    }

    .modal-header {
      position: relative;
      margin-bottom: 20px;
    }

    .close {
      color: var(--vinotinto);
      position: absolute;
      top: -15px;
      right: -15px;
      font-size: 28px;
      font-weight: bold;
      background: rgba(0, 0, 0, 0.8);
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid var(--vinotinto);
      transition: all 0.3s ease;
      z-index: 10;
    }

    .close:hover,
    .close:focus {
      color: #fff;
      background: var(--vinotinto);
      text-decoration: none;
      cursor: pointer;
    }

    .modal-section {
      margin-bottom: 20px;
      padding-bottom: 20px;
      border-bottom: 1px solid rgba(168, 58, 58, 0.3);
    }

    .modal-section:last-child {
      border-bottom: none;
    }

    #modal-text h2 {
      color: var(--vinotinto);
      margin-bottom: 20px;
      border-bottom: 1px solid var(--vinotinto);
      padding-bottom: 10px;
      font-size: 1.8rem;
    }
    
    #modal-text h5 {
      color: var(--vinotinto-claro);
      margin: 15px 0 10px;
      font-size: 1.2rem;
    }
    
    #modal-text h6 {
      color: var(--vinotinto-suave);
      margin: 10px 0 5px;
      font-size: 1rem;
      font-weight: 500;
    }
    
    #modal-text p, 
    #modal-text li {
      color: rgba(248, 249, 250, 0.9);
      line-height: 1.6;
      font-size: 0.95rem;
    }

    #modal-text ul {
      padding-left: 20px;
      margin: 10px 0;
    }

    #modal-text li {
      margin-bottom: 5px;
    }

    /* RESPONSIVIDAD */
    @media (max-width: 768px) {
      .footer-container {
        grid-template-columns: 1fr;
        gap: var(--space-lg);
      }
      
      .footer-col {
        margin-bottom: var(--space-xl);
      }
      
      .footer-legal {
        flex-direction: column;
        gap: var(--space-sm);
      }
      
      .modal-content {
        width: 90%;
        padding: 20px;
      }

      .close {
        top: -10px;
        right: -10px;
        width: 30px;
        height: 30px;
        font-size: 20px;
      }
    }

    @media (max-width: 480px) {
      .footer-top {
        padding: var(--space-xl) 0;
      }
      
      .footer-logo {
        font-size: 1.5rem;
      }
      
      .modal-content {
        width: 95%;
        padding: 15px;
      }
      
      #modal-text h2 {
        font-size: 1.5rem;
      }
      
      #modal-text h5 {
        font-size: 1.1rem;
      }
    }
  </style>
</head>
<body>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-top">
      <div class="footer-container">
        <div class="footer-col">
          <div class="footer-logo">
            <i class="fas fa-car-alt"></i>
            <span>Nacional</span>Tapizados
          </div>
          <p class="footer-about">Más de 25 años transformando vehículos con trabajos de tapicería de alta calidad y diseño personalizado.</p>
          <div class="social-icons">
            <a href="#" target="_blank" class="social-icon">
              <i class="fab fa-facebook-f"></i>
              <span class="social-tooltip">Facebook</span>
            </a>
            <a href="https://instagram.com/nacionalde_tapizados/" target="_blank" class="social-icon">
              <i class="fab fa-instagram"></i>
              <span class="social-tooltip">Instagram</span>
            </a>
            <a href="https://wa.me/573204948958?text=Hola%2C%20quiero%20más%20información" target="_blank" class="social-icon">
              <i class="fab fa-whatsapp"></i>
              <span class="social-tooltip">WhatsApp</span>
            </a>
          </div>
        </div>

        <div class="footer-col">
          <h3 class="footer-title">Horario</h3>
          <ul class="footer-list">
            <li><i class="far fa-clock"></i> Lunes a Viernes: 8:00am - 12:30am / 1:30pm - 6:00pm</li>
            <li><i class="far fa-clock"></i> Sábados: 8:00am - 2:00pm</li>
            <li><i class="far fa-calendar-times"></i> Domingos: Cerrado</li>
          </ul>
        </div>

        <div class="footer-col">
          <h3 class="footer-title">Contacto</h3>
          <ul class="footer-contact">
            <li><i class="fas fa-map-marker-alt"></i> Cr 13 #4-43, Tunja-Boyacá</li>
            <li><i class="fas fa-phone"></i> +57 320 494 8958</li>
            <li><i class="fas fa-envelope"></i> info@nacionaltapizados.com</li>
          </ul>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container">
        <p>&copy; <script>document.write(new Date().getFullYear())</script> Nacional Tapizados. Todos los derechos reservados.</p>
        <div class="footer-legal">
          <a href="#" onclick="openModal('privacy')">Política de privacidad</a>
          <a href="#" onclick="openModal('terms')">Términos de servicio</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Modal -->
  <div id="modal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <span class="close" onclick="closeModal()">&times;</span>
      </div>
      <div id="modal-text"></div>
    </div>
  </div>

  <script>
    function openModal(type) {
      const modal = document.getElementById('modal');
      const modalText = document.getElementById('modal-text');

      if (type === 'privacy') {
        modalText.innerHTML = `
          <h2>Política de Privacidad</h2>
          <div class="modal-section">
            <h5>1. Introducción</h5>
            <p>Esta política de privacidad describe cómo recopilamos, usamos y protegemos la información personal de nuestros usuarios en Nacional De Tapizados.</p>
          </div>
          <div class="modal-section">
            <h5>2. Información que Recopilamos</h5>
            <h6>Información Personal</h6>
            <p>Recopilamos datos como nombre, dirección de correo electrónico, número de teléfono y dirección física cuando los usuarios se registran o realizan una compra.</p>
            <h6>Información no Personal</h6>
            <p>También recopilamos datos que no identifican personalmente a los usuarios, como la dirección IP y el tipo de navegador.</p>
          </div>
          <div class="modal-section">
            <h5>3. Uso de la Información</h5>
            <p>Utilizamos la información recopilada para:</p>
            <ul>
              <li>Procesar pedidos y gestionar cuentas de usuario.</li>
              <li>Enviar comunicaciones sobre productos, promociones y actualizaciones.</li>
              <li>Mejorar nuestros servicios y la experiencia del usuario.</li>
            </ul>
          </div>
          <div class="modal-section">
            <h5>4. Compartición de la Información</h5>
            <p>No vendemos, intercambiamos ni transferimos a terceros la información personal de los usuarios, excepto en los siguientes casos:</p>
            <ul>
              <li>Proveedores de servicios que nos ayudan a operar nuestro sitio web.</li>
              <li>Cuando la ley lo requiera o para proteger nuestros derechos.</li>
            </ul>
          </div>
          <div class="modal-section">
            <h5>5. Seguridad de la Información</h5>
            <p>Implementamos medidas de seguridad adecuadas para proteger la información personal contra el acceso no autorizado.</p>
          </div>
          <div class="modal-section">
            <h5>6. Derechos de los Usuarios</h5>
            <p>Los usuarios tienen derecho a:</p>
            <ul>
              <li>Acceder a la información personal que tenemos sobre ellos.</li>
              <li>Solicitar la corrección de datos inexactos.</li>
              <li>Solicitar la eliminación de su información personal.</li>
            </ul>
          </div>
          <div class="modal-section">
            <h5>7. Cambios a esta Política</h5>
            <p>Nos reservamos el derecho de modificar esta política de privacidad. Cualquier cambio será publicado en esta página.</p>
          </div>
          <div class="modal-section">
            <h5>8. Contacto</h5>
            <p>Si tienes preguntas sobre esta política de privacidad, contáctanos en olfonsojose@gmail.com.</p>
          </div>`;
      } else if (type === 'terms') {
        modalText.innerHTML = `
          <h2>Términos de Servicio</h2>
          <div class="modal-section">
            <h5>1. Aceptación de los Términos</h5>
            <p>Al acceder y utilizar Nacional De Tapizados, aceptas cumplir con estos términos de servicio. Si no estás de acuerdo, no debes utilizar el sitio.</p>
          </div>
          <div class="modal-section">
            <h5>2. Modificaciones</h5>
            <p>Nos reservamos el derecho de modificar estos términos en cualquier momento. Las modificaciones se publicarán en esta página y se considerarán efectivas inmediatamente.</p>
          </div>
          <div class="modal-section">
            <h5>3. Uso del Sitio</h5>
            <h6>Elegibilidad:</h6>
            <p>Debes tener al menos 18 años para utilizar este sitio.</p>
            <h6>Conducta:</h6>
            <p>Te comprometes a no usar el sitio para actividades ilegales o no autorizadas.</p>
          </div>
          <div class="modal-section">
            <h5>4. Propiedad Intelectual</h5>
            <p>Todo el contenido del sitio está protegido por derechos de autor. No puedes reproducir, distribuir o modificar este contenido sin nuestro consentimiento.</p>
          </div>
          <div class="modal-section">
            <h5>5. Productos y Servicios</h5>
            <h6>Descripción:</h6>
            <p>Hacemos todo lo posible para garantizar que la descripción de nuestros productos sea precisa.</p>
            <h6>Precios:</h6>
            <p>Los precios están sujetos a cambios sin previo aviso.</p>
          </div>
          <div class="modal-section">
            <h5>6. Limitación de Responsabilidad</h5>
            <p>No seremos responsables de ningún daño indirecto que resulte del uso o la incapacidad de usar el sitio.</p>
          </div>
          <div class="modal-section">
            <h5>7. Enlaces a Terceros</h5>
            <p>Nuestro sitio puede contener enlaces a sitios de terceros. No somos responsables del contenido de esos sitios.</p>
          </div>
          <div class="modal-section">
            <h5>8. Legislación Aplicable</h5>
            <p>Estos términos se regirán e interpretarán de acuerdo con las leyes de [país o estado].</p>
          </div>`;
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
</body>
</html>