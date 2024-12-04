<?php
include __DIR__ . '/../../../src/database/db.php';

// Verifica que los datos se envíen mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Validar campos obligatorios
    if (isset($input['id'], $input['user_name'], $input['name'], $input['last_name'], $input['id_range'], $input['email'])) {
        $userId = $input['id'];
        $userName = $input['user_name'];
        $name = $input['name'];
        $lastName = $input['last_name'];
        $idRange = $input['id_range'];
        $email = $input['email'];
        $password = isset($input['password']) && !empty($input['password']) ? password_hash($input['password'], PASSWORD_DEFAULT) : null;

        try {
            // Procedimiento almacenado para actualizar el usuario
            $sql = "CALL UpdateUser(:id, :user_name, :name, :last_name, :id_range, :password, :email)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':user_name', $userName, PDO::PARAM_STR);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $lastName, PDO::PARAM_STR);
            $stmt->bindParam(':id_range', $idRange, PDO::PARAM_INT);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, $password ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el usuario', 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
