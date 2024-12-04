<?php
include __DIR__ . '/../../../src/database/db.php';

// Verifica si se recibe el ID del producto
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = intval($_GET['id']);

    try {
        // Llamar al procedimiento almacenado
        $sql = "CALL SP_GET_VARIATIONS(:id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $variations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retornar las variaciones en formato JSON
        echo json_encode(['success' => true, 'variations' => $variations]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener las variaciones', 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID de producto no válido']);
}
?>
