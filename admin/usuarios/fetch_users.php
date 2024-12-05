<?php
include __DIR__ . '/../../src/database/db.php';

try {
    $sql = "SELECT u.id_user, u.user_namee, u.namee, u.last_name, u.email_address, r.rangee 
            FROM users u
            JOIN ranges r ON u.id_range = r.id_range";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'users' => $users]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
