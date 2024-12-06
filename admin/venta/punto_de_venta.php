<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';
session_start();

// Si no hay una sesión activa, redirigir a la página de inicio de sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: /sesion/sesion.php');
    exit();
}

// Obtener el ID del usuario activo (vendedor)
$user_id = $_SESSION['user_id'];

// Procesar la venta cuando se presiona el botón de proceder al pago
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalize_sale'])) {
    // Guardar la venta en la base de datos
    $total_sale = $_POST['total_sale'];
    $query_sale = "INSERT INTO sales (user_id, total_price, sale_date) VALUES (:user_id, :total_price, NOW())";
    $stmt = $pdo->prepare($query_sale);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':total_price', $total_sale, PDO::PARAM_STR);
    $stmt->execute();

    // Obtener el ID de la venta registrada
    $sale_id = $pdo->lastInsertId();

    // Registrar cada producto de la venta
    foreach ($_POST['products'] as $product) {
        $query_detail = "INSERT INTO sale_details (sale_id, product_id, quantity, price, total_price) VALUES (:sale_id, :product_id, :quantity, :price, :total_price)";
        $stmt = $pdo->prepare($query_detail);
        $stmt->bindParam(':sale_id', $sale_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product['id'], PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $product['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':price', $product['price'], PDO::PARAM_STR);
        $stmt->bindParam(':total_price', $product['total_price'], PDO::PARAM_STR);
        $stmt->execute();
    }

    // Redirigir a la página de confirmación o éxito
    header('Location: /ventas/confirmacion.php');
    exit();
}

// Consultar los productos para la búsqueda
$query_products = "SELECT id, name, price FROM products";
$stmt = $pdo->prepare($query_products);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta</title>
    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://calzadojj.net/src/css/style.css">
</head>
<body>

<!-- Encabezado -->
<?php include __DIR__ . '/src/header.php'; ?>

<main class="container my-5">
    <h1>Punto de Venta</h1>

    <!-- Formulario de venta -->
    <form method="POST" action="punto_de_venta.php">
        <h3>Productos Seleccionados</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Talla</th>
                    <th>Color</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="product-list">
                <!-- Los productos se agregarán dinámicamente aquí -->
            </tbody>
        </table>

        <div class="mb-3">
            <h3>Total de la Venta: <span id="total-sale">0</span></h3>
        </div>

        <button type="submit" name="finalize_sale" class="btn btn-success">Proceder al Pago</button>

        <!-- Datos de los productos -->
        <input type="hidden" name="total_sale" id="total-sale-input">
        <input type="hidden" name="products" id="products-input">
    </form>

    <!-- Modal de búsqueda de productos -->
    <div class="modal fade" id="searchProductModal" tabindex="-1" aria-labelledby="searchProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchProductModalLabel">Buscar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="searchQuery" class="form-control" placeholder="Buscar producto por nombre...">
                    <ul id="productSearchResults" class="list-group mt-3"></ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón para abrir el modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#searchProductModal">
        Buscar Producto
    </button>

</main>

<!-- Footer -->
<?php include __DIR__ . '/src/footer.php'; ?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
<script>
    // Variables globales
    let productsList = [];
    let totalSale = 0;

    // Manejo de búsqueda de productos
    document.getElementById('searchQuery').addEventListener('input', function() {
        const query = this.value;
        if (query.length > 2) {  // Solo buscar si la consulta tiene más de 2 caracteres
            fetch('search_products.php?q=' + query)
                .then(response => response.json())
                .then(data => {
                    const results = document.getElementById('productSearchResults');
                    results.innerHTML = '';  // Limpiar los resultados anteriores
                    data.forEach(product => {
                        const listItem = document.createElement('li');
                        listItem.classList.add('list-group-item');
                        listItem.textContent = product.name;
                        listItem.addEventListener('click', function() {
                            addProductToList(product);
                            $('#searchProductModal').modal('hide');
                        });
                        results.appendChild(listItem);
                    });
                });
        }
    });

    // Agregar producto a la lista
    function addProductToList(product) {
        const quantity = prompt("¿Cuántas unidades deseas agregar?", 1);
        if (quantity && quantity > 0) {
            const price = product.price;
            const totalPrice = price * quantity;

            const productData = {
                id: product.id,
                name: product.name,
                quantity: quantity,
                price: price,
                total_price: totalPrice
            };

            // Agregar al arreglo
            productsList.push(productData);

            // Actualizar la tabla
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${product.name}</td>
                <td><input type="text" class="form-control" value="Tamaño" readonly></td>
                <td><input type="text" class="form-control" value="Color" readonly></td>
                <td>${price}</td>
                <td>${quantity}</td>
                <td>${totalPrice}</td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeProduct(${product.id})">Eliminar</button></td>
            `;
            document.getElementById('product-list').appendChild(row);

            // Actualizar el total de la venta
            totalSale += totalPrice;
            document.getElementById('total-sale').textContent = totalSale;
            document.getElementById('total-sale-input').value = totalSale;

            // Actualizar los productos para enviar en el formulario
            document.getElementById('products-input').value = JSON.stringify(productsList);
        }
    }

    // Eliminar producto de la lista
    function removeProduct(productId) {
        const index = productsList.findIndex(product => product.id === productId);
        if (index > -1) {
            const product = productsList[index];
            totalSale -= product.total_price;
            productsList.splice(index, 1);

            // Actualizar la tabla
            const rows = document.getElementById('product-list').getElementsByTagName('tr');
            rows[index].remove();

            // Actualizar el total
            document.getElementById('total-sale').textContent = totalSale;
            document.getElementById('total-sale-input').value = totalSale;

            // Actualizar los productos para enviar en el formulario
            document.getElementById('products-input').value = JSON.stringify(productsList);
        }
    }
</script>
</body>
</html>
