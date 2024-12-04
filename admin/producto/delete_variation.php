<?php
include __DIR__ . '/../../../src/database/db.php';

// Verificar si se recibe el ID de la variación
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id']) && is_numeric($data['id'])) {
    $variationId = intval($data['id']);

    try {
        // Llamar al procedimiento almacenado
        $sql = "CALL SP_DELETE_VARIATION(:id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $variationId, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Variación eliminada correctamente']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar la variación', 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID de variación no válido']);
}
?>
