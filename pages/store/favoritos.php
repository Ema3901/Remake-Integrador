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
include __DIR__ . '/../src/include/header.php';
?>


<!-- Contenido principal-->

<main class="container my-5">
    <!-- Filtros de categoría y buscador -->
    <div class="row mb-4 align-items-center">
        <!-- Filtros de categoría -->
        <div class="col-md-8 text-start">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Todos los productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Hombre</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Mujer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Unisex</a>
                </li>
            </ul>
        </div>

        <!-- Buscador -->
        <div class="col-md-4 text-end">
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Buscar productos" aria-label="Buscar">
                <button class="btn btn-outline-info" type="submit">Buscar</button>
            </form>
        </div>
    </div>

    <!-- Título del catálogo -->
    <h2 class="text-center mb-5">Tus favoritos</h2>

    <!-- Productos -->
    <div class="row text-center">
        <!-- Producto 1 -->
        <div class="col-md-4 mb-4">
            <div class="card border-0">
                <div class="product-placeholder position-relative">
                    <img src="ruta/a/imagen.png" class="img-fluid product-image" alt="Producto">
                    <button class="btn btn-outline-light position-absolute top-0 end-0 m-2 favorite-btn">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
                <div class="card-body">
                    <p class="card-text">Tenis Converse</p>
                </div>
            </div>
        </div>

        <!-- Producto 2 -->
        <div class="col-md-4 mb-4">
            <div class="card border-0">
                <div class="product-placeholder position-relative">
                    <img src="ruta/a/imagen.png" class="img-fluid product-image" alt="Producto">
                    <button class="btn btn-outline-light position-absolute top-0 end-0 m-2 favorite-btn">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
                <div class="card-body">
                    <p class="card-text">Tenis Nike</p>
                </div>
            </div>
        </div>

        <!-- Producto 3 -->
        <div class="col-md-4 mb-4">
            <div class="card border-0">
                <div class="product-placeholder position-relative">
                    <img src="ruta/a/imagen.png" class="img-fluid product-image" alt="Producto">
                    <button class="btn btn-outline-light position-absolute top-0 end-0 m-2 favorite-btn">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
                <div class="card-body">
                    <p class="card-text">Tenis Balenciaga</p>
                </div>
            </div>
        </div>

        <!-- Producto 4 -->
        <div class="col-md-4 mb-4">
            <div class="card border-0">
                <div class="product-placeholder position-relative">
                    <img src="ruta/a/imagen.png" class="img-fluid product-image" alt="Producto">
                    <button class="btn btn-outline-light position-absolute top-0 end-0 m-2 favorite-btn">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
                <div class="card-body">
                    <p class="card-text">Tenis Converse</p>
                </div>
            </div>
        </div>

        <!-- Producto 5 -->
        <div class="col-md-4 mb-4">
            <div class="card border-0">
                <div class="product-placeholder position-relative">
                    <img src="ruta/a/imagen.png" class="img-fluid product-image" alt="Producto">
                    <button class="btn btn-outline-light position-absolute top-0 end-0 m-2 favorite-btn">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
                <div class="card-body">
                    <p class="card-text">Tenis Nike</p>
                </div>
            </div>
        </div>

        <!-- Producto 6 -->
        <div class="col-md-4 mb-4">
            <div class="card border-0">
                <div class="product-placeholder position-relative">
                    <img src="ruta/a/imagen.png" class="img-fluid product-image" alt="Producto">
                    <button class="btn btn-outline-light position-absolute top-0 end-0 m-2 favorite-btn">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
                <div class="card-body">
                    <p class="card-text">Tenis Balenciaga</p>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Estilos adicionales -->
<style>
    .product-placeholder {
        background-color: #f0f0f0;
        height: 250px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .product-image {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }

    .favorite-btn {
        background-color: transparent;
        border: none;
    }

    .favorite-btn i.fas {
        color: red;
    }
</style>

<!-- Script para cambiar el color del corazón -->
<script>
    document.querySelectorAll('.favorite-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            let icon = this.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas', 'text-danger');
            } else {
                icon.classList.remove('fas', 'text-danger');
                icon.classList.add('far');
            }
        });
    });
</script>



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