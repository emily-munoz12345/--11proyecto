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
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-whatsapp"></i></a>
        </div>
      </div>

      <div class="footer-col">
        <h3 class="footer-title">Servicios</h3>
        <ul class="footer-links">
          <li><a href="#">Tapizado completo</a></li>
          <li><a href="#">Reparaciones</a></li>
          <li><a href="#">Impermeabilización</a></li>
          <li><a href="#">Personalización</a></li>
          <li><a href="#">Restauración clásica</a></li>
        </ul>
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

      <div class="footer-col">
        <h3 class="footer-title">Newsletter</h3>
        <form class="newsletter-form">
          <input type="email" placeholder="Tu correo electrónico" required>
          <button type="submit">Suscribirse</button>
        </form>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="container">
      <p>&copy; <?php echo date('Y'); ?> Nacional Tapizados. Todos los derechos reservados.</p>
      <div class="footer-legal">
        <a href="#">Política de privacidad</a>
        <a href="#">Términos de servicio</a>
      </div>
    </div>
  </div>
</footer>

<style>
.footer {
  background: 
    linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)),
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

.footer-logo i {
  color: var(--accent-light);
  font-size: 1.5rem;
}

.footer-logo span {
  color: var(--neutral-dark);
  margin-right: var(--space-xs);
}

.footer-about {
  margin-bottom: var(--space-md);
  line-height: 1.6;
}

.social-icons {
  display: flex;
  gap: var(--space-md);
}

.social-icons a {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background: rgba(255,255,255,0.1);
  border-radius: 50%;
  color: var(--neutral-dark);
  transition: var(--transition-normal);
}

.social-icons a:hover {
  background: var(--accent-color);
  color: var(--text-light);
  transform: translateY(-3px);
}

.footer-title {
  color: var(--accent-light);
  font-size: 1.3rem;
  margin-bottom: var(--space-lg);
  position: relative;
  padding-bottom: var(--space-sm);
}

.footer-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 50px;
  height: 2px;
  background: var(--accent-light);
}

.footer-links li {
  margin-bottom: var(--space-sm);
}

.footer-links a {
  color: var(--neutral-dark);
  transition: var(--transition-fast);
  display: block;
}

.footer-links a:hover {
  color: var(--accent-light);
  padding-left: var(--space-sm);
}

.footer-contact li {
  display: flex;
  align-items: center;
  margin-bottom: var(--space-sm);
  gap: var(--space-sm);
}

.footer-contact i {
  color: var(--accent-light);
  width: 20px;
  text-align: center;
}

.newsletter-form {
  display: flex;
  flex-direction: column;
  gap: var(--space-sm);
}

.newsletter-form input {
  padding: var(--space-sm);
  border: 1px solid var(--neutral-dark);
  background: rgba(255,255,255,0.1);
  color: var(--text-light);
}

.newsletter-form button {
  background: var(--gradient-leather);
  color: var(--text-light);
  padding: var(--space-sm);
  border: none;
  cursor: pointer;
  transition: var(--transition-normal);
}

.newsletter-form button:hover {
  background: var(--accent-color);
}

.footer-bottom {
  background: rgba(0,0,0,0.3);
  padding: var(--space-md) 0;
  text-align: center;
  border-top: 1px solid rgba(255,255,255,0.1);
}

.footer-bottom .container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 var(--space-lg);
}

.footer-legal {
  display: flex;
  gap: var(--space-lg);
}

.footer-legal a {
  color: var(--neutral-dark);
  transition: var(--transition-fast);
}

.footer-legal a:hover {
  color: var(--accent-light);
}

@media (max-width: 768px) {
  .footer-bottom .container {
    flex-direction: column;
    gap: var(--space-sm);
  }
}
</style>