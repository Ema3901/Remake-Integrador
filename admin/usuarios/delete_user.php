<?php
// Incluye el archivo de conexión a la base de datos
include __DIR__ . '/../../src/database/db.php';

// Verifica que se haya recibido el parámetro `id` mediante el método DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Obtiene el cuerpo de la solicitud para procesarlo como JSON
    $input = json_decode(file_get_contents('php://input'), true);

    // Valida que el ID del usuario esté presente
    if (isset($input['id']) && is_numeric($input['id'])) {
        $userId = $input['id'];

        try {
            // Llama al procedimiento almacenado `DeleteUser`
            $sql = "CALL DeleteUser(:id)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            // Devuelve una respuesta de éxito
            echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
        } catch (Exception $e) {
            // Devuelve una respuesta de error en caso de fallo
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el usuario', 'error' => $e->getMessage()]);
        }
    } else {
        // Respuesta de error si el ID no es válido
        echo json_encode(['success' => false, 'message' => 'ID de usuario no válido']);
    }
} else {
    // Respuesta de error si no es una solicitud DELETE
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
