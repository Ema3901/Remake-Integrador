<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

session_start();

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el nombre de usuario y la contraseña
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        // Llamar al nuevo procedimiento almacenado para obtener el usuario por nombre de usuario
        $sql = "CALL SP_GET_USER_BY_USERNAME_NEW(?)"; // Nuevo procedimiento almacenado
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);

        // Obtener los resultados
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Depuración: Verifica los datos que devuelve la base de datos
        var_dump($user); // Esto mostrará los datos recuperados desde la base de datos

        // Cerrar el cursor del primer statement antes de ejecutar el segundo
        $stmt->closeCursor();

        if ($user) {
            // Verificar la contraseña utilizando password_verify de PHP
            if (password_verify($password, $user['passwordd'])) {
                // Contraseña correcta, configurar la sesión
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['user_role'] = $user['id_range'];
                $_SESSION['user_namee'] = $user['user_namee']; // Guardar el nombre de usuario en la sesión

                // Depuración: Verifica que el valor de la sesión se haya asignado correctamente
                var_dump($_SESSION); // Verifica los valores de la sesión después del inicio de sesión

                // Redirigir al usuario según su rol
                if ($user['id_range'] == 1 || $user['id_range'] == 2) {
                    // Administrador o Empleado
                    header('Location: /
index.php');
                } else {
                    // Cliente
                    header('Location: /index.php');
                }
                exit();
            } else {
                $error_message = 'Usuario o contraseña incorrectos.';
            }
        } else {
            $error_message = 'Usuario o contraseña incorrectos.';
        }
    } catch (PDOException $e) {
        $error_message = 'Error al procesar la solicitud: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Zapatos | Calzado JJ</title>
    <link rel="icon" type="image/x-icon" href="/src/images/logo/favicon.png">
    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/src/css/style.css">
    <link rel="stylesheet" href="/src/css/auth.css">
</head>
<body class="auth-page">
    <!-- Header -->
    <?php include __DIR__ . '/src/header.php'; ?>

    <!-- Contenido principal -->
    <main class="auth-container">
        <div class="auth-card">
            <h2>Inicia Sesión</h2>
            <form action="sesion.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user"></i> Usuario
                    </label>
                    <input type="text" class="form-control form-control-lg" id="username" name="username" placeholder="Ingresa tu usuario" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Contraseña
                    </label>
                    <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-info btn-block btn-lg">Iniciar Sesión</button>
                </div>
            </form>

            <!-- Enlaces y mensajes -->
            <div class="text-center mt-3">
                <a href="/store/registro.php" class="text-link">¿No tienes cuenta? Regístrate</a>
            </div>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger mt-3"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/src/footer.php'; ?>
</body>
</html>
