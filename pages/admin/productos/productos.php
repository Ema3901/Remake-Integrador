<?php
include __DIR__ . '/../../../src/database/db.php';

// Obtener los productos usando el procedimiento almacenado
$sql = "CALL SP_GET_PRODUCTS()";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor(); // Importante para liberar recursos al usar procedimientos almacenados
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
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Gestión de Productos</h2>
            <div>
                <button class="btn btn-primary" onclick="window.location.href='insertar_producto.php'">
                    <i class="fas fa-plus"></i> Añadir Producto
                </button>
                <button class="btn btn-secondary" id="refreshTable">
                    <i class="fas fa-sync-alt"></i> Actualizar
                </button>
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
                            <button class="btn btn-sm btn-warning editProduct">Editar</button>
                            <button class="btn btn-sm btn-danger deleteProduct">Eliminar</button>
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
                                        <!-- Variaciones se cargarán dinámicamente con JS -->
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Expande/colapsa detalles del producto y carga variaciones dinámicamente
    document.querySelectorAll('.expandable').forEach(cell => {
        cell.addEventListener('click', () => {
            const row = cell.closest('tr'); // Fila principal del producto
            const detailsRow = row.nextElementSibling; // Fila de detalles

            if (detailsRow.style.display === 'none') {
                // Mostrar detalles y cargar variaciones
                detailsRow.style.display = 'table-row';

                const productId = row.getAttribute('data-id');
                const variationsTable = detailsRow.querySelector('tbody');

                // Evitar recargar si ya están cargadas
                if (variationsTable.innerHTML.trim() === '') {
                    fetch(`fetch_variations.php?id=${productId}`)
                        .then(response => response.json())
                        .then(data => {
                            console.log("Respuesta recibida:", data); // Depuración

                            // Manejo de datos
                            if (data.success) {
                                variationsTable.innerHTML = ''; // Limpiar tabla

                                if (data.variations.length > 0) {
                                    // Agregar filas con las variaciones
                                    data.variations.forEach(variation => {
                                        const tr = document.createElement('tr');
                                        tr.innerHTML = `
                                            <td>${variation.size}</td>
                                            <td>${variation.color}</td>
                                            <td>${variation.stock_local}</td>
                                            <td>${variation.stock_tianguis}</td>
                                            <td>
                                                <button class="btn btn-sm btn-warning editVariation" data-id="${variation.id_varition}">Editar</button>
                                                <button class="btn btn-sm btn-danger deleteVariation" data-id="${variation.id_varition}">Eliminar</button>
                                            </td>
                                        `;
                                        variationsTable.appendChild(tr);
                                    });
                                } else {
                                    // Si no hay variaciones
                                    variationsTable.innerHTML = `
                                        <tr>
                                            <td colspan="5" class="text-center">No hay variaciones disponibles</td>
                                        </tr>
                                    `;
                                }

                                // Configurar botones de edición y eliminación
                                setupEditVariation();
                                setupDeleteVariation();
                            } else {
                                // Mostrar error si no se recibieron variaciones correctamente
                                variationsTable.innerHTML = `
                                    <tr>
                                        <td colspan="5" class="text-center text-danger">
                                            ${data.message || 'Error al cargar las variaciones'}
                                        </td>
                                    </tr>
                                `;
                            }
                        })
                        .catch(error => {
                            console.error('Error al cargar las variaciones:', error);
                            variationsTable.innerHTML = `
                                <tr>
                                    <td colspan="5" class="text-center text-danger">Error al cargar las variaciones</td>
                                </tr>
                            `;
                        });
                }
            } else {
                // Ocultar detalles
                detailsRow.style.display = 'none';
            }
        });
    });

    // Configura la edición de una variación (pendiente de implementar)
    function setupEditVariation() {
        document.querySelectorAll('.editVariation').forEach(button => {
            button.addEventListener('click', event => {
                const variationId = button.getAttribute('data-id');
                alert(`Editar variación con ID: ${variationId} (pendiente de implementación)`);
            });
        });
    }

    // Configura la eliminación de una variación
    function setupDeleteVariation() {
        document.querySelectorAll('.deleteVariation').forEach(button => {
            button.addEventListener('click', event => {
                const variationId = button.getAttribute('data-id');
                if (confirm(`¿Estás seguro de que deseas eliminar la variación con ID ${variationId}?`)) {
                    fetch(`delete_variation.php`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: variationId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Variación eliminada correctamente');
                            location.reload();
                        } else {
                            alert('Error al eliminar la variación: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        });
    }
</script>
</body>
</html>
