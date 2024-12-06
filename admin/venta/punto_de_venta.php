<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

session_start();

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
        $sql = "SELECT sv.id_varition, sz.id_size, sz.sizeMX, c.color, sv.stock_local, sv.stock_tianguis
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

// Función para obtener los detalles del carrito (nombre del producto, precio, color, talla)
function obtenerDetallesCarrito($id_variation) {
    global $pdo;
    try {
        $sql = "SELECT s.model_name, s.price, sz.sizeMX, c.color
                FROM shoes_variations sv
                JOIN shoes s ON sv.id_shoe = s.id_shoe
                JOIN sizes sz ON sv.id_size = sz.id_size
                JOIN colors c ON sv.id_color = c.id_color
                WHERE sv.id_varition = :id_variation";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_variation' => $id_variation]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo "Error al obtener detalles del carrito: " . $e->getMessage();
        return [];
    }
}

// Función para actualizar el stock cuando un producto es eliminado del carrito
function actualizarStock($id_variation, $stock_type) {
    global $pdo;
    try {
        if ($stock_type == 'local') {
            $sql = "UPDATE shoes_variations SET stock_local = stock_local + 1 WHERE id_varition = :id_variation";
        } else {
            $sql = "UPDATE shoes_variations SET stock_tianguis = stock_tianguis + 1 WHERE id_varition = :id_variation";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_variation' => $id_variation]);
    } catch (Exception $e) {
        echo "Error al actualizar el stock: " . $e->getMessage();
    }
}

// Agregar al carrito
if (isset($_POST['agregar_al_carrito'])) {
    if (isset($_POST['id_variation']) && isset($_POST['stock_type'])) {
        $id_variation = $_POST['id_variation'];
        $stock_type = $_POST['stock_type'];

        // Agregar producto al carrito en sesión
        $_SESSION['carrito'][] = [
            'id_variation' => $id_variation,
            'stock_type' => $stock_type
        ];
    }
}

// Eliminar del carrito y actualizar el stock
if (isset($_POST['eliminar_del_carrito'])) {
    $id_variation = $_POST['id_variation'];
    $stock_type = $_POST['stock_type'];

    // Actualizar el stock antes de eliminar del carrito
    actualizarStock($id_variation, $stock_type);

    // Buscar el índice del producto en el carrito
    foreach ($_SESSION['carrito'] as $key => $item) {
        if ($item['id_variation'] == $id_variation) {
            unset($_SESSION['carrito'][$key]);  // Eliminar el producto del carrito
            break;
        }
    }
    // Reindexar el arreglo para evitar índices desordenados
    $_SESSION['carrito'] = array_values($_SESSION['carrito']);
}

// Pagar (limpiar el carrito sin afectar el stock y registrar la compra)
if (isset($_POST['pagar'])) {
    try {
        // Iniciar una transacción para asegurar la integridad de los datos
        $pdo->beginTransaction();

        // Calcular el precio total
        $total_price = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $detalles = obtenerDetallesCarrito($item['id_variation']);
            $total_price += $detalles['price'];
        }

        // Insertar la compra en la tabla 'orders'
        $sql = "INSERT INTO orders (user_id, total_price) VALUES (:user_id, :total_price)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $_SESSION['user_id'], ':total_price' => $total_price]);

        // Obtener el id de la nueva orden
        $id_order = $pdo->lastInsertId();

        // Insertar los productos del carrito en la tabla 'order_items'
        foreach ($_SESSION['carrito'] as $item) {
            $detalles = obtenerDetallesCarrito($item['id_variation']);
            $sql_item = "INSERT INTO order_items (id_order, id_variation, price, quantity, sizeMX, color) 
                        VALUES (:id_order, :id_variation, :price, :quantity, :sizeMX, :color)";
            $stmt_item = $pdo->prepare($sql_item);
            $stmt_item->execute([
                ':id_order' => $id_order,
                ':id_variation' => $item['id_variation'],
                ':price' => $detalles['price'],
                ':quantity' => 1, // Si hay más cantidades, ajustar aquí
                ':sizeMX' => $detalles['sizeMX'],
                ':color' => $detalles['color']
            ]);
        }

        // Vaciar el carrito
        unset($_SESSION['carrito']);

        // Confirmar la transacción
        $pdo->commit();

        // Redirigir a una página de confirmación o éxito
        header('Location: /compra_exitosa.php');
        exit();

    } catch (Exception $e) {
        // Si ocurre un error, hacer un rollback
        $pdo->rollBack();
        echo "Error al procesar la compra: " . $e->getMessage();
    }
}

// Obtener lista de productos
$productos = obtenerProductos();

// Mostrar variaciones si un producto ha sido seleccionado
$variaciones = [];
$productoSeleccionado = null;
if (isset($_POST['id_shoe'])) {
    $id_shoe = $_POST['id_shoe'];
    $variaciones = obtenerVariaciones($id_shoe);

    try {
        $producto = $pdo->prepare("SELECT model_name, price FROM shoes WHERE id_shoe = :id_shoe");
        $producto->execute([':id_shoe' => $id_shoe]);
        $productoSeleccionado = $producto->fetch(PDO::FETCH_ASSOC);
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
                <button type="submit" class="btn btn-primary mt-2">Mostrar Variaciones</button>
            </form>

            <?php if ($productoSeleccionado): ?>
                <h3>Producto Seleccionado: <?php echo $productoSeleccionado['model_name']; ?></h3>
                <p>Precio: $<?php echo $productoSeleccionado['price']; ?></p>
                
                <h4>Variaciones</h4>
                <form method="POST">
                    <div class="row">
                        <?php foreach ($variaciones as $variacion): ?>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <p><strong>Tamaño:</strong> <?php echo $variacion['sizeMX']; ?></p>
                                        <p><strong>Color:</strong> <?php echo $variacion['color']; ?></p>
                                        <p><strong>Stock Local:</strong> <?php echo $variacion['stock_local']; ?></p>
                                        <p><strong>Stock Tianguis:</strong> <?php echo $variacion['stock_tianguis']; ?></p>
                                        <button type="submit" class="btn btn-success" name="agregar_al_carrito" value="agregar" 
                                        onclick="this.form.id_variation.value='<?php echo $variacion['id_varition']; ?>'; this.form.stock_type.value='local';">Agregar al carrito</button>
                                        <input type="hidden" name="id_variation" value="">
                                        <input type="hidden" name="stock_type" value="">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </form>
            <?php endif; ?>
            
            <!-- Carrito -->
            <h3>Carrito</h3>
            <ul class="list-group">
                <?php if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])): ?>
                    <?php $total = 0; ?>
                    <?php foreach ($_SESSION['carrito'] as $key => $item): ?>
                        <?php
                            $detalles = obtenerDetallesCarrito($item['id_variation']);
                            $total += $detalles['price'];
                        ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo $detalles['model_name'] . ' | ' . $detalles['sizeMX'] . ' | ' . $detalles['color']; ?>
                            <span>$<?php echo $detalles['price']; ?></span>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id_variation" value="<?php echo $item['id_variation']; ?>">
                                <input type="hidden" name="stock_type" value="<?php echo $item['stock_type']; ?>">
                                <button type="submit" name="eliminar_del_carrito" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item">Tu carrito está vacío</li>
                <?php endif; ?>
            </ul>

            <!-- Total y Botón de Pagar -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <h4>Total: $<?php echo number_format($total, 2); ?></h4>
                <form method="POST">
                    <button type="submit" name="pagar" class="btn btn-success">Pagar</button>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../src/footer.php'; ?>

</body>
</html>
