<?php
require_once __DIR__ . '/../../php/auth.php';
require_once __DIR__ . '/../../php/conexion.php';
requireAuth();

$cotizacion_id = $_GET['id'] ?? null;
$usuario_id = getUserId();

if (!$cotizacion_id) {
    header("Location: cotizaciones_usuario.php");
    exit;
}

try {
    // Obtener información de la cotización
    $stmt = $conex->prepare("SELECT c.*, cl.nombre_cliente, cl.correo_cliente, cl.telefono_cliente, 
                            v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo, 
                            u.nombre_completo as nombre_vendedor
                           FROM cotizaciones c 
                           JOIN clientes cl ON c.id_cliente = cl.id_cliente 
                           JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
                           JOIN usuarios u ON c.id_usuario = u.id_usuario
                           WHERE c.id_cotizacion = ? AND c.id_usuario = ?");
    $stmt->execute([$cotizacion_id, $usuario_id]);
    $cotizacion = $stmt->fetch();

    if (!$cotizacion) {
        die("Cotización no encontrada o no tienes permiso para verla");
    }

    // Obtener servicios asociados a la cotización
    $stmt = $conex->prepare("SELECT s.nombre_servicio, s.descripcion_servicio, cs.precio
                            FROM cotizacion_servicios cs
                            JOIN servicios s ON cs.id_servicio = s.id_servicio
                            WHERE cs.id_cotizacion = ?");
    $stmt->execute([$cotizacion_id]);
    $servicios = $stmt->fetchAll();

    // Obtener trabajos asociados (si existen)
    $stmt = $conex->prepare("SELECT * FROM trabajos WHERE id_cotizacion = ?");
    $stmt->execute([$cotizacion_id]);
    $trabajo = $stmt->fetch();

} catch (PDOException $e) {
    die("Error al obtener detalles de la cotización: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Cotización</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/perfil.css">
    <style>
        .detail-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--neutral-light);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: var(--border-soft);
        }
        
        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--neutral-medium);
        }
        
        .detail-title {
            color: var(--primary-dark);
            margin: 0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            margin-left: 1rem;
        }
        
        .status-pendiente {
            background-color: rgba(230, 200, 140, 0.2);
            color: var(--gold-accent);
            border: 1px solid var(--gold-pastel);
        }
        
        .status-aprobado {
            background-color: rgba(138, 155, 110, 0.2);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }
        
        .status-rechazada {
            background-color: rgba(196, 90, 77, 0.2);
            color: var(--error-color);
            border: 1px solid var(--error-color);
        }
        
        .status-completada {
            background-color: rgba(94, 48, 35, 0.2);
            color: var(--primary-dark);
            border: 1px solid var(--primary-dark);
        }
        
        .detail-section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            color: var(--primary-dark);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--gold-cream);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .info-card {
            background-color: white;
            padding: 1rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            border: var(--border-soft);
        }
        
        .info-label {
            font-weight: 500;
            color: var(--accent-color);
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            color: var(--text-dark);
        }
        
        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .services-table th, .services-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--neutral-medium);
        }
        
        .services-table th {
            background-color: var(--primary-dark);
            color: white;
        }
        
        .services-table tr:hover {
            background-color: var(--neutral-medium);
        }
        
        .total-row {
            font-weight: bold;
            background-color: var(--gold-cream);
        }
        
        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn-print {
            background-color: var(--neutral-light);
            color: var(--primary-dark);
            border: 1px solid var(--neutral-dark);
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition-fast);
        }
        
        .btn-print:hover {
            background-color: var(--neutral-medium);
        }
        
        @media print {
            .no-print {
                display: none;
            }
            
            body {
                background-color: white;
                color: black;
            }
            
            .detail-container {
                box-shadow: none;
                border: none;
                padding: 0;
            }
        }
    </style>
