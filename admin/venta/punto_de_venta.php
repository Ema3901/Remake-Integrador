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

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta | Calzado JJ</title>
    <link rel="icon" href="https://calzadojj.net/src/images/logo/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://calzadojj.net/src/css/style.css">
    <link rel="stylesheet" href="https://calzadojj.net/src/css/footer.css">
</head>
<body>

    <!-- Header -->
    <?php include __DIR__ . '/src/header.php'; ?>

    <!-- Main content -->
    <main class="container my-5">
        <h1 class="mb-4">Punto de Venta</h1>

        <!-- Modal para buscar productos -->
        <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#productSearchModal">Buscar Producto</button>

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


        <!-- Formulario de venta -->
        <form id="saleForm" method="POST" action="punto_de_venta.php">
            <div class="mb-3">
                <label for="shoe_id" class="form-label">Producto Seleccionado:</label>
                <input type="text" class="form-control" id="shoe_name" name="shoe_name" readonly required>
                <input type="hidden" id="shoe_id" name="shoe_id">
            </div>

            <div class="mb-3">
                <label for="size_id" class="form-label">Selecciona la talla:</label>
                <select name="size_id" id="size_id" class="form-select" required>
                    <option value="">Selecciona una talla</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="color_id" class="form-label">Selecciona el color:</label>
                <select name="color_id" id="color_id" class="form-select" required>
                    <option value="">Selecciona un color</option>
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
    <script>
        // Función para buscar productos
        function searchProducts() {
            const query = document.getElementById('productSearchInput').value;
            if (query.length > 0) {
                fetch('search_products.php?query=' + query)
                    .then(response => response.json())
                    .then(data => {
                        const productList = document.getElementById('productList');
                        productList.innerHTML = '';
                        data.forEach(product => {
                            const listItem = document.createElement('li');
                            listItem.classList.add('list-group-item');
                            listItem.innerHTML = `${product.model_name} - $${product.price}`;
                            listItem.addEventListener('click', () => selectProduct(product));
                            productList.appendChild(listItem);
                        });
                    });
            }
        }

        // Función para seleccionar un producto
        function selectProduct(product) {
            document.getElementById('shoe_id').value = product.id_shoe;
            document.getElementById('shoe_name').value = product.model_name;

            // Cargar las variaciones del producto (talla y color)
            fetch('get_variations.php?shoe_id=' + product.id_shoe)
                .then(response => response.json())
                .then(data => {
                    // Actualizar los select de tamaño y color
                    const sizeSelect = document.getElementById('size_id');
                    const colorSelect = document.getElementById('color_id');

                    sizeSelect.innerHTML = '<option value="">Selecciona una talla</option>';
                    colorSelect.innerHTML = '<option value="">Selecciona un color</option>';

                    data.sizes.forEach(size => {
                        sizeSelect.innerHTML += `<option value="${size.id_size}">${size.sizeMX}</option>`;
                    });

                    data.colors.forEach(color => {
                        colorSelect.innerHTML += `<option value="${color.id_color}">${color.color}</option>`;
                    });
                });

            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('productSearchModal'));
            modal.hide();
        }
    </script>

</body>
</html>
