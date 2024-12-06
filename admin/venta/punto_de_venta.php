<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

session_start();

// Si no hay una sesión activa, redirigir a /sesion/sesion.php
if (!isset($_SESSION['user_id'])) {
    header('Location: /sesion/sesion.php');  // Cambia esto por la URL de tu página de sesión
    exit();
}

// Obtiene el ID del usuario activo (vendedor)
$user_id = $_SESSION['user_id'];

// Verifica si se ha enviado el formulario de venta
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibe los datos del formulario
    $shoe_id = $_POST['shoe_id'];
    $quantity = $_POST['quantity'];
    $venta_tipo = $_POST['venta_tipo'];  // "local" o "tianguis"
    
    // 1. Obtener el nombre del vendedor
    $query_vendedor = "SELECT namee FROM users WHERE id_user = :user_id";
    $stmt = $pdo->prepare($query_vendedor);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $vendedor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 2. Obtener los detalles del producto (tenis)
    $query_tenis = "SELECT shoes.model_name, shoes.price, shoes.img_main
                    FROM shoes 
                    WHERE shoes.id_shoe = :shoe_id";
    $stmt = $pdo->prepare($query_tenis);
    $stmt->bindParam(':shoe_id', $shoe_id, PDO::PARAM_INT);
    $stmt->execute();
    $tenis = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Obtener tamaño y color del tenis
    $query_variaciones = "SELECT sizes.sizeMX, colors.color
                          FROM shoes_variations
                          JOIN sizes ON shoes_variations.id_size = sizes.id_size
                          JOIN colors ON shoes_variations.id_color = colors.id_color
                          WHERE shoes_variations.id_shoe = :shoe_id";
    $stmt = $pdo->prepare($query_variaciones);
    $stmt->bindParam(':shoe_id', $shoe_id, PDO::PARAM_INT);
    $stmt->execute();
    $variacion = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Registrar la venta
    $query_venta = "INSERT INTO sales (quantity, datee, fk_user, fk_shoe) 
                    VALUES (:quantity, NOW(), :user_id, :shoe_id)";
    $stmt = $pdo->prepare($query_venta);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':shoe_id', $shoe_id, PDO::PARAM_INT);
    $stmt->execute();
    
    // 4. Actualizar el stock dependiendo del tipo de venta
    if ($venta_tipo == 'local') {
        $query_stock_local = "UPDATE shoes_variations 
                              SET stock_local = stock_local - :quantity 
                              WHERE id_shoe = :shoe_id AND id_size = :size_id AND id_color = :color_id";
        $stmt = $pdo->prepare($query_stock_local);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_STR);
        $stmt->bindParam(':shoe_id', $shoe_id, PDO::PARAM_INT);
        $stmt->bindParam(':size_id', $variacion['id_size'], PDO::PARAM_INT);
        $stmt->bindParam(':color_id', $variacion['id_color'], PDO::PARAM_INT);
        $stmt->execute();
    } elseif ($venta_tipo == 'tianguis') {
        $query_stock_tianguis = "UPDATE shoes_variations 
                                 SET stock_tianguis = stock_tianguis - :quantity 
                                 WHERE id_shoe = :shoe_id AND id_size = :size_id AND id_color = :color_id";
        $stmt = $pdo->prepare($query_stock_tianguis);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_STR);
        $stmt->bindParam(':shoe_id', $shoe_id, PDO::PARAM_INT);
        $stmt->bindParam(':size_id', $variacion['id_size'], PDO::PARAM_INT);
        $stmt->bindParam(':color_id', $variacion['id_color'], PDO::PARAM_INT);
        $stmt->execute();
    }

    // Mensaje de confirmación
    echo "Venta registrada exitosamente. Vendedor: " . $vendedor['namee'] . "<br>";
    echo "Producto: " . $tenis['model_name'] . "<br>";
    echo "Tamaño: " . $variacion['sizeMX'] . " - Color: " . $variacion['color'] . "<br>";
    echo "Cantidad vendida: " . $quantity . "<br>";
    echo "Tipo de venta: " . $venta_tipo . "<br>";
    echo "Precio total: $" . ($tenis['price'] * $quantity) . "<br>";
} else {
    // Si no se envió el formulario, muestra el formulario
    ?>
    <h1>Punto de Venta</h1>
    <form method="POST" action="punto_de_venta.php">
        <label for="shoe_id">Selecciona el tenis:</label>
        <select name="shoe_id" required>
            <?php
            $query_tenis = "SELECT id_shoe, model_name FROM shoes";
            $stmt = $pdo->prepare($query_tenis);
            $stmt->execute();
            $tenisList = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($tenisList as $row) {
                echo "<option value='{$row['id_shoe']}'>{$row['model_name']}</option>";
            }
            ?>
        </select><br><br>

        <label for="quantity">Cantidad:</label>
        <input type="number" name="quantity" min="1" required><br><br>

        <label for="venta_tipo">Tipo de venta:</label>
        <select name="venta_tipo" required>
            <option value="local">Local</option>
            <option value="tianguis">Tianguis</option>
        </select><br><br>

        <button type="submit">Registrar Venta</button>
    </form>
    <?php
}
?>
