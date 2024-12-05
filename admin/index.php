<?php 
/* inicio de comentada
session_start();

// Verifica si la sesión está iniciada, por ejemplo, revisando si existe una variable de sesión como 'user_id'
if (!isset($_SESSION['user_id'])) {
    // Si la sesión no está iniciada, redirige al usuario a la página de cuentas
    header('Location: /usuarios/usuario.php');
    exit; // Asegúrate de detener la ejecución del código después de la redirección
}
    Comento todo antes porque no tengo procedimients*/
?>


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

    <!-- Exclusive Footer CSS -->
    <link rel="stylesheet" href="https://calzadojj.net/src/css/footer.css">
</head>

<body>

    <!-- Header -->
    <?php include __DIR__ . '/src/header.php'; ?>

    <!-- Main content -->
     <main>
        <div> "Hola" </div>
     </main>

    <!-- Footer -->
    <?php include __DIR__ . '/src/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="/src/js/sidebar.js"></script>
</body>
</html>


<!-- version 0.0.5 -->