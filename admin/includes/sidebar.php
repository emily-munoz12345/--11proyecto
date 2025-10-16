<?php
require_once __DIR__ . '/../../php/auth.php';
require_once __DIR__ . '/../../php/conexion.php';

// Obtener el rol del usuario actual
$userRole = getUserRole();

// Definir qué opciones puede ver cada rol
$allowedMenuItems = [
    'Administrador' => ['inicio', 'clientes', 'vehiculos', 'cotizaciones', 'trabajos', 'materiales', 'servicios', 'usuarios', 'papelera', 'buzon'],
    'Tecnico' => ['inicio', 'clientes', 'vehiculos', 'trabajos', 'materiales'],
    'Vendedor' => ['inicio', 'clientes', 'vehiculos', 'cotizaciones', 'servicios']
];

// Obtener las opciones permitidas para el rol actual
$userMenuItems = $allowedMenuItems[$userRole] ?? ['inicio'];

// Función para contar registros activos
function getActiveCount($conex, $table) {
    try {
        $stmt = $conex->query("SELECT COUNT(*) as total FROM $table WHERE activo = 1");
        return $stmt->fetch()['total'];
    } catch (PDOException $e) {
        error_log("Error al contar registros en $table: " . $e->getMessage());
        return 0;
    }
}

// Obtener conteos para mostrar en badges
$counts = [];
if (in_array('clientes', $userMenuItems)) $counts['clientes'] = getActiveCount($conex, 'clientes');
if (in_array('vehiculos', $userMenuItems)) $counts['vehiculos'] = getActiveCount($conex, 'vehiculos');
if (in_array('cotizaciones', $userMenuItems)) $counts['cotizaciones'] = getActiveCount($conex, 'cotizaciones');
if (in_array('materiales', $userMenuItems)) $counts['materiales'] = getActiveCount($conex, 'materiales');
if (in_array('servicios', $userMenuItems)) $counts['servicios'] = getActiveCount($conex, 'servicios');
if (in_array('usuarios', $userMenuItems)) $counts['usuarios'] = getActiveCount($conex, 'usuarios');

