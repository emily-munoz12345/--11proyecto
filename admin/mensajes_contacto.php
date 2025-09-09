<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../php/conexion.php';
require_once __DIR__ . '/../php/auth.php';

// Verificar permisos (solo admin y técnicos)
if (!isAdmin() && !isTechnician()) {
    header('Location: ../dashboard.php');
    exit;
}

// Obtener mensajes de la base de datos
$query = "SELECT * FROM mensajes_contacto WHERE activo = 1 ORDER BY fecha_envio DESC";
$stmt = $conex->query($query);
$mensajes = $stmt->fetchAll();

// Obtener estadísticas
$totalMensajes = count($mensajes);
$mensajesNoLeidos = 0;
$mensajesHoy = 0;

$hoy = date('Y-m-d');
foreach ($mensajes as $mensaje) {
    if ($mensaje['leido'] == 0) {
        $mensajesNoLeidos++;
    }
    if (date('Y-m-d', strtotime($mensaje['fecha_envio'])) == $hoy) {
        $mensajesHoy++;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buzón de Mensajes | Nacional Tapizados</title>
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.8);
            --primary-hover: rgba(140, 74, 63, 1);
            --secondary-color: rgba(108, 117, 125, 0.8);
            --text-color: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --bg-transparent: rgba(255, 255, 255, 0.1);
            --bg-transparent-light: rgba(255, 255, 255, 0.15);
            --bg-input: rgba(0, 0, 0, 0.4);
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
            color: var(--text-color);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .main-container {
            max-width: 1200px;
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

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
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
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            justify-content: center;
        }

        .summary-card {
            background-color: var(--primary-color);
            border-radius: 10px;
            padding: 1.5rem;
            min-width: 200px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-color);
        }

        .summary-card h3 {
            margin-top: 0;
            font-size: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-color);
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

        /* Estilos para la lista de mensajes */
        .messages-list {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .message-item {
            padding: 1.2rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .message-item.unread {
            background-color: rgba(13, 202, 240, 0.1);
            border-left: 4px solid var(--info-color);
        }

        .message-item:hover {
            background-color: var(--bg-transparent);
        }

        .message-item:last-child {
            border-bottom: none;
        }

        .message-info {
            flex-grow: 1;
        }

        .message-sender {
            font-weight: 500;
            font-size: 1.1rem;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .message-subject {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 0.3rem;
        }

        .message-preview {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .message-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.5rem;
        }

        .message-date {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .message-status {
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 500;
        }

        .status-unread {
            background-color: var(--info-color);
            color: white;
        }

        .status-read {
            background-color: var(--secondary-color);
            color: white;
        }

        .message-arrow {
            margin-left: 1rem;
            opacity: 0.7;
            transition: all 0.3s ease;
            color: var(--text-color);
        }

        .message-item:hover .message-arrow {
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
            word-break: break-word;
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

        .message-section {
            grid-column: 1 / -1;
            background-color: var(--bg-input);
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1rem;
            border: 1px solid var(--border-color);
        }

        .message-content {
            white-space: pre-wrap;
            line-height: 1.6;
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
                flex-direction: column;
            }

            .search-container {
                flex-direction: column;
            }

            .message-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .message-meta {
                flex-direction: row;
                align-items: center;
                margin-top: 0.5rem;
                width: 100%;
                justify-content: space-between;
            }

            .message-arrow {
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
            <h1 class="page-title"><i class="fas fa-envelope"></i>Buzón de Mensajes</h1>
            <a href="../dashboard.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i>Volver al Dashboard
            </a>
        </div>

        <!-- Resumen de mensajes -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total de Mensajes</h3>
                <p><?php echo $totalMensajes; ?></p>
            </div>
            <div class="summary-card">
                <h3>No Leídos</h3>
                <p><?php echo $mensajesNoLeidos; ?></p>
            </div>
            <div class="summary-card">
                <h3>Recibidos Hoy</h3>
                <p><?php echo $mensajesHoy; ?></p>
            </div>
        </div>

        <!-- Buscador -->
        <div class="search-container">
            <input type="text" id="searchInput" class="search-input" placeholder="Buscar mensajes..." onkeyup="filterMessages()">
            <button class="search-button" onclick="filterMessages()">
                <i class="fas fa-search"></i>Buscar
            </button>
        </div>

        <!-- Lista de mensajes -->
        <div class="messages-list" id="messagesList">
            <?php foreach ($mensajes as $mensaje): 
                $isUnread = $mensaje['leido'] == 0;
                $shortMessage = strlen($mensaje['mensaje']) > 100 
                    ? substr($mensaje['mensaje'], 0, 100) . '...' 
                    : $mensaje['mensaje'];
            ?>
                <div class="message-item <?php echo $isUnread ? 'unread' : ''; ?>" 
                    data-id="<?php echo $mensaje['id_mensaje']; ?>"
                    onclick="showMessageDetails(
                        <?php echo $mensaje['id_mensaje']; ?>,
                        '<?php echo addslashes($mensaje['nombre_completo']); ?>',
                        '<?php echo addslashes($mensaje['correo_electronico']); ?>',
                        '<?php echo addslashes($mensaje['telefono'] ?? 'No especificado'); ?>',
                        '<?php echo addslashes($mensaje['asunto']); ?>',
                        '<?php echo addslashes($mensaje['mensaje']); ?>',
                        '<?php echo $mensaje['fecha_envio']; ?>',
                        <?php echo $mensaje['leido']; ?>
                    )">
                    <div class="message-info">
                        <div class="message-sender">
                            <?php if ($isUnread): ?>
                                <i class="fas fa-envelope"></i>
                            <?php else: ?>
                                <i class="fas fa-envelope-open"></i>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($mensaje['nombre_completo']); ?>
                        </div>
                        <div class="message-subject">
                            <strong>Asunto:</strong> <?php echo htmlspecialchars($mensaje['asunto']); ?>
                        </div>
                        <div class="message-preview">
                            <?php echo htmlspecialchars($shortMessage); ?>
                        </div>
                    </div>
                    <div class="message-meta">
                        <span class="message-date">
                            <?php echo date('d/m/Y H:i', strtotime($mensaje['fecha_envio'])); ?>
                        </span>
                        <span class="message-status <?php echo $isUnread ? 'status-unread' : 'status-read'; ?>">
                            <?php echo $isUnread ? 'No leído' : 'Leído'; ?>
                        </span>
                    </div>
                    <div class="message-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Overlay para fondo oscuro -->
    <div class="overlay" id="overlay" onclick="hideMessageDetails()"></div>

    <!-- Tarjeta flotante de detalles del mensaje -->
    <div class="floating-card" id="messageDetailCard">
        <div class="card-header">
            <h2 class="card-title" id="detailMessageSubject"></h2>
            <button class="close-card" onclick="hideMessageDetails()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="card-content">
            <div class="detail-item">
                <div class="detail-label">Remitente</div>
                <div class="detail-value" id="detailMessageSender"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Correo Electrónico</div>
                <div class="detail-value" id="detailMessageEmail"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Teléfono</div>
                <div class="detail-value" id="detailMessagePhone"></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Fecha de Envío</div>
                <div class="detail-value" id="detailMessageDate"></div>
            </div>

            <div class="message-section">
                <div class="detail-label">Mensaje</div>
                <div class="detail-value message-content" id="detailMessageContent"></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Función para filtrar mensajes
        function filterMessages() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const messagesList = document.getElementById('messagesList');
            const messages = messagesList.getElementsByClassName('message-item');

            for (let i = 0; i < messages.length; i++) {
                const sender = messages[i].querySelector('.message-sender').textContent;
                const subject = messages[i].querySelector('.message-subject').textContent;
                const preview = messages[i].querySelector('.message-preview').textContent;
                
                if (sender.toUpperCase().indexOf(filter) > -1 || 
                    subject.toUpperCase().indexOf(filter) > -1 || 
                    preview.toUpperCase().indexOf(filter) > -1) {
                    messages[i].style.display = "flex";
                } else {
                    messages[i].style.display = "none";
                }
            }
        }

        // Función para mostrar detalles del mensaje
        function showMessageDetails(id, name, email, phone, subject, message, date, isRead) {
            document.getElementById('detailMessageSubject').textContent = subject;
            document.getElementById('detailMessageSender').textContent = name;
            document.getElementById('detailMessageEmail').textContent = email;
            document.getElementById('detailMessagePhone').textContent = phone;
            document.getElementById('detailMessageContent').textContent = message;

            // Formatear fecha
            if (date) {
                const formattedDate = new Date(date).toLocaleString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                document.getElementById('detailMessageDate').textContent = formattedDate;
            } else {
                document.getElementById('detailMessageDate').textContent = 'No especificada';
            }

            // Marcar como leído si no lo está
            if (isRead == 0) {
                markAsRead(id);
            }

            // Mostrar overlay y tarjeta flotante
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('messageDetailCard').style.display = 'block';

            // Deshabilitar scroll del body
            document.body.style.overflow = 'hidden';
        }

        // Función para marcar mensaje como leído
        function markAsRead(messageId) {
            // Enviar solicitud AJAX para marcar como leído
            fetch('../php/marcar_leido.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id_mensaje=' + messageId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar la interfaz
                    const messageItem = document.querySelector(`.message-item[data-id="${messageId}"]`);
                    if (messageItem) {
                        messageItem.classList.remove('unread');
                        const statusElement = messageItem.querySelector('.message-status');
                        if (statusElement) {
                            statusElement.textContent = 'Leído';
                            statusElement.classList.remove('status-unread');
                            statusElement.classList.add('status-read');
                        }
                        const iconElement = messageItem.querySelector('.message-sender i');
                        if (iconElement) {
                            iconElement.className = 'fas fa-envelope-open';
                        }
                        
                        // Actualizar contador de no leídos
                        const noLeidosElement = document.querySelector('.summary-cards .summary-card:nth-child(2) p');
                        if (noLeidosElement) {
                            let currentCount = parseInt(noLeidosElement.textContent);
                            if (currentCount > 0) {
                                noLeidosElement.textContent = currentCount - 1;
                            }
                        }
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Función para ocultar detalles del mensaje
        function hideMessageDetails() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('messageDetailCard').style.display = 'none';

            // Habilitar scroll del body
            document.body.style.overflow = 'auto';
        }

        // Cerrar con tecla ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideMessageDetails();
            }
        });

        // Inicializar el filtro al cargar la página
        document.addEventListener('DOMContentLoaded', filterMessages);
    </script>
</body>
</html>