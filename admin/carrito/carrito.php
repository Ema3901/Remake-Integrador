<?php
session_start(); // Iniciar sesión

// Verificar si el carrito existe, si no, inicializarlo
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Verificar si se ha enviado un producto al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $id_producto = $_POST['id_producto'] ?? null;
    $modelo = $_POST['modelo'] ?? null;
    $precio = $_POST['precio'] ?? null;
    $talla = $_POST['talla'] ?? null;
    $color = $_POST['color'] ?? null;

    // Validar que los datos estén presentes
    if ($id_producto && $modelo && $precio && $talla && $color) {
        // Crear una clave única para el producto
        $producto_id = "{$id_producto}_{$talla}_{$color}";

        // Verificar si el producto ya está en el carrito
        if (isset($_SESSION['cart'][$producto_id])) {
            // Si ya está, incrementar la cantidad
            $_SESSION['cart'][$producto_id]['cantidad']++;
        } else {
            // Si no está, agregar el producto con cantidad 1
            $_SESSION['cart'][$producto_id] = [
                'id_producto' => $id_producto,
                'modelo' => $modelo,
                'precio' => $precio,
                'talla' => $talla,
                'color' => $color,
                'cantidad' => 1
            ];
        }
    }
}

// Depuración para verificar el estado del carrito
echo '<pre>';
var_dump($_SESSION['cart']); // Esto te ayudará a ver si los datos están llegando correctamente
echo '</pre>';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="icon" type="image/x-icon" href="/src/images/logo/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/src/css/style.css">
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../../src/include/header.php'; ?>

    <!-- Contenido del carrito -->
    <main class="container my-5">
        <h2>Carrito de Compras</h2>
        <?php if (empty($_SESSION['cart'])): ?>
            <p>No hay productos en el carrito.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Talla</th>
                        <th>Color</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['modelo']) ?></td>
                            <td><?= htmlspecialchars($item['talla']) ?></td>
                            <td style="background-color: <?= htmlspecialchars($item['color']) ?>;"></td>
                            <td>$<?= number_format($item['precio'], 2) ?></td>
                            <td><?= $item['cantidad'] ?></td>
                            <td>$<?= number_format($item['precio'] * $item['cantidad'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- Enlace para proceder al pago -->
            <a href="checkout.php" class="btn btn-success">Proceder al pago</a>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../../src/include/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
