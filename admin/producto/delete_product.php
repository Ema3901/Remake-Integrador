<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: /sesion/sesion.php');
    exit();
}

// Obtener el ID del producto desde la URL
$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    header('Location: gestion_productos.php');
    exit();
}

// Obtener el producto para obtener las rutas de las imágenes
$sql = "SELECT * FROM shoes WHERE id_shoe = :product_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':product_id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: gestion_productos.php');
    exit();
}

// Eliminar las variaciones del producto
$sql = "DELETE FROM shoes_variations WHERE id_shoe = :product_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':product_id' => $product_id]);

// Eliminar las imágenes del servidor si existen
$images = ['img_main', 'img_profile', 'img_front', 'img_rear'];
foreach ($images as $image) {
    if (!empty($product[$image]) && file_exists($product[$image])) {
        unlink($product[$image]);  // Eliminar archivo de la carpeta
    }
}

// Eliminar el producto de la base de datos
$sql = "DELETE FROM shoes WHERE id_shoe = :product_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':product_id' => $product_id]);

// Redirigir al listado de productos
header('Location: gestion_productos.php');
exit();
?>
