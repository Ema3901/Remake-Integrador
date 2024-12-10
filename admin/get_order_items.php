<?php
include __DIR__ . '/../src/database/db.php';

header('Content-Type: application/json');

// Validar si se envió el parámetro `id_order`
if (!isset($_GET['id_order'])) {
    echo json_encode(['success' => false, 'message' => 'ID del ticket no proporcionado.']);
    exit();
}

$id_order = intval($_GET['id_order']);

$sql = "SELECT * FROM order_items WHERE id_order = ?";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([$id_order]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'items' => $items]);
} catch (Exception $e) {
    error_log("Error al obtener los detalles del ticket: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al obtener los detalles del ticket.']);
}
?>
