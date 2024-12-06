<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

session_start();

// Si no hay una sesión activa, redirigir a /sesion/sesion.php
if (!isset($_SESSION['user_id'])) {
    header('Location: /sesion/sesion.php');
    exit();
}

// Obtener las órdenes del usuario
$sql = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mostrar el resumen de las compras
foreach ($orders as $order) {
    echo "<h3>Orden ID: " . $order['id_order'] . " - Total: $" . $order['total_price'] . "</h3>";
    echo "<p>Fecha: " . $order['created_at'] . "</p>";

    // Obtener los detalles de los productos en la orden
    $sql_items = "SELECT oi.*, s.model_name FROM order_items oi
                  JOIN shoes_variations sv ON oi.id_variation = sv.id_varition
                  JOIN shoes s ON sv.id_shoe = s.id_shoe
                  WHERE oi.id_order = :id_order";
    $stmt_items = $pdo->prepare($sql_items);
    $stmt_items->execute([':id_order' => $order['id_order']]);
    $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

    echo "<ul>";
    foreach ($items as $item) {
        echo "<li>" . $item['model_name'] . " - " . $item['sizeMX'] . " | " . $item['color'] . " - $" . $item['price'] . "</li>";
    }
    echo "</ul>";
}
?>
