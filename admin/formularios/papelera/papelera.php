<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Papelera - Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: rgba(140, 74, 63, 0.8);
            --primary-hover: rgba(140, 74, 63, 1);
            --secondary-color: rgba(108, 117, 125, 0.8);
            --text-color: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
            --bg-transparent: rgba(255, 255, 255, 0.1);
            --bg-transparent-light: rgba(255, 255, 255, 0.15);
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

        /* Estilos para tarjetas */
        .card {
            background-color: var(--bg-transparent-light);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: 1px solid var(--border-color);
            font-weight: 500;
            padding: 1rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Estilos para botones */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            gap: 0.5rem;
        }

        .btn i {
            font-size: 0.9rem;
        }

        .btn-sm {
            padding: 0.35rem 0.5rem;
            font-size: 0.8rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-secondary:hover {
            background-color: rgba(108, 117, 125, 1);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: rgba(220, 53, 69, 1);
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: rgba(25, 135, 84, 1);
        }

        /* Estilos para formularios */
        .form-label {
            color: var(--text-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-select, .form-control {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            border: none;
            background-color: var(--bg-transparent-light);
            color: var(--text-color);
            font-size: 1rem;
            backdrop-filter: blur(5px);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .form-select:focus, .form-control:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--primary-color);
            background-color: rgba(255, 255, 255, 0.2);
        }

        .form-select::placeholder, .form-control::placeholder {
            color: var(--text-muted);
        }

        /* Estilos para tablas */
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
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-color);
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

        /* Badges */
        .badge {
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .bg-info {
            background-color: var(--info-color) !important;
        }

        .bg-warning {
            background-color: var(--warning-color) !important;
            color: #000 !important;
        }

        /* Checkboxes */
        .form-check-input {
            background-color: var(--bg-transparent-light);
            border: 1px solid var(--border-color);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Modal */
        .modal-content {
            background-color: var(--bg-transparent);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            color: var(--text-color);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
        }

        .btn-close {
            filter: invert(1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                margin: 1rem;
                padding: 1.5rem;
            }

            .header-section {
                flex-direction: column;
                align-items: flex-start;
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
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Encabezado -->
        <div class="header-section">
            <h1 class="page-title">
                <i class="fas fa-trash"></i> Sistema de Papelera
            </h1>
            <div class="d-flex gap-2">
                <a href="../../dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="m-0"><i class="fas fa-filter me-2"></i>Filtros</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipo de Elemento</label>
                        <select class="form-select" id="filterType">
                            <option value="all">Todos los elementos</option>
                            <option value="clientes">Clientes</option>
                            <option value="productos">Productos</option>
                            <option value="pedidos">Pedidos</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha de Eliminación</label>
                        <select class="form-select" id="filterDate">
                            <option value="all">Cualquier fecha</option>
                            <option value="today">Hoy</option>
                            <option value="week">Esta semana</option>
                            <option value="month">Este mes</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Buscar</label>
                        <input type="text" class="form-control" placeholder="Buscar...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Elementos en Papelera -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0"><i class="fas fa-trash-alt me-2"></i>Elementos Eliminados</h5>
                <div>
                    <button class="btn btn-danger btn-sm" id="emptyTrash">
                        <i class="fas fa-trash"></i> Vaciar Papelera
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table id="trashTable">
                        <thead>
                            <tr>
                                <th width="50px">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Eliminado por</th>
                                <th>Fecha de Eliminación</th>
                                <th width="120px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-label="Seleccionar">
                                    <input type="checkbox" class="form-check-input item-checkbox">
                                </td>
                                <td data-label="Nombre">Juan Pérez</td>
                                <td data-label="Tipo"><span class="badge bg-primary">Cliente</span></td>
                                <td data-label="Eliminado por">admin@ejemplo.com</td>
                                <td data-label="Fecha">20/10/2023 14:30</td>
                                <td data-label="Acciones">
                                    <button class="btn btn-success btn-sm action-btn restore-btn" title="Restaurar">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm action-btn delete-btn" title="Eliminar permanentemente">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td data-label="Seleccionar">
                                    <input type="checkbox" class="form-check-input item-checkbox">
                                </td>
                                <td data-label="Nombre">Producto Ejemplo</td>
                                <td data-label="Tipo"><span class="badge bg-info">Producto</span></td>
                                <td data-label="Eliminado por">admin@ejemplo.com</td>
                                <td data-label="Fecha">19/10/2023 10:15</td>
                                <td data-label="Acciones">
                                    <button class="btn btn-success btn-sm action-btn restore-btn" title="Restaurar">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm action-btn delete-btn" title="Eliminar permanentemente">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td data-label="Seleccionar">
                                    <input type="checkbox" class="form-check-input item-checkbox">
                                </td>
                                <td data-label="Nombre">Pedido #12345</td>
                                <td data-label="Tipo"><span class="badge bg-warning">Pedido</span></td>
                                <td data-label="Eliminado por">admin@ejemplo.com</td>
                                <td data-label="Fecha">18/10/2023 16:45</td>
                                <td data-label="Acciones">
                                    <button class="btn btn-success btn-sm action-btn restore-btn" title="Restaurar">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm action-btn delete-btn" title="Eliminar permanentemente">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td data-label="Seleccionar">
                                    <input type="checkbox" class="form-check-input item-checkbox">
                                </td>
                                <td data-label="Nombre">María García</td>
                                <td data-label="Tipo"><span class="badge bg-primary">Cliente</span></td>
                                <td data-label="Eliminado por">admin@ejemplo.com</td>
                                <td data-label="Fecha">17/10/2023 09:20</td>
                                <td data-label="Acciones">
                                    <button class="btn btn-success btn-sm action-btn restore-btn" title="Restaurar">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm action-btn delete-btn" title="Eliminar permanentemente">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Confirmar acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    ¿Estás seguro de que deseas realizar esta acción?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmAction">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionar todos los elementos
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
            
            // Restaurar elemento
            const restoreButtons = document.querySelectorAll('.restore-btn');
            restoreButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                    document.getElementById('modalTitle').textContent = 'Restaurar Elemento';
                    document.getElementById('modalBody').textContent = '¿Estás seguro de que deseas restaurar este elemento?';
                    
                    document.getElementById('confirmAction').onclick = function() {
                        // Aquí iría la lógica para restaurar el elemento
                        const row = button.closest('tr');
                        row.style.opacity = '0.5';
                        setTimeout(() => {
                            row.remove();
                        }, 1000);
                        
                        // Cerrar el modal
                        bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
                    };
                    
                    modal.show();
                });
            });
            
            // Eliminar permanentemente
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                    document.getElementById('modalTitle').textContent = 'Eliminar Permanentemente';
                    document.getElementById('modalBody').textContent = '¿Estás seguro de que deseas eliminar permanentemente este elemento? Esta acción no se puede deshacer.';
                    
                    document.getElementById('confirmAction').onclick = function() {
                        // Aquí iría la lógica para eliminar permanentemente
                        const row = button.closest('tr');
                        row.style.opacity = '0.5';
                        setTimeout(() => {
                            row.remove();
                        }, 1000);
                        
                        // Cerrar el modal
                        bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
                    };
                    
                    modal.show();
                });
            });
            
            // Vaciar papelera
            document.getElementById('emptyTrash').addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                document.getElementById('modalTitle').textContent = 'Vaciar Papelera';
                document.getElementById('modalBody').textContent = '¿Estás seguro de que deseas vaciar la papelera? Todos los elementos serán eliminados permanentemente. Esta acción no se puede deshacer.';
                
                document.getElementById('confirmAction').onclick = function() {
                    // Aquí iría la lógica para vaciar la papelera
                    const rows = document.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        row.style.opacity = '0.5';
                        setTimeout(() => {
                            row.remove();
                        }, 1000);
                    });
                    
                    // Cerrar el modal
                    bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
                };
                
                modal.show();
            });
        });
    </script>
</body>
</html>