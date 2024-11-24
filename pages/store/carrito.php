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
</head>
<body>

    <!-- Header -->
    <?php
// Incluir el header
include __DIR__ . '/../../src/include/header.php';
?>

<!-- Contenido principal-->

<main class="container my-5">
    <!-- Título del carrito -->
    <h2 class="mb-4 text-center">Carrito de compras</h2>

    <!-- Tabla del carrito -->
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <!-- Producto 1 -->
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="product-image-container">
                                <img src="ruta/a/imagen1.png" class="img-fluid" alt="Tenis Converse">
                            </div>
                            <div class="ms-3">
                                <p class="mb-0">Tenis Converse</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center align-items-center">
                            <button class="btn btn-outline-secondary" type="button">-</button>
                            <input type="text" value="1" class="form-control text-center mx-2" style="width: 50px;">
                            <button class="btn btn-outline-secondary" type="button">+</button>
                        </div>
                    </td>
                    <td>
                        <p class="mb-0">599.00</p>
                    </td>
                    <td>
                        <button class="btn btn-danger">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>

                <!-- Producto 2 -->
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="product-image-container">
                                <img src="ruta/a/imagen2.png" class="img-fluid" alt="Tenis Balenciaga">
                            </div>
                            <div class="ms-3">
                                <p class="mb-0">Tenis Balenciaga</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center align-items-center">
                            <button class="btn btn-outline-secondary" type="button">-</button>
                            <input type="text" value="1" class="form-control text-center mx-2" style="width: 50px;">
                            <button class="btn btn-outline-secondary" type="button">+</button>
                        </div>
                    </td>
                    <td>
                        <p class="mb-0">1200.00</p>
                    </td>
                    <td>
                        <button class="btn btn-danger">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Resumen del pedido -->
    <div class="row justify-content-end">
        <div class="col-md-4">
            <table class="table">
                <tr>
                    <td>Subtotal</td>
                    <td class="text-end">1200.00</td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td class="text-end">1200.00</td>
                </tr>
            </table>
            <div class="d-grid">
                <button class="btn btn-info btn-lg">GENERAR RECIBO</button>
            </div>
        </div>
    </div>
</main>

<!-- Estilos adicionales -->
<style>
    .table th, .table td {
        vertical-align: middle;
    }

    /* Contenedor de imagen con tamaño predefinido */
    .product-image-container {
        width: 100px;
        height: 100px;
        background-color: #f0f0f0; /* Fondo gris claro en caso de que no haya imagen */
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden; /* Para asegurarse de que las imágenes se ajusten bien */
    }

    .product-image-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain; /* Asegura que la imagen mantenga sus proporciones dentro del contenedor */
    }
</style>

<!-- Font Awesome Integration -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>


<!-- Font Awesome Integration -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>


<!-- Footer -->
<footer class="bg-dark text-white pt-5 pb-4">
    <div class="container text-center text-md-start">
      <div class="row">
        <!-- Categories -->
        <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
          <h5 class="text-uppercase mb-4 font-weight-bold">Categorías</h5>
          <p><a href="/pages/store/catalogo.php" class="text-white text-decoration-none">Hombre</a></p>
          <p><a href="/pages/store/catalogo.php" class="text-white text-decoration-none">Mujer</a></p>
          <p><a href="/pages/store/catalogo.php" class="text-white text-decoration-none">Unisex</a></p>
        </div>
  
        <!-- Help -->
        <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
          <h5 class="text-uppercase mb-4 font-weight-bold">Ayuda</h5>
          <p><a href="/pages/store/conocenos.php" class="text-white text-decoration-none">Sobre Nosotros</a></p>
          <p><a href="/pages/store/contactanos.php" class="text-white text-decoration-none">Contacto</a></p>
          <p><a href="/pages/store/sesion.php" class="text-white text-decoration-none">Cuenta</a></p>
        </div>
  
        <!-- Contact Info -->
        <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
          <h5 class="text-uppercase mb-4 font-weight-bold">Encuéntranos</h5>
          <p>Reynosa, Tamaulipas, Mexico, Calle Querétaro #25 Col. Los Muros</p>
          <div>
            <a href="https://www.facebook.com/JanYJuan.JJ" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com/calzado_j_j_/" class="text-white me-3"><i class="fab fa-instagram"></i></a>
            <a href="#" class="text-white me-3"><i class="fab fa-whatsapp"></i></a>
          </div>
        </div>
  
        <!-- Subscribe -->
      <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
        <h5 class="text-uppercase mb-4 font-weight-bold">Suscríbete</h5>
        <form>
          <div class="input-group">
            <input type="email" class="form-control" placeholder="correo@ejemplo.com">
          </div>
          <button class="btn btn-info mt-2 w-100 rounded-pill" type="submit">Suscribirse</button>
        </form>
      </div>
    </div>
  
      <!-- Copyright -->
      <div class="text-center pt-4">
        <p>© 2024 Calzado JJ. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>
  
  <!-- Font Awesome Integration -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html> 

<!-- version 0.0.5 -->