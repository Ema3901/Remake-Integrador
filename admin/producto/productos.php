<?php
include __DIR__ . '/../../src/database/db.php';

// Obtener los productos usando el procedimiento almacenado
$sql = "CALL SP_GET_PRODUCTS()";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor(); // Liberar recursos
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
</head>
<body>
    
<!-- Header -->
<?php include __DIR__ . '/../../../src/include/header.php'; ?>

<!-- Sidebar -->
<div class="container-fluid">
    <div class="sidebar" id="sidebar">
        <nav class="nav flex-column">
            <a class="nav-link" href="/admin/panel.php"><i class="fas fa-tachometer-alt"></i> <span>Panel</span></a>
            <button class="btn btn-toggle" data-bs-toggle="collapse" data-bs-target="#submenu1" aria-expanded="false">
                <i class="fas fa-chart-line"></i> Resumen
            </button>
            <div class="collapse" id="submenu1">
                <a class="nav-link" href="/admin/registro/registro.php"><i class="fas fa-clipboard-list"></i> Registro</a>
            </div>
            <button class="btn btn-toggle" data-bs-toggle="collapse" data-bs-target="#submenu2" aria-expanded="false">
                <i class="fas fa-cogs"></i> Administración
            </button>
            <div class="collapse" id="submenu2">
                <a class="nav-link" href="/admin/usuarios/cuentas.php"><i class="fas fa-users"></i> Usuarios</a>
                <a class="nav-link" href="/admin/producto/productos.php"><i class="fas fa-boxes"></i> Inventario</a>
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
                <a href="/admin/panel.php" class="btn btn-danger">Regresar</a>
            </div>
        </div>

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
                <?php foreach ($products as $product): ?>
                    <tr data-id="<?= $product['id_shoe'] ?>" class="product-row">
                        <td><?= $product['id_shoe'] ?></td>
                        <td class="expandable" style="cursor: pointer;">
                            <?= htmlspecialchars($product['model_name']) ?>
                        </td>
                        <td><?= htmlspecialchars($product['brand']) ?></td>
                        <td><?= htmlspecialchars($product['gender']) ?></td>
                        <td>$<?= number_format($product['price'], 2) ?></td>
                        <td><?= htmlspecialchars($product['description']) ?></td>
                        <td>
                            <a href="editar.php?id=<?= $product['id_shoe'] ?>" class="btn btn-sm btn-warning">Editar</a>
                            <button class="btn btn-sm btn-danger deleteProduct" data-id="<?= $product['id_shoe'] ?>">Eliminar</button>
                        </td>
                    </tr>
                    <tr class="product-details" style="display: none;">
                        <td colspan="7">
                            <div>
                                <h5>Imágenes:</h5>
                                <img src="<?= $product['img_main'] ?>" alt="Imagen Principal" style="max-height: 100px;">
                                <img src="<?= $product['img_profile'] ?>" alt="Imagen Perfil" style="max-height: 100px;">
                                <img src="<?= $product['img_front'] ?>" alt="Imagen Frontal" style="max-height: 100px;">
                                <img src="<?= $product['img_rear'] ?>" alt="Imagen Trasera" style="max-height: 100px;">
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
            </tbody>
        </table>
    </div>
</div>

<!-- Footer -->
<?php include __DIR__ . '/../../../src/include/footer.php'; ?>

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
                                        <td>
                                            <button class="btn btn-sm btn-danger deleteVariation" data-id="${variation.id_varition}">Eliminar</button>
                                        </td>
                                    `;
                                    variationsTable.appendChild(tr);
                                });
                                setupDeleteVariation();
                            } else {
                                variationsTable.innerHTML = `<tr><td colspan="5" class="text-center">No hay variaciones disponibles</td></tr>`;
                            }
                        })
                        .catch(error => console.error('Error al cargar las variaciones:', error));
                }
            } else {
                detailsRow.style.display = 'none';
            }
        });
    });

    // Configurar botón de eliminar variación
    function setupDeleteVariation() {
        document.querySelectorAll('.deleteVariation').forEach(button => {
            button.addEventListener('click', () => {
                const variationId = button.getAttribute('data-id');
                fetch(`delete_variation.php?id=${variationId}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.closest('tr').remove();
                    } else {
                        alert('Error al eliminar la variación.');
                    }
                });
            });
        });
    }

    // Eliminar producto
    document.querySelectorAll('.deleteProduct').forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.getAttribute('data-id');
            if (confirm('¿Estás seguro de eliminar este producto?')) {
                fetch(`delete_product.php?id=${productId}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.closest('tr').remove();
                    } else {
                        alert('Error al eliminar el producto.');
                    }
                });
            }
        });
    });
</script>
</body>
</html>
