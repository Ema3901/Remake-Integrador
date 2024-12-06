<?php
// catalogo.php - Página principal del catálogo de productos

// Incluir el archivo de conexión
include __DIR__ . '/../src/database/db.php';

// Obtener filtro de género de la URL
$id_genero = isset($_GET['genero']) ? intval($_GET['genero']) : 0;

// Obtener el término de búsqueda de la URL
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Título del catálogo
$titulo_catalogo = "Todos los productos";
if ($id_genero > 0) {
    // Consulta para obtener el nombre del género
    $sql_genero = "SELECT genre FROM genres WHERE id_genre = ?";
    $stmt_genero = $pdo->prepare($sql_genero);
    $stmt_genero->bindParam(1, $id_genero, PDO::PARAM_INT);
    $stmt_genero->execute();
    $result_genero = $stmt_genero->fetch(PDO::FETCH_ASSOC);

    if ($result_genero) {
        $titulo_catalogo = "Productos para " . htmlspecialchars($result_genero['genre']);
    }
    $stmt_genero->closeCursor(); // Liberar el recurso
}

// Consulta dinámica de productos con búsqueda
$sql_productos = "
    SELECT 
        s.id_shoe, 
        s.model_name, 
        s.img_main, 
        b.brands AS brand_name
    FROM shoes s
    LEFT JOIN brands b ON s.id_brand = b.id_brand
    WHERE (? = 0 OR s.id_genre = ?) 
    AND s.model_name LIKE ?
";
$stmt_productos = $pdo->prepare($sql_productos);
$stmt_productos->bindParam(1, $id_genero, PDO::PARAM_INT);
$stmt_productos->bindParam(2, $id_genero, PDO::PARAM_INT);
$stmt_productos->bindValue(3, '%' . $search_term . '%', PDO::PARAM_STR); // Agregar búsqueda con LIKE
$stmt_productos->execute();
$result_productos = $stmt_productos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Zapatos | Calzado JJ</title>

    <link rel="icon" type="image/x-icon" href="/src/images/logo/favicon.png">

    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/src/css/style.css">
    <link rel="stylesheet" href="/src/css/footer.css">

</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../src/include/header.php'; ?>

    <!-- Main content -->
<main style="min-height: 53.6vh;">
    <main class="container my-5">
        <!-- Filtros de categoría y buscador -->
        <div class="row mb-4 align-items-center">
            <!-- Filtros de categoría -->
            <div class="col-md-8 text-start">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link <?= $id_genero === 0 ? 'active' : '' ?>" href="catalogo.php">Todos los productos</a>
                    </li>
                    <?php
                    // Generar filtros dinámicos de géneros
                    $sql_categorias = "SELECT id_genre, genre FROM genres";
                    $result_categorias = $pdo->query($sql_categorias);

                    while ($row = $result_categorias->fetch(PDO::FETCH_ASSOC)) {
                        $active_class = $id_genero === intval($row['id_genre']) ? 'active' : '';
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link ' . $active_class . '" href="catalogo.php?genero=' . $row['id_genre'] . '">' . htmlspecialchars($row['genre']) . '</a>';
                        echo '</li>';
                    }
                    ?>
                </ul>
            </div>

            <!-- Buscador -->
            <div class="col-md-4 text-end">
                <form class="d-flex" role="search" method="get" action="catalogo.php">
                    <input class="form-control me-2" type="search" name="search" value="<?= htmlspecialchars($search_term) ?>" placeholder="Buscar productos" aria-label="Buscar">
                    <button class="btn btn-outline-info" type="submit">Buscar</button>
                </form>
            </div>
        </div>

        <!-- Título del catálogo -->
        <h2 class="text-center mb-5"><?= htmlspecialchars($titulo_catalogo) ?></h2>

        <!-- Productos -->
        <div class="row text-center">
            <?php
            if (count($result_productos) > 0) {
                foreach ($result_productos as $row) {
                    $id_shoe = $row['id_shoe'];
                    $model_name = htmlspecialchars($row['model_name']);
                    $brand_name = htmlspecialchars($row['brand_name']);
                    $img_main = $row['img_main'] ? $row['img_main'] : '/src/images/default-placeholder.png';  // Verifica la imagen

                    // Si las imágenes son rutas relativas, asegúrate de que las imágenes estén en el lugar correcto
                    // Para evitar problemas de carga, puedes usar rutas absolutas en producción:
                    $img_main_url = (strpos($img_main, 'http') === false) ? 'https://calzadojj.net' . $img_main : $img_main;

                    echo '<div class="col-md-4 mb-4">';
                    echo '    <div class="card border-0">';
                    echo '        <div class="product-placeholder position-relative">';
                    echo '            <img src="' . $img_main_url . '" class="img-fluid product-image" alt="' . $model_name . '">';
                    echo '        </div>';
                    echo '        <div class="card-body">';
                    echo '            <p class="card-text"><strong>' . $brand_name . '</strong> - ' . $model_name . '</p>';
                    echo '            <p class="fw-bold"><a href="detalleProducto.php?id=' . $id_shoe . '" class="text-decoration-none">Ver Producto</a></p>';
                    echo '        </div>';
                    echo '    </div>';
                    echo '</div>';
                }
            } else {
                echo '<p class="text-center">No se encontraron productos para esta búsqueda.</p>';
            }
            ?>
        </div>
    </main>

    <!-- Footer -->
</main>
    <?php include __DIR__ . '/../src/include/footer.php'; ?>

    <!-- Font Awesome Integration -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Liberar recursos
$stmt_productos->closeCursor();
$pdo = null;
?>
