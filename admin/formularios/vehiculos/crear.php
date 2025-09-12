<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

// Verificar permisos
if (!isAdmin() && !isSeller()) {
    $_SESSION['mensaje'] = 'No tienes permisos para esta acción';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: ../dashboard.php');
    exit;
}

// Obtener lista de clientes para el select
try {
    $stmt = $conex->query("SELECT id_cliente, nombre_cliente FROM clientes WHERE activo = 1 ORDER BY nombre_cliente");
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al obtener clientes: ' . $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'danger';
    $clientes = [];
}

$title = 'Nacional Tapizados - Nuevo Vehículo';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.8);
            --primary-hover: rgba(140, 74, 63, 1);
            --secondary-color: rgba(108, 117, 125, 0.8);
            --text-color: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --bg-transparent: rgba(255, 255, 255, 0.1);
            --bg-transparent-light: rgba(255, 255, 255, 0.15);
            --border-color: rgba(255, 255, 255, 0.2);
            --success-color: rgba(25, 135, 84, 0.8);
            --danger-color: rgba(220, 53, 69, 0.8);
            --warning-color: rgba(255, 193, 7, 0.8);
            --info-color: rgba(13, 202, 240, 0.8);
        }

        body {
            background-image: url('https://pfst.cf2.poecdn.net/base/image/60ab54eef562f30f85a67bde31f924f078199dae0b7bc6c333dfb467a2c13471?w=1024&h=768&pmaid=442168253');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            min-height: 100vh;
        }

        .main-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--bg-transparent);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .page-title i {
            margin-right: 12px;
            color: var(--primary-color);
        }

        /* Estilos para formularios */
        .form-container {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
        }

        .form-label {
            color: var(--text-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            padding: 0.75rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px var(--primary-color);
            color: var(--text-color);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-text {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        /* Estilos para botones */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            gap: 0.5rem;
        }

        .btn i {
            font-size: 1rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-secondary:hover {
            background-color: rgba(108, 117, 125, 1);
        }

        .btn-outline-secondary {
            background-color: transparent;
            border: 1px solid var(--secondary-color);
            color: var(--text-color);
        }

        .btn-outline-secondary:hover {
            background-color: var(--secondary-color);
            color: white;
        }

        /* Estilos para alertas */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .alert-success {
            background-color: rgba(25, 135, 84, 0.2);
            border-left: 4px solid var(--success-color);
            color: white;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            border-left: 4px solid var(--danger-color);
            color: white;
        }

        .alert-warning {
            background-color: rgba(255, 193, 7, 0.2);
            border-left: 4px solid var(--warning-color);
            color: white;
        }

        .alert-info {
            background-color: rgba(13, 202, 240, 0.2);
            border-left: 4px solid var(--info-color);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                margin: 1rem;
                padding: 1.5rem;
            }

            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-container {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .d-md-flex {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-car"></i> Nuevo Vehículo
            </h1>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Mensajes -->
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
                <div>
                    <i class="fas fa-<?=
                                        $_SESSION['tipo_mensaje'] === 'success' ? 'check-circle' : ($_SESSION['tipo_mensaje'] === 'danger' ? 'times-circle' : ($_SESSION['tipo_mensaje'] === 'warning' ? 'exclamation-triangle' : 'info-circle'))
                                        ?> me-2"></i>
                    <?= htmlspecialchars($_SESSION['mensaje']) ?>
                </div>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
            </div>
            <?php unset($_SESSION['mensaje']); unset($_SESSION['tipo_mensaje']); ?>
        <?php endif; ?>
        
        <!-- Formulario -->
        <div class="form-container">
            <form action="procesar.php" method="POST" id="formVehiculo">
                <input type="hidden" name="accion" value="crear">
                
<div class="cliente-section">
    <div class="row mb-4">
        <div class="col-md-6 ">
            <label for="cliente" class="form-label">Cliente *</label>
            <select class="form-select " id="cliente" name="id_cliente" required>
                <option value="">Seleccione un cliente</option>
                                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['id_cliente'] ?>">
                                    <?= htmlspecialchars($cliente['nombre_cliente']) ?>
                                </option>
                            <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="marca" class="form-label">Marca *</label>
                        <input type="text" class="form-control" id="marca" name="marca" required
                               pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]{2,50}" 
                               title="Solo letras (2-50 caracteres)"
                               placeholder="Ej: Toyota, Ford, Chevrolet">
                    </div>
                    <div class="col-md-6">
                        <label for="modelo" class="form-label">Modelo *</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" required
                               pattern="[A-Za-záéíóúÁÉÍÓÚñÑ0-9\s]{1,50}" 
                               title="Letras y números (1-50 caracteres)"
                               placeholder="Ej: Corolla, F-150, Spark">
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="placa" class="form-label">Placa *</label>
                        <input type="text" class="form-control" id="placa" name="placa" 
                               placeholder="Ejemplo: ABC123" required
                               pattern="[A-Za-z]{3}[0-9]{3,4}" 
                               title="3 letras seguidas de 3-4 números (ej: ABC123)">
                        <div class="form-text">Formato: 3 letras seguidas de 3-4 números (ej: ABC123)</div>
                    </div>
                    <div class="col-md-6">
                        <label for="anio" class="form-label">Año</label>
                        <input type="number" class="form-control" id="anio" name="anio" 
                               min="1900" max="<?= date('Y') + 1 ?>" 
                               placeholder="Ej: 2020">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="color" class="form-label">Color</label>
                        <input type="text" class="form-control" id="color" name="color" 
                               pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]{2,30}" 
                               title="Solo letras (2-30 caracteres)"
                               placeholder="Ej: Rojo, Azul, Negro">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="notas" class="form-label">Notas Adicionales</label>
                    <textarea class="form-control" id="notas" name="notas" rows="4" 
                              maxlength="500" placeholder="Observaciones o detalles adicionales del vehículo..."></textarea>
                    <div class="form-text">Máximo 500 caracteres</div>
                </div>
                
                <div class="d-flex gap-2 justify-content-end">
                    <button type="reset" class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i> Limpiar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Vehículo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Validación adicional del formulario
    document.getElementById('formVehiculo').addEventListener('submit', function(e) {
        // Convertir placa a mayúsculas
        const placaInput = document.getElementById('placa');
        placaInput.value = placaInput.value.toUpperCase();
        
        // Validar formato de placa
        const placaPattern = /^[A-Z]{3}[0-9]{3,4}$/;
        if (!placaPattern.test(placaInput.value)) {
            e.preventDefault();
            alert('El formato de la placa no es válido. Debe ser 3 letras seguidas de 3-4 números (ej: ABC123)');
            placaInput.focus();
            return false;
        }
        
        // Convertir marca y modelo a formato adecuado (primera letra en mayúscula)
        const marcaInput = document.getElementById('marca');
        const modeloInput = document.getElementById('modelo');
        const colorInput = document.getElementById('color');
        
        if (marcaInput.value) {
            marcaInput.value = marcaInput.value.charAt(0).toUpperCase() + marcaInput.value.slice(1).toLowerCase();
        }
        
        if (modeloInput.value) {
            modeloInput.value = modeloInput.value.charAt(0).toUpperCase() + modeloInput.value.slice(1).toLowerCase();
        }
        
        if (colorInput.value) {
            colorInput.value = colorInput.value.charAt(0).toUpperCase() + colorInput.value.slice(1).toLowerCase();
        }
    });

    // Auto-completar año actual
    document.getElementById('anio').addEventListener('focus', function() {
        if (!this.value) {
            this.value = new Date().getFullYear();
        }
    });
    </script>
</body>
</html>