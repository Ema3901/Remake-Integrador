<?php
include __DIR__ . '/../src/database/db.php';

$sql = "SELECT o.id_order, o.order_date, o.total, oi.quantity, oi.price, s.model_name, s.sizeMX, s.color
        FROM orders o
        JOIN order_items oi ON o.id_order = oi.id_order
        JOIN shoes s ON oi.id_shoe = s.id_shoe";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'orders' => $orders]);
?>