// Contar mensajes no leídos (solo para administradores)
if (in_array('buzon', $userMenuItems)) {
    try {
        $stmt = $conex->query("SELECT COUNT(*) as total FROM mensajes_contacto WHERE leido = 0 AND activo = 1");
        $counts['buzon'] = $stmt->fetch()['total'];
    } catch (PDOException $e) {
        $counts['buzon'] = 0;
    }
}
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <h4 class="sidebar-title">
            <button class="collapse-btn" title="Colapsar menú" style="background: none; border: none; padding: 0; cursor: pointer; color: #FFF8E1;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FFF8E1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                    <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"></path>
                    <circle cx="7" cy="17" r="2"></circle>
                    <path d="M9 17h6"></path>
                    <circle cx="17" cy="17" r="2"></circle>
                </svg>
                <span>Nacional Tapizados</span>
            </button>
        </h4>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <!-- Inicio - Disponible para todos -->
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/dashboard.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span>Inicio</span>
                </a>
            </li>

            <!-- Clientes -->
            <?php if (in_array('clientes', $userMenuItems)): ?>
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/formularios/clientes/index.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                    </svg>
                    <span>Clientes</span>
                    <span class="badge-notification"><?= $counts['clientes'] ?? 0 ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Vehículos -->
            <?php if (in_array('vehiculos', $userMenuItems)): ?>
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/formularios/vehiculos/index.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                        <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"></path>
                        <circle cx="7" cy="17" r="2"></circle>
                        <path d="M9 17h6"></path>
                        <circle cx="17" cy="17" r="2"></circle>
                    </svg>
                    <span>Vehículos</span>
                    <span class="badge-notification"><?= $counts['vehiculos'] ?? 0 ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Cotizaciones -->
            <?php if (in_array('cotizaciones', $userMenuItems)): ?>
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/formularios/cotizaciones/index.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" x2="8" y1="13" y2="13"></line>
                        <line x1="16" x2="8" y1="17" y2="17"></line>
                    </svg>
                    <span>Cotizaciones</span>
                    <span class="badge-notification"><?= $counts['cotizaciones'] ?? 0 ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Trabajos -->
            <?php if (in_array('trabajos', $userMenuItems)): ?>
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/formularios/trabajos/index.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                    <span>Trabajos</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Materiales -->
            <?php if (in_array('materiales', $userMenuItems)): ?>
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/formularios/materiales/index.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                        <path d="M20 7h-3a2 2 0 0 1-2-2V2"></path>
                        <path d="M9 6a2 2 0 0 1 2-2h2v12a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2z"></path>
                    </svg>
                    <span>Materiales</span>
                    <span class="badge-notification"><?= $counts['materiales'] ?? 0 ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Servicios -->
            <?php if (in_array('servicios', $userMenuItems)): ?>
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/formularios/servicios/index.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    <span>Servicios</span>
                    <span class="badge-notification"><?= $counts['servicios'] ?? 0 ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Usuarios -->
            <?php if (in_array('usuarios', $userMenuItems)): ?>
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/formularios/usuarios/index.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>Usuarios</span>
                    <span class="badge-notification"><?= $counts['usuarios'] ?? 0 ?></span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Papelera -->
            <?php if (in_array('papelera', $userMenuItems)): ?>
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/formularios/papelera.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                        <path d="M3 6h18"></path>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                    </svg>
                    <span>Papelera</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Buzón de Entrada -->
            <?php if (in_array('buzon', $userMenuItems)): ?>
            <li class="nav-item">
                <a class="nav-link" href="/--11proyecto/admin/formularios/buzon.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <span>Buzón de Entrada</span>
                    <?php if (($counts['buzon'] ?? 0) > 0): ?>
                    <span class="badge-notification"><?= $counts['buzon'] ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>

            <!-- Cerrar Sesión - Disponible para todos -->
            <li class="nav-item mt-auto">
                <a class="nav-link logout-link" href="/--11proyecto/php/logout.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" x2="9" y1="12" y2="12"></line>
                    </svg>
                    <span>Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<style>
    /* Estilos para el sidebar colapsable */
    .admin-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 250px;
        background-color: #2c3e50;
        color: #FFF8E1;
        transition: width 0.3s ease;
        z-index: 1000;
        overflow-x: hidden;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }
    
    .admin-sidebar.collapsed {
        width: 60px;
    }
    
    .admin-sidebar.collapsed .sidebar-title span,
    .admin-sidebar.collapsed .nav-link span,
    .admin-sidebar.collapsed .badge-notification {
        display: none;
    }
    
    .admin-sidebar.collapsed .nav-link {
        padding: 0.75rem 1rem;
        justify-content: center;
    }
    
    .admin-sidebar.collapsed .nav-link svg {
        margin-right: 0;
    }
    
    .sidebar-header {
        padding: 1rem;
        border-bottom: 1px solid rgba(255,248,225,0.1);
    }
    
    .sidebar-title {
        margin: 0;
        display: flex;
        align-items: center;
    }
    
    .sidebar-nav {
        padding: 1rem 0;
        height: calc(100vh - 60px);
        overflow-y: auto;
    }
    
    .nav-link {
        color: #FFF8E1;
        padding: 0.75rem 1.5rem;
        display: flex;
        align-items: center;
        transition: background-color 0.2s;
    }
    
    .nav-link:hover {
        background-color: rgba(255,255,255,0.1);
    }
    
    .nav-link svg {
        margin-right: 0.75rem;
        flex-shrink: 0;
    }
    
    .badge-notification {
        background-color: #e74c3c;
        color: white;
        border-radius: 50%;
        padding: 0.15rem 0.45rem;
        font-size: 0.75rem;
        margin-left: auto;
    }
    
    .logout-link {
        border-top: 1px solid rgba(255,248,225,0.1);
        margin-top: auto;
    }
    
    /* Overlay para cerrar al hacer clic fuera */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0,0,0,0.5);
        z-index: 999;
        display: none;
    }
    
    @media (min-width: 992px) {
        .admin-sidebar.collapsed + .sidebar-overlay {
            display: none !important;
        }
    }
</style>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.admin-sidebar');
        const collapseBtn = document.querySelector('.collapse-btn');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        // Función para colapsar el sidebar
        function collapseSidebar() {
            sidebar.classList.add('collapsed');
            localStorage.setItem('sidebarCollapsed', 'true');
        }
        
        // Función para expandir el sidebar
        function expandSidebar() {
            sidebar.classList.remove('collapsed');
            localStorage.setItem('sidebarCollapsed', 'false');
            sidebarOverlay.style.display = 'none';
        }
        
        // Alternar colapso/expansión al hacer clic en el botón
        collapseBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (sidebar.classList.contains('collapsed')) {
                expandSidebar();
            } else {
                collapseSidebar();
                // En dispositivos móviles, mostrar overlay cuando está colapsado
                if (window.innerWidth < 992) {
                    sidebarOverlay.style.display = 'block';
                }
            }
        });
        
        // Cerrar sidebar al hacer clic fuera (solo en móviles)
        sidebarOverlay.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                collapseSidebar();
                sidebarOverlay.style.display = 'none';
            }
        });
        
        // Cerrar sidebar al hacer clic fuera en desktop (solo cuando está expandido)
        document.addEventListener('click', function(e) {
            if (window.innerWidth >= 992 && 
                !sidebar.contains(e.target) && 
                !sidebar.classList.contains('collapsed')) {
                collapseSidebar();
            }
        });
        
        // Prevenir que los clics dentro del sidebar cierren el sidebar
        sidebar.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // Cargar estado del sidebar desde localStorage
        const isCollapsed = localStorage.getItem('sidebarCollapsed');
        if (isCollapsed === 'true') {
            collapseSidebar();
        }
        
        // Ajustar comportamiento en cambio de tamaño de ventana
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                sidebarOverlay.style.display = 'none';
            } else if (sidebar.classList.contains('collapsed')) {
                sidebarOverlay.style.display = 'block';
            }
        });
    });
</script>