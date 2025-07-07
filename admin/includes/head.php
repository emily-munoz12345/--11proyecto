<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Nacional Tapizados - Expertos en Tapicería Automotriz' ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Nuestros estilos personalizados -->
    <link rel="stylesheet" href="/--11proyecto/css/variables.css">
    <link rel="stylesheet" href="/--11proyecto/css/public.css">
    
    <!-- Favicon -->
    <link rel="icon" href="/--11proyecto/public/assets/favicon.ico" type="image/x-icon">
    
    <!-- Meta descripción -->
    <meta name="description" content="Expertos en tapicería automotriz con más de 25 años de experiencia, ofreciendo calidad y servicio personalizado.">
    
    <style>
        :root {
            --bs-dark: #1a1a1a;
            --bs-primary: #8b0000;
            --bs-secondary: #2d2d2d;
        }
        
        .bg-dark {
            background-color: var(--bs-dark) !important;
        }
        
        .bg-primary {
            background-color: var(--bs-primary) !important;
        }
        
        .text-primary {
            color: var(--bs-primary) !important;
        }
        
        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }
        
        .btn-outline-primary {
            color: var(--bs-primary);
            border-color: var(--bs-primary);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--bs-primary);
            color: white;
        }
    </style>
</head>
<body>