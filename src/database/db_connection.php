<?php
$servername = "localhost";
$username = "root"; // Ajusta según tu configuración
$password = ""; // Ajusta si tienes una contraseña para MySQL
$dbname = "inventory_jj"; // Nombre de tu base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// <!-- version 0.0.5 -->
?>


