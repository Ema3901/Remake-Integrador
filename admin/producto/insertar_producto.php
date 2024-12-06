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
    
    // Verificar que las imágenes no estén vacías antes de insertar
    if ($img_main || $img_profile || $img_front || $img_rear) {
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
            ':img_main' => $img_main ?: null,  // Si la imagen no existe, insertamos NULL
            ':img_profile' => $img_profile ?: null,
            ':img_front' => $img_front ?: null,
            ':img_rear' => $img_rear ?: null,
        ]);

        // Redirigir a la página de gestión de productos
        header('Location: productos.php');
        exit();
    }
}

// Función para subir las imágenes
function uploadImage($image) {
    if ($image['error'] == 0) {
        $target_dir = __DIR__ . '/../uploads/';
        $target_file = $target_dir . basename($image['name']);
        
        // Verificar si el archivo ya existe y generar un nombre único
        if (file_exists($target_file)) {
            $target_file = $target_dir . uniqid() . '-' . basename($image['name']);
        }

        // Verificar el tipo de archivo
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($image['type'], $allowed_types)) {
            return ''; // Retornar vacío si no es un tipo de imagen permitido
        }

        // Mover la imagen al directorio de uploads
        if (move_uploaded_file($image['tmp_name'], $target_file)) {
            return '/uploads/' . basename($target_file); // Retornamos la ruta relativa
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

            <button type="submit" class="btn btn-success">Guardar Producto</button>
        </form>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../src/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
