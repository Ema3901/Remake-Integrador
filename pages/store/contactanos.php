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
</head>
<body>

    <!-- Header -->
    <?php
// Incluir el header
include __DIR__ . '/../../src/include/header.php';
?>


<!-- Contenido principal-->

<main class="container my-5">
    <div class="row">
        <!-- Formulario de contacto -->
<div class="col-md-6 mb-4">
    <h4>Envíanos un mensaje</h4>
    <form action="/src/includes/send_email.php" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="fas fa-envelope"></i> Correo electrónico
            </label>
            <input type="email" class="form-control" id="email" name="email" placeholder="correo@ejemplo.com" required>
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">
                <i class="fas fa-comment"></i> Tu mensaje
            </label>
            <textarea class="form-control" id="message" name="message" rows="5" placeholder="Escribe tu mensaje aquí..." required></textarea>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-info btn-block">Enviar</button>
        </div>
    </form>
</div>


        <!-- Información de contacto -->
        <div class="col-md-6 mb-4">
            <h4>Dirección:</h4>
            <p><i class="fas fa-map-marker-alt"></i> Calle Querétaro #25 Col. Los Muros ó Tianguis los muros local 5D y 4D</p>
            
            <h4>Teléfono:</h4>
            <p><i class="fas fa-phone"></i> 899-501-4675</p>
            
            <h4>Horario:</h4>
            <ul class="list-unstyled">
                <li><i class="fas fa-clock"></i> Martes-Miércoles: 8:00 - 15:00</li>
                <li><i class="fas fa-clock"></i> Jueves-Domingo: 16:00 - 23:00</li>
                <li><i class="fas fa-times-circle"></i> Lunes: Cerrado</li>
            </ul>
        </div>
    </div>

    <!-- Mapa -->
    <div class="row my-4">
        <div class="col">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3584.32064107868!2d-98.34508648829!3d26.05577547707297!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x866505ef3b96828b%3A0xa15f3a59654305ec!2scalzado%20JJ!5e0!3m2!1ses-419!2smx!4v1728518085655!5m2!1ses-419!2smx"
                width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</main>


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
  
      <!-- Suscripción -->
<div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
    <h5 class="text-uppercase mb-4 font-weight-bold">Suscríbete</h5>
    <form action="/src/includes/send_subscription_email.php" method="POST">
        <div class="input-group">
            <input type="email" class="form-control" name="email" placeholder="correo@ejemplo.com" required>
        </div>
        <button class="btn btn-info mt-2 w-100 rounded-pill" type="submit">Suscribirse</button>
    </form>
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