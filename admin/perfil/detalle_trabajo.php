<?php
require_once __DIR__ . '/../../php/auth.php';
require_once __DIR__ . '/../../php/conexion.php';
requireAuth();

$trabajo_id = $_GET['id'] ?? null;
$usuario_id = getUserId();

if (!$trabajo_id) {
    header("Location: trabajos_usuario.php");
    exit;
}

try {
    // Obtener información del trabajo
    $stmt = $conex->prepare("SELECT t.*, c.id_cliente, cl.nombre_cliente, cl.correo_cliente, cl.telefono_cliente,
                            v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo,
                            u.nombre_completo as nombre_tecnico
                           FROM trabajos t
                           JOIN cotizaciones c ON t.id_cotizacion = c.id_cotizacion
                           JOIN clientes cl ON c.id_cliente = cl.id_cliente
                           JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
                           JOIN usuarios u ON c.id_usuario = u.id_usuario
                           WHERE t.id_trabajos = ? AND c.id_usuario = ?");
    $stmt->execute([$trabajo_id, $usuario_id]);
    $trabajo = $stmt->fetch();

    if (!$trabajo) {
        die("Trabajo no encontrado o no tienes permiso para verlo");
    }

    // Obtener servicios asociados a la cotización
    $stmt = $conex->prepare("SELECT s.nombre_servicio, s.descripcion_servicio, cs.precio
                            FROM cotizacion_servicios cs
                            JOIN servicios s ON cs.id_servicio = s.id_servicio
                            WHERE cs.id_cotizacion = ?");
    $stmt->execute([$trabajo['id_cotizacion']]);
    $servicios = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error al obtener detalles del trabajo: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Trabajo</title>
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
        
        .status-en-progreso {
            background-color: rgba(214, 163, 77, 0.2);
            color: var(--gold-dark);
            border: 1px solid var(--gold-dark);
        }
        
        .status-entregado {
            background-color: rgba(138, 155, 110, 0.2);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }
        
        .status-cancelado {
            background-color: rgba(196, 90, 77, 0.2);
            color: var(--error-color);
            border: 1px solid var(--error-color);
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
        
        .photo-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .photo-item {
            position: relative;
            overflow: hidden;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            cursor: pointer;
        }
        
        .photo-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            transition: var(--transition-normal);
        }
        
        .photo-item:hover img {
            transform: scale(1.1);
        }
        
        .photo-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(0,0,0,0.7);
            color: white;
            padding: 0.5rem;
            font-size: 0.8rem;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
        }
        
        .modal-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 90%;
            margin-top: 5%;
        }
        
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body class="admin-body">
    <?php include '../includes/head.php'; ?>

    <div class="content-wrapper">
        <main class="detail-container">
            <div class="detail-header">
                <h1 class="detail-title">
                    Trabajo #<?= $trabajo['id_trabajos'] ?>
                    <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $trabajo['estado'])) ?>">
                        <?= $trabajo['estado'] ?>
                    </span>
                </h1>
                <div>
                    <div class="info-label">Cotización asociada:</div>
                    <div class="info-value">#<?= $trabajo['id_cotizacion'] ?></div>
                </div>
            </div>
            
            <div class="detail-section">
                <h2 class="section-title">Información del Cliente</h2>
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">Nombre:</div>
                        <div class="info-value"><?= htmlspecialchars($trabajo['nombre_cliente']) ?></div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">Correo electrónico:</div>
                        <div class="info-value"><?= htmlspecialchars($trabajo['correo_cliente']) ?></div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">Teléfono:</div>
                        <div class="info-value"><?= htmlspecialchars($trabajo['telefono_cliente']) ?></div>
                    </div>
                </div>
            </div>
            
            <div class="detail-section">
                <h2 class="section-title">Información del Vehículo</h2>
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">Marca:</div>
                        <div class="info-value"><?= htmlspecialchars($trabajo['marca_vehiculo']) ?></div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">Modelo:</div>
                        <div class="info-value"><?= htmlspecialchars($trabajo['modelo_vehiculo']) ?></div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">Placa:</div>
                        <div class="info-value"><?= htmlspecialchars($trabajo['placa_vehiculo']) ?></div>
                    </div>
                </div>
            </div>
            
            <div class="detail-section">
                <h2 class="section-title">Detalles del Trabajo</h2>
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">Técnico asignado:</div>
                        <div class="info-value"><?= htmlspecialchars($trabajo['nombre_tecnico']) ?></div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">Fecha de inicio:</div>
                        <div class="info-value"><?= date('d/m/Y', strtotime($trabajo['fecha_inicio'])) ?></div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">Fecha de entrega:</div>
                        <div class="info-value">
                            <?= $trabajo['fecha_fin'] != '0000-00-00' ? date('d/m/Y', strtotime($trabajo['fecha_fin'])) : 'Pendiente' ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="detail-section">
                <h2 class="section-title">Servicios Realizados</h2>
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
                </table>
            </div>
            
            <?php if (!empty($trabajo['notas'])): ?>
            <div class="detail-section">
                <h2 class="section-title">Notas del Trabajo</h2>
                <div class="info-card">
                    <?= nl2br(htmlspecialchars($trabajo['notas'])) ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($trabajo['fotos'])): ?>
            <div class="detail-section">
                <h2 class="section-title">Fotos del Trabajo</h2>
                <div class="photo-gallery">
                    <?php 
                        $fotos = explode(',', $trabajo['fotos']);
                        foreach ($fotos as $index => $foto): 
                            if (!empty($foto)):
                    ?>
                        <div class="photo-item" onclick="openModal('<?= htmlspecialchars(trim($foto)) ?>')">
                            <img src="<?= htmlspecialchars(trim($foto)) ?>">
                            <div class="photo-caption">Foto <?= $index + 1 ?></div>
                        </div>
                    <?php 
                            endif;
                        endforeach; 
                    ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="actions">
                <a href="trabajos.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a trabajos
                </a>
            </div>
        </main>
    </div>

    <!-- Modal para visualización de fotos -->
    <div id="imageModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script>
        function openModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            
            modal.style.display = "block";
            modalImg.src = imageSrc;
        }
        
        function closeModal() {
            document.getElementById('imageModal').style.display = "none";
        }
        
        // Cerrar modal al hacer clic fuera de la imagen
        window.onclick = function(event) {
            const modal = document.getElementById('imageModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>