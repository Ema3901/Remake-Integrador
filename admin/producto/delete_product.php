<?php
// Incluir archivo de conexión
include __DIR__ . '/../../src/database/db.php';

session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No estás autenticado']);
    exit();
}

// Obtener el ID del producto desde la solicitud POST
if (isset($_POST['id'])) {
    $productId = $_POST['id'];

    // Preparar y ejecutar la consulta de eliminación
    $sql = "DELETE FROM shoes WHERE id_shoe = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Producto eliminado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID de producto no proporcionado']);
}
?>
