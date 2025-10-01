<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../php/conexion.php';
require_once __DIR__ . '/../../../php/auth.php';

if (!isAdmin() && !isSeller() && !isTechnician()) {
    header('Location: ../dashboard.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

try {
    // Obtener datos del vehículo
    $sql = "SELECT v.*, 
                   c.nombre_cliente, c.telefono_cliente, c.correo_cliente,
                   COUNT(cot.id_cotizacion) as total_cotizaciones
            FROM vehiculos v
            LEFT JOIN cliente_vehiculo cv ON v.id_vehiculo = cv.id_vehiculo
            LEFT JOIN clientes c ON cv.id_cliente = c.id_cliente
            LEFT JOIN cotizaciones cot ON v.id_vehiculo = cot.id_vehiculo
            WHERE v.id_vehiculo = ? AND v.activo = 1
            GROUP BY v.id_vehiculo";
    $stmt = $conex->prepare($sql);
    $stmt->execute([$id]);
    $vehiculo = $stmt->fetch();

    if (!$vehiculo) {
        header('Location: index.php?error=Vehículo no encontrado');
        exit;
    }

    // Obtener historial de cotizaciones del vehículo
    $sqlCotizaciones = "SELECT c.id_cotizacion, c.fecha_cotizacion, c.total_cotizacion, 
                                c.estado_cotizacion, u.nombre_completo as vendedor
                        FROM cotizaciones c
                        JOIN usuarios u ON c.id_usuario = u.id_usuario
                        WHERE c.id_vehiculo = ? AND c.activo = 1
                        ORDER BY c.fecha_cotizacion DESC";
    $stmtCotizaciones = $conex->prepare($sqlCotizaciones);
    $stmtCotizaciones->execute([$id]);
    $cotizaciones = $stmtCotizaciones->fetchAll();

    // Obtener historial de trabajos del vehículo
    $sqlTrabajos = "SELECT t.id_trabajos, t.fecha_inicio, t.fecha_fin, t.estado,
                           c.id_cotizacion, c.total_cotizacion
                    FROM trabajos t
                    JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
                    WHERE c.id_vehiculo = ? AND t.activo = 1
                    ORDER BY t.fecha_inicio DESC";
    $stmtTrabajos = $conex->prepare($sqlTrabajos);
    $stmtTrabajos->execute([$id]);
    $trabajos = $stmtTrabajos->fetchAll();

} catch (PDOException $e) {
    error_log("Error al obtener vehículo: " . $e->getMessage());
    header('Location: index.php?error=Error al obtener vehículo');
    exit;
}

require_once __DIR__ . '/../../includes/head.php';
$title = 'Ver Vehículo | Nacional Tapizados';
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
            max-width: 1000px;
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
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
            color: var(--text-color);
        }

        .info-row {
            display: flex;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .info-label {
            font-weight: 600;
            color: var(--text-color);
            width: 40%;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        .info-value {
            color: var(--text-color);
            width: 60%;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
            font-weight: 500;
        }

        .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--text-color);
            --bs-table-border-color: var(--border-color);
            width: 100%;
        }

        .table th {
            background-color: rgba(140, 74, 63, 0.4);
            color: var(--text-color);
            font-weight: 600;
            border-color: var(--border-color);
        }

        .table td, .table th {
            padding: 0.75rem;
            border-color: var(--border-color);
            color: var(--text-color);
        }

        .table tbody tr {
            background-color: rgba(0, 0, 0, 0.2);
        }

        .table tbody tr:nth-child(even) {
            background-color: rgba(0, 0, 0, 0.3);
        }

        .table tfoot th {
            background-color: rgba(140, 74, 63, 0.3);
            font-weight: 600;
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

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(5px);
            border-left: 4px solid var(--info-color);
            background-color: rgba(13, 202, 240, 0.2);
            color: var(--text-color);
        }

        .alert strong {
            color: var(--text-color);
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background-color: var(--bg-transparent-light);
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            border: 1px solid var(--border-color);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-muted);
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

            .info-row {
                flex-direction: column;
            }

            .info-label, .info-value {
                width: 100%;
            }

            .info-label {
                margin-bottom: 0.5rem;
            }

            .btn-group {
                width: 100%;
                justify-content: center;
                margin-top: 1rem;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
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
    </style>   
</head>

<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-car"></i> Vehículo #<?= $vehiculo['id_vehiculo'] ?>
            </h1>
            <div class="d-flex gap-2 flex-wrap">
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="editar.php?id=<?= $vehiculo['id_vehiculo'] ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
            </div>
        </div>

        <!-- Información General del Vehículo -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Vehículo</h5>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-label">Marca:</div>
                    <div class="info-value"><?= htmlspecialchars($vehiculo['marca_vehiculo']) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Modelo:</div>
                    <div class="info-value"><?= htmlspecialchars($vehiculo['modelo_vehiculo']) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Placa:</div>
                    <div class="info-value"><?= htmlspecialchars($vehiculo['placa_vehiculo']) ?></div>
                </div>
                <?php if (!empty($vehiculo['nombre_cliente'])): ?>
                <div class="info-row">
                    <div class="info-label">Propietario:</div>
                    <div class="info-value"><?= htmlspecialchars($vehiculo['nombre_cliente']) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Teléfono Propietario:</div>
                    <div class="info-value"><?= htmlspecialchars($vehiculo['telefono_cliente']) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Correo Propietario:</div>
                    <div class="info-value"><?= htmlspecialchars($vehiculo['correo_cliente']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($vehiculo['notas_vehiculo'])): ?>
                <div class="info-row">
                    <div class="info-label">Notas:</div>
                    <div class="info-value"><?= nl2br(htmlspecialchars($vehiculo['notas_vehiculo'])) ?></div>
                </div>
                <?php endif; ?>
                <div class="info-row">
                    <div class="info-label">Estado:</div>
                    <div class="info-value">
                        <span class="badge bg-success">Activo</span>
                    </div>
                </div>
            </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
