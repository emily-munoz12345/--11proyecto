<header class="header">
  <nav class="navbar">
    <a href="/--11proyecto/public/index.php" class="logo">
      <i class="fas fa-car-alt me-2"></i>
      <span>Nacional</span>Tapizados
    </a>
    
    <div class="nav-menu">
      <a href="/--11proyecto/public/servicios.php" class="nav-link">Servicios</a>
      <a href="/--11proyecto/public/trabajos.php" class="nav-link">Trabajos</a>
      <a href="/--11proyecto/public/materiales.php" class="nav-link">Materiales</a>
      <a href="/--11proyecto/public/nosotros.php" class="nav-link">Nosotros</a>
      <a href="/--11proyecto/public/contacto.php" class="nav-link">Contacto</a>
    </div>
    
    <div class="nav-actions">
      <button class="mobile-menu-btn">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </nav>
</header>

<style>
.header {
  background: var(--primary-dark);
  position: sticky;
  top: 0;
  z-index: 1000;
  box-shadow: var(--shadow-md);
  border-bottom: var(--border-stitched);
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 var(--space-lg);
  height: 80px;
}

.logo {
  color: var(--text-light);
  font-size: 1.8rem;
  font-weight: 700;
  display: flex;
  align-items: center;
}

.logo i {
  color: var(--accent-light);
  font-size: 1.5rem;
}

.logo span {
  color: var(--neutral-dark);
  margin-right: var(--space-xs);
}

.nav-menu {
  display: flex;
  gap: var(--space-xl);
}

.nav-link {
  color: var(--neutral-dark);
  font-weight: 600;
  padding: var(--space-sm) 0;
  position: relative;
  transition: var(--transition-normal);
}

.nav-link:hover {
  color: var(--accent-light);
}

.nav-link::after {
  content: '';
  position: absolute;
  width: 0;
  height: 3px;
  bottom: 0;
  left: 0;
  background: var(--accent-light);
  transition: var(--transition-normal);
}

.nav-link:hover::after {
  width: 100%;
}

.btn-cotizar {
  background: var(--gradient-leather);
  color: var(--text-light);
  padding: var(--space-sm) var(--space-md);
  border-radius: 4px;
  font-weight: 600;
  transition: var(--transition-normal);
  margin-right: var(--space-md);
}

.btn-cotizar:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-sm);
}

.mobile-menu-btn {
  display: none;
  background: none;
  border: none;
  color: var(--neutral-dark);
  font-size: 1.5rem;
  cursor: pointer;
}

@media (max-width: 992px) {
  .nav-menu {
    position: fixed;
    top: 80px;
    left: -100%;
    width: 100%;
    height: calc(100vh - 80px);
    background: var(--primary-dark);
    flex-direction: column;
    align-items: center;
    padding: var(--space-xl) 0;
    transition: var(--transition-normal);
    z-index: 999;
  }

  .nav-menu.active {
    left: 0;
  }

  .mobile-menu-btn {
    display: block;
  }

  .btn-cotizar {
    display: none;
  }
}
</style>

<script>
document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
  document.querySelector('.nav-menu').classList.toggle('active');
});
</script>