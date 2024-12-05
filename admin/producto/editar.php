<?php
// editar.php - Página para editar productos existentes

include __DIR__ . '/../../../src/database/db.php';

// Ruta base para guardar imágenes
$imageBasePath = __DIR__ . "/../../../src/images/uploads";

// Verificar si se recibió un ID de producto
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de producto inválido.");
}

$id_product = intval($_GET['id']);

// Obtener información del producto
$sql = "CALL SP_GET_PRODUCT_BY_ID(:id)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id_product, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

if (!$product) {
    die("Producto no encontrado.");
}

// Obtener variaciones del producto
$sql_variations = "CALL SP_GET_VARIATIONS(:id)";
$stmt = $pdo->prepare($sql_variations);
$stmt->bindParam(':id', $id_product, PDO::PARAM_INT);
$stmt->execute();
$variations = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_brand = intval($_POST['id_brand']);
    $model_name = $_POST['model_name'];
    $price = floatval($_POST['price']);
    $description = $_POST['description'];
    $id_genre = intval($_POST['id_genre']);

    // Procesar imágenes y moverlas al directorio si se subieron nuevas
    $img_main = !empty($_FILES['img_main']['name']) ? saveImage($_FILES['img_main'], $imageBasePath) : $product['img_main'];
    $img_profile = !empty($_FILES['img_profile']['name']) ? saveImage($_FILES['img_profile'], $imageBasePath) : $product['img_profile'];
    $img_front = !empty($_FILES['img_front']['name']) ? saveImage($_FILES['img_front'], $imageBasePath) : $product['img_front'];
    $img_rear = !empty($_FILES['img_rear']['name']) ? saveImage($_FILES['img_rear'], $imageBasePath) : $product['img_rear'];

    try {
        // Actualizar producto
        $sql = "CALL SP_UPDATE_PRODUCT(?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $id_product, $id_brand, $model_name, $price, $description,
            $img_main, $img_profile, $img_front, $img_rear, $id_genre
        ]);

        // Actualizar variaciones
        if (!empty($_POST['id_sizes'])) {
            $variations = [];
            foreach ($_POST['id_sizes'] as $index => $id_size) {
                $variations[] = [
                    'id_size' => intval($id_size),
                    'id_color' => intval($_POST['id_colors'][$index]),
                    'stock_local' => intval($_POST['stocks_local'][$index]),
                    'stock_tianguis' => intval($_POST['stocks_tianguis'][$index])
                ];
            }

            // Convertir las variaciones a JSON y actualizar
            $variations_json = json_encode($variations);
            $sql_variations = "CALL SP_UPDATE_PRODUCT_VARIATIONS(?, ?)";
            $stmt = $pdo->prepare($sql_variations);
            $stmt->execute([$id_product, $variations_json]);
        }

        // Redirigir con mensaje de éxito
        header("Location: editar.php?id=$id_product&success=1");
        exit;
    } catch (Exception $e) {
        die("Error al actualizar el producto: " . $e->getMessage());
    }
}

/**
 * Guarda la imagen en el directorio especificado.
 *
 * @param array $file Información del archivo (de $_FILES).
 * @param string $destination Ruta destino para guardar.
 * @return string Ruta relativa del archivo guardado.
 */
