<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: /sesion/sesion.php');
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

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar los datos del formulario
    $model_name = $_POST['model_name'];
    $id_brand = $_POST['id_brand'];
    $id_genre = $_POST['id_genre'];
    $price = $_POST['price'];
    $descriptionn = $_POST['descriptionn'];
    $img_main = $_FILES['img_main']['name'];  // Aquí se pueden subir imágenes
    $img_profile = $_FILES['img_profile']['name'];
    $img_front = $_FILES['img_front']['name'];
    $img_rear = $_FILES['img_rear']['name'];

    // Subir las imágenes al servidor
    move_uploaded_file($_FILES['img_main']['tmp_name'], '/path/to/upload/' . $img_main);
    move_uploaded_file($_FILES['img_profile']['tmp_name'], '/path/to/upload/' . $img_profile);
    move_uploaded_file($_FILES['img_front']['tmp_name'], '/path/to/upload/' . $img_front);
    move_uploaded_file($_FILES['img_rear']['tmp_name'], '/path/to/upload/' . $img_rear);

    // Insertar el producto en la base de datos
    $sql = "INSERT INTO shoes (model_name, id_brand, id_genre, price, descriptionn, img_main, img_profile, img_front, img_rear) 
            VALUES (:model_name, :id_brand, :id_genre, :price, :descriptionn, :img_main, :img_profile, :img_front, :img_rear)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':model_name' => $model_name,
        ':id_brand' => $id_brand,
        ':id_genre' => $id_genre,
        ':price' => $price,
        ':descriptionn' => $descriptionn,
        ':img_main' => $img_main,
        ':img_profile' => $img_profile,
        ':img_front' => $img_front,
        ':img_rear' => $img_rear
    ]);

    // Redirigir al listado de productos o mostrar un mensaje de éxito
    header('Location: gestion_productos.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto | Calzado JJ</title>

    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Header -->
<?php include __DIR__ . '/../src/header.php'; ?>

<div class="container mt-5">
    <h2>Agregar Producto</h2>
    <form action="insertar_producto.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="model_name" class="form-label">Modelo</label>
            <input type="text" class="form-control" id="model_name" name="model_name" required>
        </div>

        <div class="mb-3">
            <label for="id_brand" class="form-label">Marca</label>
            <select class="form-control" id="id_brand" name="id_brand" required>
                <option value="">Seleccionar Marca</option>
                <?php foreach ($brands as $brand): ?>
                    <option value="<?= $brand['id_brand'] ?>"><?= $brand['brands'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="id_genre" class="form-label">Género</label>
            <select class="form-control" id="id_genre" name="id_genre" required>
                <option value="">Seleccionar Género</option>
                <?php foreach ($genres as $genre): ?>
                    <option value="<?= $genre['id_genre'] ?>"><?= $genre['genre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Precio</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
        </div>

        <div class="mb-3">
            <label for="descriptionn" class="form-label">Descripción</label>
            <textarea class="form-control" id="descriptionn" name="descriptionn" rows="3"></textarea>
        </div>

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

        <button type="submit" class="btn btn-primary">Guardar Producto</button>
        <a href="gestion_productos.php" class="btn btn-danger">Cancelar</a>
    </form>
</div>

<!-- Footer -->
<?php include __DIR__ . '/../src/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
