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
    $size_id = $_POST['size_id'];
    $color_id = $_POST['color_id'];
    
    // 1. Obtener el nombre del vendedor
    $query_vendedor = "CALL sp_get_seller_name(:user_id)";
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

    // 3. Registrar la venta llamando al procedimiento almacenado
    $query_venta = "CALL sp_register_sale(:shoe_id, :quantity, :venta_tipo, :user_id, :size_id, :color_id)";
    $stmt = $pdo->prepare($query_venta);
    $stmt->bindParam(':shoe_id', $shoe_id, PDO::PARAM_INT);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $stmt->bindParam(':venta_tipo', $venta_tipo, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':size_id', $size_id, PDO::PARAM_INT);
    $stmt->bindParam(':color_id', $color_id, PDO::PARAM_INT);
    $stmt->execute();

    // 4. Obtener las variaciones del producto (tamaño y color)
    $query_variaciones = "CALL sp_get_variationsventa(:shoe_id)";
    $stmt = $pdo->prepare($query_variaciones);
    $stmt->bindParam(':shoe_id', $shoe_id, PDO::PARAM_INT);
    $stmt->execute();
    $variaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mensaje de confirmación
    echo "<h2>Venta registrada exitosamente</h2>";
    echo "<p><strong>Vendedor:</strong> " . $vendedor['namee'] . "</p>";
    echo "<p><strong>Producto:</strong> " . $tenis['model_name'] . "</p>";
    echo "<p><strong>Tamaño:</strong> " . $variaciones[0]['sizeMX'] . " - <strong>Color:</strong> " . $variaciones[0]['color'] . "</p>";
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
    <title>Administracion | Calzado JJ</title>

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
    <main class="container my-5">
        <h1 class="mb-4">Punto de Venta</h1>
        
        <!-- Formulario de Venta -->
        <form method="POST" action="punto_de_venta.php">
            
            <!-- Seleccionar el producto -->
            <div class="mb-3">
                <label for="shoe_id" class="form-label">Selecciona el producto:</label>
                <input type="text" id="shoe_name" class="form-control" readonly>
                <input type="hidden" name="shoe_id" id="shoe_id">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#searchProductModal">Buscar Producto</button>
            </div>

            <!-- Seleccionar la talla -->
            <div class="mb-3">
                <label for="size_id" class="form-label">Selecciona la talla:</label>
                <select name="size_id" id="size_id" class="form-select" required>
                    <option value="">Selecciona una talla</option>
                </select>
            </div>

            <!-- Seleccionar el color -->
            <div class="mb-3">
                <label for="color_id" class="form-label">Selecciona el color:</label>
                <select name="color_id" id="color_id" class="form-select" required>
                    <option value="">Selecciona un color</option>
                </select>
            </div>

            <!-- Cantidad -->
            <div class="mb-3">
                <label for="quantity" class="form-label">Cantidad:</label>
                <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
            </div>

            <!-- Tipo de venta -->
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

    <!-- Modal de búsqueda de productos -->
    <div class="modal fade" id="searchProductModal" tabindex="-1" aria-labelledby="searchProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchProductModalLabel">Buscar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="searchQuery" class="form-control" placeholder="Buscar por nombre de producto...">
                    <ul id="productList" class="list-group mt-3"></ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/src/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script>
        // Actualización de los selectores de tamaño y color mediante AJAX
        document.getElementById('shoe_id').addEventListener('change', function() {
            var shoeId = this.value;
            if (shoeId) {
                // Llamar a la API para obtener las variaciones (talla y color)
                fetch('get_variationsventa.php?shoe_id=' + shoeId)
                    .then(response => response.json())
                    .then(data => {
                        // Actualizar el select de tamaño
                        var sizeSelect = document.getElementById('size_id');
                        sizeSelect.innerHTML = '<option value="">Selecciona una talla</option>';
                        data.forEach(size => {
                            sizeSelect.innerHTML += `<option value="${size.id_size}">${size.sizeMX}</option>`;
                        });

                        // Actualizar el select de color
                        var colorSelect = document.getElementById('color_id');
                        colorSelect.innerHTML = '<option value="">Selecciona un color</option>';
                        data.forEach(color => {
                            colorSelect.innerHTML += `<option value="${color.id_color}">${color.color}</option>`;
                        });
                    });
            }
        });

        // Función para el modal de búsqueda de productos
        document.getElementById('searchQuery').addEventListener('input', function() {
            var query = this.value;

            if (query.length > 2) {  // Comienza la búsqueda después de 2 caracteres
                fetch('search_products.php?q=' + query)
                    .then(response => response.json())
                    .then(data => {
                        var productList = document.getElementById('productList');
                        productList.innerHTML = '';  // Limpiar los resultados previos
                        
                        data.forEach(product => {
                            var listItem = document.createElement('li');
                            listItem.classList.add('list-group-item');
                            listItem.textContent = product.model_name;
                            listItem.addEventListener('click', function() {
                                document.getElementById('shoe_id').value = product.id_shoe;
                                document.getElementById('shoe_name').value = product.model_name;
                                $('#searchProductModal').modal('hide');  // Cerrar el modal
                            });
                            productList.appendChild(listItem);
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
