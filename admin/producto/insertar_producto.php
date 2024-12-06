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

    // Obtener el ID del producto insertado
    $id_shoe = $pdo->lastInsertId();

    // Insertar las variaciones
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
            ':id_shoe' => $id_shoe,
            ':id_size' => $id_size,
            ':id_color' => $id_color,
            ':stock_local' => $stock_local,
            ':stock_tianguis' => $stock_tianguis,
        ]);
    }

    // Redirigir a la página de gestión de productos
    header('Location: productos.php');
    exit();
}

// Función para subir las imágenes
function uploadImage($image) {
    // Depuración: Verificar si hay errores en la subida
    if ($image['error'] != 0) {
        echo "Error en la subida de la imagen: " . $image['error'] . "<br>";
        return ''; // Si hay un error en la subida, retornamos vacío
    }

    $target_dir = __DIR__ . '/../uploads';
    
    // Verificar si el directorio de destino existe, si no, crearlo
    if (!is_dir($target_dir)) {
        echo "El directorio de subida no existe. Intentando crear: $target_dir<br>";
        if (!mkdir($target_dir, 0777, true)) {
            echo "Error al crear el directorio de subida.<br>";
            return ''; // Si no se puede crear el directorio, retornamos vacío
        }
    }
    
    // Obtener la extensión del archivo
    $file_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
    
    // Generar un nombre único para el archivo utilizando el timestamp
    $new_file_name = uniqid('img_', true) . '.' . $file_extension;
    $target_file = $target_dir . '/' . $new_file_name;

    // Depuración: Verificar si el archivo ya existe
    if (file_exists($target_file)) {
        echo "El archivo ya existe: $target_file<br>";
        return ''; // Si el archivo ya existe, retornamos vacío
    }

    // Intentar mover el archivo al directorio de uploads
    if (move_uploaded_file($image['tmp_name'], $target_file)) {
        echo "Imagen subida con éxito: $target_file<br>"; // Depuración: Confirmar que la imagen se movió correctamente
        return '/uploads/' . $new_file_name; // Retornamos la ruta relativa con el nuevo nombre
    } else {
        echo "Error al mover la imagen: $target_file<br>";
        return ''; // Si no se pudo mover el archivo, retornamos vacío
    }
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
    <link rel="stylesheet" href="https://calzadojj.net/src/css/style.css">
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
                    <!-- Imagen Principal -->
                    <div class="mb-3">
                        <label for="img_main" class="form-label">Imagen Principal</label>
                        <input type="file" class="form-control" id="img_main" name="img_main" accept="image/*" required>
                    </div>

                    <!-- Imagen Perfil -->
                    <div class="mb-3">
                        <label for="img_profile" class="form-label">Imagen Perfil</label>
                        <input type="file" class="form-control" id="img_profile" name="img_profile" accept="image/*">
                    </div>

                    <!-- Imagen Frontal -->
                    <div class="mb-3">
                        <label for="img_front" class="form-label">Imagen Frontal</label>
                        <input type="file" class="form-control" id="img_front" name="img_front" accept="image/*">
                    </div>

                    <!-- Imagen Trasera -->
                    <div class="mb-3">
                        <label for="img_rear" class="form-label">Imagen Trasera</label>
                        <input type="file" class="form-control" id="img_rear" name="img_rear" accept="image/*">
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <h3>Variaciones</h3>

                <div id="variations-container">
                    <div class="row variation-group mb-3">
                        <div class="col-md-3">
                            <label for="size_1" class="form-label">Talla</label>
                            <select class="form-control" name="variations[0][size]" required>
                                <option value="">Seleccionar Talla</option>
                                <?php foreach ($sizes as $size): ?>
                                    <option value="<?= $size['id_size'] ?>"><?= $size['sizeMX'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="color_1" class="form-label">Color</label>
                            <select class="form-control" name="variations[0][color]" required>
                                <option value="">Seleccionar Color</option>
                                <?php foreach ($colors as $color): ?>
                                    <option value="<?= $color['id_color'] ?>"><?= $color['color'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="stock_local_1" class="form-label">Stock Local</label>
                            <input type="number" class="form-control" name="variations[0][stock_local]" required>
                        </div>

                        <div class="col-md-2">
                            <label for="stock_tianguis_1" class="form-label">Stock Tianguis</label>
                            <input type="number" class="form-control" name="variations[0][stock_tianguis]" required>
                        </div>

                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-variation" onclick="removeVariation(this)">Eliminar</button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-primary" id="add-variation">Agregar Variación</button>
            </div>

            <button type="submit" class="btn btn-success mt-4">Guardar Producto</button>
        </form>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../src/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let variationIndex = 1;

        document.getElementById('add-variation').addEventListener('click', function() {
            const variationContainer = document.getElementById('variations-container');
            const variationGroup = document.querySelector('.variation-group').cloneNode(true);
            
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
