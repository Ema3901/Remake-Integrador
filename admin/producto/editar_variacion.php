<?php
include __DIR__ . '/../../src/database/db.php';

$variationId = $_GET['id'] ?? null;
if (!$variationId || !is_numeric($variationId)) {
    die('ID de variación no válido');
}

// Obtener datos de la variación
$sql = "CALL SP_GET_VARIATION_BY_ID(:id)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $variationId, PDO::PARAM_INT);
$stmt->execute();
$variation = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();
if (!$variation) {
    die('Variación no encontrada');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Variación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Editar Variación</h2>
        <form id="editVariationForm">
            <input type="hidden" id="variationId" value="<?= htmlspecialchars($variation['id_varition']) ?>">
            <div class="mb-3">
                <label for="size" class="form-label">Talla</label>
                <input type="text" class="form-control" id="size" value="<?= htmlspecialchars($variation['size']) ?>">
            </div>
            <div class="mb-3">
                <label for="color" class="form-label">Color</label>
                <input type="text" class="form-control" id="color" value="<?= htmlspecialchars($variation['color']) ?>">
            </div>
            <div class="mb-3">
                <label for="stockLocal" class="form-label">Stock Local</label>
                <input type="number" class="form-control" id="stockLocal" value="<?= htmlspecialchars($variation['stock_local']) ?>">
            </div>
            <div class="mb-3">
                <label for="stockTianguis" class="form-label">Stock Tianguis</label>
                <input type="number" class="form-control" id="stockTianguis" value="<?= htmlspecialchars($variation['stock_tianguis']) ?>">
            </div>
            <button type="button" class="btn btn-primary" id="saveChanges">Guardar Cambios</button>
        </form>
    </div>

    <script>
        document.getElementById('saveChanges').addEventListener('click', () => {
            const id = document.getElementById('variationId').value;
            const size = document.getElementById('size').value;
            const color = document.getElementById('color').value;
            const stockLocal = document.getElementById('stockLocal').value;
            const stockTianguis = document.getElementById('stockTianguis').value;

            fetch('update_variation.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, size, color, stock_local: stockLocal, stock_tianguis: stockTianguis })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.href = 'productos.php';
                    } else {
                        alert(`Error al actualizar: ${data.message}`);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
