<?php
include_once __DIR__ . '/../../src/database/db.php';

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

// Consultar productos relacionados utilizando otra característica (por ejemplo, precio similar)
$query_relacionados = "SELECT id_shoe, model_name, img_main 
FROM shoes 
WHERE id_shoe != :id 
ORDER BY ABS(price - :precio) ASC 
LIMIT 3";
$stmt_relacionados = $pdo->prepare($query_relacionados);
$stmt_relacionados->execute([
    'id' => $id_producto,
    'precio' => $producto['precio']
]);
$productos_relacionados = $stmt_relacionados->fetchAll(PDO::FETCH_ASSOC);
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
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../../src/include/header.php'; ?>

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
                <h2><?= htmlspecialchars($producto['modelo']) ?></h2>
                <p class="fw-bold">$<?= number_format($producto['precio'], 2) ?></p>
                <p><?= htmlspecialchars($producto['descripcion']) ?></p>

                <div class="mb-3">
                    <h6>Tallas disponibles</h6>
                    <div class="d-flex flex-wrap">
                        <?php foreach ($tallas as $talla): ?>
                            <button class="btn btn-outline-dark me-2 mb-2"><?= htmlspecialchars($talla['talla']) ?> MX</button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <h6>Colores disponibles</h6>
                    <div class="d-flex">
                        <?php foreach ($colores as $color): ?>
                            <button 
                                class="btn btn-outline-dark rounded-circle me-2" 
                                style="width: 30px; height: 30px; background-color: <?= htmlspecialchars($color['codigo']) ?>; border: 1px solid #000;"
                                title="<?= htmlspecialchars($color['nombre']) ?>"
                            ></button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related products -->
        <hr class="my-5">
        <h5 class="text-center mb-4">Te podría interesar</h5>
        <div class="row">
            <?php foreach ($productos_relacionados as $relacionado): ?>
                <div class="col-md-4">
                    <div class="product-related-container">
                        <a href="detalleProducto.php?id=<?= $relacionado['id_shoe'] ?>">
                            <img src="/<?= htmlspecialchars($relacionado['img_main']) ?>" class="img-fluid" alt="<?= htmlspecialchars($relacionado['model_name']) ?>">
                        </a>
                    </div>
                    <p><?= htmlspecialchars($relacionado['model_name']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../../src/include/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
