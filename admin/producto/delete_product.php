<?php
// Incluir archivo de conexión
include __DIR__ . '/../../src/database/db.php';

session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No estás autenticado']);
    exit();
}

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

// Verificar si se proporciona un ID de producto
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $productId = (int)$_POST['id'];

    try {
        // Verificar si el producto existe antes de eliminarlo
        $checkProduct = "SELECT COUNT(*) FROM shoes WHERE id_shoe = :id";
        $stmtCheck = $pdo->prepare($checkProduct);
        $stmtCheck->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmtCheck->execute();

        if ($stmtCheck->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'message' => 'El producto no existe']);
            exit();
        }

        // Iniciar una transacción para manejar dependencias
        $pdo->beginTransaction();

        // Eliminar variaciones asociadas al producto
        $deleteVariations = "DELETE FROM shoes_variations WHERE id_shoe = :id";
        $stmtVariations = $pdo->prepare($deleteVariations);
        $stmtVariations->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmtVariations->execute();

        // Eliminar el producto principal
        $deleteProduct = "DELETE FROM shoes WHERE id_shoe = :id";
        $stmtProduct = $pdo->prepare($deleteProduct);
        $stmtProduct->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmtProduct->execute();

        $pdo->commit();

        echo json_encode(['success' => true, 'message' => 'Producto eliminado correctamente']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID de producto no proporcionado o inválido']);
}
?>
