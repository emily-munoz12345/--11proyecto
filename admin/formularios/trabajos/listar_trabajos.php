<?php
require_once __DIR__ . '/../../../php/conexion.php';

// Consulta para obtener los registros de la tabla trabajos con información relacionada
$stmt = $conex->query("
    SELECT t.*, c.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, v.placa_vehiculo, 
           co.total_cotizacion, co.estado_cotizacion
    FROM trabajos t
    JOIN cotizaciones co ON t.id_cotizacion = co.id_cotizacion
    JOIN clientes c ON co.id_cliente = c.id_cliente
    JOIN vehiculos v ON co.id_vehiculo = v.id_vehiculo
");
$trabajos = $stmt->fetchAll();

// Obtener estadísticas
$totalTrabajos = count($trabajos);
$trabajosCompletados = count(array_filter($trabajos, function($t) { return $t['estado'] === 'Entregado'; }));
$trabajosEnProgreso = count(array_filter($trabajos, function($t) { return $t['estado'] === 'En progreso'; }));
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Trabajos | Nacional Tapizados</title>
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
            margin: 0;
            padding: 0;
        }

        .main-container {
            max-width: 1200px;
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

        .btn-outline-secondary {
            background-color: transparent;
            border: 1px solid var(--secondary-color);
            color: var(--text-color);
        }

        .btn-outline-secondary:hover {
            background-color: var(--secondary-color);
            color: white;
        }

        /* Estilos para el resumen */
        .summary-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            justify-content: center;
        }

        .summary-card {
            background-color: var(--bg-transparent-light);
            border-radius: 10px;
            padding: 1.5rem;
            min-width: 200px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            background-color: rgba(140, 74, 63, 0.3);
        }

        .summary-card h3 {
            margin-top: 0;
            font-size: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .summary-card p {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0;
            color: var(--text-color);
        }

        /* Estilos para el buscador */
        .search-container {
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
        }

        .search-input {
            flex-grow: 1;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background-color: var(--bg-input);
            color: var(--text-color);
            font-size: 1rem;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--primary-color);
            background-color: rgba(0, 0, 0, 0.7);
        }

        .search-input::placeholder {
            color: var(--text-muted);
        }

        .search-button {
            padding: 0.75rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .search-button:hover {
            background-color: var(--primary-hover);
        }

        /* Estilos para la lista de trabajos */
        .work-list {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            overflow: hidden;
            max-height: 500px;
            overflow-y: auto;
        }

        .work-item {
            padding: 1.2rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .work-item:hover {
            background-color: rgba(140, 74, 63, 0.3);
        }

        .work-item:last-child {
            border-bottom: none;
        }

        .work-info {
            flex-grow: 1;
        }

        .work-client {
            font-weight: 500;
            font-size: 1.1rem;
            margin-bottom: 0.3rem;
            color: var(--text-color);
        }

        .work-vehicle {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .work-dates {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-left: 1rem;
        }

        .work-date {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .work-status {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-top: 0.5rem;
        }

        .status-pendiente {
            background-color: rgba(255, 165, 0, 0.2);
            color: #ffcc5c;
        }

        .status-progreso {
            background-color: rgba(0, 191, 255, 0.2);
            color: #00bfff;
        }

        .status-entregado {
            background-color: rgba(0, 128, 0, 0.2);
            color: #88d8b0;
        }

        .status-cancelado {
            background-color: rgba(255, 0, 0, 0.2);
            color: #ff6b6b;
        }

        .work-arrow {
            margin-left: 1rem;
            opacity: 0.7;
            transition: all 0.3s ease;
            color: var(--text-color);
        }

        .work-item:hover .work-arrow {
            opacity: 1;
            transform: translateX(3px);
        }

        /* Estilos para la tarjeta flotante de detalles */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        .floating-card {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 700px;
            max-height: 90vh;
            background-color: rgba(40, 40, 40, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            z-index: 1001;
            animation: fadeInUp 0.4s ease;
            overflow-y: auto;
            border: 1px solid var(--border-color);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate(-50%, -40%);
            }

            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .card-title {
            margin: 0;
            font-size: 1.8rem;
            color: var(--text-color);
        }

        .close-card {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            transition: all 0.3s ease;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-card:hover {
            color: var(--text-color);
            background-color: var(--bg-transparent);
        }

        .card-content {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .detail-item {
            margin-bottom: 1rem;
        }

        .detail-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 1.1rem;
            word-break: break-word;
            color: var(--text-color);
        }

        .status-indicator {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .photos-section {
            grid-column: 1 / -1;
            background-color: var(--bg-input);
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1rem;
            border: 1px solid var(--border-color);
        }

        .photo-thumbnail {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .photo-thumbnail:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .notes-section {
            grid-column: 1 / -1;
            background-color: var(--bg-input);
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1rem;
            border: 1px solid var(--border-color);
        }

        /* Estilos para el botón de volver */
        .back-button {
            display: inline-flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 0.75rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            gap: 0.5rem;
        }

        .back-button:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        /* Estilos responsivos */
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

            .summary-cards {
                flex-direction: column;
            }

            .search-container {
                flex-direction: column;
            }

            .work-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .work-dates {
                margin-left: 0;
                margin-top: 0.5rem;
                align-items: flex-start;
            }

            .work-arrow {
                display: none;
            }

            .floating-card {
                width: 95%;
                padding: 1.5rem;
            }

            .card-content {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <div class="main-container">
        <div class="header-section">
            <h1 class="page-title"><i class="fas fa-tools"></i>Lista de Trabajos</h1>
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i>Volver
            </a>
        </div>

        <!-- Resumen de trabajos -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total de Trabajos</h3>
                <p><?php echo $totalTrabajos; ?></p>
            </div>
            <div class="summary-card">
                <h3>En Progreso</h3>
                <p><?php echo $trabajosEnProgreso; ?></p>
            </div>
            <div class="summary-card">
                <h3>Completados</h3>
                <p><?php echo $trabajosCompletados; ?></p>
            </div>
        </div>

        <!-- Buscador -->
        <div class="search-container">
            <input type="text" id="searchInput" class="search-input" placeholder="Buscar trabajo por cliente o vehículo..." onkeyup="filterWorks()">
            <button class="search-button" onclick="filterWorks()">
                <i class="fas fa-search"></i>Buscar
            </button>
        </div>

        <!-- Lista de trabajos -->
        <div class="work-list" id="workList">
            <?php foreach ($trabajos as $trabajo):
                // Determinar clase de estado
                $statusClass = 'status-pendiente';
                if ($trabajo['estado'] === 'En progreso') {
                    $statusClass = 'status-progreso';
                } elseif ($trabajo['estado'] === 'Entregado') {
                    $statusClass = 'status-entregado';
                } elseif ($trabajo['estado'] === 'Cancelado') {
                    $statusClass = 'status-cancelado';
                }
                
                // Formatear fechas
                $fechaInicio = $trabajo['fecha_inicio'] !== '0000-00-00' 
                    ? date('d/m/Y', strtotime($trabajo['fecha_inicio'])) 
                    : 'No iniciado';
                $fechaFin = $trabajo['fecha_fin'] !== '0000-00-00' 
                    ? date('d/m/Y', strtotime($trabajo['fecha_fin'])) 
                    : 'En progreso';
                
                // Procesar fotos
                $fotosArray = !empty($trabajo['fotos']) ? explode(',', $trabajo['fotos']) : [];
            ?>
                <div class="work-item"
                    onclick="showWorkDetails(
                         '<?php echo htmlspecialchars($trabajo['id_trabajos'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($trabajo['nombre_cliente'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($trabajo['marca_vehiculo'] . ' ' . $trabajo['modelo_vehiculo'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($trabajo['placa_vehiculo'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($trabajo['fecha_inicio'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($trabajo['fecha_fin'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($trabajo['estado'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($trabajo['notas'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($trabajo['fotos'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($trabajo['total_cotizacion'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($trabajo['estado_cotizacion'], ENT_QUOTES); ?>'
                     )">
                    <div class="work-info">
                        <div class="work-client"><?php echo htmlspecialchars($trabajo['nombre_cliente']); ?></div>
                        <div class="work-vehicle">
                            <?php echo htmlspecialchars($trabajo['marca_vehiculo'] . ' ' . $trabajo['modelo_vehiculo']); ?> - 
                            <?php echo htmlspecialchars($trabajo['placa_vehiculo']); ?>
                        </div>
                        <span class="work-status <?php echo $statusClass; ?>">
                            <?php echo htmlspecialchars($trabajo['estado']); ?>
                        </span>
                    </div>
                    <div class="work-dates">
                        <div class="work-date"><strong>Inicio:</strong> <?php echo $fechaInicio; ?></div>
                        <div class="work-date"><strong>Fin:</strong> <?php echo $fechaFin; ?></div>
                    </div>
                    <div class="work-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Overlay para fondo oscuro -->
    <div class="overlay" id="overlay" onclick="hideWorkDetails()"></div>

    <!-- Tarjeta flotante de detalles del trabajo -->
    <div class="floating-card" id="workDetailCard">
        <div class="card-header">
            <h2 class="card-title" id="detailWorkClient"></h2>
            <button class="close-detail close-card" onclick="hideWorkDetails()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="card-content">
            <div class="detail-item">
                <div class="detail-label">ID Trabajo</div>
                <div class="detail-value" id="detailWorkId"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Vehículo</div>
                <div class="detail-value" id="detailWorkVehicle"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Placa</div>
                <div class="detail-value" id="detailWorkPlate"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Estado</div>
                <div class="detail-value" id="detailWorkStatus"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Fecha Inicio</div>
                <div class="detail-value" id="detailWorkStart"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Fecha Fin</div>
                <div class="detail-value" id="detailWorkEnd"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Total Cotización</div>
                <div class="detail-value" id="detailWorkQuote"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Estado Cotización</div>
                <div class="detail-value" id="detailWorkQuoteStatus"></div>
            </div>

            <?php if (!empty($fotosArray)): ?>
            <div class="photos-section">
                <div class="detail-label">Fotos del Trabajo</div>
                <div id="detailWorkPhotos"></div>
            </div>
            <?php endif; ?>

            <div class="notes-section">
                <div class="detail-label">Notas</div>
                <div class="detail-value" id="detailWorkNotes"></div>
            </div>
        </div>
    </div>

<?php include '../../includes/bot.php'; ?>
    <script>setHelpModule('Trabajos');</script>
    <!-- Scripts -->
    <script>
        // Función para filtrar trabajos
        function filterWorks() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const workList = document.getElementById('workList');
            const works = workList.getElementsByClassName('work-item');

            for (let i = 0; i < works.length; i++) {
                const clientName = works[i].querySelector('.work-client').textContent;
                const vehicleInfo = works[i].querySelector('.work-vehicle').textContent;
                if (clientName.toUpperCase().indexOf(filter) > -1 || vehicleInfo.toUpperCase().indexOf(filter) > -1) {
                    works[i].style.display = "flex";
                } else {
                    works[i].style.display = "none";
                }
            }
        }

        // Función para mostrar detalles del trabajo
        function showWorkDetails(id, client, vehicle, plate, startDate, endDate, status, notes, photos, quoteTotal, quoteStatus) {
            document.getElementById('detailWorkId').textContent = id;
            document.getElementById('detailWorkClient').textContent = client;
            document.getElementById('detailWorkVehicle').textContent = vehicle;
            document.getElementById('detailWorkPlate').textContent = plate;
            
            // Formatear estado con color
            let statusClass = 'status-pendiente';
            if (status === 'En progreso') {
                statusClass = 'status-progreso';
            } else if (status === 'Entregado') {
                statusClass = 'status-entregado';
            } else if (status === 'Cancelado') {
                statusClass = 'status-cancelado';
            }
            document.getElementById('detailWorkStatus').innerHTML = 
                `<span class="status-indicator ${statusClass}">${status}</span>`;
            
            // Formatear fechas
            document.getElementById('detailWorkStart').textContent = 
                startDate !== '0000-00-00' 
                    ? new Date(startDate).toLocaleDateString('es-ES') 
                    : 'No iniciado';
            document.getElementById('detailWorkEnd').textContent = 
                endDate !== '0000-00-00' 
                    ? new Date(endDate).toLocaleDateString('es-ES') 
                    : 'En progreso';
            
            // Formatear total de cotización
            if (quoteTotal) {
                document.getElementById('detailWorkQuote').textContent = '$' + parseFloat(quoteTotal).toLocaleString('es-ES');
            } else {
                document.getElementById('detailWorkQuote').textContent = 'No especificado';
            }
            
            // Estado de cotización
            document.getElementById('detailWorkQuoteStatus').textContent = quoteStatus || 'No especificado';
            
            // Mostrar fotos si existen
            const photosContainer = document.getElementById('detailWorkPhotos');
            if (photosContainer) {
                photosContainer.innerHTML = '';
                if (photos) {
                    const photosArray = photos.split(',');
                    photosArray.forEach(photo => {
                        if (photo.trim() !== '') {
                            const img = document.createElement('img');
                            img.src = photo.trim();
                            img.className = 'photo-thumbnail';
                            img.onclick = function() {
                                window.open(photo.trim(), '_blank');
                            };
                            photosContainer.appendChild(img);
                        }
                    });
                }
            }
            
            // Notas
            document.getElementById('detailWorkNotes').textContent = notes || 'No hay notas disponibles';

            // Mostrar overlay y tarjeta flotante
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('workDetailCard').style.display = 'block';

            // Deshabilitar scroll del body
            document.body.style.overflow = 'hidden';
        }

        // Función para ocultar detalles del trabajo
        function hideWorkDetails() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('workDetailCard').style.display = 'none';

            // Habilitar scroll del body
            document.body.style.overflow = 'auto';
        }

        // Cerrar con tecla ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideWorkDetails();
            }
        });

        // Inicializar el filtro al cargar la página
        document.addEventListener('DOMContentLoaded', filterWorks);
    </script>
</body>

</html>