<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $project = '/--11proyecto';
    return "$protocol://$host$project";
}

function requireAuth() {
    if (!isset($_SESSION['usuario_id'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: " . getBaseUrl() . "/admin/login.php");
        exit;
    }
}

function redirectIfAuthenticated() {
    if (isset($_SESSION['usuario_id'])) {
        $redirect_url = $_SESSION['redirect_url'] ?? getBaseUrl() . '/admin/dashboard.php';
        unset($_SESSION['redirect_url']);
        header("Location: " . $redirect_url);
        exit;
    }
}

function getUserRole() {
    return $_SESSION['usuario_rol'] ?? 'Invitado';
}

function getUserName() {
    return $_SESSION['usuario_nombre'] ?? 'Usuario';
}

function getUserId() {
    return $_SESSION['usuario_id'] ?? null;
}

function isAdmin() {
    return getUserRole() === 'Administrador';
}

function isTechnician() {
    return getUserRole() === 'Tecnico';
}

function isSeller() {
    return getUserRole() === 'Vendedor';
}

function logout() {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

function getUserEmail() {
    return $_SESSION['usuario_correo'] ?? '';
}
?>