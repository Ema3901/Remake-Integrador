<li?php
// Comprobar si la sesión ya ha sido iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>

<header>
    <div class="container">
        <div class="row align-items-center" style="margin-bottom: 10px;">
            <!-- Columna única: Logo y elementos a la derecha -->
            <div class="col-12 d-flex justify-content-between align-items-center">
                <!-- Logo a la izquierda -->
                <a href="/index.php" class="logo-container">
                    <img src="https://calzadojj.net/src/images/logo/logo_landscape.png" alt="Logo de Calzado JJ" class="logo-header">
                </a>
                <!-- Iconos y barra de navegación a la derecha -->
                <div class="d-flex justify-content-end">
                    <!-- Botón que abre el modal para iniciar/cerrar sesión -->
                    <!-- Aquí irían tus iconos, botones, etc. -->

                    <!-- Barra de navegación -->
                    <nav class="navbar navbar-expand-lg navbar-light">
                        <div class="container">
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <a class="nav-link text-dark" href="/index.php">Panel</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-dark" href="/producto/productos.php">Productos</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-dark" href="/usuarios/cuentas.php">Usuarios</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-dark" href="https://calzadojj.net" target="_blank">Tienda</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" id="loginModalTrigger" data-bs-toggle="modal" data-bs-target="#loginModal" class="text-muted me-3" aria-label="Cuenta de usuario"><i class="fas fa-user"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
        <hr class="text-muted mt-0">
    </div>
</header>
                
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
                <a href="/
index.php" class="btn btn-success w-100 mb-2">Acceder al Panel</a>
                <a href="/src/include/logout.php" class="btn btn-danger w-100">Cerrar sesión</a>
            <?php else: ?>
                <!-- Si el usuario no ha iniciado sesión -->
                <a href="/
sesion/sesion.php" class="btn btn-primary w-100 mb-2">Iniciar sesión</a>
            <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>