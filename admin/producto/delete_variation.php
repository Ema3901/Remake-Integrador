<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No estás logueado']);
    exit();
}

// Obtener el ID de la variación desde la URL
$variation_id = $_GET['id'] ?? null;

if (!$variation_id) {
    echo json_encode(['success' => false, 'message' => 'No se especificó la variación']);
    exit();
}

// Verificar que la variación existe
$sql = "SELECT * FROM shoes_variations WHERE id_varition = :variation_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':variation_id' => $variation_id]);
$variation = $stmt->fetch();

if (!$variation) {
    echo json_encode(['success' => false, 'message' => 'La variación no existe']);
    exit();
}

// Eliminar la variación de la base de datos
$sql = "DELETE FROM shoes_variations WHERE id_varition = :variation_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':variation_id' => $variation_id]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => true, 'message' => 'Variación eliminada exitosamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo eliminar la variación']);
}
?>
