<?php
$host = 'localhost';
$dbname = 'bd_peproyect';
$username = 'root';  // Cambiar si es necesario
$password = '';      // Cambiar si es necesario

try {
    $conex = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conex->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conex->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>