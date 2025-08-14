<?php
require_once __DIR__ . '/../../../php/conexion.php';

// Consulta para obtener los registros de la tabla cotizaciones con joins para obtener información relacionada
$stmt = $conex->query("
    SELECT c.*, cl.nombre_cliente, v.marca_vehiculo, v.modelo_vehiculo, u.nombre_completo as nombre_usuario
    FROM cotizaciones c
    JOIN clientes cl ON c.id_cliente = cl.id_cliente
    JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
    JOIN usuarios u ON c.id_usuario = u.id_usuario
    ORDER BY c.fecha_cotizacion DESC
");
$cotizaciones = $stmt->fetchAll();

// Obtener estadísticas
$totalCotizaciones = count($cotizaciones);
$ultimaCotizacion = $totalCotizaciones > 0 ? max(array_column($cotizaciones, 'fecha_cotizacion')) : null;

// Contar cotizaciones por estado
$estadosCotizaciones = array_count_values(array_column($cotizaciones, 'estado_cotizacion'));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Cotizaciones | Nacional Tapizados</title>
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

        /* Estilos para la lista de cotizaciones */
        .cotizacion-list {
            background-color: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            max-height: 500px;
            overflow-y: auto;
        }

        .cotizacion-item {
            padding: 1.2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cotizacion-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .cotizacion-item:last-child {
            border-bottom: none;
        }

        .cotizacion-info {
            flex-grow: 1;
        }

        .cotizacion-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .cotizacion-title {
            font-weight: 500;
            font-size: 1.1rem;
            margin: 0;
        }

        .cotizacion-estado {
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
            border-radius: 12px;
            font-weight: bold;
        }

        .estado-pendiente {
            background-color: rgba(255, 193, 7, 0.2);
            color: #FFC107;
        }

        .estado-aprobado {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28A745;
        }

        .estado-rechazada {
            background-color: rgba(220, 53, 69, 0.2);
            color: #DC3545;
        }

        .estado-completada {
            background-color: rgba(23, 162, 184, 0.2);
            color: #17A2B8;
        }

        .cotizacion-details {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .cotizacion-detail {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .cotizacion-arrow {
            margin-left: 1rem;
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .cotizacion-item:hover .cotizacion-arrow {
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
            max-width: 800px;
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

        .servicios-section {
            grid-column: 1 / -1;
            margin-top: 1rem;
        }

        .servicios-table {
            width: 100%;
            border-collapse: collapse;
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            overflow: hidden;
        }

        .servicios-table th {
            background-color: rgba(140, 74, 63, 0.5);
            padding: 0.75rem;
            text-align: left;
        }

        .servicios-table td {
            padding: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .servicios-table tr:last-child td {
            border-bottom: none;
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
            
            .cotizacion-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .cotizacion-arrow {
                display: none;
            }
            
            .floating-card {
                width: 95%;
                padding: 1.5rem;
            }
            
            .card-content {
                grid-template-columns: 1fr;
            }
            
            .cotizacion-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
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
        
        <h1><i class="fas fa-file-invoice-dollar"></i> Lista de Cotizaciones</h1>
        
        <!-- Resumen de cotizaciones -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total de Cotizaciones</h3>
                <p><?php echo $totalCotizaciones; ?></p>
            </div>
            <div class="summary-card">
                <h3>Última Cotización</h3>
                <p><?php echo $ultimaCotizacion ? date('d/m/Y', strtotime($ultimaCotizacion)) : 'N/A'; ?></p>
            </div>
            <div class="summary-card">
                <h3>Pendientes</h3>
                <p><?php echo $estadosCotizaciones['Pendiente'] ?? 0; ?></p>
            </div>
            <div class="summary-card">
                <h3>Aprobadas</h3>
                <p><?php echo $estadosCotizaciones['Aprobado'] ?? 0; ?></p>
            </div>
        </div>
        
        <!-- Buscador -->
        <div class="search-container">
            <input type="text" id="searchInput" class="search-input" placeholder="Buscar cotización por cliente, vehículo o estado..." onkeyup="filterCotizaciones()">
            <button class="search-button" onclick="filterCotizaciones()">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>
        
        <!-- Lista de cotizaciones -->
        <div class="cotizacion-list" id="cotizacionList">
            <?php foreach ($cotizaciones as $cotizacion): 
                $estadoClass = 'estado-' . strtolower(str_replace('á', 'a', $cotizacion['estado_cotizacion']));
            ?>
                <div class="cotizacion-item" 
                     onclick="showCotizacionDetails(
                         '<?php echo htmlspecialchars($cotizacion['id_cotizacion'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($cotizacion['nombre_cliente'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($cotizacion['nombre_usuario'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($cotizacion['fecha_cotizacion'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($cotizacion['subtotal_cotizacion'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($cotizacion['valor_adicional'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($cotizacion['iva'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($cotizacion['total_cotizacion'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($cotizacion['estado_cotizacion'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($cotizacion['notas_cotizacion'], ENT_QUOTES); ?>'
                     )">
                    <div class="cotizacion-info">
                        <div class="cotizacion-header">
                            <h3 class="cotizacion-title">Cotización #<?php echo htmlspecialchars($cotizacion['id_cotizacion']); ?></h3>
                            <span class="cotizacion-estado <?php echo $estadoClass; ?>">
                                <?php echo htmlspecialchars($cotizacion['estado_cotizacion']); ?>
                            </span>
                        </div>
                        <div class="cotizacion-details">
                            <div class="cotizacion-detail">
                                <i class="fas fa-user"></i>
                                <span><?php echo htmlspecialchars($cotizacion['nombre_cliente']); ?></span>
                            </div>
                            <div class="cotizacion-detail">
                                <i class="fas fa-car"></i>
                                <span><?php echo htmlspecialchars($cotizacion['marca_vehiculo'] . ' ' . $cotizacion['modelo_vehiculo']); ?></span>
                            </div>
                            <div class="cotizacion-detail">
                                <i class="fas fa-dollar-sign"></i>
                                <span>$<?php echo number_format($cotizacion['total_cotizacion'], 2); ?></span>
                            </div>
                            <div class="cotizacion-detail">
                                <i class="fas fa-calendar-alt"></i>
                                <span><?php echo date('d/m/Y', strtotime($cotizacion['fecha_cotizacion'])); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="cotizacion-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Overlay para fondo oscuro -->
    <div class="overlay" id="overlay" onclick="hideCotizacionDetails()"></div>
    
    <!-- Tarjeta flotante de detalles de la cotización -->
    <div class="floating-card" id="cotizacionDetailCard">
        <div class="card-header">
            <h2 class="card-title" id="detailCotizacionTitle"></h2>
            <button class="close-detail close-card" onclick="hideCotizacionDetails()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="card-content">
            <div class="detail-item">
                <div class="detail-label">Cliente</div>
                <div class="detail-value" id="detailCotizacionCliente"></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Vehículo</div>
                <div class="detail-value" id="detailCotizacionVehiculo"></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Responsable</div>
                <div class="detail-value" id="detailCotizacionResponsable"></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Fecha</div>
                <div class="detail-value" id="detailCotizacionFecha"></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Estado</div>
                <div class="detail-value" id="detailCotizacionEstado"></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Subtotal</div>
                <div class="detail-value" id="detailCotizacionSubtotal"></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Valor Adicional</div>
                <div class="detail-value" id="detailCotizacionAdicional"></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">IVA</div>
                <div class="detail-value" id="detailCotizacionIva"></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Total</div>
                <div class="detail-value" id="detailCotizacionTotal"></div>
            </div>
            
            <div class="servicios-section">
                <div class="detail-label">Servicios incluidos</div>
                <table class="servicios-table" id="detailCotizacionServicios">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los servicios se llenarán con JavaScript -->
                    </tbody>
                </table>
            </div>
            
            <div class="notes-section">
                <div class="detail-label">Notas</div>
                <div class="detail-value" id="detailCotizacionNotas"></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Función para filtrar cotizaciones
        function filterCotizaciones() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const cotizacionList = document.getElementById('cotizacionList');
            const cotizaciones = cotizacionList.getElementsByClassName('cotizacion-item');
            
            for (let i = 0; i < cotizaciones.length; i++) {
                const cliente = cotizaciones[i].querySelector('.cotizacion-details .cotizacion-detail:nth-child(1) span').textContent;
                const vehiculo = cotizaciones[i].querySelector('.cotizacion-details .cotizacion-detail:nth-child(2) span').textContent;
                const estado = cotizaciones[i].querySelector('.cotizacion-estado').textContent;
                
                if (cliente.toUpperCase().indexOf(filter) > -1 || 
                    vehiculo.toUpperCase().indexOf(filter) > -1 || 
                    estado.toUpperCase().indexOf(filter) > -1) {
                    cotizaciones[i].style.display = "flex";
                } else {
                    cotizaciones[i].style.display = "none";
                }
            }
        }
        
        // Función para mostrar detalles de la cotización
        function showCotizacionDetails(id, cliente, vehiculo, responsable, fecha, subtotal, adicional, iva, total, estado, notas) {
            document.getElementById('detailCotizacionTitle').textContent = `Cotización #${id}`;
            document.getElementById('detailCotizacionCliente').textContent = cliente;
            document.getElementById('detailCotizacionVehiculo').textContent = vehiculo;
            document.getElementById('detailCotizacionResponsable').textContent = responsable;
            
            // Formatear fecha
            if (fecha) {
                const formattedDate = new Date(fecha).toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                document.getElementById('detailCotizacionFecha').textContent = formattedDate;
            } else {
                document.getElementById('detailCotizacionFecha').textContent = 'No especificada';
            }
            
            // Estado con color
            const estadoElement = document.getElementById('detailCotizacionEstado');
            estadoElement.textContent = estado;
            estadoElement.className = 'detail-value estado-' + estado.toLowerCase().replace('á', 'a');
            
            // Valores monetarios
            document.getElementById('detailCotizacionSubtotal').textContent = `$${parseFloat(subtotal).toFixed(2)}`;
            document.getElementById('detailCotizacionAdicional').textContent = `$${parseFloat(adicional).toFixed(2)}`;
            document.getElementById('detailCotizacionIva').textContent = `$${parseFloat(iva).toFixed(2)}`;
            document.getElementById('detailCotizacionTotal').textContent = `$${parseFloat(total).toFixed(2)}`;
            
            document.getElementById('detailCotizacionNotas').textContent = notas || 'No hay notas disponibles';
            
            // Obtener servicios de esta cotización (simulado - en producción harías una petición AJAX)
            // Aquí simulamos los servicios para el ejemplo
            const serviciosTable = document.getElementById('detailCotizacionServicios').getElementsByTagName('tbody')[0];
            serviciosTable.innerHTML = '';
            
            // En una implementación real, harías una petición AJAX para obtener los servicios de esta cotización
            // Por ahora simulamos datos basados en el ID
            if (id == 1 || id == 3) {
                addServicioToTable(serviciosTable, 'Reparación de asiento', 180000.00);
            }
            if (id == 2 || id == 4) {
                addServicioToTable(serviciosTable, 'Cambio de alfombra', 120000.00);
            }
            if (id == 4) {
                addServicioToTable(serviciosTable, 'Reparación de asiento', 180000.00);
            }
            
            // Mostrar overlay y tarjeta flotante
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('cotizacionDetailCard').style.display = 'block';
            
            // Deshabilitar scroll del body
            document.body.style.overflow = 'hidden';
        }
        
        function addServicioToTable(table, nombre, precio) {
            const row = table.insertRow();
            const cellNombre = row.insertCell(0);
            const cellPrecio = row.insertCell(1);
            
            cellNombre.textContent = nombre;
            cellPrecio.textContent = `$${precio.toFixed(2)}`;
        }
        
        // Función para ocultar detalles de la cotización
        function hideCotizacionDetails() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('cotizacionDetailCard').style.display = 'none';
            
            // Habilitar scroll del body
            document.body.style.overflow = 'auto';
        }
        
        // Cerrar con tecla ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideCotizacionDetails();
            }
        });
        
        // Inicializar el filtro al cargar la página
        document.addEventListener('DOMContentLoaded', filterCotizaciones);
    </script>
</body>
</html>