<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: /sesion/sesion.php');
    exit();
}

$brands = [];
$genres = [];
$sizes = [];
$colors = [];

// Obtener los datos necesarios para el formulario
$brand_query = "SELECT id_brand, brands FROM brands";
$genre_query = "SELECT id_genre, genre FROM genres";
$size_query = "SELECT id_size, sizeMX FROM sizes";
$color_query = "SELECT id_color, color FROM colors";

$stmt = $pdo->query($brand_query);
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query($genre_query);
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query($size_query);
$sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query($color_query);
$colors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si el formulario es enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar y obtener los datos del formulario
    $model_name = $_POST['model_name'] ?? '';
    $brand_id = $_POST['brand_id'] ?? '';
    $genre_id = $_POST['genre_id'] ?? '';
    $price = $_POST['price'] ?? '';
    $description = $_POST['description'] ?? '';
    
    // Subida de imágenes
    $img_main = uploadImage($_FILES['img_main']);
    $img_profile = uploadImage($_FILES['img_profile']);
    $img_front = uploadImage($_FILES['img_front']);
    $img_rear = uploadImage($_FILES['img_rear']);
    
    // Insertar el producto en la base de datos
    $sql = "INSERT INTO shoes (id_brand, id_genre, model_name, price, descriptionn, img_main, img_profile, img_front, img_rear)
            VALUES (:brand_id, :genre_id, :model_name, :price, :description, :img_main, :img_profile, :img_front, :img_rear)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':brand_id' => $brand_id,
        ':genre_id' => $genre_id,
        ':model_name' => $model_name,
        ':price' => $price,
        ':description' => $description,
        ':img_main' => $img_main,
        ':img_profile' => $img_profile,
        ':img_front' => $img_front,
        ':img_rear' => $img_rear,
    ]);
    
    $id_shoe = $pdo->lastInsertId(); // Obtener el ID del producto recién insertado

    // Insertar las variaciones
    if (isset($_POST['sizes']) && isset($_POST['colors']) && !empty($_POST['sizes']) && !empty($_POST['colors'])) {
        foreach ($_POST['sizes'] as $size_id) {
            foreach ($_POST['colors'] as $color_id) {
                $stock_local = $_POST['stock_local_' . $size_id . '_' . $color_id];
                $stock_tianguis = $_POST['stock_tianguis_' . $size_id . '_' . $color_id];

                // Insertar en la tabla shoes_variations
                $sql_variation = "INSERT INTO shoes_variations (id_shoe, id_size, id_color, stock_local, stock_tianguis) 
                                  VALUES (:id_shoe, :id_size, :id_color, :stock_local, :stock_tianguis)";
                $stmt_variation = $pdo->prepare($sql_variation);
                $stmt_variation->execute([
                    ':id_shoe' => $id_shoe,
                    ':id_size' => $size_id,
                    ':id_color' => $color_id,
                    ':stock_local' => $stock_local,
                    ':stock_tianguis' => $stock_tianguis
                ]);
            }
        }
    }

    // Redirigir a la página de gestión de productos
    header('Location: productos.php');
    exit();
}

// Función para subir las imágenes
function uploadImage($image) {
    if ($image['error'] == 0) {
        $target_dir = __DIR__ . '/../uploads/';
        $target_file = $target_dir . basename($image['name']);
        
        // Verificar si el archivo ya existe
        if (file_exists($target_file)) {
            return ''; // Si el archivo ya existe, retornamos vacío
        }

        // Mover la imagen al directorio de uploads
        if (move_uploaded_file($image['tmp_name'], $target_file)) {
            return '/uploads/' . basename($image['name']); // Retornamos la ruta relativa
        } else {
            return ''; // Si no se pudo mover el archivo, retornamos vacío
        }
    }
    return ''; // Si hubo error en la subida, retornamos vacío
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar Producto | Calzado JJ</title>
    <link rel="icon" type="image/x-icon" href="https://calzadojj.net/src/images/logo/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="www.calzadojj.net/src/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../src/header.php'; ?>

    <div class="container mt-4">
        <h2>Agregar Nuevo Producto</h2>

        <form action="insertar_producto.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <!-- Modelo -->
                    <div class="mb-3">
                        <label for="model_name" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="model_name" name="model_name" required>
                    </div>

                    <!-- Marca -->
                    <div class="mb-3">
                        <label for="brand_id" class="form-label">Marca</label>
                        <select class="form-control" id="brand_id" name="brand_id" required>
                            <option value="">Seleccionar Marca</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= $brand['id_brand'] ?>"><?= $brand['brands'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Género -->
                    <div class="mb-3">
                        <label for="genre_id" class="form-label">Género</label>
                        <select class="form-control" id="genre_id" name="genre_id" required>
                            <option value="">Seleccionar Género</option>
                            <?php foreach ($genres as $genre): ?>
                                <option value="<?= $genre['id_genre'] ?>"><?= $genre['genre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Precio -->
                    <div class="mb-3">
                        <label for="price" class="form-label">Precio</label>
                        <input type="number" class="form-control" id="price" name="price" required>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Imágenes -->
                    <div class="mb-3">
                        <label for="img_main" class="form-label">Imagen Principal</label>
                        <input type="file" class="form-control" id="img_main" name="img_main" accept="image/*" required>
                    </div>

                    <div class="mb-3">
                        <label for="img_profile" class="form-label">Imagen Perfil</label>
                        <input type="file" class="form-control" id="img_profile" name="img_profile" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label for="img_front" class="form-label">Imagen Frontal</label>
                        <input type="file" class="form-control" id="img_front" name="img_front" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label for="img_rear" class="form-label">Imagen Trasera</label>
                        <input type="file" class="form-control" id="img_rear" name="img_rear" accept="image/*">
                    </div>

                    <!-- Variaciones -->
                    <div class="mb-3">
                        <label for="sizes" class="form-label">Tamaño</label>
                        <select class="form-control" id="sizes" name="sizes[]" multiple required>
                            <?php foreach ($sizes as $size): ?>
                                <option value="<?= $size['id_size'] ?>"><?= $size['sizeMX'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="colors" class="form-label">Color</label>
                        <select class="form-control" id="colors" name="colors[]" multiple required>
                            <?php foreach ($colors as $color): ?>
                                <option value="<?= $color['id_color'] ?>"><?= $color['color'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Stock -->
                    <div id="stock_fields"></div>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Guardar Producto</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Script para generar campos de stock según las combinaciones de talla y color seleccionadas
        document.getElementById('sizes').addEventListener('change', updateStockFields);
        document.getElementById('colors').addEventListener('change', updateStockFields);

        function updateStockFields() {
            const sizes = Array.from(document.getElementById('sizes').selectedOptions).map(option => option.value);
            const colors = Array.from(document.getElementById('colors').selectedOptions).map(option => option.value);
            const stockContainer = document.getElementById('stock_fields');

            stockContainer.innerHTML = ''; // Limpiar los campos de stock previos

            sizes.forEach(size_id => {
                colors.forEach(color_id => {
                    const stockId = `stock_${size_id}_${color_id}`;
                    stockContainer.innerHTML += `
                        <div class="mb-3">
                            <label for="${stockId}" class="form-label">Stock Local - Talla ${size_id} Color ${color_id}</label>
                            <input type="number" class="form-control" name="stock_local_${size_id}_${color_id}" required>
                            <label for="${stockId}" class="form-label">Stock Tianguis - Talla ${size_id} Color ${color_id}</label>
                            <input type="number" class="form-control" name="stock_tianguis_${size_id}_${color_id}" required>
                        </div>
                    `;
                });
            });
        }
    </script>
</body>
</html>
