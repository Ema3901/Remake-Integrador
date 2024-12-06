<?php
// Incluir el archivo de conexión
include __DIR__ . '/../src/database/db.php';

// Asegurarse de que la respuesta sea JSON
header('Content-Type: application/json');

try {
    // Verificar si el usuario está logueado
    session_start();
    if (!isset($_SESSION['user_id'])) {
        // Si no hay sesión activa, devolver un error JSON
        echo json_encode(['success' => false, 'message' => 'No estás autenticado.']);
        exit();
    }

    // Llamar al procedimiento almacenado para obtener los tickets de venta
    $sql = "CALL GetSalesTickets()";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si no se encontraron tickets, devolver un mensaje de error
    if (!$tickets) {
        throw new Exception("No se encontraron tickets de venta.");
    }

    // Si todo fue correcto, devolver los tickets en formato JSON
    echo json_encode(['success' => true, 'tickets' => $tickets]);

} catch (Exception $e) {
    // Si ocurre un error en la consulta o en el proceso, devolver un mensaje de error
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
