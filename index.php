<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Zapatos | Calzado JJ</title>

    <link rel="icon" type="image/x-icon" href="/src/images/logo/favicon.png">

    <!-- Meta para SEO -->
    <meta name="description" content="Tienda de calzado JJ. Venta de zapatos para hombre, mujer y unisex en Reynosa, Tamaulipas. Descubre nuestras colecciones.">
    <meta name="keywords" content="zapatos, calzado, venta de calzado, tienda de zapatos, Reynosa, hombre, mujer, unisex, CalzadoJJ, calzadojj, Calzadojj">
    
    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/src/css/style.css">
    <link rel="stylesheet" href="/src/css/footer.css">
    <link rel="stylesheet" href="/src/css/carrusel.css">
    <link rel="stylesheet" href="/src/css/categorias.css">

</head>
<body>

    <!-- Header -->
    <?php include __DIR__ . '/src/include/header.php'; ?>

    <!-- Contenido principal -->

<!-- Carrusel -->
<div id="carouselExample" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <a href="/store/detalleProducto.php?id=0">
                <img src="/src/images/index/carrusel/car1.png" class="d-block w-100" alt="Ofertas de Calzado JJ">
            </a>
        </div>
        <div class="carousel-item">
            <a href="/store/detalleProducto.php?id=0">
                <img src="/src/images/index/carrusel/car2.png" class="d-block w-100" alt="Ofertas de Calzado JJ">
            </a>
        </div>
        <div class="carousel-item">
            <a href="/store/detalleProducto.php?id=43">
                <img src="/src/images/index/carrusel/car3.png" class="d-block w-100" alt="Ofertas de Calzado JJ">
            </a>
        </div>
        <div class="carousel-item">
            <a href="/store/detalleProducto.php?id=52">
                <img src="/src/images/index/carrusel/car4.png" class="d-block w-100" alt="Ofertas de Calzado JJ">
            </a>
        </div>
        <div class="carousel-item">
            <a href="/store/detalleProducto.php?id=82">
                <img src="/src/images/index/carrusel/car5.png" class="d-block w-100" alt="Nuevas colecciones para hombre">
            </a>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
    </button>
</div>




<!-- Sección de categorías -->
<div class="container my-5 text-center categorias">
    <div class="row">
        <div class="col-md-4">
            <div class="category-box" onclick="window.location.href='/store/catalogo.php?genero=1'">
                <img src="/src/images/index/acceso/hombres.png" alt="Categoría Hombre" class="img-fluid">
                <div class="category-overlay">
                    <h3 class="category-title">Hombres</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="category-box" onclick="window.location.href='/store/catalogo.php?genero=3'">
                <img src="/src/images/index/acceso/unisex.png" alt="Categoría Unisex" class="img-fluid">
                <div class="category-overlay">
                    <h3 class="category-title">Unisex</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="category-box" onclick="window.location.href='/store/catalogo.php?genero=2'">
                <img src="/src/images/index/acceso/mujeres.png" alt="Categoría Mujer" class="img-fluid">
                <div class="category-overlay">
                    <h3 class="category-title">Mujeres</h3>
                </div>
            </div>
        </div>
    </div>
</div>


    <!-- Footer -->
    <?php include __DIR__ . '/src/include/footer.php'; ?>


<!-- Scripts de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
