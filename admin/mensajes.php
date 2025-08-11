<?php
require_once __DIR__ . '../../php/conexion.php';

// Consulta para obtener los mensajes de contacto
$stmt = $conex->query("SELECT * FROM mensajes_contacto ORDER BY fecha_envio DESC");
$mensajes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes de Contacto | Nacional Tapizados</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .unread {
            background-color: #f8f9fa;
            font-weight: 500;
        }
        .message-preview {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 300px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><i class="fas fa-envelope me-2"></i>Mensajes de Contacto</h1>
                </div>

                <?php if (isset($_GET['status'])): ?>
                    <div class="alert alert-<?= $_GET['status'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                        <?= $_GET['status'] === 'success' ? 'Operación realizada con éxito.' : htmlspecialchars($_GET['message'] ?? 'Error en la operación.') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Teléfono</th>
                                        <th>Asunto</th>
                                        <th>Mensaje</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($mensajes) > 0): ?>
                                        <?php foreach ($mensajes as $mensaje): ?>
                                        <tr class="<?= $mensaje['leido'] ? '' : 'unread' ?>">
                                            <td><?= htmlspecialchars($mensaje['id_mensaje']) ?></td>
                                            <td><?= htmlspecialchars($mensaje['nombre_completo']) ?></td>
                                            <td><?= htmlspecialchars($mensaje['correo_electronico']) ?></td>
                                            <td><?= htmlspecialchars($mensaje['telefono']) ?></td>
                                            <td><?= htmlspecialchars($mensaje['asunto']) ?></td>
                                            <td class="message-preview" title="<?= htmlspecialchars($mensaje['mensaje']) ?>">
                                                <?= htmlspecialchars($mensaje['mensaje']) ?>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($mensaje['fecha_envio'])) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $mensaje['leido'] ? 'success' : 'warning' ?>">
                                                    <?= $mensaje['leido'] ? 'Leído' : 'No leído' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="ver_mensaje.php?id=<?= $mensaje['id_mensaje'] ?>" class="btn btn-sm btn-primary" title="Ver mensaje">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center py-4">No hay mensajes de contacto</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>