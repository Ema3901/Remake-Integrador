<?php
// Activar la visualización de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
$product = null;
$variations = [];

// Obtener el ID del producto desde la URL
$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    echo "Producto no encontrado.";
    exit();
}

// Obtener los datos del producto y sus variaciones
try {
    // Obtener los detalles del producto
    $product_query = "SELECT id_shoe, id_brand, id_genre, model_name, price, descriptionn, img_main, img_profile, img_front, img_rear
                      FROM shoes WHERE id_shoe = :product_id";
    $stmt = $pdo->prepare($product_query);
    $stmt->execute([':product_id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener las variaciones del producto
    $variation_query = "SELECT sv.id_size, sv.id_color, sv.stock_local, sv.stock_tianguis, s.sizeMX, c.color
                        FROM shoes_variations sv
                        JOIN sizes s ON sv.id_size = s.id_size
                        JOIN colors c ON sv.id_color = c.id_color
                        WHERE sv.id_shoe = :product_id";
    $stmt = $pdo->prepare($variation_query);
    $stmt->execute([':product_id' => $product_id]);
    $variations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener las listas de marcas, géneros, tamaños y colores
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

} catch (PDOException $e) {
    echo "Error al obtener datos de la base de datos: " . $e->getMessage();
    exit();
}

// Si el formulario es enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar y obtener los datos del formulario
    $model_name = $_POST['model_name'] ?? '';
    $brand_id = $_POST['brand_id'] ?? '';
    $genre_id = $_POST['genre_id'] ?? '';
    $price = $_POST['price'] ?? '';
    $description = $_POST['description'] ?? '';

    // Subida de imágenes
    try {
        // Solo subimos nuevas imágenes si el campo no está vacío
        $img_main = !empty($_FILES['img_main']['name']) ? uploadImage($_FILES['img_main']) : $product['img_main'];
        $img_profile = !empty($_FILES['img_profile']['name']) ? uploadImage($_FILES['img_profile']) : $product['img_profile'];
        $img_front = !empty($_FILES['img_front']['name']) ? uploadImage($_FILES['img_front']) : $product['img_front'];
        $img_rear = !empty($_FILES['img_rear']['name']) ? uploadImage($_FILES['img_rear']) : $product['img_rear'];
    } catch (Exception $e) {
        echo "Error en la subida de imágenes: " . $e->getMessage();
        exit();
    }

    // Actualizar el producto en la base de datos
    try {
        $sql = "UPDATE shoes SET id_brand = :brand_id, id_genre = :genre_id, model_name = :model_name, price = :price,
                descriptionn = :description, img_main = :img_main, img_profile = :img_profile, img_front = :img_front, img_rear = :img_rear
                WHERE id_shoe = :product_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':product_id' => $product_id,
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

        // Eliminar las variaciones existentes
        $sql_delete_variations = "DELETE FROM shoes_variations WHERE id_shoe = :product_id";
        $stmt_delete_variations = $pdo->prepare($sql_delete_variations);
        $stmt_delete_variations->execute([':product_id' => $product_id]);

        // Insertar las nuevas variaciones
        foreach ($_POST['variations'] as $variation) {
            $id_size = $variation['size'];
            $id_color = $variation['color'];
            $stock_local = $variation['stock_local'];
            $stock_tianguis = $variation['stock_tianguis'];

            // Insertar variaciones en la base de datos
            $sql_variation = "INSERT INTO shoes_variations (id_shoe, id_size, id_color, stock_local, stock_tianguis)
                              VALUES (:id_shoe, :id_size, :id_color, :stock_local, :stock_tianguis)";
            $stmt_variation = $pdo->prepare($sql_variation);
            $stmt_variation->execute([
                ':id_shoe' => $product_id,
                ':id_size' => $id_size,
                ':id_color' => $id_color,
                ':stock_local' => $stock_local,
                ':stock_tianguis' => $stock_tianguis,
            ]);
        }

        // Redirigir a la página de gestión de productos
        header('Location: productos.php');
        exit();
    } catch (PDOException $e) {
        echo 'Error de base de datos: ' . $e->getMessage();
        exit();
    }
}

// Función para subir las imágenes
function uploadImage($image) {
    // Verificar si hay errores en la subida
    if ($image['error'] != 0) {
        throw new Exception("Error en la subida de la imagen: " . $image['error']);
    }

    $target_dir = __DIR__ . '/../uploads';
    
    // Verificar si el directorio de destino existe, si no, crearlo
    if (!is_dir($target_dir) && !mkdir($target_dir, 0777, true)) {
        throw new Exception("Error al crear el directorio de subida.");
    }
    
    // Obtener la extensión del archivo
    $file_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
    
    // Generar un nombre único para el archivo utilizando el timestamp
    $new_file_name = uniqid('img_', true) . '.' . $file_extension;
    $target_file = $target_dir . '/' . $new_file_name;

    // Verificar si el archivo ya existe
    if (file_exists($target_file)) {
        throw new Exception("El archivo ya existe: $target_file");
    }

    // Intentar mover el archivo al directorio de uploads
    if (!move_uploaded_file($image['tmp_name'], $target_file)) {
        throw new Exception("Error al mover la imagen: $target_file");
    }

    return '/uploads/' . $new_file_name; // Retornar la ruta relativa con el nuevo nombre
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto | Calzado JJ</title>
    <link rel="icon" type="image/x-icon" href="https://calzadojj.net/src/images/logo/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://calzadojj.net/src/css/style.css">
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../src/header.php'; ?>

    <div class="container mt-4">
        <h2>Editar Producto</h2>

        <?php if ($product): ?>
            <form action="editar.php?id=<?= $product['id_shoe'] ?>" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Modelo -->
                        <div class="mb-3">
                            <label for="model_name" class="form-label">Modelo</label>
                            <input type="text" class="form-control" id="model_name" name="model_name" value="<?= $product['model_name'] ?>" required>
                        </div>

                        <!-- Marca -->
                        <div class="mb-3">
                            <label for="brand_id" class="form-label">Marca</label>
                            <select class="form-select" id="brand_id" name="brand_id">
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= $brand['id_brand'] ?>" <?= $product['id_brand'] == $brand['id_brand'] ? 'selected' : '' ?>><?= $brand['brands'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Género -->
                        <div class="mb-3">
                            <label for="genre_id" class="form-label">Género</label>
                            <select class="form-select" id="genre_id" name="genre_id">
                                <?php foreach ($genres as $genre): ?>
                                    <option value="<?= $genre['id_genre'] ?>" <?= $product['id_genre'] == $genre['id_genre'] ? 'selected' : '' ?>><?= $genre['genre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Precio -->
                        <div class="mb-3">
                            <label for="price" class="form-label">Precio</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= $product['price'] ?>" required>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= $product['descriptionn'] ?></textarea>
                        </div>

<!-- Imágenes -->
<div class="mb-3">
    <label for="img_main" class="form-label">Imagen Principal</label>
    <div class="d-flex gap-3">
        <!-- Imagen Principal -->
        <div class="d-flex flex-column align-items-center">
            <input type="file" class="form-control" id="img_main" name="img_main" accept="image/*">
            <?php if ($product['img_main']): ?>
                <img src="<?= $product['img_main'] ?>" alt="Imagen Principal" class="img-fluid mt-2" width="200">
            <?php endif; ?>
        </div>

        <!-- Imagen Perfil -->
        <div class="d-flex flex-column align-items-center">
            <label for="img_profile" class="form-label">Imagen Perfil</label>
            <input type="file" class="form-control" id="img_profile" name="img_profile" accept="image/*">
            <?php if ($product['img_profile']): ?>
                <img src="<?= $product['img_profile'] ?>" alt="Imagen Perfil" class="img-fluid mt-2" width="200">
            <?php endif; ?>
        </div>

        <!-- Imagen Frontal -->
        <div class="d-flex flex-column align-items-center">
            <label for="img_front" class="form-label">Imagen Frontal</label>
            <input type="file" class="form-control" id="img_front" name="img_front" accept="image/*">
            <?php if ($product['img_front']): ?>
                <img src="<?= $product['img_front'] ?>" alt="Imagen Frontal" class="img-fluid mt-2" width="200">
            <?php endif; ?>
        </div>

        <!-- Imagen Trasera -->
        <div class="d-flex flex-column align-items-center">
            <label for="img_rear" class="form-label">Imagen Trasera</label>
            <input type="file" class="form-control" id="img_rear" name="img_rear" accept="image/*">
            <?php if ($product['img_rear']): ?>
                <img src="<?= $product['img_rear'] ?>" alt="Imagen Trasera" class="img-fluid mt-2" width="200">
            <?php endif; ?>
        </div>
    </div>
</div>

<button type="submit" class="btn btn-primary">Actualizar Producto</button>
</div>

<!-- Variaciones -->
<div class="col-md-6">
    <h4>Variaciones</h4>
    <div id="variationsContainer">
        <?php foreach ($variations as $index => $variation): ?>
            <div class="mb-3 variation">
                <label for="size_<?= $index ?>" class="form-label">Tamaño</label>
                <select class="form-select" id="size_<?= $index ?>" name="variations[<?= $index ?>][size]">
                    <?php foreach ($sizes as $size): ?>
                        <option value="<?= $size['id_size'] ?>" <?= $size['id_size'] == $variation['id_size'] ? 'selected' : '' ?>>
                            <?= $size['sizeMX'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="color_<?= $index ?>" class="form-label">Color</label>
                <select class="form-select" id="color_<?= $index ?>" name="variations[<?= $index ?>][color]">
                    <?php foreach ($colors as $color): ?>
                        <option value="<?= $color['id_color'] ?>" <?= $color['id_color'] == $variation['id_color'] ? 'selected' : '' ?>>
                            <?= $color['color'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="stock_local_<?= $index ?>" class="form-label">Stock Local</label>
                <input type="number" class="form-control" id="stock_local_<?= $index ?>" name="variations[<?= $index ?>][stock_local]" value="<?= $variation['stock_local'] ?>" required>
                <label for="stock_tianguis_<?= $index ?>" class="form-label">Stock Tianguis</label>
                <input type="number" class="form-control" id="stock_tianguis_<?= $index ?>" name="variations[<?= $index ?>][stock_tianguis]" value="<?= $variation['stock_tianguis'] ?>" required>
            </div>
        <?php endforeach; ?>
    </div>
</div>


    <!-- Footer -->
    <?php include __DIR__ . '/../src/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let variationIndex = <?= count($variations) ?>;

    document.getElementById('add-variation').addEventListener('click', function() {
        const variationContainer = document.getElementById('variations-container');
        const variationGroup = document.querySelector('.variation-group').cloneNode(true);  <!-- Aquí se corrigió el error -->

        // Actualizar el nombre de los campos para que cada variación tenga un índice único
        variationGroup.querySelectorAll('select, input').forEach(input => {
            input.name = input.name.replace(/\[\d\]/, `[${variationIndex}]`);
        });

        // Agregar la nueva variación al contenedor
        variationContainer.appendChild(variationGroup);
        variationIndex++;
    });

    function removeVariation(button) {
        button.closest('.variation-group').remove();
    }
    </script>

</body>
</html>
