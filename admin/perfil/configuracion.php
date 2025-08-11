<?php
require_once __DIR__. '/../../php/auth.php';
requireAuth();

if (!isAdmin()) {
    header("Location: dashboard.php");
    exit;
}

$titulo_pagina = "Ajustes del Sistema";
?>

<?php include '../includes/head.php'; ?>
    <a href="javascript:history.back()" class="back-button">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
<div class="main-content">
    <div class="dashboard-content">
        <div class="dashboard-head">
            <h1 class="dashboard-title">Ajustes del Sistema</h1>
            <div class="user-role-badge">
                <span class="role-badge">Administrador</span>
            </div>
        </div>

        <div class="summary-grid">
            <!-- Tarjeta de Configuración General -->
            <div class="summary-card">
                <div class="summary-content">
                    <div>
                        <h3 class="summary-title">Configuración General</h3>
                        <p class="summary-value"><i class="fas fa-cog"></i></p>
                    </div>
                    <div class="summary-icon">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                </div>
                <div class="card-body p-3">
                    <form>
                        <div class="form-group">
                            <label>Nombre del Taller</label>
                            <input type="text" class="form-control" value="Taller de Tapicería" disabled>
                        </div>
                        <div class="form-group">
                            <label>IVA (%)</label>
                            <input type="number" class="form-control" value="19" disabled>
                        </div>
                        <button class="btn btn-secondary" disabled>Guardar Cambios</button>
                    </form>
                </div>
            </div>

            <!-- Tarjeta de Apariencia -->
            <div class="summary-card">
                <div class="summary-content">
                    <div>
                        <h3 class="summary-title">Apariencia</h3>
                        <p class="summary-value"><i class="fas fa-palette"></i></p>
                    </div>
                    <div class="summary-icon">
                        <i class="fas fa-brush"></i>
                    </div>
                </div>
                <div class="card-body p-3">
                    <form>
                        <div class="form-group">
                            <label>Color Principal</label>
                            <input type="color" class="form-control" value="#5E3023" disabled>
                        </div>
                        <div class="form-group">
                            <label>Color Secundario</label>
                            <input type="color" class="form-control" value="#8C4A3F" disabled>
                        </div>
                        <button class="btn btn-secondary" disabled>Aplicar Cambios</button>
                    </form>
                </div>
            </div>

            <!-- Tarjeta de Usuarios -->
            <div class="summary-card">
                <div class="summary-content">
                    <div>
                        <h3 class="summary-title">Usuarios</h3>
                        <p class="summary-value"><i class="fas fa-users"></i></p>
                    </div>
                    <div class="summary-icon">
                        <i class="fas fa-user-cog"></i>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Esta sección muestra los usuarios del sistema.
                    </div>
                    <a href="usuarios.php" class="btn btn-primary">Administrar Usuarios</a>
                </div>
            </div>

            <!-- Tarjeta de Sistema -->
            <div class="summary-card">
                <div class="summary-content">
                    <div>
                        <h3 class="summary-title">Sistema</h3>
                        <p class="summary-value"><i class="fas fa-server"></i></p>
                    </div>
                    <div class="summary-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="system-info">
                        <p><strong>Versión:</strong> 1.0.0</p>
                        <p><strong>PHP:</strong> <?php echo phpversion(); ?></p>
                        <p><strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
                    </div>
                    <button class="btn btn-outline-secondary mt-2" disabled>
                        <i class="fas fa-download"></i> Generar Backup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>