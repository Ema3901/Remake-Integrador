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
    echo "<h2>Venta registrada exitosamente</h2>";
    echo "<p><strong>Vendedor:</strong> " . $vendedor['namee'] . "</p>";
    echo "<p><strong>Producto:</strong> " . $tenis['model_name'] . "</p>";
    echo "<p><strong>Tamaño:</strong> " . $variacion['sizeMX'] . " - <strong>Color:</strong> " . $variacion['color'] . "</p>";
    echo "<p><strong>Cantidad vendida:</strong> " . $quantity . "</p>";
    echo "<p><strong>Tipo de venta:</strong> " . $venta_tipo . "</p>";
    echo "<p><strong>Precio total:</strong> $" . ($tenis['price'] * $quantity) . "</p>";
    echo "<br><a href='punto_de_venta.php'>Añadir otro producto</a><br>";
} else {
    // Si no se envió el formulario, muestra el formulario
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Punto de Venta | Calzado JJ</title>

        <link rel="icon" type="image/x-icon" href="https://calzadojj.net/src/images/logo/favicon.png">
        
        <!-- Bootstrap 5.3 CDN -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Font Awesome CDN -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

        <!-- Custom CSS -->
        <link rel="stylesheet" href="https://calzadojj.net/src/css/style.css">
    </head>
    <body>

        <!-- Header -->
        <?php include __DIR__ . '/src/header.php'; ?>

        <!-- Main content -->
        <main class="container my-5">
            <h1 class="mb-4">Punto de Venta</h1>
            <form method="POST" action="punto_de_venta.php">
                <div class="mb-3">
                    <label for="shoe_id" class="form-label">Selecciona el tenis:</label>
                    <select name="shoe_id" id="shoe_id" class="form-select" required>
                        <?php
                        $query_tenis = "SELECT id_shoe, model_name FROM shoes";
                        $stmt = $pdo->prepare($query_tenis);
                        $stmt->execute();
                        $tenisList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($tenisList as $row) {
                            echo "<option value='{$row['id_shoe']}'>{$row['model_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Cantidad:</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                    <label for="venta_tipo" class="form-label">Tipo de venta:</label>
                    <select name="venta_tipo" id="venta_tipo" class="form-select" required>
                        <option value="local">Local</option>
                        <option value="tianguis">Tianguis</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Registrar Venta</button>
            </form>

            <br>
            <a href="punto_de_venta.php" class="btn btn-link">Añadir otro producto</a>
        </main>

        <!-- Footer -->
        <?php include __DIR__ . '/src/footer.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    </body>
    </html>
    
    <?php
}
?>
