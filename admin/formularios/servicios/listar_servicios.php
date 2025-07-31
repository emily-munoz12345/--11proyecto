<?php
require_once __DIR__ . '/../../../php/conexion.php';

// Consulta para obtener los registros de la tabla servicios
$stmt = $conex->query("SELECT * FROM servicios ORDER BY precio_servicio DESC");
$servicios = $stmt->fetchAll();

// Obtener estadísticas
$totalServicios = count($servicios);
$servicioMasCaro = $servicios ? max(array_column($servicios, 'precio_servicio')) : 0;
$servicioMasBarato = $servicios ? min(array_column($servicios, 'precio_servicio')) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Servicios | Nacional Tapizados</title>
    <style>
        body {
            background-image: url('https://pfst.cf2.poecdn.net/base/image/fe72e5f0bf336b4faca086bc6a42c20a45e904d165e796b52eca655a143283b8?w=1024&h=768&pmaid=426747789');
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

        /* Estilos para la lista de servicios */
        .service-list {
            background-color: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            max-height: 500px;
            overflow-y: auto;
        }

        .service-item {
            padding: 1.2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .service-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .service-item:last-child {
            border-bottom: none;
        }

        .service-name {
            font-weight: 500;
            font-size: 1.1rem;
        }

        .service-description {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 0.3rem;
        }

        .service-info {
            flex-grow: 1;
        }

        .service-price {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-left: 1rem;
            background-color: rgba(76, 175, 80, 0.2);
            color: #4CAF50;
        }

        .service-time {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-left: 1rem;
            background-color: rgba(33, 150, 243, 0.2);
            color: #2196F3;
        }

        .service-arrow {
            margin-left: 1rem;
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .service-item:hover .service-arrow {
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
            
            .service-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .service-price, .service-time {
                margin-left: 0;
                margin-top: 0.5rem;
            }
            
            .service-arrow {
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
        <a href="javascript:history.back()" class="back-button">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        
        <h1><i class="fas fa-tools"></i> Lista de Servicios</h1>
        
        <!-- Resumen de servicios -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total Servicios</h3>
                <p><?php echo $totalServicios; ?></p>
            </div>
            <div class="summary-card">
                <h3>Servicio Más Costoso</h3>
                <p>$<?php echo number_format($servicioMasCaro, 2); ?></p>
            </div>
            <div class="summary-card">
                <h3>Servicio Más Económico</h3>
                <p>$<?php echo number_format($servicioMasBarato, 2); ?></p>
            </div>
        </div>
        
        <!-- Buscador -->
        <div class="search-container">
            <input type="text" id="searchInput" class="search-input" placeholder="Buscar servicio por nombre o categoría..." onkeyup="filterServices()">
            <button class="search-button" onclick="filterServices()">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>
        
        <!-- Lista de servicios -->
        <div class="service-list" id="serviceList">
            <?php foreach ($servicios as $servicio): 
                $shortDescription = !empty($servicio['descripcion_servicio']) 
                    ? (strlen($servicio['descripcion_servicio']) > 50 
                        ? substr($servicio['descripcion_servicio'], 0, 50) . '...' 
                        : $servicio['descripcion_servicio'])
                    : 'Sin descripción disponible';
            ?>
                <div class="service-item" 
                     onclick="showServiceDetails(
                         '<?php echo htmlspecialchars($servicio['id_servicio'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($servicio['nombre_servicio'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($servicio['descripcion_servicio'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($servicio['precio_servicio'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($servicio['tiempo_estimado'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($servicio['categoria_servicio'], ENT_QUOTES); ?>'
                     )">
                    <div class="service-info">
                        <div class="service-name"><?php echo htmlspecialchars($servicio['nombre_servicio']); ?></div>
                        <div class="service-description"><?php echo htmlspecialchars($shortDescription); ?></div>
                    </div>
                    <div class="service-price">
                        $<?php echo htmlspecialchars(number_format($servicio['precio_servicio'], 2)); ?>
                    </div>
                    <div class="service-time">
                        <?php echo htmlspecialchars($servicio['tiempo_estimado']); ?>
                    </div>
                    <div class="service-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Overlay para fondo oscuro -->
    <div class="overlay" id="overlay" onclick="hideServiceDetails()"></div>
    
    <!-- Tarjeta flotante de detalles del servicio -->
    <div class="floating-card" id="serviceDetailCard">
        <div class="card-header">
            <h2 class="card-title" id="detailServiceName"></h2>
            <button class="close-detail close-card" onclick="hideServiceDetails()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="card-content">
            <div class="detail-item">
                <div class="detail-label">ID Servicio</div>
                <div class="detail-value" id="detailServiceId"></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Precio</div>
                <div class="detail-value" id="detailServicePrice"></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Tiempo Estimado</div>
                <div class="detail-value" id="detailServiceTime"></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Categoría</div>
                <div class="detail-value" id="detailServiceCategory"></div>
            </div>
            
            <div class="notes-section">
                <div class="detail-label">Descripción</div>
                <div class="detail-value" id="detailServiceDescription"></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Función para filtrar servicios
        function filterServices() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const serviceList = document.getElementById('serviceList');
            const services = serviceList.getElementsByClassName('service-item');
            
            for (let i = 0; i < services.length; i++) {
                const serviceName = services[i].querySelector('.service-name').textContent;
                const serviceCategory = services[i].querySelector('.service-description').textContent;
                if (serviceName.toUpperCase().indexOf(filter) > -1 || serviceCategory.toUpperCase().indexOf(filter) > -1) {
                    services[i].style.display = "flex";
                } else {
                    services[i].style.display = "none";
                }
            }
        }
        
        // Función para mostrar detalles del servicio
        function showServiceDetails(id, name, description, price, time, category) {
            document.getElementById('detailServiceId').textContent = id;
            document.getElementById('detailServiceName').textContent = name;
            document.getElementById('detailServiceDescription').textContent = description || 'No hay descripción disponible';
            document.getElementById('detailServiceTime').textContent = time || 'No especificado';
            document.getElementById('detailServiceCategory').textContent = category || 'No especificada';
            
            // Mostrar overlay y tarjeta flotante
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('serviceDetailCard').style.display = 'block';
            
            // Deshabilitar scroll del body
            document.body.style.overflow = 'hidden';
        }
        
        // Función para ocultar detalles del servicio
        function hideServiceDetails() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('serviceDetailCard').style.display = 'none';
            
            // Habilitar scroll del body
            document.body.style.overflow = 'auto';
        }
        
        // Cerrar con tecla ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideServiceDetails();
            }
        });
        
        // Inicializar el filtro al cargar la página
        document.addEventListener('DOMContentLoaded', filterServices);
    </script>
</body>
</html>