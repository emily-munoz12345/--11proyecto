<?php
require_once __DIR__ . '/../../../php/conexion.php';

// Consulta para obtener los registros de la tabla usuarios con información de roles
$stmt = $conex->query("
    SELECT u.*, r.nombre_rol 
    FROM usuarios u 
    JOIN roles r ON u.id_rol = r.id_rol
");
$usuarios = $stmt->fetchAll();

// Obtener estadísticas
$totalUsuarios = count($usuarios);
$usuariosPorRol = array_count_values(array_column($usuarios, 'nombre_rol'));
$usuariosActivos = count(array_filter($usuarios, function($usuario) {
    return $usuario['activo_usuario'] === 'Activo';
}));
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios | Nacional Tapizados</title>
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

        /* Estilos para la lista de usuarios */
        .user-list {
            background-color: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            max-height: 500px;
            overflow-y: auto;
        }

        .user-item {
            padding: 1.2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .user-item:last-child {
            border-bottom: none;
        }

        .user-name {
            font-weight: 500;
            font-size: 1.1rem;
        }

        .user-username {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 0.3rem;
        }

        .user-info {
            flex-grow: 1;
        }

        .user-role {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 0.3rem 0.6rem;
            border-radius: 12px;
            font-size: 0.9rem;
            margin-left: 1rem;
        }

        .user-status {
            background-color: rgba(0, 128, 0, 0.3);
            padding: 0.3rem 0.6rem;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-left: 0.5rem;
        }

        .user-status.inactive {
            background-color: rgba(255, 0, 0, 0.3);
        }

        .user-arrow {
            margin-left: 1rem;
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .user-item:hover .user-arrow {
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

        .role-tag {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            background-color: rgba(140, 74, 63, 0.3);
            border-radius: 12px;
            font-size: 0.9rem;
        }

        .status-tag {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            border-radius: 12px;
            font-size: 0.9rem;
        }

        .status-active {
            background-color: rgba(0, 128, 0, 0.3);
            color: #7cfc00;
        }

        .status-inactive {
            background-color: rgba(255, 0, 0, 0.3);
            color: #ff6347;
        }

        .contact-section {
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

            .user-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .user-role, .user-status {
                margin-left: 0;
                margin-top: 0.5rem;
            }

            .user-arrow {
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

    <h1><i class="fas fa-users"></i> Lista de Usuarios</h1>
    <div class="main-container">
        <!-- Resumen de usuarios -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total de Usuarios</h3>
                <p><?php echo $totalUsuarios; ?></p>
            </div>
            <div class="summary-card">
                <h3>Usuarios Activos</h3>
                <p><?php echo $usuariosActivos; ?></p>
            </div>
            <?php foreach ($usuariosPorRol as $rol => $cantidad): ?>
                <div class="summary-card">
                    <h3><?php echo htmlspecialchars($rol); ?></h3>
                    <p><?php echo $cantidad; ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Buscador -->
        <div class="search-container">
            <input type="text" id="searchInput" class="search-input" placeholder="Buscar usuario por nombre..." onkeyup="filterUsers()">
            <button class="search-button" onclick="filterUsers()">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>

        <!-- Lista de usuarios -->
        <div class="user-list" id="userList">
            <?php foreach ($usuarios as $usuario): ?>
                <div class="user-item"
                    onclick="showUserDetails(
                         '<?php echo htmlspecialchars($usuario['id_usuario'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($usuario['nombre_completo'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($usuario['username_usuario'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($usuario['nombre_rol'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($usuario['correo_usuario'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($usuario['telefono_usuario'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($usuario['activo_usuario'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($usuario['fecha_creacion'], ENT_QUOTES); ?>',
                         '<?php echo htmlspecialchars($usuario['ultima_actividad'], ENT_QUOTES); ?>'
                     )">
                    <div class="user-info">
                        <div class="user-name"><?php echo htmlspecialchars($usuario['nombre_completo']); ?></div>
                        <div class="user-username">@<?php echo htmlspecialchars($usuario['username_usuario']); ?></div>
                    </div>
                    <div class="user-role">
                        <?php echo htmlspecialchars($usuario['nombre_rol']); ?>
                    </div>
                    <div class="user-status <?php echo $usuario['activo_usuario'] === 'Inactivo' ? 'inactive' : ''; ?>">
                        <?php echo htmlspecialchars($usuario['activo_usuario']); ?>
                    </div>
                    <div class="user-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Overlay para fondo oscuro -->
    <div class="overlay" id="overlay" onclick="hideUserDetails()"></div>

    <!-- Tarjeta flotante de detalles del usuario -->
    <div class="floating-card" id="userDetailCard">
        <div class="card-header">
            <h2 class="card-title" id="detailUserName"></h2>
            <button class="close-detail close-card" onclick="hideUserDetails()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="card-content">
            <div class="detail-item">
                <div class="detail-label">ID Usuario</div>
                <div class="detail-value" id="detailUserId"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Nombre de Usuario</div>
                <div class="detail-value" id="detailUserUsername"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Rol</div>
                <div class="detail-value"><span class="role-tag" id="detailUserRole"></span></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Estado</div>
                <div class="detail-value"><span class="status-tag" id="detailUserStatus"></span></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Fecha de Creación</div>
                <div class="detail-value" id="detailUserCreationDate"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Última Actividad</div>
                <div class="detail-value" id="detailUserLastActivity"></div>
            </div>

            <div class="contact-section">
                <div class="detail-label">Información de Contacto</div>
                <div class="detail-value"><i class="fas fa-envelope"></i> <span id="detailUserEmail"></span></div>
                <div class="detail-value"><i class="fas fa-phone"></i> <span id="detailUserPhone"></span></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Función para filtrar usuarios
        function filterUsers() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const userList = document.getElementById('userList');
            const users = userList.getElementsByClassName('user-item');

            for (let i = 0; i < users.length; i++) {
                const userName = users[i].querySelector('.user-name').textContent;
                const userUsername = users[i].querySelector('.user-username').textContent;
                if (userName.toUpperCase().indexOf(filter) > -1 || userUsername.toUpperCase().indexOf(filter) > -1) {
                    users[i].style.display = "flex";
                } else {
                    users[i].style.display = "none";
                }
            }
        }

        // Función para mostrar detalles del usuario
        function showUserDetails(id, name, username, role, email, phone, status, creationDate, lastActivity) {
            document.getElementById('detailUserId').textContent = id;
            document.getElementById('detailUserName').textContent = name;
            document.getElementById('detailUserUsername').textContent = '@' + username;
            document.getElementById('detailUserRole').textContent = role;
            
            // Estado con color
            const statusElement = document.getElementById('detailUserStatus');
            statusElement.textContent = status;
            statusElement.className = 'status-tag ' + (status === 'Activo' ? 'status-active' : 'status-inactive');
            
            document.getElementById('detailUserEmail').textContent = email || 'No especificado';
            document.getElementById('detailUserPhone').textContent = phone || 'No especificado';
            
            // Formatear fechas
            const formatDate = (dateString) => {
                if (!dateString) return 'No disponible';
                const date = new Date(dateString);
                return date.toLocaleString('es-ES');
            };
            
            document.getElementById('detailUserCreationDate').textContent = formatDate(creationDate);
            document.getElementById('detailUserLastActivity').textContent = formatDate(lastActivity);

            // Mostrar overlay y tarjeta flotante
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('userDetailCard').style.display = 'block';

            // Deshabilitar scroll del body
            document.body.style.overflow = 'hidden';
        }

        // Función para ocultar detalles del usuario
        function hideUserDetails() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('userDetailCard').style.display = 'none';

            // Habilitar scroll del body
            document.body.style.overflow = 'auto';
        }

        // Cerrar con tecla ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideUserDetails();
            }
        });

        // Inicializar el filtro al cargar la página
        document.addEventListener('DOMContentLoaded', filterUsers);
    </script>
</body>

</html>