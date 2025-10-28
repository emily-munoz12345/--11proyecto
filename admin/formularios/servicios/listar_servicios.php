<?php
require_once __DIR__ . '/../../../php/conexion.php';

// Consulta para obtener los registros de la tabla servicios
$stmt = $conex->query("SELECT * FROM servicios");
$servicios = $stmt->fetchAll();

// Obtener estadísticas
$totalServicios = count($servicios);
$serviciosPorCategoria = array_count_values(array_column($servicios, 'categoria_servicio'));
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Servicios | Nacional Tapizados</title>
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
            margin: 0;
            padding: 0;
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
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            background-color: var(--bg-transparent-light);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
            border: 1px solid var(--border-color);
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

        /* Estilos para la lista de servicios */
        .service-list {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            overflow: hidden;
            max-height: 500px;
            overflow-y: auto;
        }

        .service-item {
            padding: 1.2rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .service-item:hover {
            background-color: rgba(140, 74, 63, 0.3);
        }

        .service-item:last-child {
            border-bottom: none;
        }

        .service-name {
            font-weight: 500;
            font-size: 1.1rem;
            color: var(--text-color);
        }

        .service-description {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 0.3rem;
        }

        .service-info {
            flex-grow: 1;
        }

        .service-price {
            background-color: var(--bg-input);
            padding: 0.3rem 0.6rem;
            border-radius: 12px;
            font-size: 0.9rem;
            margin-left: 1rem;
            color: var(--text-color);
        }

        .service-category {
            background-color: var(--primary-color);
            padding: 0.3rem 0.6rem;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-left: 0.5rem;
            color: white;
        }

        .service-arrow {
            margin-left: 1rem;
            opacity: 0.7;
            transition: all 0.3s ease;
            color: var(--text-color);
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

        .price-tag {
            font-weight: bold;
            color: #88d8b0;
        }

        .time-estimate {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            background-color: rgba(13, 202, 240, 0.2);
            color: #88d8b0;
            border-radius: 12px;
            font-size: 0.9rem;
        }

        .category-tag {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            background-color: var(--primary-color);
            border-radius: 12px;
            font-size: 0.9rem;
            color: white;
        }

        .description-section {
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

        .back-button i {
            margin-right: 5px;
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
                grid-template-columns: 1fr;
            }

            .search-container {
                flex-direction: column;
            }

            .service-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .service-price, .service-category {
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
        <div class="header-section">
            <h1 class="page-title"><i class="fas fa-concierge-bell"></i> Lista de Servicios</h1>
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Resumen de servicios -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total de Servicios</h3>
                <p><?php echo $totalServicios; ?></p>
            </div>
            <?php foreach ($serviciosPorCategoria as $categoria => $cantidad): ?>
                <div class="summary-card">
                    <h3><?php echo htmlspecialchars($categoria); ?></h3>
                    <p><?php echo $cantidad; ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Buscador -->
        <div class="search-container">
            <input type="text" id="searchInput" class="search-input" placeholder="Buscar servicio por nombre..." onkeyup="filterServices()">
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
                        $<?php echo number_format($servicio['precio_servicio'], 0, ',', '.'); ?>
                    </div>
                    <div class="service-category">
                        <?php echo htmlspecialchars($servicio['categoria_servicio']); ?>
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
                <div class="detail-value"><span class="price-tag" id="detailServicePrice"></span></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Tiempo Estimado</div>
                <div class="detail-value"><span class="time-estimate" id="detailServiceTime"></span></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Categoría</div>
                <div class="detail-value"><span class="category-tag" id="detailServiceCategory"></span></div>
            </div>

            <div class="description-section">
                <div class="detail-label">Descripción Completa</div>
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
                if (serviceName.toUpperCase().indexOf(filter) > -1) {
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
            
            // Formatear precio
            if (price) {
                document.getElementById('detailServicePrice').textContent = '$' + parseFloat(price).toLocaleString('es-ES');
            } else {
                document.getElementById('detailServicePrice').textContent = 'No especificado';
            }
            
            document.getElementById('detailServiceTime').textContent = time || 'No especificado';
            document.getElementById('detailServiceCategory').textContent = category || 'No especificada';
            document.getElementById('detailServiceDescription').textContent = description || 'No hay descripción disponible';

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