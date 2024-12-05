<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Zapatos | Calzado JJ</title>

    <link rel="icon" type="image/x-icon" href="https://calzadojj.net/src/images/logo/favicon.png">

    <!-- Meta para SEO -->
    <meta name="description" content="Tienda de calzado JJ. Venta de zapatos para hombre, mujer y unisex en Reynosa, Tamaulipas. Descubre nuestras colecciones.">
    <meta name="keywords" content="zapatos, calzado, venta de calzado, tienda de zapatos, Reynosa, hombre, mujer, unisex">
    
    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    <!-- Custom CSS -->
    <link rel="stylesheet" href="https://calzadojj.net/src/css/style.css">

    <!-- Exclusive panel CSS-->
    <link rel="stylesheet" href="https://administracion.calzadojj.net/src/css/admin.css">
</head>

<body>

    <!-- Header -->
    <?php
// Incluir el header
include __DIR__ . '/src/include/header.php';
?>

    <!-- Main content -->
    <div class="container-fluid">
        <!-- Sidebar --><!-- Sidebar -->
        <!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <nav class="nav flex-column">
        <a class="nav-link" href="/admin/panel.php">
            <i class="fas fa-tachometer-alt"></i> <span>Panel</span>
        </a>
        <button class="btn btn-toggle" data-bs-toggle="collapse" data-bs-target="#submenu1" aria-expanded="false">
            <i class="fas fa-chart-line"></i> Resumen
        </button>
        <div class="collapse" id="submenu1">
            <a class="nav-link" href="/admin/registro/registro.php">
                <i class="fas fa-clipboard-list"></i> Registro
            </a>
        </div>
        <button class="btn btn-toggle" data-bs-toggle="collapse" data-bs-target="#submenu2" aria-expanded="false">
            <i class="fas fa-cogs"></i> Administración
        </button>
        <div class="collapse" id="submenu2">
            <a class="nav-link" href="/admin/usuarios/cuentas.php">
                <i class="fas fa-users"></i> Usuarios
            </a>
            <a class="nav-link" href="/admin/productos/productos.php">
                <i class="fas fa-boxes"></i> Inventario
            </a>
        </div>
    </nav>
</div>


        <!-- Content -->
        <div class="content">
            <div class="charts-container">
                <div class="chart-box">
                    <h5>Ganancia del Día</h5>
                    <canvas id="dailySalesChart" aria-label="Gráfico de ventas del día" role="img"></canvas>
                    <div class="total-sales">Total: $5,000 MXN</div>
                </div>

                <div class="chart-box">
                    <h5>Ganancias del Mes</h5>
                    <canvas id="monthlySalesChart" aria-label="Gráfico de ventas del mes" role="img"></canvas>
                    <div class="total-sales">Total: $50,000 MXN</div>
                </div>
            </div>

            <div class="ventas-recientes">
                <h5>Ventas Recientes</h5>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Vendedor</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="ventasBody">
                        <tr>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>example@gmail.com</td>
                            <td>John Doe</td>
                            <td>$299.99</td>
                            <td class="action-icons">
                                <i class="fas fa-pen edit-icon" aria-label="Editar"></i> 
                                <i class="fas fa-trash delete-icon" aria-label="Eliminar"></i>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Jane Doe</td>
                            <td>jane.doe@gmail.com</td>
                            <td>Jane Doe</td>
                            <td>$1,239.99</td>
                            <td class="action-icons">
                                <i class="fas fa-pen edit-icon" aria-label="Editar"></i> 
                                <i class="fas fa-trash delete-icon" aria-label="Eliminar"></i>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Pedro López</td>
                            <td>pedro.lopez@gmail.com</td>
                            <td>Pedro López</td>
                            <td>$678.12</td>
                            <td class="action-icons">
                                <i class="fas fa-pen edit-icon" aria-label="Editar"></i> 
                                <i class="fas fa-trash delete-icon" aria-label="Eliminar"></i>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-4 mt-auto">
        <div class="container text-center text-md-start">
            <div class="row">
                <!-- Categorías -->
                <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold">Categorías</h5>
                    <p><a href="/store/catalogo.php" class="text-white">Hombre</a></p>
                    <p><a href="/store/catalogo.php" class="text-white">Mujer</a></p>
                    <p><a href="/store/catalogo.php" class="text-white">Unisex</a></p>
                </div>
                <!-- Ayuda -->
                <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold">Ayuda</h5>
                    <p><a href="/store/conocenos.php" class="text-white">Sobre Nosotros</a></p>
                    <p><a href="/store/contactanos.php" class="text-white">Contacto</a></p>
                    <p><a href="/store/sesion.php" class="text-white">Cuenta</a></p>
                </div>
                <!-- Encuéntranos -->
                <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold">Encuéntranos</h5>
                    <p>Reynosa, Tamaulipas, México, Calle Querétaro #25 Col. Los Muros</p>
                    <div>
                        <a href="https://www.facebook.com/JanYJuan.JJ" class="text-white me-3" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/calzado_j_j_/" class="text-white me-3" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://wa.me/528994952565" class="text-white me-3" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <!-- Suscríbete -->
                <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold">Suscríbete</h5>
                    <form>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="correo@ejemplo.com" aria-label="Correo electrónico">
                        </div>
                        <button class="btn btn-info mt-2 w-100" type="submit">Suscribirse</button>
                    </form>
                </div>
            </div>
            <div class="text-center pt-4">
                <p>© 2024 Calzado JJ. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="/src/js/sidebar.js"></script>
</body>
</html>


<!-- version 0.0.5 -->