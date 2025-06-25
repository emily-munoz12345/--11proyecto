<?php
session_start();

function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $project = '/--11proyecto';
    return "$protocol://$host$project";
}

function requireAuth() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: " . getBaseUrl() . "/admin/login.php");
        exit;
    }
}

function getUserRole() {
    return $_SESSION['usuario_rol'] ?? null;
}

function getUserName() {
    return $_SESSION['usuario_nombre'] ?? 'Usuario';
}

function getUserId() {
    return $_SESSION['usuario_id'] ?? null;
}

function isAdmin() {
    return ($_SESSION['usuario_rol'] ?? '') === 'Administrador';
}

function isTechnician() {
    return ($_SESSION['usuario_rol'] ?? '') === 'Tecnico';
}

function isSeller() {
    return ($_SESSION['usuario_rol'] ?? '') === 'Vendedor';
}
?>