<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Botón Flotante</title>
    <style>
        .floating-help-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #8B0000 0%, #581C38 100%);
            color: #fff;
            border-radius: 50%;
            border: none;
            box-shadow: 0 4px 20px rgba(139, 0, 0, 0.6), 0 0 0 0 rgba(139, 0, 0, 0.7);
            cursor: pointer;
            font-size: 28px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 1000;
            animation: pulse 2s infinite;
            text-decoration: none;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 4px 20px rgba(139, 0, 0, 0.6), 0 0 0 0 rgba(139, 0, 0, 0.7);
            }
            70% {
                box-shadow: 0 4px 20px rgba(139, 0, 0, 0.6), 0 0 0 15px rgba(139, 0, 0, 0);
            }
            100% {
                box-shadow: 0 4px 20px rgba(139, 0, 0, 0.6), 0 0 0 0 rgba(139, 0, 0, 0);
            }
        }

        .floating-help-btn:hover {
            transform: scale(1.1);
            animation: none;
            box-shadow: 0 8px 25px rgba(139, 0, 0, 0.8);
        }
    </style>
</head>
<body>
    <!-- Botón flotante de ayuda -->
    <a class="floating-help-btn" id="helpButton" href="clientes.php">?</a>

    <script>
        // Configuración de rutas
        const moduleRoutes = {
            'Clientes': '../../ayuda/clientes.php',
            'Cotizaciones': '../../ayuda/cotizaciones.php',
            'Servicios': '../../ayuda/servicios.php',
            'Trabajos': '../../ayuda/trabajos.php',
            'Usuarios': '../../ayuda/usuarios.php',
            'Materiales': '../../ayuda/materiales.php', 
            'Vehiculos': '../../ayuda/vehiculos.php',
            'Inicio': '/--11proyecto/admin/ayuda/ayudapublic.php',
        };

        // Función para cambiar el módulo
        function setHelpModule(moduleName) {
            const helpButton = document.getElementById('helpButton');
            if (moduleRoutes[moduleName]) {
                helpButton.href = moduleRoutes[moduleName];
                console.log(`Botón configurado para: ${moduleRoutes[moduleName]}`);
            }
        }

        // Ejemplos de uso:
        // setHelpModule('Clientes');
        // setHelpModule('Materiales');
        // setHelpModule('Vehículos');
    </script>
</body>
</html>