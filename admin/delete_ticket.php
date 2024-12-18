<?php
// Incluir el archivo de conexión
include __DIR__ . '/../src/database/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_order = $_POST['id_order'] ?? null;

    if ($id_order) {
        $sql = "DELETE FROM orders WHERE id_order = :id_order";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([':id_order' => $id_order]);
            echo json_encode(['success' => true]);
            exit();
        } catch (Exception $e) {
            error_log("Error al eliminar el ticket: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'ID del ticket no proporcionado.']);
        exit();
    }
}
