<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin() && !isSeller()) {
    header('Location: ../dashboard.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

try {
    // Obtener datos de la cotización
    $sql = "SELECT c.*, cl.nombre_cliente, cl.telefono_cliente, cl.correo_cliente,
            v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
            u.nombre_completo as nombre_vendedor
            FROM cotizaciones c
            JOIN clientes cl ON c.id_cliente = cl.id_cliente
            JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
            JOIN usuarios u ON c.id_usuario = u.id_usuario
            WHERE c.id_cotizacion = ?";
    $stmt = $conex->prepare($sql);
    $stmt->execute([$id]);
    $cotizacion = $stmt->fetch();

    if (!$cotizacion) {
        header('Location: index.php?error=Cotización no encontrada');
        exit;
    }

    // Obtener servicios de la cotización
    $sqlServicios = "SELECT s.nombre_servicio, cs.precio 
                    FROM cotizacion_servicios cs
                    JOIN servicios s ON cs.id_servicio = s.id_servicio
                    WHERE cs.id_cotizacion = ?";
    $stmtServicios = $conex->prepare($sqlServicios);
    $stmtServicios->execute([$id]);
    $servicios = $stmtServicios->fetchAll();

    // Verificar si ya existe un trabajo para esta cotización
    $stmtTrabajo = $conex->prepare("SELECT * FROM trabajos WHERE id_cotizacion = ?");
    $stmtTrabajo->execute([$id]);
    $trabajo = $stmtTrabajo->fetch();
} catch (PDOException $e) {
    error_log("Error al obtener cotización: " . $e->getMessage());
    header('Location: index.php?error=Error al obtener cotización');
    exit;
}

// Calcular totales para mostrar correctamente
$subtotal = $cotizacion['subtotal_cotizacion'];
$iva = $cotizacion['iva'];
$valor_adicional = $cotizacion['valor_adicional'] ?? 0;
$total = $cotizacion['total_cotizacion'];

require_once __DIR__ . '/../../includes/head.php';
$title = 'Ver Cotización | Nacional Tapizados';
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
            --dark-bg: #1a1a1a;
            --darker-bg: #121212;
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
            max-width: 1400px;
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

        /* Estilos para tarjetas */
        .card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0 !important;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Estilos para tablas */
        .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--text-color);
            --bs-table-border-color: var(--border-color);
            width: 100%;
        }

        .table th {
            background-color: rgba(140, 74, 63, 0.3);
            color: var(--text-color);
            font-weight: 500;
        }

        .table td, .table th {
            padding: 0.75rem;
            border-color: var(--border-color);
        }

        .table tfoot th {
            background-color: rgba(140, 74, 63, 0.2);
        }

        /* Estilos para botones */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            gap: 0.5rem;
        }

        .btn i {
            font-size: 0.9rem;
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

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: rgba(25, 135, 84, 1);
        }

        .btn-warning {
            background-color: var(--warning-color);
            color: black;
        }

        .btn-warning:hover {
            background-color: rgba(255, 193, 7, 1);
        }

        .btn-info {
            background-color: var(--info-color);
            color: white;
        }

        .btn-info:hover {
            background-color: rgba(13, 202, 240, 1);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: rgba(220, 53, 69, 1);
        }

        /* Estilos para badges */
        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
            font-weight: 500;
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

        .alert-info {
            background-color: rgba(13, 202, 240, 0.2);
            border-left: 4px solid var(--info-color);
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

            .btn-group {
                width: 100%;
                justify-content: center;
                margin-top: 1rem;
            }
        }
    </style>   
</head>

<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-file-invoice-dollar"></i> Cotización #<?= $cotizacion['id_cotizacion'] ?>
            </h1>
            <div class="d-flex gap-2 flex-wrap">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="editar.php?id=<?= $cotizacion['id_cotizacion'] ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <?php if (!$trabajo): ?>
                <a href="../trabajos/crear.php?cotizacion=<?= $cotizacion['id_cotizacion'] ?>" class="btn btn-primary">
                    <i class="fas fa-hammer"></i> Crear Trabajo
                </a>
                <?php else: ?>
                <a href="../trabajos/ver.php?id=<?= $trabajo['id_trabajos'] ?>" class="btn btn-info">
                    <i class="fas fa-eye"></i> Ver Trabajo
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Información General -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Cliente:</strong> <?= htmlspecialchars($cotizacion['nombre_cliente']) ?></p>
                        <p><strong>Teléfono:</strong> <?= htmlspecialchars($cotizacion['telefono_cliente']) ?></p>
                        <p><strong>Correo:</strong> <?= htmlspecialchars($cotizacion['correo_cliente']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Vehículo:</strong> <?= htmlspecialchars($cotizacion['marca_vehiculo']) ?> <?= htmlspecialchars($cotizacion['modelo_vehiculo']) ?></p>
                        <p><strong>Placa:</strong> <?= htmlspecialchars($cotizacion['placa_vehiculo']) ?></p>
                        <p><strong>Vendedor:</strong> <?= htmlspecialchars($cotizacion['nombre_vendedor']) ?></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($cotizacion['fecha_cotizacion'])) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Estado:</strong> 
                            <span class="badge bg-<?= 
                                $cotizacion['estado_cotizacion'] == 'Aprobado' ? 'success' : 
                                ($cotizacion['estado_cotizacion'] == 'Rechazada' ? 'danger' : 
                                ($cotizacion['estado_cotizacion'] == 'Completada' ? 'primary' : 'warning')) 
                            ?>">
                                <?= $cotizacion['estado_cotizacion'] ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Servicios -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Servicios y Costos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th class="text-end">Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicios as $servicio): ?>
                            <tr>
                                <td><?= htmlspecialchars($servicio['nombre_servicio']) ?></td>
                                <td class="text-end">$ <?= number_format($servicio['precio'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Subtotal Servicios</th>
                                <td class="text-end">$ <?= number_format($subtotal, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <th>IVA (19%)</th>
                                <td class="text-end">$ <?= number_format($iva, 0, ',', '.') ?></td>
                            </tr>
                            <?php if ($valor_adicional > 0): ?>
                            <tr>
                                <th>Valor Adicional</th>
                                <td class="text-end">$ <?= number_format($valor_adicional, 0, ',', '.') ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th class="fs-5">TOTAL</th>
                                <td class="text-end fs-5 fw-bold">$ <?= number_format($total, 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <?php if ($valor_adicional > 0 && !empty($cotizacion['notas_cotizacion'])): ?>
                <div class="alert alert-info mt-3">
                    <div>
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota sobre el valor adicional:</strong><br>
                        <?= nl2br(htmlspecialchars($cotizacion['notas_cotizacion'])) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Notas Adicionales (si no están relacionadas con el valor adicional) -->
        <?php if (!empty($cotizacion['notas_cotizacion']) && $valor_adicional == 0): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notas Adicionales</h5>
            </div>
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($cotizacion['notas_cotizacion'])) ?></p>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Botones de acción -->
        <div class="d-flex justify-content-center gap-3 mt-4">
            <button class="btn btn-success" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Imprimir Cotización
            </button>
            <a href="generar_pdf.php?id=<?= $cotizacion['id_cotizacion'] ?>" class="btn btn-danger">
                <i class="fas fa-file-pdf me-1"></i>Generar PDF
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>