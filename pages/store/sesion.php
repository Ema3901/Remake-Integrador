<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

session_start();

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        // Llamar al procedimiento almacenado para buscar al usuario
        $sql = "CALL SP_VALIDATE_USER(?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $password]);

        // Obtener los resultados
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verificar la contraseña
            if (password_verify($password, $user['passwordd'])) {
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['user_role'] = $user['id_range'];

                // Redirigir según el rango del usuario
                if ($user['id_range'] == 1 || $user['id_range'] == 2) {
                    // Administrador o Empleado
                    header('Location: /pages/admin/panel.php');
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
    <link rel="stylesheet" href="/src/css/login.css">
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../../src/include/header.php'; ?>

    <!-- Contenido principal -->
    <div class="login-container">
        <div class="login-card">
            <h2>Inicia Sesión</h2>
            <form action="sesion.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Ingresa tu usuario" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                </div>
            </form>

            <!-- Enlaces y mensajes -->
            <div class="text-center mt-3">
                <a href="/pages/store/registro.php" class="text-link">¿No tienes cuenta? Regístrate</a>
            </div>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger mt-3"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../../src/include/footer.php'; ?>
</body>
</html>
