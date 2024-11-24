<?php
$servername = "localhost"; // Cambia esto si es necesario
$username = "root";        // Usuario de tu servidor
$password = "";            // Contraseña de tu servidor
$database = "databasecalzadojj"; // Nombre de tu base de datos

try {
    // Crear conexión usando PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
