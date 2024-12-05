<?php
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
                    <img src="/src/images/logo/logo_landscape.png" alt="Logo de Calzado JJ" class="logo-header">
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
            </div>
        </div>
        <hr class="text-muted mt-0">
    </div>
</header>