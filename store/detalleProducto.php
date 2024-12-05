<?php
session_start(); // Iniciar la sesión para manejar el carrito

include_once __DIR__ . '/../src/database/db.php';

// Obtener el ID del producto
$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_producto <= 0) {
    die("Producto no encontrado.");
}

// Consultar los detalles del producto
$query_producto = "SELECT 
    s.id_shoe, 
    s.model_name AS modelo, 
    s.price AS precio, 
    s.descriptionn AS descripcion, 
    s.img_main AS img_principal, 
    s.img_profile AS img_perfil, 
    s.img_front AS img_frontal, 
    s.img_rear AS img_trasera
FROM shoes s
WHERE s.id_shoe = :id";

$stmt = $pdo->prepare($query_producto);
$stmt->execute(['id' => $id_producto]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    die("Producto no encontrado.");
}

// Consultar las tallas disponibles
$query_tallas = "SELECT sz.sizeMX AS talla
FROM shoes_variations sv
JOIN sizes sz ON sv.id_size = sz.id_size
WHERE sv.id_shoe = :id";
$stmt_tallas = $pdo->prepare($query_tallas);
$stmt_tallas->execute(['id' => $id_producto]);
$tallas = $stmt_tallas->fetchAll(PDO::FETCH_ASSOC);

// Consultar los colores disponibles (incluyendo color_code)
$query_colores = "SELECT c.color AS nombre, c.color_code AS codigo
FROM shoes_variations sv
JOIN colors c ON sv.id_color = c.id_color
WHERE sv.id_shoe = :id";
$stmt_colores = $pdo->prepare($query_colores);
$stmt_colores->execute(['id' => $id_producto]);
$colores = $stmt_colores->fetchAll(PDO::FETCH_ASSOC);

// Consultar productos aleatorios para recomendaciones
$query_recomendados = "SELECT 
    s.id_shoe, 
    s.model_name AS modelo, 
    s.price AS precio, 
    s.img_main AS img_principal 
FROM shoes s 
ORDER BY RAND() LIMIT 4"; // Muestra 4 productos aleatorios
$stmt_recomendados = $pdo->prepare($query_recomendados);
$stmt_recomendados->execute();
$productos_recomendados = $stmt_recomendados->fetchAll(PDO::FETCH_ASSOC);

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

    <!-- Contenido principal -->
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

                <!-- Información oculta -->
                <input type="hidden" name="id_producto" value="<?= $producto['id_shoe'] ?>">
                <input type="hidden" name="modelo" value="<?= htmlspecialchars($producto['modelo']) ?>">
                <input type="hidden" name="precio" value="<?= $producto['precio'] ?>">
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
                                <a href="producto.php?id=<?= $producto_recomendado['id_shoe'] ?>" class="btn btn-primary">Ver producto</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../src/include/footer.php'; ?>

    <!-- Font Awesome Integration -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
