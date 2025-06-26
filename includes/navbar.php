<?php
// Verificar si el usuario está logueado (opcional)
$usuarioLogueado = isset($_SESSION['id_usuario']);
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="/--11proyecto/public/index.php">
            <i class="fas fa-car me-2"></i>Nacional Tapizados
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="/--11proyecto/public/index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'trabajos-realizados.php' ? 'active' : '' ?>" href="/--11proyecto/public/trabajos.php">Trabajos Realizados</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'materiales.php' ? 'active' : '' ?>" href="/--11proyecto/public/materiales.php">Materiales</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'servicios.php' ? 'active' : '' ?>" href="/--11proyecto/public/servicios.php">Servicios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'nosotros.php' ? 'active' : '' ?>" href="/--11proyecto/public/nosotros.php">Nosotros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'contacto.php' ? 'active' : '' ?>" href="/--11proyecto/public/contacto.php">Contacto</a>
                </li>
            </ul>
        </div>
    </div>
</nav>