<?php
session_start();

// 1. Destruir variables de sesión
$_SESSION = [];

// 2. Eliminar cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Destruir sesión
session_destroy();


// Redirigir al login
header('Location: ../admin/login.php');
exit;
?>