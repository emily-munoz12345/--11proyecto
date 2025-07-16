<!-- Sidebar -->
<aside class="admin-sidebar">
    <div class="sidebar-header text-center py-4">
        <h4 class="sidebar-title">
            <i class="fas fa-car-alt me-2"></i>
            <span>Nacional Tapizados</span>
        </h4>
        <p class="sidebar-subtitle">Sistema de Gestión</p>
    </div>
    
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Opciones para todos los roles -->
             
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/formularios/clientes/index.php">
                    <i class="fas fa-users me-2"></i>
                    <span>Clientes</span>
                    <span class="badge-notification">5</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/formularios/vehiculos/index.php">
                    <i class="fas fa-car me-2"></i>
                    <span>Vehículos</span>
                </a>
            </li>
            
            <!-- Opciones para Vendedor y Admin -->
            <?php if (isSeller() || isAdmin()): ?>
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/formularios/cotizaciones/index.php">
                    <i class="fas fa-file-invoice-dollar me-2"></i>
                    <span>Cotizaciones</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Opciones para Técnico y Admin -->
            <?php if (isAdmin()): ?>
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/formularios/trabajos/index.php">
                    <i class="fas fa-tools me-2"></i>
                    <span>Trabajos</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/formularios/materiales/index.php">
                    <i class="fas fa-box-open me-2"></i>
                    <span>Materiales</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/formularios/servicios/index.php">
                    <i class="fas fa-concierge-bell me-2"></i>
                    <span>Servicios</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Opción solo para Admin -->
            <?php if (isAdmin()): ?>
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/formularios/usuarios/index.php">
                    <i class="fas fa-user-cog me-2"></i>
                    <span>Usuarios</span>
                </a>
            </li>
            <?php endif; ?>
            
            <li class="nav-item mt-auto">
                <a class="nav-link logout-link" href="/--11proyecto/php/logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>