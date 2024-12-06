<?php
// Incluir archivo de conexión a la base de datos
include __DIR__ . '/../../src/database/db.php';

if (isset($_GET['q'])) {
    $query = $_GET['q'];
    // Buscar productos por nombre (usamos LIKE para que busque coincidencias parciales)
    $stmt = $pdo->prepare("SELECT id_shoe, model_name, price FROM shoes WHERE model_name LIKE :query");
    $stmt->bindValue(':query', '%' . $query . '%');
    $stmt->execute();

    // Retorna los resultados como JSON
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);
} else {
    echo json_encode([]);
}
?>
