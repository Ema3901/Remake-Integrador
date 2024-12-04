<!-- Footer -->
<footer class="bg-dark text-white pt-5 pb-4">
    <div class="container text-center text-md-start">
        <div class="row">
            <!-- Categorías -->
            <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold">Categorías</h5>
                <p><a href="/store/catalogo.php?genero=1" class="text-white text-decoration-none">Hombre</a></p>
                <p><a href="/store/catalogo.php?genero=2" class="text-white text-decoration-none">Mujer</a></p>
                <p><a href="/store/catalogo.php?genero=3" class="text-white text-decoration-none">Unisex</a></p>
            </div>

            <!-- Ayuda -->
            <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold">Ayuda</h5>
                <p><a href="/store/conocenos.php" class="text-white text-decoration-none">Sobre Nosotros</a></p>
                <p><a href="/store/contactanos.php" class="text-white text-decoration-none">Contacto</a></p>
                <p><a href="/store/sesion.php" class="text-white text-decoration-none">Cuenta</a></p>
            </div>

            <!-- Encuéntranos -->
            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold">Encuéntranos</h5>
                <p>Reynosa, Tamaulipas, México, Calle Querétaro #25 Col. Los Muros</p>
                <div class="social-icons mt-3">
                    <a href="https://www.facebook.com/JanYJuan.JJ" class="text-white me-3" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/calzado_j_j_/" class="text-white me-3" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://wa.me/528998733689" class="text-white me-3" aria-label="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>

<!-- Suscríbete -->
<div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
    <h5 class="text-uppercase mb-4 font-weight-bold">Suscríbete</h5>
    <form id="formSuscripcion" method="POST">
        <div class="input-group">
            <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required>
        </div>
        <button class="btn btn-info mt-2 w-100 rounded-pill" type="submit">Suscribirse</button>
    </form>
</div>

<!-- Contenedor del mensaje de éxito (pop-up) -->
<div id="mensajeExito" style="display: none; position: fixed; top: 20px; right: 20px; background-color: #4CAF50; color: white; padding: 10px 20px; border-radius: 5px;">
    El correo se mandó con éxito.
</div>


        <!-- Derechos de autor -->
        <div class="text-center pt-4">
            <p>© 2024 Calzado JJ. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<script src="../js/suscripcion.js"></script>

