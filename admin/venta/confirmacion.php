<?php
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Obtener detalles de la orden
    $sql = "SELECT * FROM orders WHERE id_order = :id_order";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_order' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        echo "<h1>Compra realizada con éxito</h1>";
        echo "<p>Orden ID: " . $order['id_order'] . "</p>";
        echo "<p>Total de la compra: $" . $order['total_price'] . "</p>";
    } else {
        echo "<p>Orden no encontrada.</p>";
    }
} else {
    echo "<p>No se ha recibido un ID de orden.</p>";
}
?>
