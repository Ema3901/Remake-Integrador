<?php
include __DIR__ . '/../../src/database/db.php';

$sql = "SELECT id_range, rangee FROM ranges";
$stmt = $pdo->prepare($sql);
$stmt->execute();

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
