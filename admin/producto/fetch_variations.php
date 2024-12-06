<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

// Verificar que se haya pasado un ID de producto
$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'No se especificó el producto']);
    exit();
}

// Obtener las variaciones del producto
$sql = "
    SELECT 
        sv.id_varition, 
        sv.stock_local, 
        sv.stock_tianguis, 
        s.sizeMX, 
        c.color, 
        c.color_code 
    FROM shoes_variations sv
    INNER JOIN sizes s ON sv.id_size = s.id_size
    INNER JOIN colors c ON sv.id_color = c.id_color
    WHERE sv.id_shoe = :product_id
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':product_id' => $product_id]);
$variations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver las variaciones en formato JSON
if ($variations) {
    echo json_encode(['success' => true, 'variations' => $variations]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontraron variaciones']);
}
?>
