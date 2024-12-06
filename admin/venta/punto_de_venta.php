<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

session_start();

// Si no hay una sesión activa, redirigir a /sesion/sesion.php
if (!isset($_SESSION['user_id'])) {
    header('Location: /sesion/sesion.php');  // Cambia esto por la URL de tu página de sesión
    exit();
}

// Función para buscar productos
function buscarProducto($term) {
    global $pdo;
    $sql = "SELECT id_shoe, model_name FROM shoes WHERE model_name LIKE :term OR id_shoe = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':term' => "%$term%", ':id' => $term]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener las variaciones de un producto
function obtenerVariaciones($id_shoe) {
    global $pdo;
    $sql = "SELECT sv.id_varition, s.id_size, sz.sizeMX, c.color, sv.stock_local, sv.stock_tianguis
            FROM shoes_variations sv
            JOIN sizes sz ON sv.id_size = sz.id_size
            JOIN colors c ON sv.id_color = c.id_color
            WHERE sv.id_shoe = :id_shoe";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_shoe' => $id_shoe]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para actualizar el stock después de una compra
function actualizarStock($id_variation, $stock_type) {
    global $pdo;
    if ($stock_type == 'local') {
        $sql = "UPDATE shoes_variations SET stock_local = stock_local - 1 WHERE id_varition = :id_variation AND stock_local > 0";
    } else {
        $sql = "UPDATE shoes_variations SET stock_tianguis = stock_tianguis - 1 WHERE id_varition = :id_variation AND stock_tianguis > 0";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_variation' => $id_variation]);
}

// Agregar al carrito
if (isset($_POST['agregar_al_carrito'])) {
    $id_variation = $_POST['id_variation'];
    $stock_type = $_POST['stock_type'];

    // Agregar producto al carrito en sesión
    $_SESSION['carrito'][] = [
        'id_variation' => $id_variation,
        'stock_type' => $stock_type
    ];

    // Actualizar el stock
    actualizarStock($id_variation, $stock_type);
}

// Buscar productos cuando se envía el formulario
$productos = [];
if (isset($_POST['buscar_producto'])) {
    $term = $_POST['term'];
    $productos = buscarProducto($term);
}
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
    <link rel="stylesheet" href="https://calzadojj.net/src/css/footer.css">
</head>
<body>

    <!-- Header -->
    <?php include __DIR__ . '/src/header.php'; ?>

    <!-- Main content -->
    <main style="min-height: 53.6vh;">
        <div class="container mt-4">
            <h2>Punto de Venta</h2>

            <!-- Formulario de búsqueda de productos -->
            <form method="POST" class="mb-4">
                <label for="term">Buscar Producto por Nombre o ID:</label>
                <input type="text" name="term" id="term" class="form-control" required>
                <button type="submit" name="buscar_producto" class="btn btn-primary mt-2">Buscar</button>
            </form>

            <?php if (!empty($productos)): ?>
                <h3>Resultados de la búsqueda</h3>
                <ul class="list-group">
                    <?php foreach ($productos as $producto): ?>
                        <li class="list-group-item">
                            <a href="punto_de_venta.php?id_shoe=<?php echo $producto['id_shoe']; ?>">
                                <?php echo $producto['model_name']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php
            // Mostrar variaciones si un producto ha sido seleccionado
            if (isset($_GET['id_shoe'])) {
                $id_shoe = $_GET['id_shoe'];
                $variaciones = obtenerVariaciones($id_shoe);
                $producto = $pdo->prepare("SELECT model_name, price FROM shoes WHERE id_shoe = :id_shoe");
                $producto->execute([':id_shoe' => $id_shoe]);
                $producto = $producto->fetch(PDO::FETCH_ASSOC);
            ?>

            <h3>Producto: <?php echo $producto['model_name']; ?> - $<?php echo $producto['price']; ?></h3>

            <!-- Mostrar variaciones -->
            <form method="POST">
                <input type="hidden" name="id_shoe" value="<?php echo $id_shoe; ?>">

                <?php foreach ($variaciones as $variacion): ?>
                    <div class="mb-3">
                        <h5>Tamaño: <?php echo $variacion['sizeMX']; ?> | Color: <?php echo $variacion['color']; ?></h5>
                        <p>Stock Local: <?php echo $variacion['stock_local']; ?> | Stock Tianguis: <?php echo $variacion['stock_tianguis']; ?></p>

                        <!-- Selección de stock -->
                        <label for="stock_type_<?php echo $variacion['id_varition']; ?>">Elegir Stock:</label>
                        <select name="stock_type" id="stock_type_<?php echo $variacion['id_varition']; ?>" class="form-select" required>
                            <option value="local">Local</option>
                            <option value="tianguis">Tianguis</option>
                        </select>

                        <button type="submit" name="agregar_al_carrito" class="btn btn-success mt-2">Agregar al carrito</button>
                        <input type="hidden" name="id_variation" value="<?php echo $variacion['id_varition']; ?>">
                    </div>
                <?php endforeach; ?>
            </form>

            <?php } ?>

            <!-- Mostrar carrito -->
            <h3>Carrito</h3>
            <ul class="list-group">
                <?php if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])): ?>
                    <?php foreach ($_SESSION['carrito'] as $item): ?>
                        <li class="list-group-item">
                            Producto ID Variación: <?php echo $item['id_variation']; ?> | Stock: <?php echo $item['stock_type']; ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item">No hay productos en el carrito.</li>
                <?php endif; ?>
            </ul>

        </div>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/src/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</body>
</html>
