<?php
include 'db_connection.php';

if ($conn->connect_error) {
    echo "Conexión fallida: " . $conn->connect_error;
} else {
    echo "Conexión exitosa a la base de datos";
}

$conn->close();

// <!-- version 0.0.5 -->
?>
