<?php
// Incluir el archivo de conexión a la base de datos
include __DIR__ . '/../../src/database/db.php';

session_start();

// Verifica si el usuario está logueado, sino redirige a la página de login
if (!isset($_SESSION['user_id'])) {
    header('Location: /sesion/sesion.php');  // Cambia esto por la URL de tu página de login
    exit();
}

// Obtiene el ID del usuario activo (vendedor)
$user_id = $_SESSION['user_id'];

// Si se ha enviado el formulario de venta
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibe los productos seleccionados (en formato JSON)
    $products = json_decode($_POST['products'], true);

    // 1. Registrar la venta
    $total_sale = 0;
    foreach ($products as $product) {
        $total_sale += $product['total_price'];
    }

    // Inserta la venta en la tabla `sales`
    $stmt = $pdo->prepare("INSERT INTO sales (user_id, total_amount) VALUES (:user_id, :total_amount)");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':total_amount', $total_sale, PDO::PARAM_STR);
    $stmt->execute();

    // Obtener el ID de la venta recién insertada
    $sale_id = $pdo->lastInsertId();

    // 2. Registrar los detalles de la venta en `sale_details`
    foreach ($products as $product) {
        $stmt = $pdo->prepare("INSERT INTO sale_details (sale_id, product_id, size_id, color_id, quantity, price, total_price)
            VALUES (:sale_id, :product_id, :size_id, :color_id, :quantity, :price, :total_price)");

        $stmt->bindParam(':sale_id', $sale_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product['id'], PDO::PARAM_INT);
        $stmt->bindParam(':size_id', $product['size_id'], PDO::PARAM_INT);
        $stmt->bindParam(':color_id', $product['color_id'], PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $product['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':price', $product['price'], PDO::PARAM_STR);
        $stmt->bindParam(':total_price', $product['total_price'], PDO::PARAM_STR);
        $stmt->execute();
    }

    // Mensaje de confirmación
    echo "<h2>Venta registrada exitosamente</h2>";
    echo "<p><strong>Vendedor:</strong> " . $_SESSION['user_name'] . "</p>";
    echo "<p><strong>Total de la venta:</strong> $" . $total_sale . "</p>";
    echo "<br><a href='punto_de_venta.php'>Añadir otro producto</a><br>";

} else {
    // Si no se ha enviado el formulario, muestra el formulario
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta</title>

    <!-- Estilos y Scripts (Bootstrap y jQuery) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <!-- Header -->
    <?php include __DIR__ . '/src/header.php'; ?>

    <!-- Main content -->
    <main class="container my-5">
        <h1 class="mb-4">Punto de Venta</h1>

        <!-- Modal de búsqueda de productos -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#searchModal">Buscar Producto</button>
        
        <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="searchModalLabel">Buscar Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="search-query" class="form-control" placeholder="Buscar producto...">
                        <ul id="search-results" class="list-group mt-3"></ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de venta -->
        <form method="POST" action="punto_de_venta.php">
            <input type="hidden" name="products" id="products-input">

            <!-- Tabla de productos agregados -->
            <table class="table" id="product-list">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Talla</th>
                        <th>Color</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Productos agregados aquí dinámicamente -->
                </tbody>
            </table>

            <hr>
            <h3>Total: $<span id="total-sale">0.00</span></h3>

            <button type="submit" class="btn btn-success">Proceder al Pago</button>
        </form>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/src/footer.php'; ?>

    <script>
        let productsList = [];
        let totalSale = 0;

        // Función para agregar un producto a la lista
        function addProduct(product) {
            productsList.push(product);
            totalSale += product.total_price;
            updateProductList();
            updateTotalSale();
        }

        // Función para actualizar la lista de productos en la tabla
        function updateProductList() {
            const tbody = document.getElementById('product-list').getElementsByTagName('tbody')[0];
            tbody.innerHTML = '';
            productsList.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${product.name}</td>
                    <td>${product.size}</td>
                    <td>${product.color}</td>
                    <td>$${product.price}</td>
                    <td>${product.quantity}</td>
                    <td>$${product.total_price}</td>
                    <td><button class="btn btn-danger" onclick="removeProduct(${product.id})">Eliminar</button></td>
                `;
                tbody.appendChild(row);
            });
        }

        // Función para actualizar el total de la venta
        function updateTotalSale() {
            document.getElementById('total-sale').textContent = totalSale.toFixed(2);
            document.getElementById('products-input').value = JSON.stringify(productsList);
        }

        // Función para eliminar un producto de la lista
        function removeProduct(productId) {
            const index = productsList.findIndex(product => product.id === productId);
            if (index !== -1) {
                totalSale -= productsList[index].total_price;
                productsList.splice(index, 1);
                updateProductList();
                updateTotalSale();
            }
        }

        // Buscar productos por nombre
        document.getElementById('search-query').addEventListener('input', function () {
            const query = this.value;
            if (query) {
                fetch('search_products.php?q=' + query)
                    .then(response => response.json())
                    .then(data => {
                        const resultsContainer = document.getElementById('search-results');
                        resultsContainer.innerHTML = '';
                        data.forEach(product => {
                            const li = document.createElement('li');
                            li.classList.add('list-group-item');
                            li.textContent = product.model_name;
                            li.onclick = function () {
                                addProduct({
                                    id: product.id_shoe,
                                    name: product.model_name,
                                    size: 'Tamaño A',  // Obtener la talla del producto
                                    color: 'Color A',  // Obtener el color del producto
                                    price: product.price,
                                    quantity: 1,
                                    total_price: product.price
                                });
                                $('#searchModal').modal('hide');
                            };
                            resultsContainer.appendChild(li);
                        });
                    });
            }
        });
    </script>

</body>
</html>

<?php
}
?>
