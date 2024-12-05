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

    <link rel="stylesheet" href="/src/include/bootstrap5_3/bootstrap.min.css">


    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Google Fonts para el logo -->
<link href="https://fonts.googleapis.com/css2?family=Funnel+Display:wght@300..800&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/src/css/style.css">
    <link rel="stylesheet" href="/src/css/footer.css">

</head>
<body>

    <!-- Header -->
    <?php
// Incluir el header
include __DIR__ . '/../src/include/header.php';
?>

    <!-- Main content -->
<main style="min-height: 53.5vh;">
    <main class="container my-5">
        <!-- Primera Sección: ¿Quiénes Somos? -->
        <section class="row mb-5">
            <div class="col-md-6">
                
                    <img src="/src/images/conocenos/nosotros1.png" alt="Calzado" class="img-fluid">
                
            </div>
            <div class="col-md-6 d-flex flex-column justify-content-center">
                <h2 class="mb-4">Nuestra historia</h2>
                <p>En Calzado JJ, nuestra historia comenzó con una visión clara y un compromiso con la calidad y el servicio. Todo empezó hace 5 años, el 19 de julio de 2019, cuando inauguramos nuestro primer puesto en el Tianguis Los Muros. </p>
                <br>
                <h2 class="mb-4">Humildes comienzos</h2>
                <p>Con pocos modelos de calzado, pero con una gran pasión por el diseño y la  calidad , nos lanzamos al mercado con la misión de ofrecer calzado de alta calidad a precios accesibles. Nuestros clientes nos respaldaron desde el principio, y pronto nos dimos cuenta de que estábamos en el camino correcto.</p>
                <br>
                <h2 class="mb-4">Crecimiento exponencial</h2>
                <p>para diciembre de 2020, nuestra popularidad creció tanto que nos vimos obligados a expandirnos a 2 puestos. Este crecimiento nos permitió aumentar nuestra oferta de productos y mejorar nuestra atención al cliente.
                </p>
            
            </div>
        </section>

        <!-- Segunda Sección: Texto e Imagen -->
        <section class="row">
            <div class="col-md-6 order-md-2">
                
                    <img src="/src/images/conocenos/nosotros2.png" alt="Calzado" class="img-fluid">
                
            </div>
            <div class="col-md-6 d-flex flex-column justify-content-center order-md-1">
                <h2 class="mb-4">Nueva etapa</h2>
                <p>En 2023, nos mudamos a un puesto más grande dentro del Tianguis Los Muros, lo que nos permitió mostrar nuestra amplia variedad de calzado de manera aún más impresionante.</p>
                <br>
                <h2 class="mb-4">Expansión y consolidación</h2>
                <p>En 2021, abrimos nuestra segunda ubicación en Calle Querétaro 25, Colonia Los Muros, consolidando nuestra presencia en el mercado y reafirmando nuestro compromiso con la comunidad.</p>
                <br>
                <h2 class="mb-4">Hoy</h2>
                <p>Hoy en día, Calzado JJ es sinónimo de calidad, estilo. Nuestra historia es una prueba de que la dedicación, la innovación y la atención al cliente pueden llevar a un crecimiento sostenible y exitoso. ¡Gracias por ser parte de nuestra historia!</p>
            </div>
        </section>
    </main>

    <!-- Footer -->
</main>
    <?php include __DIR__ . '/../src/include/footer.php'; ?>

    <!-- Font Awesome Integration -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>

<!-- version 0.0.5 -->