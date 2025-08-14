<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Nacional Tapizados - Panel Administrativo' ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Nuestros estilos -->
    <link rel="stylesheet" href="/--11proyecto/public/assets/css/variables.css">
    <link rel="stylesheet" href="/--11proyecto/public/assets/css/admin.css">

    <!-- Favicon -->
    <link rel="icon" href="/--11proyecto/public/assets/favicon.ico" type="image/x-icon">

    <script>
    // Aplicar configuración al cargar cada página
    document.addEventListener('DOMContentLoaded', function() {
        const config = JSON.parse(localStorage.getItem('userConfig')) || {};
        
        if (Object.keys(config).length > 0) {
            const root = document.documentElement;
            
            // Aplicar colores
            if (config.primaryColor) {
                root.style.setProperty('--primary-dark', config.primaryColor);
                root.style.setProperty('--secondary-dark', shadeColor(config.primaryColor, -20));
                root.style.setProperty('--accent-color', shadeColor(config.primaryColor, 10));
            }
            
            if (config.secondaryColor) {
                root.style.setProperty('--gold-cream', config.secondaryColor);
                root.style.setProperty('--gold-pastel', shadeColor(config.secondaryColor, 10));
            }
            
            // Aplicar fuente
            if (config.fontFamily) {
                document.body.style.fontFamily = config.fontFamily;
            }
            
            // Aplicar tamaño de texto
            if (config.fontSize) {
                const sizes = {
                    small: '13px',
                    normal: '16px',
                    large: '18px'
                };
                document.body.style.fontSize = sizes[config.fontSize];
            }
            
            // Aplicar menú colapsado
            if (config.collapsedMenu) {
                document.querySelector('.admin-sidebar').classList.add('collapsed');
                document.querySelector('.content-wrapper').classList.add('sidebar-collapsed');
            }
        }
        
        function shadeColor(color, percent) {
            // La misma función del archivo de configuración
            let R = parseInt(color.substring(1,3), 16);
            let G = parseInt(color.substring(3,5), 16);
            let B = parseInt(color.substring(5,7), 16);

            R = parseInt(R * (100 + percent) / 100);
            G = parseInt(G * (100 + percent) / 100);
            B = parseInt(B * (100 + percent) / 100);

            R = (R<255)?R:255;  
            G = (G<255)?G:255;  
            B = (B<255)?B:255;  

            R = Math.round(R);
            G = Math.round(G);
            B = Math.round(B);

            const RR = ((R.toString(16).length==1)?"0"+R.toString(16):R.toString(16));
            const GG = ((G.toString(16).length==1)?"0"+G.toString(16):G.toString(16));
            const BB = ((B.toString(16).length==1)?"0"+B.toString(16):B.toString(16));

            return "#"+RR+GG+BB;
        }
    });
</script>
</head>

<body class="admin-body">
    