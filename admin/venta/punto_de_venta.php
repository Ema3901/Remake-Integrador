<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

session_start();

// Habilitar la visualización de errores para depuración (solo en desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Si no hay una sesión activa, redirigir a /sesion/sesion.php
if (!isset($_SESSION['user_id'])) {
    header('Location: /sesion/sesion.php');  // Cambia esto por la URL de tu página de sesión
    exit();
}

// Función para obtener todos los productos
function obtenerProductos() {
    global $pdo;
    try {
        $sql = "SELECT id_shoe, model_name FROM shoes";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo "Error al obtener productos: " . $e->getMessage();
        return [];
    }
}

// Función para obtener las variaciones de un producto
function obtenerVariaciones($id_shoe) {
    global $pdo;
    try {
        $sql = "SELECT sv.id_varition, s.id_size, sz.sizeMX, c.color, sv.stock_local, sv.stock_tianguis
                FROM shoes_variations sv
                JOIN sizes sz ON sv.id_size = sz.id_size
                JOIN colors c ON sv.id_color = c.id_color
                WHERE sv.id_shoe = :id_shoe";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_shoe' => $id_shoe]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo "Error al obtener variaciones: " . $e->getMessage();
        return [];
    }
}

// Función para actualizar el stock después de una compra
function actualizarStock($id_variation, $stock_type) {
    global $pdo;
    try {
        if ($stock_type == 'local') {
            $sql = "UPDATE shoes_variations SET stock_local = stock_local - 1 WHERE id_varition = :id_variation AND stock_local > 0";
        } else {
            $sql = "UPDATE shoes_variations SET stock_tianguis = stock_tianguis - 1 WHERE id_varition = :id_variation AND stock_tianguis > 0";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_variation' => $id_variation]);
    } catch (Exception $e) {
        echo "Error al actualizar el stock: " . $e->getMessage();
    }
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

// Obtener lista de productos
$productos = obtenerProductos();
var_dump($productos); // Depuración de la lista de productos

// Mostrar variaciones si un producto ha sido seleccionado
$variaciones = [];
$productoSeleccionado = null;
if (isset($_POST['id_shoe'])) {
    $id_shoe = $_POST['id_shoe'];
    var_dump($id_shoe); // Depuración del ID del producto seleccionado

    $variaciones = obtenerVariaciones($id_shoe);
    var_dump($variaciones); // Depuración de las variaciones del producto

    try {
        $producto = $pdo->prepare("SELECT model_name, price FROM shoes WHERE id_shoe = :id_shoe");
        $producto->execute([':id_shoe' => $id_shoe]);
        $productoSeleccionado = $producto->fetch(PDO::FETCH_ASSOC);
        var_dump($productoSeleccionado); // Depuración del producto seleccionado
    } catch (Exception $e) {
        echo "Error al obtener el producto: " . $e->getMessage();
    }
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
    <?php include __DIR__ . '/../src/header.php'; ?>

    <!-- Main content -->
    <main style="min-height: 53.6vh;">
        <div class="container mt-4">
            <h2>Punto de Venta</h2>

            <!-- Selección de producto -->
            <form method="POST" class="mb-4">
                <label for="id_shoe">Seleccionar Producto:</label>
                <select name="id_shoe" id="id_shoe" class="form-select" required>
                    <option value="" disabled selected>Selecciona un producto</option>
                    <?php foreach ($productos as $producto): ?>
                        <option value="<?php echo $producto['id_shoe']; ?>"><?php echo $producto['model_name']; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary mt-2">Ver Variaciones</button>
            </form>

            <?php if ($productoSeleccionado): ?>
                <h3>Producto: <?php echo $productoSeleccionado['model_name']; ?> - $<?php echo $productoSeleccionado['price']; ?></h3>

                <!-- Mostrar variaciones -->
                <form method="POST">
                    <input type="hidden" name="id_shoe" value="<?php echo $_POST['id_shoe']; ?>">

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
            <?php endif; ?>

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
    <?php include __DIR__ . '/../src/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</body>
</html>
