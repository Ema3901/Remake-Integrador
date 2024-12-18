<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

session_start();

// Si no hay una sesión activa, redirigir a /sesion/sesion.php
if (!isset($_SESSION['user_id'])) {
    header('Location: /sesion/sesion.php');
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
        $sql = $stock_type == 'local' 
            ? "UPDATE shoes_variations SET stock_local = stock_local + 1 WHERE id_varition = :id_variation"
            : "UPDATE shoes_variations SET stock_tianguis = stock_tianguis + 1 WHERE id_varition = :id_variation";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_variation' => $id_variation]);
    } catch (Exception $e) {
        echo "Error al actualizar el stock: " . $e->getMessage();
    }
}

// Función para reducir el stock después de registrar una venta
function reducirStock($id_variation, $stock_type) {
    global $pdo;
    try {
        $sql = $stock_type == 'local' 
            ? "UPDATE shoes_variations SET stock_local = stock_local - 1 WHERE id_varition = :id_variation"
            : "UPDATE shoes_variations SET stock_tianguis = stock_tianguis - 1 WHERE id_varition = :id_variation";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_variation' => $id_variation]);
    } catch (Exception $e) {
        echo "Error al reducir stock: " . $e->getMessage();
    }
}

// Función para registrar la venta en la tabla orders y order_items
function registrarVenta($user_id, $total_price, $carrito) {
    global $pdo;
    try {
        $pdo->beginTransaction();

        $sql = "INSERT INTO orders (user_id, total_price) VALUES (:user_id, :total_price)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':total_price' => $total_price]);
        $order_id = $pdo->lastInsertId();

        foreach ($carrito as $item) {
            $detalles = obtenerDetallesCarrito($item['id_variation']);
            $sql_item = "INSERT INTO order_items (id_order, id_variation, price, quantity, sizeMX, color) 
                         VALUES (:id_order, :id_variation, :price, :quantity, :sizeMX, :color)";
            $stmt_item = $pdo->prepare($sql_item);
            $stmt_item->execute([
                ':id_order' => $order_id,
                ':id_variation' => $item['id_variation'],
                ':price' => $detalles['price'],
                ':quantity' => 1,
                ':sizeMX' => $detalles['sizeMX'],
                ':color' => $detalles['color']
            ]);

            reducirStock($item['id_variation'], $item['stock_type']);
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error al registrar la venta: " . $e->getMessage();
    }
}

// Agregar al carrito
if (isset($_POST['agregar_al_carrito'])) {
    if (isset($_POST['id_variation']) && isset($_POST['stock_type'])) {
        $id_variation = $_POST['id_variation'];
        $stock_type = $_POST['stock_type'];

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

    actualizarStock($id_variation, $stock_type);

    foreach ($_SESSION['carrito'] as $key => $item) {
        if ($item['id_variation'] == $id_variation) {
            unset($_SESSION['carrito'][$key]);
            break;
        }
    }

    $_SESSION['carrito'] = array_values($_SESSION['carrito']);
}

// Pagar (registrar la venta y limpiar el carrito)
if (isset($_POST['pagar'])) {
    $total = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $detalles = obtenerDetallesCarrito($item['id_variation']);
        $total += $detalles['price'];
    }

    registrarVenta($_SESSION['user_id'], $total, $_SESSION['carrito']);
    unset($_SESSION['carrito']);
}

// Obtener lista de productos
$productos = obtenerProductos();

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://calzadojj.net/src/css/style.css">
</head>
<body>


    <?php include __DIR__ . '/../src/header.php'; ?>



    <!-- Main content -->
    <main style="min-height: 53.6vh;">
        <div class="container mt-4">
            <h2>Punto de Venta</h2>

            <!-- Selección de producto -->
            <form method="POST" class="mb-4 row">
                <div class="col-md-6">
                    <label for="id_shoe">Seleccionar Producto:</label>
                    <select name="id_shoe" id="id_shoe" class="form-select" required>
                        <option value="" disabled selected>Selecciona un producto</option>
                        <?php foreach ($productos as $producto): ?>
                            <option value="<?php echo $producto['id_shoe']; ?>"><?php echo $producto['model_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <button type="submit" class="btn btn-primary mt-2">Ver Variaciones</button>
                </div>
            </form>

            <?php if ($productoSeleccionado): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h3>Producto: <?php echo $productoSeleccionado['model_name']; ?> - $<?php echo $productoSeleccionado['price']; ?></h3>
                    </div>
                </div>

                <!-- Mostrar variaciones (max 4 por fila) -->
                <form method="POST">
                    <input type="hidden" name="id_shoe" value="<?php echo $_POST['id_shoe']; ?>">

                    <div class="row">
                        <?php $contador = 0; ?>
                        <?php foreach ($variaciones as $variacion): ?>
                            <div class="col-md-3 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Tamaño: <?php echo $variacion['sizeMX']; ?> | Color: <?php echo $variacion['color']; ?></h5>
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
                                </div>
                            </div>
                            <?php $contador++; ?>
                            <?php if ($contador % 4 == 0): ?>
                                <div class="w-100"></div> <!-- Nueva fila después de 4 elementos -->
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </form>
            <?php endif; ?>

            <!-- Mostrar carrito -->
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

            <!-- Mostrar total y botón de pago -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <h4>Total: $<?php echo $total; ?></h4>
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
