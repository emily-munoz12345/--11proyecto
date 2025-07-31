
<?php
// ESTO DEBE SER LO PRIMERO EN EL ARCHIVO (sin espacios/HTML antes)
define('CHECK_SESSION', true);
require_once __DIR__ . '/../php/auth.php';
require_once __DIR__ . '/../php/conexion.php';

// Obtener datos del usuario
$stmt = $conex->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$_SESSION['id_usuario']]);
$usuario = $stmt->fetch();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario | Nacional Tapizados</title>
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
            margin: 2rem auto;
            padding: 2rem;
            max-width: 800px;
        }

        h1 {
            color: white;
            text-align: center;
            margin-bottom: 2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        h2 {
            color: rgba(255, 255, 255, 0.9);
            margin-top: 2rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .profile-form {
            background-color: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem 1rem;
            background-color: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: rgba(140, 74, 63, 0.8);
            background-color: rgba(255, 255, 255, 0.3);
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .btn-submit {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: rgba(140, 74, 63, 0.7);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            background-color: rgba(140, 74, 63, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

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

        .password-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Estilos responsivos */
        @media (max-width: 768px) {
            .main-container {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            .profile-form {
                padding: 1.5rem;
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
        
        <h1><i class="fas fa-user-circle"></i> Perfil de Usuario</h1>
        
        <form class="profile-form" action="guardar_perfil.php" method="post">
            <div class="form-group">
                <label for="nombre_completo">Nombre Completo</label>
                <input type="text" id="nombre_completo" name="nombre_completo" 
                       value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="correo_usuario">Correo Electrónico</label>
                <input type="email" id="correo_usuario" name="correo_usuario" 
                       value="<?php echo htmlspecialchars($usuario['correo_usuario']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="telefono_usuario">Teléfono</label>
                <input type="text" id="telefono_usuario" name="telefono_usuario" 
                       value="<?php echo htmlspecialchars($usuario['telefono_usuario']); ?>">
            </div>
            
            <div class="password-section">
                <h2><i class="fas fa-lock"></i> Cambiar Contraseña</h2>
                
                <div class="form-group">
                    <label for="contrasena_actual">Contraseña Actual</label>
                    <input type="password" id="contrasena_actual" name="contrasena_actual" required>
                </div>
                
                <div class="form-group">
                    <label for="nueva_contrasena">Nueva Contraseña</label>
                    <input type="password" id="nueva_contrasena" name="nueva_contrasena" required>
                </div>
                
                <div class="form-group">
                    <label for="confirmar_contrasena">Confirmar Nueva Contraseña</label>
                    <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required>
                </div>
            </div>
            
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </form>
    </div>

    <!-- Script para mejorar la experiencia del formulario -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validación básica de coincidencia de contraseñas
            const form = document.querySelector('.profile-form');
            const nuevaContrasena = document.getElementById('nueva_contrasena');
            const confirmarContrasena = document.getElementById('confirmar_contrasena');
            
            form.addEventListener('submit', function(e) {
                if (nuevaContrasena.value !== confirmarContrasena.value) {
                    e.preventDefault();
                    alert('Las contraseñas nuevas no coinciden');
                    confirmarContrasena.focus();
                }
            });
            
            // Mejorar el foco de los inputs
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'none';
                });
            });
        });
    </script>
</body>
</html>