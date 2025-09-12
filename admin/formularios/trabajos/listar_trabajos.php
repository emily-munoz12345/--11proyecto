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
        body {
            background-image: url('https://pfst.cf2.poecdn.net/base/image/60ab54eef562f30f85a67bde31f924f078199dae0b7bc6c333dfb467a2c13471?w=1024&h=768&pmaid=442168253');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            color: #fff;
        }

        .main-container {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin: 2rem;
            padding: 2rem;
            min-height: calc(100vh - 4rem);
            position: relative;
        }

        h1 {
            color: white;
            text-align: center;
            margin-bottom: 2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
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
            background-color: rgba(140, 74, 63, 0.5);
            border-radius: 10px;
            padding: 1.5rem;
            min-width: 200px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .summary-card h3 {
            margin-top: 0;
            font-size: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-card p {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0;
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
            border: none;
            border-radius: 6px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 1rem;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-button {
            padding: 0.75rem 1.5rem;
            background-color: rgba(140, 74, 63, 0.7);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-button:hover {
            background-color: rgba(140, 74, 63, 0.9);
        }

        /* Estilos para la lista de trabajos */
        .work-list {
            background-color: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            max-height: 500px;
            overflow-y: auto;
        }

        .work-item {
            padding: 1.2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .work-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
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
        }

        .work-vehicle {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .work-dates {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-left: 1rem;
        }

        .work-date {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
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
            background-color: rgba(50, 50, 50, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            z-index: 1001;
            animation: fadeInUp 0.4s ease;
            overflow-y: auto;
            border: 1px solid rgba(255, 255, 255, 0.1);
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
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card-title {
            margin: 0;
            font-size: 1.8rem;
            color: #fff;
        }

        .close-card {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.7);
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
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
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
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 1.1rem;
            word-break: break-word;
            color: #fff;
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
            background-color: rgba(0, 0, 0, 0.2);
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1rem;
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
            background-color: rgba(0, 0, 0, 0.2);
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1rem;
        }

        /* Estilos para el botón de volver */
        .back-button {
            display: inline-block;
            margin-bottom: 1.5rem;
            padding: 0.5rem 1rem;
            background-color: rgba(140, 74, 63, 0.5);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background-color: rgba(140, 74, 63, 0.8);
            transform: translateY(-2px);
        }

        .back-button i {
            margin-right: 5px;
        }

        /* Estilos responsivos */
        @media (max-width: 768px) {
            .main-container {
                margin: 1rem;
                padding: 1rem;
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

    <a href="javascript:history.back()" class="back-button">
        <i class="fas fa-arrow-left"></i> Volver
    </a>

    <h1><i class="fas fa-tools"></i> Lista de Trabajos</h1>
    <div class="main-container">
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
                <i class="fas fa-search"></i> Buscar
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