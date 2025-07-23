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
    <title>Lista de Registros de Clientes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        @media (max-width: 600px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            th, td {
                box-sizing: border-box;
                width: 100%;
            }
            td {
                text-align: right;
                position: relative;
                padding-left: 50%;
            }
            td::before {
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 10px;
                white-space: nowrap;
                text-align: left;
            }
        }
    </style>
</head>
<body>

<h1>Lista de Registros de Clientes</h1>
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
            <td><?php echo htmlspecialchars($cliente['id_cliente']); ?></td>
            <td><?php echo htmlspecialchars($cliente['nombre_cliente']); ?></td>
            <td><?php echo htmlspecialchars($cliente['correo_cliente']); ?></td>
            <td><?php echo htmlspecialchars($cliente['telefono_cliente']); ?></td>
            <td><?php echo htmlspecialchars($cliente['direccion_cliente']); ?></td>
            <td><?php echo htmlspecialchars($cliente['fecha_registro']); ?></td>
            <td><?php echo htmlspecialchars($cliente['notas_cliente']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>