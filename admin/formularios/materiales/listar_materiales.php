<?php
require_once __DIR__ . '/../../../php/conexion.php';

// Consulta para obtener los registros de la tabla materiales
$stmt = $conex->query("SELECT * FROM materiales");
$materiales = $stmt->fetchAll();

// Obtener estadísticas
$totalMateriales = count($materiales);
$ultimoRegistro = $totalMateriales > 0 ? max(array_column($materiales, 'fecha_actualizacion')) : null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Materiales | Nacional Tapizados</title>
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

        /* Estilos para la lista de materiales */
        .material-list {
            background-color: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            max-height: 500px;
            overflow-y: auto;
        }

        .material-item {
            padding: 1.2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .material-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .material-item:last-child {
            border-bottom: none;
        }

        .material-name {
            font-weight: 500;
            font-size: 1.1rem;
        }

        .material-description {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 0.3rem;
        }

        .material-info {
            flex-grow: 1;
        }

        .material-stock {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 0.3rem 0.6rem;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-left: 1rem;
        }

        .material-arrow {
            margin-left: 1rem;
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .material-item:hover .material-arrow {
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

        .stock-indicator {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .stock-low {
            background-color: rgba(255, 0, 0, 0.2);
            color: #ff6b6b;
        }

        .stock-medium {
            background-color: rgba(255, 165, 0, 0.2);
            color: #ffcc5c;
        }

        .stock-high {
            background-color: rgba(0, 128, 0, 0.2);
            color: #88d8b0;
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

            .material-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .material-stock {
                margin-left: 0;
                margin-top: 0.5rem;
            }

            .material-arrow {
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

    <h1><i class="fas fa-boxes"></i> Lista de Materiales</h1>
    <div class="main-container">
        <!-- Resumen de materiales -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total de Materiales</h3>
                <p><?php echo $totalMateriales; ?></p>
            </div>
            <div class="summary-card">
                <h3>Última Actualización</h3>
                <p><?php echo $ultimoRegistro ? date('d/m/Y', strtotime($ultimoRegistro)) : 'N/A'; ?></p>
            </div>
        </div>

        <!-- Buscador -->
        <div class="search-container">
            <input type="text" id="searchInput" class="search-input" placeholder="Buscar material por nombre..." onkeyup="filterMaterials()">
            <button class="search-button" onclick="filterMaterials()">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>

        <!-- Lista de materiales -->
        <div class="material-list" id="materialList">
            <?php foreach ($materiales as $material):
                $shortDescription = !empty($material['descripcion_material'])
                    ? (strlen($material['descripcion_material']) > 50
                        ? substr($material['descripcion_material'], 0, 50) . '...'
                        : $material['descripcion_material'])
                    : 'Sin descripción disponible';
                
                // Determinar clase de stock
                $stockClass = 'stock-high';
                if ($material['stock_material'] < 5) {
                    $stockClass = 'stock-low';
                } elseif ($material['stock_material'] < 10) {
                    $stockClass = 'stock-medium';
                }
            ?>
                <div class="material-item"
                    onclick="showMaterialDetails(
                         '<?php echo htmlspecialchars($material['id_material'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($material['nombre_material'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($material['descripcion_material'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($material['precio_metro'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($material['stock_material'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($material['categoria_material'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($material['proveedor_material'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($material['fecha_actualizacion'], ENT_QUOTES); ?>'
                     )">
                    <div class="material-info">
                        <div class="material-name"><?php echo htmlspecialchars($material['nombre_material']); ?></div>
                        <div class="material-description"><?php echo htmlspecialchars($shortDescription); ?></div>
                    </div>
                    <div class="material-stock <?php echo $stockClass; ?>">
                        <?php echo htmlspecialchars($material['stock_material']); ?> unidades
                    </div>
                    <div class="material-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Overlay para fondo oscuro -->
    <div class="overlay" id="overlay" onclick="hideMaterialDetails()"></div>

    <!-- Tarjeta flotante de detalles del material -->
    <div class="floating-card" id="materialDetailCard">
        <div class="card-header">
            <h2 class="card-title" id="detailMaterialName"></h2>
            <button class="close-detail close-card" onclick="hideMaterialDetails()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="card-content">
            <div class="detail-item">
                <div class="detail-label">ID Material</div>
                <div class="detail-value" id="detailMaterialId"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Precio por metro</div>
                <div class="detail-value" id="detailMaterialPrice"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Stock disponible</div>
                <div class="detail-value" id="detailMaterialStock"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Categoría</div>
                <div class="detail-value" id="detailMaterialCategory"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Proveedor</div>
                <div class="detail-value" id="detailMaterialSupplier"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Última actualización</div>
                <div class="detail-value" id="detailMaterialDate"></div>
            </div>

            <div class="notes-section">
                <div class="detail-label">Descripción</div>
                <div class="detail-value" id="detailMaterialDescription"></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Función para filtrar materiales
        function filterMaterials() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const materialList = document.getElementById('materialList');
            const materials = materialList.getElementsByClassName('material-item');

            for (let i = 0; i < materials.length; i++) {
                const materialName = materials[i].querySelector('.material-name').textContent;
                if (materialName.toUpperCase().indexOf(filter) > -1) {
                    materials[i].style.display = "flex";
                } else {
                    materials[i].style.display = "none";
                }
            }
        }

        // Función para mostrar detalles del material
        function showMaterialDetails(id, name, description, price, stock, category, supplier, date) {
            document.getElementById('detailMaterialId').textContent = id;
            document.getElementById('detailMaterialName').textContent = name;
            document.getElementById('detailMaterialDescription').textContent = description || 'No hay descripción disponible';
            
            // Formatear precio
            if (price) {
                document.getElementById('detailMaterialPrice').textContent = '$' + parseFloat(price).toLocaleString('es-ES');
            } else {
                document.getElementById('detailMaterialPrice').textContent = 'No especificado';
            }
            
            // Mostrar stock con indicador de color
            let stockClass = 'stock-high';
            if (stock < 5) {
                stockClass = 'stock-low';
            } else if (stock < 10) {
                stockClass = 'stock-medium';
            }
            document.getElementById('detailMaterialStock').innerHTML = 
                `<span class="stock-indicator ${stockClass}">${stock} unidades</span>`;
            
            document.getElementById('detailMaterialCategory').textContent = category || 'No especificada';
            document.getElementById('detailMaterialSupplier').textContent = supplier || 'No especificado';

            // Formatear fecha
            if (date) {
                const formattedDate = new Date(date).toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                document.getElementById('detailMaterialDate').textContent = formattedDate;
            } else {
                document.getElementById('detailMaterialDate').textContent = 'No especificada';
            }

            // Mostrar overlay y tarjeta flotante
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('materialDetailCard').style.display = 'block';

            // Deshabilitar scroll del body
            document.body.style.overflow = 'hidden';
        }

        // Función para ocultar detalles del material
        function hideMaterialDetails() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('materialDetailCard').style.display = 'none';

            // Habilitar scroll del body
            document.body.style.overflow = 'auto';
        }

        // Cerrar con tecla ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideMaterialDetails();
            }
        });

        // Inicializar el filtro al cargar la página
        document.addEventListener('DOMContentLoaded', filterMaterials);
    </script>
</body>

</html>