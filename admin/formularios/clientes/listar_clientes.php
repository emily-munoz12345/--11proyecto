<?php
require_once __DIR__ . '/../../../php/conexion.php';

// Consulta para obtener los registros de la tabla clientes
$stmt = $conex->query("SELECT * FROM clientes");
$clientes = $stmt->fetchAll();

// Obtener estadísticas
$totalClientes = count($clientes);
$ultimoRegistro = $totalClientes > 0 ? max(array_column($clientes, 'fecha_registro')) : null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Clientes | Nacional Tapizados</title>
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

        /* Estilos para la lista de clientes */
        .client-list {
            background-color: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            max-height: 500px;
            overflow-y: auto;
        }

        .client-item {
            padding: 1.2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .client-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .client-item:last-child {
            border-bottom: none;
        }

        .client-name {
            font-weight: 500;
            font-size: 1.1rem;
        }

        .client-description {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 0.3rem;
        }

        .client-info {
            flex-grow: 1;
        }

        .client-arrow {
            margin-left: 1rem;
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .client-item:hover .client-arrow {
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

            .client-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .client-arrow {
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

    <h1><i class="fas fa-users"></i> Lista de Clientes</h1>
    <div class="main-container">
        <!-- Resumen de clientes -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total de Clientes</h3>
                <p><?php echo $totalClientes; ?></p>
            </div>
            <div class="summary-card">
                <h3>Último Registro</h3>
                <p><?php echo $ultimoRegistro ? date('d/m/Y', strtotime($ultimoRegistro)) : 'N/A'; ?></p>
            </div>
        </div>

        <!-- Buscador -->
        <div class="search-container">
            <input type="text" id="searchInput" class="search-input" placeholder="Buscar cliente por nombre..." onkeyup="filterClients()">
            <button class="search-button" onclick="filterClients()">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>

        <!-- Lista de clientes -->
        <div class="client-list" id="clientList">
            <?php foreach ($clientes as $cliente):
                $shortDescription = !empty($cliente['notas_cliente'])
                    ? (strlen($cliente['notas_cliente']) > 50
                        ? substr($cliente['notas_cliente'], 0, 50) . '...'
                        : $cliente['notas_cliente'])
                    : 'Sin descripción disponible';
            ?>
                <div class="client-item"
                    onclick="showClientDetails(
                         '<?php echo htmlspecialchars($cliente['id_cliente'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($cliente['nombre_cliente'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($cliente['correo_cliente'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($cliente['telefono_cliente'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($cliente['direccion_cliente'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($cliente['fecha_registro'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($cliente['notas_cliente'], ENT_QUOTES); ?>'
                     )">
                    <div class="client-info">
                        <div class="client-name"><?php echo htmlspecialchars($cliente['nombre_cliente']); ?></div>
                        <div class="client-description"><?php echo htmlspecialchars($shortDescription); ?></div>
                    </div>
                    <div class="client-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Overlay para fondo oscuro -->
    <div class="overlay" id="overlay" onclick="hideClientDetails()"></div>

    <!-- Tarjeta flotante de detalles del cliente -->
    <div class="floating-card" id="clientDetailCard">
        <div class="card-header">
            <h2 class="card-title" id="detailClientName"></h2>
            <button class="close-detail close-card" onclick="hideClientDetails()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="card-content">
            <div class="detail-item">
                <div class="detail-label">ID Cliente</div>
                <div class="detail-value" id="detailClientId"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Correo Electrónico</div>
                <div class="detail-value" id="detailClientEmail"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Teléfono</div>
                <div class="detail-value" id="detailClientPhone"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Dirección</div>
                <div class="detail-value" id="detailClientAddress"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Fecha de Registro</div>
                <div class="detail-value" id="detailClientDate"></div>
            </div>

            <div class="notes-section">
                <div class="detail-label">Notas</div>
                <div class="detail-value" id="detailClientNotes"></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Función para filtrar clientes
        function filterClients() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const clientList = document.getElementById('clientList');
            const clients = clientList.getElementsByClassName('client-item');

            for (let i = 0; i < clients.length; i++) {
                const clientName = clients[i].querySelector('.client-name').textContent;
                if (clientName.toUpperCase().indexOf(filter) > -1) {
                    clients[i].style.display = "flex";
                } else {
                    clients[i].style.display = "none";
                }
            }
        }

        // Función para mostrar detalles del cliente
        function showClientDetails(id, name, email, phone, address, date, notes) {
            document.getElementById('detailClientId').textContent = id;
            document.getElementById('detailClientName').textContent = name;
            document.getElementById('detailClientEmail').textContent = email || 'No especificado';
            document.getElementById('detailClientPhone').textContent = phone || 'No especificado';
            document.getElementById('detailClientAddress').textContent = address || 'No especificado';

            // Formatear fecha
            if (date) {
                const formattedDate = new Date(date).toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
                document.getElementById('detailClientDate').textContent = formattedDate;
            } else {
                document.getElementById('detailClientDate').textContent = 'No especificada';
            }

            document.getElementById('detailClientNotes').textContent = notes || 'No hay notas disponibles';

            // Mostrar overlay y tarjeta flotante
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('clientDetailCard').style.display = 'block';

            // Deshabilitar scroll del body
            document.body.style.overflow = 'hidden';
        }

        // Función para ocultar detalles del cliente
        function hideClientDetails() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('clientDetailCard').style.display = 'none';

            // Habilitar scroll del body
            document.body.style.overflow = 'auto';
        }

        // Cerrar con tecla ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideClientDetails();
            }
        });

        // Inicializar el filtro al cargar la página
        document.addEventListener('DOMContentLoaded', filterClients);
    </script>
</body>

</html>