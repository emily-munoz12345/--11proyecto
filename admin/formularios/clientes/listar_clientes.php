<?php
require_once __DIR__ . '/../../../php/conexion.php';

// Consulta para obtener los registros de la tabla clientes
$stmt = $conex->query("SELECT * FROM clientes");
$clientes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Registros de Clientes | Nacional Tapizados</title>
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
        }

        h1 {
            color: white;
            text-align: center;
            margin-bottom: 2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .table-container {
            background-color: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            color: white;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background-color: rgba(140, 74, 63, 0.5);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        @media (max-width: 768px) {
            .main-container {
                margin: 1rem;
                padding: 1rem;
            }
            
            table, thead, tbody, th, td, tr {
                display: block;
            }
            
            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            
            tr {
                margin-bottom: 1rem;
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 8px;
            }
            
            td {
                border: none;
                position: relative;
                padding-left: 50%;
                text-align: right;
            }
            
            td::before {
                content: attr(data-label);
                position: absolute;
                left: 1rem;
                width: calc(50% - 1rem);
                padding-right: 10px;
                text-align: left;
                font-weight: bold;
                color: rgba(255, 255, 255, 0.8);
            }
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
    </style>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="main-container">
        <a href="javascript:history.back()" class="back-button">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        
        <h1><i class="fas fa-users"></i> Lista de Registros de Clientes</h1>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID Cliente</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Fecha de Registro</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td data-label="ID Cliente"><?php echo htmlspecialchars($cliente['id_cliente']); ?></td>
                        <td data-label="Nombre"><?php echo htmlspecialchars($cliente['nombre_cliente']); ?></td>
                        <td data-label="Correo"><?php echo htmlspecialchars($cliente['correo_cliente']); ?></td>
                        <td data-label="Teléfono"><?php echo htmlspecialchars($cliente['telefono_cliente']); ?></td>
                        <td data-label="Dirección"><?php echo htmlspecialchars($cliente['direccion_cliente']); ?></td>
                        <td data-label="Fecha Registro"><?php echo htmlspecialchars($cliente['fecha_registro']); ?></td>
                        <td data-label="Notas"><?php echo htmlspecialchars($cliente['notas_cliente']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Formatear fechas para mejor visualización
        document.addEventListener('DOMContentLoaded', function() {
            const fechaCells = document.querySelectorAll('td[data-label="Fecha Registro"]');
            fechaCells.forEach(cell => {
                if (cell.textContent) {
                    const fecha = new Date(cell.textContent);
                    cell.textContent = fecha.toLocaleDateString('es-ES', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                }
            });
        });
    </script>
</body>
</html>