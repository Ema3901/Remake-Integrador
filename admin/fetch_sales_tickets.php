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
    $sql = "CALL GetSalesTickets()"; // Procedimiento almacenado definido en tu base de datos
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Depuración: Verificar los resultados obtenidos
    if (!$tickets) {
        throw new Exception("No se encontraron tickets de venta.");
    }

    // Depuración: Imprimir resultados en logs del servidor
    error_log("Tickets obtenidos: " . print_r($tickets, true));

    // Si todo fue correcto, devolver los tickets en formato JSON
    echo json_encode(['success' => true, 'tickets' => $tickets]);

} catch (PDOException $pdoEx) {
    // Captura errores específicos de la base de datos
    error_log("Error de base de datos: " . $pdoEx->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $pdoEx->getMessage()]);

} catch (Exception $e) {
    // Captura errores generales
    error_log("Error al obtener los tickets: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
