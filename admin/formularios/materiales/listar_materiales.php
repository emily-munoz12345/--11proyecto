<?php
require_once __DIR__ . '/../../../php/conexion.php';

$stmt = $conex->query("SELECT * FROM materiales ORDER BY fecha_actualizacion DESC");
$materiales = $stmt->fetchAll();

$totalMateriales = count($materiales);
$stockTotal = array_sum(array_column($materiales, 'stock_material'));
$materialMasCaro = $materiales ? max(array_column($materiales, 'precio_metro')) : 0;
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
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-left: 1rem;
            background-color: rgba(33, 150, 243, 0.2);
            color: #2196F3;
        }

        .material-price {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-left: 1rem;
            background-color: rgba(76, 175, 80, 0.2);
            color: #4CAF50;
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
            
            .material-stock, .material-price {
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
    <div class="main-container">
        <a href="javascript:history.back()" class="back-button">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        
        <h1><i class="fas fa-box-open"></i> Lista de Materiales</h1>
        
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total Materiales</h3>
                <p><?php echo $totalMateriales; ?></p>
            </div>
            <div class="summary-card">
                <h3>Stock Total</h3>
                <p><?php echo $stockTotal; ?> unidades</p>
            </div>
            <div class="summary-card">
                <h3>Material Más Caro</h3>
                <p>$<?php echo number_format($materialMasCaro, 2); ?>/m</p>
            </div>
        </div>
        
             <!-- Buscador -->
        <div class="search-container">
            <input type="text" id="searchInput" class="search-input" placeholder="Buscar material por nombre o categoría..." onkeyup="filterQuotes()">
            <button class="search-button" onclick="filterQuotes()">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>
        
        
        <div class="material-list" id="materialList">
            <?php foreach ($materiales as $material): 
                $shortDescription = !empty($material['descripcion_material']) 
                    ? (strlen($material['descripcion_material']) > 50 
                        ? substr($material['descripcion_material'], 0, 50) . '...' 
                        : $material['descripcion_material'])
                    : 'Sin descripción disponible';
                $fechaFormateada = date('d/m/Y', strtotime($material['fecha_actualizacion']));
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
                         '<?php echo htmlspecialchars($fechaFormateada, ENT_QUOTES); ?>'
                     )">
                    <div class="material-info">
                        <div class="material-name"><?php echo htmlspecialchars($material['nombre_material']); ?></div>
                        <div class="material-description"><?php echo htmlspecialchars($shortDescription); ?></div>
                    </div>
                    <div class="material-stock">
                        <?php echo htmlspecialchars($material['stock_material']); ?> unidades
                    </div>
                    <div class="material-price">
                        $<?php echo htmlspecialchars(number_format($material['precio_metro'], 2)); ?>/m
                    </div>
                    <div class="material-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="overlay" id="overlay" onclick="hideMaterialDetails()"></div>
    
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
                <div class="detail-label">Precio por Metro</div>
                <div class="detail-value" id="detailMaterialPrice"></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Stock Disponible</div>
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
                <div class="detail-label">Última Actualización</div>
                <div class="detail-value" id="detailMaterialDate"></div>
            </div>
            
            <div class="notes-section">
                <div class="detail-label">Descripción</div>
                <div class="detail-value" id="detailMaterialDescription"></div>
            </div>
        </div>
    </div>

    <script>
        // Función mejorada para filtrar materiales
        function filterMaterials() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const materialList = document.getElementById('materialList');
            const materials = materialList.getElementsByClassName('material-item');
            
            for (let i = 0; i < materials.length; i++) {
                const materialName = materials[i].querySelector('.material-name').textContent.toUpperCase();
                const materialDesc = materials[i].querySelector('.material-description').textContent.toUpperCase();
                
                if (materialName.includes(filter) || materialDesc.includes(filter)) {
                    materials[i].style.display = "flex";
                } else {
                    materials[i].style.display = "none";
                }
            }
        }
        
        // Función corregida para mostrar detalles
        function showMaterialDetails(id, name, description, price, stock, category, supplier, date) {
            // Actualizar el contenido de la tarjeta
            document.getElementById('detailMaterialId').textContent = id;
            document.getElementById('detailMaterialName').textContent = name;
            document.getElementById('detailMaterialDescription').textContent = description || 'No hay descripción disponible';
            
            // Formatear el precio
            const formattedPrice = price ? 
                `$${parseFloat(price).toLocaleString('es-CO', {minimumFractionDigits: 2})} por metro` : 
                'No especificado';
            document.getElementById('detailMaterialPrice').textContent = formattedPrice;
            
            document.getElementById('detailMaterialStock').textContent = stock ? `${stock} unidades` : 'Sin stock';
            document.getElementById('detailMaterialCategory').textContent = category || 'No especificada';
            document.getElementById('detailMaterialSupplier').textContent = supplier || 'No especificado';
            document.getElementById('detailMaterialDate').textContent = date || 'No especificada';
            
            // Mostrar elementos
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('materialDetailCard').style.display = 'block';
            
            // Deshabilitar scroll
            document.body.style.overflow = 'hidden';
            
            // Detener la propagación del evento para evitar que se cierre al hacer clic dentro
            event.stopPropagation();
        }
        
        function hideMaterialDetails() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('materialDetailCard').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Cerrar con ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideMaterialDetails();
            }
        });
        
        // Inicializar filtro al cargar
        document.addEventListener('DOMContentLoaded', function() {
            filterMaterials();
            
            // Asegurarse de que los event listeners están correctamente asignados
            document.querySelectorAll('.material-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    // Esto asegura que el clic en el ítem funcione correctamente
                    e.stopPropagation();
                });
            });
        });
    </script>
</body>
</html>