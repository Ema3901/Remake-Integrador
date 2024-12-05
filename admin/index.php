<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administracion | Calzado JJ</title>

    <link rel="icon" type="image/x-icon" href="https://calzadojj.net/src/images/logo/favicon.png">
    
    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="https://calzadojj.net/src/css/style.css">

    <!-- Exclusive panel CSS-->
    <link rel="stylesheet" href="https://calzadojj.net/src/css/admin.css">

    <!-- Exclusive Footer CSS -->
    <link rel="stylesheet" href="https://calzadojj.net/src/css/footer.css">
</head>

<body>

    <!-- Header -->
    <?php include __DIR__ . '/src/header.php'; ?>

    <!-- Main content -->
    <div class="container-fluid">
        <!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <nav class="nav flex-column">
        <a class="nav-link" href="/
index.php">
            <i class="fas fa-tachometer-alt"></i> <span>Panel</span>
        </a>
        <button class="btn btn-toggle" data-bs-toggle="collapse" data-bs-target="#submenu1" aria-expanded="false">
            <i class="fas fa-chart-line"></i> Resumen
        </button>
        <div class="collapse" id="submenu1">
            <a class="nav-link" href="/
registro/registro.php">
                <i class="fas fa-clipboard-list"></i> Registro
            </a>
        </div>
        <button class="btn btn-toggle" data-bs-toggle="collapse" data-bs-target="#submenu2" aria-expanded="false">
            <i class="fas fa-cogs"></i> Administración
        </button>
        <div class="collapse" id="submenu2">
            <a class="nav-link" href="/
usuarios/cuentas.php">
                <i class="fas fa-users"></i> Usuarios
            </a>
            <a class="nav-link" href="/
productos/productos.php">
                <i class="fas fa-boxes"></i> Inventario
            </a>
        </div>
    </nav>
</div>


        <!-- Content -->

    <!-- Footer -->
    <?php include __DIR__ . '/src/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="/src/js/sidebar.js"></script>
</body>
</html>


<!-- version 0.0.5 -->