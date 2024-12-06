<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: /sesion/sesion.php');
    exit();
}

// Obtener el ID del producto de la URL
$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    header('Location: gestion_productos.php');
    exit();
}

// Obtener los datos del producto
$sql = "SELECT * FROM shoes WHERE id_shoe = :product_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':product_id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: gestion_productos.php');
    exit();
}

// Obtener las marcas, géneros y tallas disponibles para el formulario
$sql = "SELECT * FROM brands";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM genres";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM sizes";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar si se envió el formulario para actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar los datos del formulario
    $model_name = $_POST['model_name'];
    $id_brand = $_POST['id_brand'];
    $id_genre = $_POST['id_genre'];
    $price = $_POST['price'];
    $descriptionn = $_POST['descriptionn'];

    // Función para manejar la subida de imágenes
    function uploadImage($image, $imageName) {
        $targetDir = 'uploads/';
        $targetFile = $targetDir . $imageName;
        move_uploaded_file($image['tmp_name'], $targetFile);
        return $targetFile;
    }

    // Si se subieron nuevas imágenes, manejarlas
    $img_main_path = $product['img_main']; // Mantener la imagen actual si no se sube una nueva
    $img_profile_path = $product['img_profile'];
    $img_front_path = $product['img_front'];
    $img_rear_path = $product['img_rear'];

    if (!empty($_FILES['img_main']['name'])) {
        $img_main = time() . '_' . basename($_FILES['img_main']['name']);
        $img_main_path = uploadImage($_FILES['img_main'], $img_main);
    }
    if (!empty($_FILES['img_profile']['name'])) {
        $img_profile = time() . '_' . basename($_FILES['img_profile']['name']);
        $img_profile_path = uploadImage($_FILES['img_profile'], $img_profile);
    }
    if (!empty($_FILES['img_front']['name'])) {
        $img_front = time() . '_' . basename($_FILES['img_front']['name']);
        $img_front_path = uploadImage($_FILES['img_front'], $img_front);
    }
    if (!empty($_FILES['img_rear']['name'])) {
        $img_rear = time() . '_' . basename($_FILES['img_rear']['name']);
        $img_rear_path = uploadImage($_FILES['img_rear'], $img_rear);
    }

    // Actualizar el producto en la base de datos
    $sql = "UPDATE shoes SET model_name = :model_name, id_brand = :id_brand, id_genre = :id_genre, 
            price = :price, descriptionn = :descriptionn, img_main = :img_main, img_profile = :img_profile, 
            img_front = :img_front, img_rear = :img_rear WHERE id_shoe = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':model_name' => $model_name,
        ':id_brand' => $id_brand,
        ':id_genre' => $id_genre,
        ':price' => $price,
        ':descriptionn' => $descriptionn,
        ':img_main' => $img_main_path,
        ':img_profile' => $img_profile_path,
        ':img_front' => $img_front_path,
        ':img_rear' => $img_rear_path,
        ':product_id' => $product_id
    ]);

    // Redirigir al listado de productos
    header('Location: gestion_productos.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto | Calzado JJ</title>

    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Header -->
<?php include __DIR__ . '/../src/header.php'; ?>

<div class="container mt-5">
    <h2>Editar Producto</h2>
    <form action="editar.php?id=<?= $product['id_shoe'] ?>" method="POST" enctype="multipart/form-data">
        <div class="row">
            <!-- Primera columna -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="model_name" class="form-label">Modelo</label>
                    <input type="text" class="form-control" id="model_name" name="model_name" value="<?= htmlspecialchars($product['model_name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="id_brand" class="form-label">Marca</label>
                    <select class="form-control" id="id_brand" name="id_brand" required>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?= $brand['id_brand'] ?>" <?= $product['id_brand'] == $brand['id_brand'] ? 'selected' : '' ?>><?= $brand['brands'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="id_genre" class="form-label">Género</label>
                    <select class="form-control" id="id_genre" name="id_genre" required>
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?= $genre['id_genre'] ?>" <?= $product['id_genre'] == $genre['id_genre'] ? 'selected' : '' ?>><?= $genre['genre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Precio</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= $product['price'] ?>" required>
                </div>

                <div class="mb-3">
                    <label for="descriptionn" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descriptionn" name="descriptionn" rows="3"><?= htmlspecialchars($product['descriptionn']) ?></textarea>
                </div>
            </div>

            <!-- Segunda columna -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="img_main" class="form-label">Imagen Principal</label>
                    <input type="file" class="form-control" id="img_main" name="img_main" accept="image/*">
                    <?php if ($product['img_main']): ?>
                        <img src="<?= $product['img_main'] ?>" alt="Imagen Principal" class="img-thumbnail mt-2" style="max-width: 150px;">
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="img_profile" class="form-label">Imagen Perfil</label>
                    <input type="file" class="form-control" id="img_profile" name="img_profile" accept="image/*">
                    <?php if ($product['img_profile']): ?>
                        <img src="<?= $product['img_profile'] ?>" alt="Imagen Perfil" class="img-thumbnail mt-2" style="max-width: 150px;">
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="img_front" class="form-label">Imagen Frontal</label>
                    <input type="file" class="form-control" id="img_front" name="img_front" accept="image/*">
                    <?php if ($product['img_front']): ?>
                        <img src="<?= $product['img_front'] ?>" alt="Imagen Frontal" class="img-thumbnail mt-2" style="max-width: 150px;">
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="img_rear" class="form-label">Imagen Trasera</label>
                    <input type="file" class="form-control" id="img_rear" name="img_rear" accept="image/*">
                    <?php if ($product['img_rear']): ?>
                        <img src="<?= $product['img_rear'] ?>" alt="Imagen Trasera" class="img-thumbnail mt-2" style="max-width: 150px;">
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </form>
</div>

<!-- Footer -->
<?php include __DIR__ . '/../src/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
