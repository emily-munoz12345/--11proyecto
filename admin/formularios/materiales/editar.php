<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin()) {
    header('Location: ../dashboard.php');
    exit;
}

// Inicializar variables de sesión para mensajes
if (!isset($_SESSION['mensaje'])) {
    $_SESSION['mensaje'] = '';
    $_SESSION['tipo_mensaje'] = '';
}

// Obtener el ID del material a editar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener información del material
$material = null;
if ($id > 0) {
    try {
        $stmt = $conex->prepare("SELECT * FROM materiales WHERE id_material = ?");
        $stmt->execute([$id]);
        $material = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'Error al obtener el material: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
}

// Si no se encuentra el material, redirigir
if (!$material) {
    $_SESSION['mensaje'] = 'Material no encontrado';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_material'] ?? '';
    $precio = $_POST['precio_metro'] ?? '';
    $stock = $_POST['stock_material'] ?? '';
    $categoria = $_POST['categoria_material'] ?? '';
    $proveedor = $_POST['proveedor_material'] ?? '';
    $descripcion = $_POST['descripcion_material'] ?? '';

    try {
        $stmt = $conex->prepare("UPDATE materiales SET nombre_material = ?, precio_metro = ?, stock_material = ?, categoria_material = ?, proveedor_material = ?, descripcion_material = ? WHERE id_material = ?");
        
        if ($stmt->execute([$nombre, $precio, $stock, $categoria, $proveedor, $descripcion, $id])) {
            $_SESSION['mensaje'] = 'Material actualizado correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
            header('Location: index.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'Error al actualizar el material: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
    }
}

require_once __DIR__ . '/../../includes/head.php';
$title = 'Editar Material ';
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
            --bg-transparent: rgba(0, 0, 0, 0.5);
            --bg-transparent-light: rgba(0, 0, 0, 0.4);
            --bg-input: rgba(0, 0, 0, 0.6);
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
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--bg-transparent);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
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
            color: var(--text-color);
        }

        .page-title i {
            margin-right: 12px;
            color: var(--primary-color);
        }

        .card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label i {
            color: var(--primary-color);
            width: 16px;
        }

        .form-control {
            background-color: var(--bg-input);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }

        .form-control:focus {
            background-color: rgba(0, 0, 0, 0.7);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(140, 74, 63, 0.25);
            color: var(--text-color);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

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

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-secondary:hover {
            background-color: rgba(108, 117, 125, 1);
            transform: translateY(-2px);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(5px);
            border-left: 4px solid var(--danger-color);
            background-color: rgba(220, 53, 69, 0.2);
            color: var(--text-color);
        }

        .alert-success {
            background-color: rgba(25, 135, 84, 0.2);
            border-left: 4px solid var(--success-color);
        }

        .alert-warning {
            background-color: rgba(255, 193, 7, 0.2);
            border-left: 4px solid var(--warning-color);
        }

        .alert-info {
            background-color: rgba(13, 202, 240, 0.2);
            border-left: 4px solid var(--info-color);
        }

        .required-field::after {
            content: " *";
            color: var(--danger-color);
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-section-title i {
            color: var(--primary-color);
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

            .page-title {
                font-size: 1.5rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .d-md-flex {
                flex-direction: column;
            }
        }

        @media (max-width: 576px) {
            .form-section-title {
                font-size: 1.1rem;
            }
            
            .row {
                margin-left: -0.5rem;
                margin-right: -0.5rem;
            }
            
            .col-md-6 {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-edit"></i> Editar Material
            </h1>
            <div class="d-flex gap-2">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if (!empty($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
                <div>
                    <i class="fas fa-<?=
                                        $_SESSION['tipo_mensaje'] === 'success' ? 'check-circle' : 'exclamation-triangle'
                                        ?> me-2"></i>
                    <?= $_SESSION['mensaje'] ?>
                </div>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
            </div>
            <?php
            $_SESSION['mensaje'] = '';
            $_SESSION['tipo_mensaje'] = '';
            ?>
        <?php endif; ?>

        <!-- Formulario -->
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-cube"></i>
                            Información del Material
                        </h3>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nombre_material" class="form-label required-field">
                                    <i class="fas fa-tag"></i>
                                    Nombre del Material
                                </label>
                                <input type="text" class="form-control" id="nombre_material" name="nombre_material" 
                                       value="<?= htmlspecialchars($material['nombre_material']) ?>" 
                                       placeholder="Ingrese el nombre del material"
                                       required>
                            </div>
                            <div class="col-md-6">
                                <label for="categoria_material" class="form-label required-field">
                                    <i class="fas fa-layer-group"></i>
                                    Categoría
                                </label>
                                <input type="text" class="form-control" id="categoria_material" name="categoria_material" 
                                       value="<?= htmlspecialchars($material['categoria_material']) ?>" 
                                       placeholder="Categoría del material"
                                       required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="precio_metro" class="form-label required-field">
                                    <i class="fas fa-dollar-sign"></i>
                                    Precio por Metro
                                </label>
                                <input type="number" class="form-control" id="precio_metro" name="precio_metro" 
                                       value="<?= htmlspecialchars($material['precio_metro']) ?>" 
                                       placeholder="0.00"
                                       step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label for="stock_material" class="form-label required-field">
                                    <i class="fas fa-boxes"></i>
                                    Stock Disponible (metros)
                                </label>
                                <input type="number" class="form-control" id="stock_material" name="stock_material" 
                                       value="<?= htmlspecialchars($material['stock_material']) ?>" 
                                       placeholder="0.0"
                                       step="0.1" min="0" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="proveedor_material" class="form-label required-field">
                                <i class="fas fa-truck"></i>
                                Proveedor
                            </label>
                            <input type="text" class="form-control" id="proveedor_material" name="proveedor_material" 
                                   value="<?= htmlspecialchars($material['proveedor_material']) ?>" 
                                   placeholder="Nombre del proveedor"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label for="descripcion_material" class="form-label">
                                <i class="fas fa-align-left"></i>
                                Descripción
                            </label>
                            <textarea class="form-control" id="descripcion_material" name="descripcion_material" 
                                      rows="4" placeholder="Descripción opcional del material..."><?= htmlspecialchars($material['descripcion_material']) ?></textarea>
                            <div class="form-text text-muted mt-1">
                                <i class="fas fa-info-circle"></i>
                                Campo opcional para información adicional del material.
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="index.php" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Actualizar Material
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include '../../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const nombre = document.getElementById('nombre_material').value.trim();
            const categoria = document.getElementById('categoria_material').value.trim();
            const precio = document.getElementById('precio_metro').value.trim();
            const stock = document.getElementById('stock_material').value.trim();
            const proveedor = document.getElementById('proveedor_material').value.trim();
            
            if (!nombre || !categoria || !precio || !stock || !proveedor) {
                e.preventDefault();
                alert('Por favor complete todos los campos requeridos (*)');
                return false;
            }
            
            // Validación de números positivos
            if (parseFloat(precio) < 0 || parseFloat(stock) < 0) {
                e.preventDefault();
                alert('El precio y stock deben ser valores positivos');
                return false;
            }
        });

        // Efectos visuales para los campos
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    </script>
</body>
</html>