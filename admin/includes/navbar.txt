<header class="header">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/admin/dashboard.php">
            <i class="fas fa-tools me-2"></i>Taller Admin
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/admin/dashboard.php">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-users me-1"></i> Clientes
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/admin/formularios/clientes/listar.php">Listar</a></li>
                        <li><a class="dropdown-item" href="/admin/formularios/clientes/crear.php">Nuevo</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/formularios/cotizaciones/listar.php">
                        <i class="fas fa-file-invoice-dollar me-1"></i> Cotizaciones
                    </a>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['user_name'] ?? 'Admin'; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-1"></i> Configuración</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/admin/logout.php"><i class="fas fa-sign-out-alt me-1"></i> Salir</a></li>
                    </ul>
                </li>
            </ul>
        </div>
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