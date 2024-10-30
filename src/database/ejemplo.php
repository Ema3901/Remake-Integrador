<?php
include 'db_connection.php';

$sql = "SELECT tn.marca, tn.nombre_modelo, tn.precio, tn.descripcion, t.tallaMX, t.tallaEUA, c.color, g.genero, te.stock
        FROM tenis tn
        JOIN tenis_estilos te ON tn.id_tenis = te.id_tenis
        JOIN tallas t ON te.id_talla = t.id_talla
        JOIN colores c ON te.id_color = c.id_color
        JOIN generos g ON te.id_genero = g.id_genero";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h2>" . $row["marca"] . " - " . $row["nombre_modelo"] . "</h2>";
        echo "<p>Precio: $" . $row["precio"] . "</p>";
        echo "<p>Descripción: " . $row["descripcion"] . "</p>";
        echo "<p>Talla: " . $row["tallaMX"] . " (MX) / " . $row["tallaEUA"] . " (USA)</p>";
        echo "<p>Color: " . $row["color"] . "</p>";
        echo "<p>Género: " . $row["genero"] . "</p>";
        echo "<p>Stock: " . $row["stock"] . "</p>";
        echo "</div>";
    }
} else {
    echo "No se encontraron productos.";
}

$conn->close();
// <!-- version 0.0.5 -->
?>
