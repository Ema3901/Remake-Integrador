<?php
// Comprobar si la sesión ya ha sido iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>

<header>
    <div class="container">
        <div class="row align-items-center" style="margin-bottom: 10px;">
            <!-- Columna: Logo -->
            <div class="col-md-4 d-flex justify-content-center align-items-center logo-container">
                <a href="/index.php">
                    <img src="/src/images/logo/logo_landscape.png" alt="Logo de Calzado JJ" class="logo-header">
                </a>
            </div>
            <!-- Columna: Iconos -->
            <div class="col-md-4 d-flex justify-content-end icon-links">
                <!-- Botón que abre el modal para iniciar/cerrar sesión -->
            </div>
        </div>
        <hr class="text-muted mt-0">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="/index.php">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="/store/catalogo.php">Catálogo</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="/store/conocenos.php">Sobre Nosotros</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="/store/contactanos.php">Contacto</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>






<a href="javascript:void(0);" id="loginModalTrigger" data-bs-toggle="modal" data-bs-target="#loginModal" class="text-muted me-3" aria-label="Cuenta de usuario">
                    <i class="fas fa-user"></i>
                </a>

                
<!-- Modal de Inicio/Cierre de sesión -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="loginModalLabel">Mi Cuenta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div id="loginOptions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Si el usuario ha iniciado sesión -->
                <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_namee']); ?></p>
                <a href="/admin/panel.php" class="btn btn-success w-100 mb-2">Acceder al Panel</a>
                <a href="/src/include/logout.php" class="btn btn-danger w-100">Cerrar sesión</a>
            <?php else: ?>
                <!-- Si el usuario no ha iniciado sesión -->
                <a href="/admin/sesion/sesion.php" class="btn btn-primary w-100 mb-2">Iniciar sesión</a>
            <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>