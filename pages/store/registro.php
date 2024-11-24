<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $password_confirmation = trim($_POST['password_confirmation']);

    try {
        // Validar que las contraseñas coincidan
        if ($password !== $password_confirmation) {
            $error_message = 'Las contraseñas no coinciden.';
        } else {
            // Encriptar la contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Llamar al procedimiento almacenado para registrar el usuario
            $sql = "CALL SP_REGISTER_NEW_USER(?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $username,
                $name,
                $surname,
                $email,
                $hashed_password
            ]);

            // Redirigir al login después del registro exitoso
            header('Location: /pages/store/sesion.php');
            exit();
        }
    } catch (PDOException $e) {
        // Manejar errores
        if (str_contains($e->getMessage(), 'El nombre de usuario ya está en uso')) {
            $error_message = 'El nombre de usuario ya está en uso.';
        } else {
            $error_message = 'Error al procesar el registro: ' . $e->getMessage();
        }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="/src/css/style.css">
    <link rel="stylesheet" href="/src/css/login.css">

</head>
<body>

    <!-- Header -->
    <?php include __DIR__ . '/../../src/include/header.php'; ?>

    <!-- Contenido principal -->
    <main class="container my-5 d-flex justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-md-6 col-lg-5">
            <div class="card bg-dark text-white p-5 shadow-lg rounded" style="border-radius: 20px;">
                <h2 class="text-center mb-4" style="letter-spacing: 1px; font-weight: bold;">Crear cuenta</h2>

                <!-- Formulario de registro -->
                <form action="registro.php" method="POST">
                    <div class="mb-4">
                        <label for="username" class="form-label">
                            <i class="fas fa-user" aria-hidden="true"></i> Usuario
                        </label>
                        <input type="text" class="form-control form-control-lg" id="username" name="username" placeholder="Ingresa tu usuario" required>
                    </div>
                    <div class="mb-4">
                        <label for="name" class="form-label">
                            <i class="fas fa-user-tag" aria-hidden="true"></i> Nombre
                        </label>
                        <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="Ingresa tu nombre" required>
                    </div>
                    <div class="mb-4">
                        <label for="surname" class="form-label">
                            <i class="fas fa-user-tag" aria-hidden="true"></i> Apellido
                        </label>
                        <input type="text" class="form-control form-control-lg" id="surname" name="surname" placeholder="Ingresa tu apellido" required>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope" aria-hidden="true"></i> Correo
                        </label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Ingresa tu correo" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock" aria-hidden="true"></i> Contraseña
                        </label>
                        <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                    </div>
                    <div class="mb-4">
                        <label for="password-confirm" class="form-label">
                            <i class="fas fa-lock" aria-hidden="true"></i> Repetir contraseña
                        </label>
                        <input type="password" class="form-control form-control-lg" id="password-confirm" name="password_confirmation" placeholder="Repite tu contraseña" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-info btn-block btn-lg" style="border-radius: 30px;">Crear cuenta</button>
                    </div>
                </form>

                <!-- Enlace para iniciar sesión -->
                <div class="text-center mt-4">
                    <p>¿Ya tienes cuenta? <a href="/pages/store/sesion.php" class="text-info">Inicia sesión</a></p>
                </div>

                <?php if (isset($error_message) && !empty($error_message)): ?>
                    <div class="alert alert-danger mt-3"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../../src/include/footer.php'; ?>
</body>
</html>
