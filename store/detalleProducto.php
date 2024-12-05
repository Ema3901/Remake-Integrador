<?php
session_start(); // Iniciar la sesión para manejar el carrito

include_once __DIR__ . '/../src/database/db.php';

// Obtener el ID del producto
$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_producto <= 0) {
    die("Producto no encontrado.");
}

// Consultar los detalles del producto usando un procedimiento almacenado
$stmt = $pdo->prepare("CALL GetProductDetails(:id)");
$stmt->execute(['id' => $id_producto]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor(); // Es necesario cerrar el cursor para usar otra consulta

if (!$producto) {
    die("Producto no encontrado.");
}

// Consultar las tallas disponibles usando un procedimiento almacenado
$stmt_tallas = $pdo->prepare("CALL GetAvailableSizes(:id)");
$stmt_tallas->execute(['id' => $id_producto]);
$tallas = $stmt_tallas->fetchAll(PDO::FETCH_ASSOC);
$stmt_tallas->closeCursor();

// Consultar los colores disponibles usando un procedimiento almacenado
$stmt_colores = $pdo->prepare("CALL GetAvailableColors(:id)");
$stmt_colores->execute(['id' => $id_producto]);
$colores = $stmt_colores->fetchAll(PDO::FETCH_ASSOC);
$stmt_colores->closeCursor();

// Consultar productos aleatorios para recomendaciones usando un procedimiento almacenado
$stmt_recomendados = $pdo->prepare("CALL GetRecommendedProducts()");
$stmt_recomendados->execute();
$productos_recomendados = $stmt_recomendados->fetchAll(PDO::FETCH_ASSOC);
$stmt_recomendados->closeCursor();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Zapatos | Calzado JJ</title>
    <link rel="icon" type="image/x-icon" href="/src/images/logo/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/src/css/style.css">
    <link rel="stylesheet" href="/src/css/footer.css">
    <script>
        function textToSpeech() {
            const description = document.getElementById("product-description").textContent;
            const utterance = new SpeechSynthesisUtterance(description);
            window.speechSynthesis.speak(utterance);
        }
    </script>
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../src/include/header.php'; ?>

    <!-- Main content -->
<main style="min-height: 53.5vh;">
    <main class="container my-5">
        <div class="row">
            <!-- Thumbnails -->
            <div class="col-md-1 d-flex flex-column align-items-center">
                <div class="product-thumbnail-container mb-3">
                    <img src="/<?= htmlspecialchars($producto['img_perfil']) ?>" class="img-fluid" alt="Vista lateral">
                </div>
                <div class="product-thumbnail-container mb-3">
                    <img src="/<?= htmlspecialchars($producto['img_frontal']) ?>" class="img-fluid" alt="Vista frontal">
                </div>
                <div class="product-thumbnail-container mb-3">
                    <img src="/<?= htmlspecialchars($producto['img_trasera']) ?>" class="img-fluid" alt="Vista trasera">
                </div>
            </div>

            <!-- Main image -->
            <div class="col-md-7">
                <div class="product-image-container text-center">
                    <img src="/<?= htmlspecialchars($producto['img_principal']) ?>" class="img-fluid" alt="<?= htmlspecialchars($producto['modelo']) ?>">
                </div>
            </div>

            <!-- Product details -->
            <div class="col-md-4">
                <h3><?= htmlspecialchars($producto['modelo']) ?></h3>
                <p id="product-description"><?= htmlspecialchars($producto['descripcion']) ?></p>
                <h4>$<?= number_format($producto['precio'], 2) ?></h4>

                <!-- Botón Text to Speech -->
                <button class="btn btn-outline-primary mt-3" onclick="textToSpeech()">
                    <i class="fas fa-volume-up"></i> Leer descripción
                </button>

                <div class="mb-3 mt-4">
                    <h6>Tallas disponibles</h6>
                    <ul>
                        <?php foreach ($tallas as $talla): ?>
                            <li><?= htmlspecialchars($talla['talla']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="mb-3">
                    <h6>Colores disponibles</h6>
                    <div class="d-flex">
                        <?php foreach ($colores as $color): ?>
                            <label class="me-2">
                                <input type="radio" name="color" value="<?= htmlspecialchars($color['codigo']) ?>" style="display:none;" required>
                                <span class="btn btn-outline-dark rounded-circle" style="width: 30px; height: 30px; background-color: <?= htmlspecialchars($color['codigo']) ?>; border: 1px solid #000;" title="<?= htmlspecialchars($color['nombre']) ?>"></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos recomendados -->
        <div class="container my-5">
            <h4>Productos recomendados</h4>
            <div class="row">
                <?php foreach ($productos_recomendados as $producto_recomendado): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card">
                            <img src="/<?= htmlspecialchars($producto_recomendado['img_principal']) ?>" class="card-img-top" alt="<?= htmlspecialchars($producto_recomendado['modelo']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($producto_recomendado['modelo']) ?></h5>
                                <p class="card-text">$<?= number_format($producto_recomendado['precio'], 2) ?></p>
                                <a href="detalleProducto.php?id=<?= $producto_recomendado['id_shoe'] ?>" class="btn btn-primary">Ver producto</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
</main>
    <?php include __DIR__ . '/../src/include/footer.php'; ?>

    <!-- Font Awesome Integration -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
