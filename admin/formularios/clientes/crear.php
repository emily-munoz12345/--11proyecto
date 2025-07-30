<?php
require_once __DIR__ . '/../../../php/conexion.php';

// Consulta para obtener los registros de la tabla clientes
$stmt = $conex->query("SELECT * FROM clientes ORDER BY fecha_registro DESC");
$clientes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Clientes | Nacional Tapizados</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.8);
            --primary-hover: rgba(140, 74, 63, 1);
            --text-color: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --bg-transparent: rgba(255, 255, 255, 0.1);
            --bg-transparent-light: rgba(255, 255, 255, 0.15);
            --border-color: rgba(255, 255, 255, 0.2);
        }
        
        body {
            background-image: url('https://pfst.cf2.poecdn.net/base/image/fe72e5f0bf336b4faca086bc6a42c20a45e904d165e796b52eca655a143283b8?w=1024&h=768&pmaid=426747789');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Roboto', sans-serif;
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
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
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
        }

        .page-title i {
            margin-right: 12px;
            color: var(--primary-color);
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary {
            background-color: var(--bg-transparent-light);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .table-container {
            overflow-x: auto;
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        th, td {
            padding: 1.2rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--primary-color);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            color: white;
            position: sticky;
            top: 0;
        }

        tr:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .badge {
            display: inline-block;
            padding: 0.35rem 0.65rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .text-truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }

        @media (max-width: 992px) {
            .main-container {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .action-buttons {
                width: 100%;
                justify-content: flex-end;
            }
        }

        @media (max-width: 768px) {
            .table-container {
                border-radius: 8px;
            }
            
            table {
                min-width: 100%;
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
                border: 1px solid var(--border-color);
                border-radius: 8px;
                padding: 0.5rem;
            }
            
            td {
                border: none;
                position: relative;
                padding-left: 50%;
                text-align: right;
                padding-right: 1rem;
            }
            
            td::before {
                content: attr(data-label);
                position: absolute;
                left: 1rem;
                width: calc(50% - 1rem);
                padding-right: 10px;
                text-align: left;
                font-weight: 600;
                color: var(--text-muted);
            }
            
            .text-truncate {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-users"></i>Lista de Clientes
            </h1>
            <div class="action-buttons">
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="crear.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Cliente
                </a>
            </div>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Contacto</th>
                        <th>Dirección</th>
                        <th>Registro</th>
                        <th>Notas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($clientes) > 0): ?>
                        <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td data-label="ID">
                                <span class="badge badge-primary"><?= htmlspecialchars($cliente['id_cliente']) ?></span>
                            </td>
                            <td data-label="Cliente">
                                <strong><?= htmlspecialchars($cliente['nombre_cliente']) ?></strong>
                            </td>
                            <td data-label="Contacto">
                                <div><?= htmlspecialchars($cliente['correo_cliente']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($cliente['telefono_cliente']) ?></small>
                            </td>
                            <td data-label="Dirección">
                                <?= htmlspecialchars($cliente['direccion_cliente']) ?>
                            </td>
                            <td data-label="Registro">
                                <?= date('d/m/Y', strtotime($cliente['fecha_registro'])) ?>
                            </td>
                            <td data-label="Notas" class="text-truncate">
                                <?= htmlspecialchars($cliente['notas_cliente']) ?>
                            </td>
                            <td data-label="Acciones">
                                <a href="editar.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">
                                No se encontraron clientes registrados
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Mejorar visualización de notas en dispositivos móviles
        document.addEventListener('DOMContentLoaded', function() {
            const truncateElements = document.querySelectorAll('.text-truncate');
            
            truncateElements.forEach(el => {
                el.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        this.style.whiteSpace = this.style.whiteSpace === 'nowrap' ? 'normal' : 'nowrap';
                        this.style.maxWidth = this.style.maxWidth === '200px' ? '100%' : '200px';
                    }
                });
            });
        });
    </script>
</body>
</html>