<?php
include __DIR__ . '/../../src/database/db.php';

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
    <title>Agregar Usuario | Panel Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Header -->
    <?php include __DIR__ . '/../src/header.php'; ?>


    <div class="container my-5">
        <h2>Agregar Nuevo Usuario</h2>

        <!-- Mostrar mensaje de éxito o error -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success">Usuario agregado correctamente.</div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Formulario para agregar usuario -->
        <form method="POST" action="">
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
                <a href="javascript:history.back()" class="btn btn-secondary mt-3 me-3">Regresar</a> <!-- Botón Regresar -->
                <button type="submit" class="btn btn-primary mt-3">Agregar Usuario</button>
            </div>
        </form>
    </div>

        <!-- Header -->
        <?php include __DIR__ . '/../src/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
