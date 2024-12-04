<?php
include __DIR__ . '/../../../src/database/db.php';

// Verifica si se recibe el ID del usuario
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = $_GET['id'];

    try {
        // Consulta para obtener los datos del usuario
        $sql = "SELECT u.id_user, u.user_namee, u.namee, u.last_name, u.email_address, u.passwordd, r.rangee, r.id_range 
                FROM users u
                JOIN ranges r ON u.id_range = r.id_range
                WHERE u.id_user = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener el usuario', 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID de usuario no válido']);
}
?>
