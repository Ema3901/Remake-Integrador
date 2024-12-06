<?php
include __DIR__ . '/../../src/database/db.php';

if (isset($_GET['shoe_id'])) {
    $shoe_id = $_GET['shoe_id'];

    // Consultar las variaciones de color y talla
    $query = "
        SELECT colors.id_color, colors.color, sizes.id_size, sizes.sizeMX
        FROM shoes_variations
        JOIN colors ON shoes_variations.id_color = colors.id_color
        JOIN sizes ON shoes_variations.id_size = sizes.id_size
        WHERE shoes_variations.id_shoe = :shoe_id
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':shoe_id', $shoe_id, PDO::PARAM_INT);
    $stmt->execute();

    $variations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organizar los resultados
    $colors = [];
    $sizes = [];
    foreach ($variations as $variation) {
        $colors[] = ['id_color' => $variation['id_color'], 'color' => $variation['color']];
        $sizes[] = ['id_size' => $variation['id_size'], 'sizeMX' => $variation['sizeMX']];
    }

    echo json_encode(['colors' => $colors, 'sizes' => $sizes]);
}
?>
