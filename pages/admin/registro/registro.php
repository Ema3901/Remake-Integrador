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

    <style>
        body {
            font-family: "Gruppo", sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .container-fluid {
            flex: 1;
            display: flex;
        }

        .sidebar {
            position: sticky;
            top: 0;
            height: calc(100vh - 100px);
            width: 250px;
            background-color: #f8f9fa;
            overflow-y: auto;
        }

        .content {
            flex: 1;
            padding: 20px;
            margin-left: 260px;
        }

        footer {
            background-color: #333;
            color: #fff;
        }

        footer a {
            text-decoration: none;
            color: #fff;
        }

        .btn-toggle {
            width: 100%;
            background: none;
            border: none;
            text-align: left;
            padding: 0.5rem 1rem;
            color: #333;
        }

        .btn-toggle:hover {
            background-color: #e9ecef;
        }

        .download-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
        }

        .download-link:hover {
            background-color: #e9ecef;
        }

        .download-link i {
            color: #007bff;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <?php
// Incluir el header
include __DIR__ . '/../../src/include/header.php';
?>

    <div class="container-fluid">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <nav class="nav flex-column">
                <a class="nav-link" href="/pages/admin/panel.php"><i class="fas fa-tachometer-alt"></i> <span>Panel</span></a>
                <button class="btn btn-toggle" data-bs-toggle="collapse" data-bs-target="#submenu1" aria-expanded="false">
                    <i class="fas fa-chart-line"></i> Resumen
                </button>
                <div class="collapse" id="submenu1">
                    <a class="nav-link" href="/pages/admin/registro.php"><i class="fas fa-clipboard-list"></i> Registro</a>
                </div>
                <button class="btn btn-toggle" data-bs-toggle="collapse" data-bs-target="#submenu2" aria-expanded="false">
                    <i class="fas fa-cogs"></i> Administración
                </button>
                <div class="collapse" id="submenu2">
                    <a class="nav-link" href="/pages/admin/cuentas.php"><i class="fas fa-users"></i> Usuarios</a>
                    <a class="nav-link" href="/pages/admin/inventario.php"><i class="fas fa-boxes"></i> Inventario</a>
                </div>
            </nav>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Registro Mensual</h2>
            <div id="registro-list">
                <div class="download-link">
                    <span>Ventas_09_2024.pdf</span>
                    <a href="/src/images/ventas_09_2024.pdf" download>
                        <i class="fas fa-download"></i> Descargar
                    </a>
                </div>
                <div class="download-link">
                    <span>Ventas_08_2024.pdf</span>
                    <a href="/src/images/ventas_08_2024.pdf" download>
                        <i class="fas fa-download"></i> Descargar
                    </a>
                </div>
                <div class="download-link">
                    <span>Ventas_07_2024.pdf</span>
                    <a href="/src/images/ventas_07_2024.pdf" download>
                        <i class="fas fa-download"></i> Descargar
                    </a>
                </div>
                <!-- Añadir más registros según sea necesario -->
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
                    <p><a href="/pages/store/catalogo.php" class="text-white">Hombre</a></p>
                    <p><a href="/pages/store/catalogo.php" class="text-white">Mujer</a></p>
                    <p><a href="/pages/store/catalogo.php" class="text-white">Unisex</a></p>
                </div>
                <!-- Ayuda -->
                <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold">Ayuda</h5>
                    <p><a href="/pages/store/conocenos.php" class="text-white">Sobre Nosotros</a></p>
                    <p><a href="/pages/store/contactanos.php" class="text-white">Contacto</a></p>
                    <p><a href="/pages/store/sesion.php" class="text-white">Cuenta</a></p>
                </div>
                <!-- Encuéntranos -->
                <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold">Encuéntranos</h5>
                    <p>Reynosa, Tamaulipas, México, Calle Querétaro #25 Col. Los Muros</p>
                    <div>
                        <a href="https://www.facebook.com/JanYJuan.JJ" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/calzado_j_j_/" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="https://wa.me/528994952565" class="text-white me-3"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <!-- Suscríbete -->
                <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold">Suscríbete</h5>
                    <form>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="correo@ejemplo.com">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!-- version 0.0.5 -->