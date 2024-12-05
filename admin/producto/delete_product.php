<?php
include __DIR__ . '/../../src/database/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id']) && is_numeric($data['id'])) {
    $productId = intval($data['id']);

    try {
        $sql = "CALL SP_DELETE_PRODUCT(:id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Producto eliminado correctamente']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto', 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID de producto no válido']);
}
?>
