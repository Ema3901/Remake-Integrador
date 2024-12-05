<?php
include __DIR__ . '/../../src/database/db.php';

// Inicializar la variable de error
$error_message = '';

try {
    // Obtener los productos usando el procedimiento almacenado
    $sql = "CALL SP_GetProducts()";  // Llamar al procedimiento con el nuevo nombre
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($products === false) {
        throw new Exception("No se obtuvieron productos.");
    }

    $stmt->closeCursor(); // Liberar recursos

} catch (Exception $e) {
    // Si hay algún error, capturarlo y mostrarlo
    $error_message = "Error al ejecutar la consulta: " . $e->getMessage();
    $products = []; // Asegurar que no falle la parte de mostrar la tabla
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos | Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/src/css/style.css">
    <link rel="stylesheet" href="/src/css/admin.css">
    <link rel="stylesheet" href="/src/css/crud.css">
</head>
<body>
    
<!-- Header -->
<?php include __DIR__ . '/src/header.php'; ?>

<!-- Sidebar -->
<div class="container-fluid">
    <div class="sidebar" id="sidebar">
        <nav class="nav flex-column">
            <a class="nav-link" href="/index.php"><i class="fas fa-tachometer-alt"></i> <span>Panel</span></a>
            <button class="btn btn-toggle" data-bs-toggle="collapse" data-bs-target="#submenu1" aria-expanded="false">
                <i class="fas fa-chart-line"></i> Resumen
            </button>
            <div class="collapse" id="submenu1">
                <a class="nav-link" href="/registro/registro.php"><i class="fas fa-clipboard-list"></i> Registro</a>
            </div>
            <button class="btn btn-toggle" data-bs-toggle="collapse" data-bs-target="#submenu2" aria-expanded="false">
                <i class="fas fa-cogs"></i> Administración
            </button>
            <div class="collapse" id="submenu2">
                <a class="nav-link" href="/usuarios/cuentas.php"><i class="fas fa-users"></i> Usuarios</a>
                <a class="nav-link" href="/producto/productos.php"><i class="fas fa-boxes"></i> Inventario</a>
            </div>
        </nav>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Gestión de Productos</h2>
            <div>
                <button class="btn btn-primary" onclick="window.location.href='insertar_producto.php'">
                    <i class="fas fa-plus"></i> Añadir Producto
                </button>
                <button class="btn btn-secondary" id="refreshTable">
                    <i class="fas fa-sync-alt"></i> Actualizar
                </button>
                <a href="/index.php" class="btn btn-danger">Regresar</a>
            </div>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <strong>Error!</strong> <?= $error_message ?>
            </div>
        <?php endif; ?>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Modelo</th>
                    <th>Marca</th>
                    <th>Género</th>
                    <th>Precio</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="productsTableBody">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <tr data-id="<?= $product['product_id'] ?>" class="product-row">
                            <td><?= $product['product_id'] ?></td>
                            <td class="expandable" style="cursor: pointer;">
                                <?= htmlspecialchars($product['model']) ?>
                            </td>
                            <td><?= htmlspecialchars($product['brand']) ?></td>
                            <td><?= htmlspecialchars($product['gender']) ?></td>
                            <td>$<?= number_format($product['price'], 2) ?></td>
                            <td><?= htmlspecialchars($product['description']) ?></td>
                            <td>
                                <a href="editar.php?id=<?= $product['product_id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                <button class="btn btn-sm btn-danger deleteProduct" data-id="<?= $product['product_id'] ?>">Eliminar</button>
                            </td>
                        </tr>
                        <tr class="product-details" style="display: none;">
                            <td colspan="7">
                                <div>
                                    <h5>Imágenes:</h5>
                                    <img src="<?= $product['main_image'] ?>" alt="Imagen Principal" style="max-height: 100px;">
                                    <img src="<?= $product['profile_image'] ?>" alt="Imagen Perfil" style="max-height: 100px;">
                                    <img src="<?= $product['front_image'] ?>" alt="Imagen Frontal" style="max-height: 100px;">
                                    <img src="<?= $product['rear_image'] ?>" alt="Imagen Trasera" style="max-height: 100px;">
                                    <h5>Variaciones:</h5>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Talla</th>
                                                <th>Color</th>
                                                <th>Stock Local</th>
                                                <th>Stock Tianguis</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No se encontraron productos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer -->
<?php include __DIR__ . '/src/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Expandir detalles y cargar variaciones dinámicamente
document.querySelectorAll('.expandable').forEach(cell => {
    cell.addEventListener('click', () => {
        const row = cell.closest('tr');
        const detailsRow = row.nextElementSibling;

        if (detailsRow.style.display === 'none') {
            detailsRow.style.display = 'table-row';
            const productId = row.getAttribute('data-id');
            const variationsTable = detailsRow.querySelector('tbody');

            if (variationsTable.innerHTML.trim() === '') {
                fetch(`fetch_variations.php?id=${productId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.variations.length > 0) {
                            variationsTable.innerHTML = '';
                            data.variations.forEach(variation => {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                    <td>${variation.size}</td>
                                    <td>${variation.color}</td>
                                    <td>${variation.stock_local}</td>
                                    <td>${variation.stock_tianguis}</td>
                                `;
                                variationsTable.appendChild(tr);
                            });
                        }
                    });
            }
        } else {
            detailsRow.style.display = 'none';
        }
    });
});
</script>

</body>
</html>
