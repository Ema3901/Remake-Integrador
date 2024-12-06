<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

session_start();

// Si no hay una sesión activa, redirigir a /sesion/sesion.php
if (!isset($_SESSION['user_id'])) {
    header('Location: /sesion/sesion.php');  // Cambia esto por la URL de tu página de sesión
    exit();
}

// Procesar el formulario cuando se envíe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = $_POST['user_name'];
    $name = $_POST['name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $idRange = intval($_POST['id_range']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validar que las contraseñas coincidan
    if ($password !== $confirmPassword) {
        $error = "Las contraseñas no coinciden. Por favor, verifica e inténtalo nuevamente.";
    } else {
        // Encripta la contraseña
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Insertar el nuevo usuario usando un procedimiento almacenado
            $sql = "CALL InsertUser(:user_name, :name, :last_name, :id_range, :password, :email)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':user_name' => $userName,
                ':name' => $name,
                ':last_name' => $lastName,
                ':id_range' => $idRange,
                ':password' => $hashedPassword,
                ':email' => $email,
            ]);

            // Redirigir con mensaje de éxito
            header("Location: agregar_usuario.php?success=1");
            exit;
        } catch (Exception $e) {
            $error = "Error al agregar el usuario: " . $e->getMessage();
        }
    }
}
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
    <?php include __DIR__ . '/../src/header.php'; ?>

    <main style="min-height: 53.6vh;">
    <div class="container my-5">
        <h2>Agregar Nuevo Usuario</h2>

        <!-- Mostrar mensaje de éxito o error -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success">Usuario agregado correctamente.</div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Formulario para agregar usuario -->
        <form method="POST" action="" id="add-user-form">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="user_name" class="form-label">Nombre de Usuario</label>
                        <input type="text" class="form-control" name="user_name" id="user_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Apellido</label>
                        <input type="text" class="form-control" name="last_name" id="last_name" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_range" class="form-label">Rango</label>
                        <select class="form-select" name="id_range" id="id_range" required>
                            <?php
                            $sql_ranges = "SELECT id_range, rangee FROM ranges";
                            $result_ranges = $pdo->query($sql_ranges);
                            while ($row = $result_ranges->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$row['id_range']}'>{$row['rangee']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <a href="cuentas.php" class="btn btn-secondary mt-3 me-3">Cancelar</a>
                <a href="cuentas.php" class="btn btn-secondary mt-3 me-3">Regresar</a>
                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#confirmationModal">Agregar Usuario</button>
            </div>
        </form>
    </div>
    </main>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirmación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de agregar este usuario?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <!-- Aquí cambiamos a un botón con un ID específico para enviar el formulario -->
                    <button type="button" class="btn btn-primary" id="confirm-button">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../src/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script para enviar el formulario al hacer clic en Confirmar -->
    <script>
        document.getElementById('confirm-button').addEventListener('click', function() {
            document.getElementById('add-user-form').submit();  // Enviar el formulario
        });
    </script>
</body>
</html>
