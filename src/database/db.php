<?php
$servername = "localhost"; // Cambia esto si es necesario
$username = "u864758327_DevCalzadoJJ";        // Usuario de tu servidor
$password = "calzadoJJ_marsopa69";            // Contraseña de tu servidor
$database = "u864758327_CalzadoJJ"; // Nombre de tu base de datos

try {
    // Crear conexión usando PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