</head>
<body class="admin-body">
    <?php include '../includes/head.php'; ?>

    <div class="content-wrapper">
        <main class="detail-container">
            <div class="detail-header">
                <h1 class="detail-title">
                    Cotización #<?= $cotizacion['id_cotizacion'] ?>
                    <span class="status-badge status-<?= strtolower($cotizacion['estado_cotizacion']) ?>">
                        <?= $cotizacion['estado_cotizacion'] ?>
                    </span>
                </h1>
                <div>
                    <div class="info-label">Fecha:</div>
                    <div class="info-value"><?= date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])) ?></div>
                </div>
            </div>
            
            <div class="detail-section">
                <h2 class="section-title">Información del Cliente</h2>
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">Nombre:</div>
                        <div class="info-value"><?= htmlspecialchars($cotizacion['nombre_cliente']) ?></div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">Correo electrónico:</div>
                        <div class="info-value"><?= htmlspecialchars($cotizacion['correo_cliente']) ?></div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">Teléfono:</div>
                        <div class="info-value"><?= htmlspecialchars($cotizacion['telefono_cliente']) ?></div>
                    </div>
                </div>
            </div>
            
            <div class="detail-section">
                <h2 class="section-title">Información del Vehículo</h2>
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">Marca:</div>
                        <div class="info-value"><?= htmlspecialchars($cotizacion['marca_vehiculo']) ?></div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">Modelo:</div>
                        <div class="info-value"><?= htmlspecialchars($cotizacion['modelo_vehiculo']) ?></div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">Placa:</div>
                        <div class="info-value"><?= htmlspecialchars($cotizacion['placa_vehiculo']) ?></div>
                    </div>
                </div>
            </div>
            
            <div class="detail-section">
                <h2 class="section-title">Servicios Cotizados</h2>
                <table class="services-table">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servicios as $servicio): ?>
                        <tr>
                            <td><?= htmlspecialchars($servicio['nombre_servicio']) ?></td>
                            <td><?= htmlspecialchars($servicio['descripcion_servicio']) ?></td>
                            <td>$<?= number_format($servicio['precio'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" style="text-align: right;"><strong>Subtotal:</strong></td>
                            <td>$<?= number_format($cotizacion['subtotal_cotizacion'], 2) ?></td>
                        </tr>
                        <?php if ($cotizacion['valor_adicional'] > 0): ?>
                        <tr>
                            <td colspan="2" style="text-align: right;"><strong>Valor Adicional:</strong></td>
                            <td>$<?= number_format($cotizacion['valor_adicional'], 2) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="2" style="text-align: right;"><strong>IVA (<?= $cotizacion['iva'] ?>%):</strong></td>
                            <td>$<?= number_format(($cotizacion['subtotal_cotizacion'] + $cotizacion['valor_adicional']) * ($cotizacion['iva'] / 100), 2) ?></td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="2" style="text-align: right;"><strong>Total:</strong></td>
                            <td>$<?= number_format($cotizacion['total_cotizacion'], 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="detail-section">
                <h2 class="section-title">Notas</h2>
                <div class="info-card">
                    <?= nl2br(htmlspecialchars($cotizacion['notas_cotizacion'])) ?>
                </div>
            </div>
            
            <?php if ($trabajo): ?>
            <div class="detail-section">
                <h2 class="section-title">Información del Trabajo</h2>
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">Estado:</div>
                        <div class="info-value">
                            <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $trabajo['estado'])) ?>">
                                <?= $trabajo['estado'] ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">Fecha Inicio:</div>
                        <div class="info-value"><?= date('d/m/Y', strtotime($trabajo['fecha_inicio'])) ?></div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">Fecha Fin:</div>
                        <div class="info-value">
                            <?= $trabajo['fecha_fin'] != '0000-00-00' ? date('d/m/Y', strtotime($trabajo['fecha_fin'])) : 'Pendiente' ?>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($trabajo['notas'])): ?>
                <div class="info-card" style="margin-top: 1rem;">
                    <div class="info-label">Notas del trabajo:</div>
                    <div class="info-value"><?= nl2br(htmlspecialchars($trabajo['notas'])) ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($trabajo['fotos'])): ?>
                <div style="margin-top: 1rem;">
                    <div class="info-label">Fotos del trabajo:</div>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.5rem;">
                        <?php 
                            $fotos = explode(',', $trabajo['fotos']);
                            foreach ($fotos as $foto): 
                                if (!empty($foto)):
                        ?>
                            <img src="<?= htmlspecialchars(trim($foto)) ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px; cursor: pointer;" 
                                 onclick="window.open('<?= htmlspecialchars(trim($foto)) ?>', '_blank')">
                        <?php 
                                endif;
                            endforeach; 
                        ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <div class="actions no-print">
                <a href="cotizaciones.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a cotizaciones
                </a>
                
                <a href="imprimir_cotizaciones.php" class="btn-print" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimir cotización
                </a>
            </div>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>