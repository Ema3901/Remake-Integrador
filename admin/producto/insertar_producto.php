<?php
// insertar_producto.php - Página para insertar nuevos productos

// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

// Ruta base para guardar imágenes
$imageBasePath = __DIR__ . "/../../../src/images/uploads";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_brand = intval($_POST['id_brand']);
    $model_name = $_POST['model_name'];
    $price = floatval($_POST['price']);
    $description = $_POST['description'];
    $id_genre = intval($_POST['id_genre']);

    // Procesar imágenes y moverlas al directorio
    $img_main = !empty($_FILES['img_main']['name']) ? saveImage($_FILES['img_main'], $imageBasePath) : null;
    $img_profile = !empty($_FILES['img_profile']['name']) ? saveImage($_FILES['img_profile'], $imageBasePath) : null;
    $img_front = !empty($_FILES['img_front']['name']) ? saveImage($_FILES['img_front'], $imageBasePath) : null;
    $img_rear = !empty($_FILES['img_rear']['name']) ? saveImage($_FILES['img_rear'], $imageBasePath) : null;

    try {
        // Crear un array para almacenar las variaciones
        $variations = [];

        // Recorrer las tallas, colores y stocks enviados desde el formulario
        foreach ($_POST['id_sizes'] as $index => $id_size) {
            $variations[] = [
                'id_size' => intval($id_size),
                'id_color' => intval($_POST['id_colors'][$index]),
                'stock_local' => intval($_POST['stocks_local'][$index]),
                'stock_tianguis' => intval($_POST['stocks_tianguis'][$index])
            ];
        }

        // Convertir las variaciones a JSON
        $variations_json = json_encode($variations);

        // Preparar el procedimiento almacenado
        $sql = "CALL SP_INSERT_NEW_PRODUCT(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        // Ejecutar el procedimiento almacenado
        $stmt->execute([
            $id_brand, $model_name, $price, $description,
            $img_main, $img_profile, $img_front, $img_rear,
            $id_genre, $variations_json
        ]);

        // Redirigir con mensaje de éxito
        header("Location: insertar_producto.php?success=1");
        exit;
    } catch (Exception $e) {
        die("Error al insertar el producto: " . $e->getMessage());
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
        // Retornar la ruta relativa
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
    <title>Tienda de Zapatos | Calzado JJ</title>

    <link rel="icon" type="image/x-icon" href="/src/images/logo/favicon.png">

    <!-- Meta para SEO -->
    <meta name="description" content="Tienda de calzado JJ. Venta de zapatos para hombre, mujer y unisex en Reynosa, Tamaulipas. Descubre nuestras colecciones.">
    <meta name="keywords" content="zapatos, calzado, venta de calzado, tienda de zapatos, Reynosa, hombre, mujer, unisex">
    
    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/src/css/style.css">
    <link rel="stylesheet" href="/src/css/panel.css">
</head>
<body>
        <!-- Footer -->
        <?php include __DIR__ . '/../../../src/include/header.php'; ?>

    <div class="d-flex">
        

        <!-- Main Content -->
        <main class="container-fluid my-5" id="mainContent">
    <main class="container my-5">
        <h2>Agregar Nuevo Producto</h2>
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success">Producto insertado correctamente.</div>
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
                                echo "<option value='{$row['id_brand']}'>{$row['brands']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="model_name" class="form-label">Modelo</label>
                        <input type="text" class="form-control" name="model_name" id="model_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Precio</label>
                        <input type="number" step="0.01" class="form-control" name="price" id="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control" name="description" id="description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="id_genre" class="form-label">Género</label>
                        <select class="form-select" name="id_genre" id="id_genre" required>
                            <?php
                            $sql_genres = "SELECT id_genre, genre FROM genres";
                            $result_genres = $pdo->query($sql_genres);
                            while ($row = $result_genres->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$row['id_genre']}'>{$row['genre']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="img_main" class="form-label">Imagen Principal</label>
                        <input type="file" class="form-control" name="img_main" id="img_main" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label for="img_profile" class="form-label">Imagen Perfil</label>
                        <input type="file" class="form-control" name="img_profile" id="img_profile" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="img_front" class="form-label">Imagen Frontal</label>
                        <input type="file" class="form-control" name="img_front" id="img_front" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="img_rear" class="form-label">Imagen Trasera</label>
                        <input type="file" class="form-control" name="img_rear" id="img_rear" accept="image/*">
                    </div>
                </div>
            </div>
            <h3>Variaciones</h3>
            <div id="variations">
                <div class="row variation">
                    <div class="col-md-3">
                        <label for="id_size[]" class="form-label">Talla</label>
                        <select class="form-select" name="id_sizes[]" required>
                            <?php
                            $sql_sizes = "SELECT id_size, sizeMX FROM sizes";
                            $result_sizes = $pdo->query($sql_sizes);
                            while ($row = $result_sizes->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$row['id_size']}'>{$row['sizeMX']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="id_color[]" class="form-label">Color</label>
                        <select class="form-select" name="id_colors[]" required>
                            <?php
                            $sql_colors = "SELECT id_color, color FROM colors";
                            $result_colors = $pdo->query($sql_colors);
                            while ($row = $result_colors->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$row['id_color']}'>{$row['color']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="stocks_local[]" class="form-label">Stock Local</label>
                        <input type="number" class="form-control" name="stocks_local[]" required>
                    </div>
                    <div class="col-md-3">
                        <label for="stocks_tianguis[]" class="form-label">Stock Tianguis</label>
                        <input type="number" class="form-control" name="stocks_tianguis[]" required>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-secondary mt-3" id="addVariation">Agregar Variación</button>
            <button type="submit" class="btn btn-primary mt-3">Guardar Producto</button>
            <a href="/admin/productos/productos.php" class="btn btn-danger mt-3">Regresar</a>
            
        </form>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../../../src/include/footer.php'; ?>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous" defer></script>

    <!-- Dependencias de Bootstrap y JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" defer></script>

    <script>
        document.getElementById('addVariation').addEventListener('click', function() {
            const variations = document.getElementById('variations');
            const newVariation = variations.querySelector('.variation').cloneNode(true);
            variations.appendChild(newVariation);
        });
    </script>
</body>
</html>