function saveImage($file, $destination) {
    $fileName = uniqid() . "_" . basename($file['name']);
    $targetFile = $destination . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return "src/images/uploads/productos/" . $fileName;
    } else {
        throw new Exception("Error al subir el archivo: " . $file['name']);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto | Calzado JJ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/src/css/style.css">
</head>
<body>
    <div class="container my-5">
        <h2>Editar Producto</h2>
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success">Producto actualizado correctamente.</div>
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="id_brand" class="form-label">Marca</label>
                        <select class="form-select" name="id_brand" id="id_brand" required>
                            <?php
                            $sql_brands = "SELECT id_brand, brands FROM brands";
                            $result_brands = $pdo->query($sql_brands);
                            while ($row = $result_brands->fetch(PDO::FETCH_ASSOC)) {
                                $selected = $row['id_brand'] == $product['id_brand'] ? 'selected' : '';
                                echo "<option value='{$row['id_brand']}' $selected>{$row['brands']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="model_name" class="form-label">Modelo</label>
                        <input type="text" class="form-control" name="model_name" id="model_name" value="<?= htmlspecialchars($product['model_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Precio</label>
                        <input type="number" step="0.01" class="form-control" name="price" id="price" value="<?= $product['price'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control" name="description" id="description" required><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="id_genre" class="form-label">Género</label>
                        <select class="form-select" name="id_genre" id="id_genre" required>
                            <?php
                            $sql_genres = "SELECT id_genre, genre FROM genres";
                            $result_genres = $pdo->query($sql_genres);
                            while ($row = $result_genres->fetch(PDO::FETCH_ASSOC)) {
                                $selected = $row['id_genre'] == $product['id_genre'] ? 'selected' : '';
                                echo "<option value='{$row['id_genre']}' $selected>{$row['genre']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="img_main" class="form-label">Imagen Principal</label>
                        <input type="file" class="form-control" name="img_main" id="img_main" accept="image/*">
                        <small>Actual: <?= $product['img_main'] ?></small>
                    </div>
                    <div class="mb-3">
                        <label for="img_profile" class="form-label">Imagen Perfil</label>
                        <input type="file" class="form-control" name="img_profile" id="img_profile" accept="image/*">
                        <small>Actual: <?= $product['img_profile'] ?></small>
                    </div>
                    <div class="mb-3">
                        <label for="img_front" class="form-label">Imagen Frontal</label>
                        <input type="file" class="form-control" name="img_front" id="img_front" accept="image/*">
                        <small>Actual: <?= $product['img_front'] ?></small>
                    </div>
                    <div class="mb-3">
                        <label for="img_rear" class="form-label">Imagen Trasera</label>
                        <input type="file" class="form-control" name="img_rear" id="img_rear" accept="image/*">
                        <small>Actual: <?= $product['img_rear'] ?></small>
                    </div>
                </div>
            </div>
            <h3>Variaciones</h3>
            <div id="variations">
                <?php foreach ($variations as $variation): ?>
                <div class="row variation mb-3">
                    <div class="col-md-3">
                        <label for="id_sizes[]" class="form-label">Talla</label>
                        <select class="form-select" name="id_sizes[]" required>
                            <?php
                            $sql_sizes = "SELECT id_size, sizeMX FROM sizes";
                            $result_sizes = $pdo->query($sql_sizes);
                            while ($row = $result_sizes->fetch(PDO::FETCH_ASSOC)) {
                                $selected = $row['sizeMX'] == $variation['size'] ? 'selected' : '';
                                echo "<option value='{$row['id_size']}' $selected>{$row['sizeMX']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="id_colors[]" class="form-label">Color</label>
                        <select class="form-select" name="id_colors[]" required>
                            <?php
                            $sql_colors = "SELECT id_color, color FROM colors";
                            $result_colors = $pdo->query($sql_colors);
                            while ($row = $result_colors->fetch(PDO::FETCH_ASSOC)) {
                                $selected = $row['color'] == $variation['color'] ? 'selected' : '';
                                echo "<option value='{$row['id_color']}' $selected>{$row['color']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="stocks_local[]" class="form-label">Stock Local</label>
                        <input type="number" class="form-control" name="stocks_local[]" value="<?= $variation['stock_local'] ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="stocks_tianguis[]" class="form-label">Stock Tianguis</label>
                        <input type="number" class="form-control" name="stocks_tianguis[]" value="<?= $variation['stock_tianguis'] ?>" required>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-secondary mt-3" id="addVariation">Agregar Variación</button>
            <button type="submit" class="btn btn-primary mt-3">Guardar Cambios</button>
            <a href="/admin/productos/productos.php" class="btn btn-danger mt-3">Cancelar</a>
        </form>
    </div>
    <script>
        document.getElementById('addVariation').addEventListener('click', function () {
            const variations = document.getElementById('variations');
            const newVariation = variations.querySelector('.variation').cloneNode(true);
            newVariation.querySelectorAll('input, select').forEach(input => input.value = '');
            variations.appendChild(newVariation);
        });
    </script>
</body>
</html>
