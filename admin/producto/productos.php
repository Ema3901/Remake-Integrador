<?php
// Incluir el archivo de conexión
include __DIR__ . '/../../src/database/db.php';

session_start();

// Si no hay una sesión activa, redirigir a /sesion/sesion.php
if (!isset($_SESSION['user_id'])) {
    header('Location: /sesion/sesion.php');  // Cambia esto por la URL de tu página de sesión
    exit();
}

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
    <title>Administración | Calzado JJ</title>

    <link rel="icon" type="image/x-icon" href="https://calzadojj.net/src/images/logo/favicon.png">
    
    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="https://calzadojj.net/src/css/style.css">
    <link rel="stylesheet" href="https://calzadojj.net/src/css/footer.css">
</head>
<body>
    
<!-- Header -->
<?php include __DIR__ . '/../src/header.php'; ?>

<!-- Main content -->
<main style="min-height: 53.6vh;">

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
        </div>
    </div>

    <!-- Tabla de Productos -->
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre del modelo</th>
                <th>Marca</th>
                <th>Género</th>
                <th>Precio</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="productsTableBody">
            <?php 
                // Crear una fila por cada producto, sin duplicarlos por variación
                foreach ($products as $product): 
            ?>
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
                            <table class="table table-bordered" id="variationsTable-<?= $product['id_shoe'] ?>"> <!-- ID único para cada tabla -->
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
</main>

<!-- Footer -->
<?php include __DIR__ . '/../src/footer.php'; ?>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro de que quieres eliminar este producto? Esta acción no se puede deshacer.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteButton">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let currentProductId = null; // Variable para almacenar la ID del producto seleccionado
    let currentVariationId = null; // Variable para almacenar la ID de la variación seleccionada

    // Expandir detalles y cargar variaciones dinámicamente
    document.querySelectorAll('.expandable').forEach(cell => {
        cell.addEventListener('click', () => {
            const row = cell.closest('tr');
            const detailsRow = row.nextElementSibling;

            if (detailsRow.style.display === 'none') {
                detailsRow.style.display = 'table-row'; // Mostrar detalles del producto
                const productId = row.getAttribute('data-id');
                const variationsTable = document.getElementById(`variationsTable-${productId}`);

                if (variationsTable.querySelector('tbody').innerHTML.trim() === '') {
                    fetch(`fetch_variations.php?id=${productId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.variations.length > 0) {
                                variationsTable.querySelector('tbody').innerHTML = ''; // Limpiar la tabla
                                data.variations.forEach(variation => {
                                    const tr = document.createElement('tr');
                                    tr.innerHTML = `
                                        <td>${variation.sizeMX}</td>
                                        <td>${variation.color}</td>
                                        <td>${variation.stock_local}</td>
                                        <td>${variation.stock_tianguis}</td>
                                        <td>
                                            <button class="btn btn-sm btn-danger deleteVariation" data-id="${variation.id_varition}">Eliminar</button>
                                        </td>
                                    `;
                                    variationsTable.querySelector('tbody').appendChild(tr);
                                });
                                setupDeleteVariation(); // Configurar el evento de eliminación
                            } else {
                                variationsTable.querySelector('tbody').innerHTML = `<tr><td colspan="5" class="text-center">No hay variaciones disponibles</td></tr>`;
                            }
                        })
                        .catch(error => console.error('Error al cargar las variaciones:', error));
                }
            } else {
                detailsRow.style.display = 'none'; // Ocultar detalles
            }
        });
    });

    // Configurar el evento para eliminar variaciones
    function setupDeleteVariation() {
        document.querySelectorAll('.deleteVariation').forEach(button => {
            button.addEventListener('click', function () {
                currentVariationId = this.getAttribute('data-id'); // Obtener la ID de la variación
                // Mostrar el modal de confirmación
                const modal = new bootstrap.Modal(document.getElementById('confirmDeleteVariationModal'));
                modal.show();
            });
        });
    }

    // Configurar el botón de confirmación en el modal para variaciones
    document.getElementById('confirmDeleteVariationButton').addEventListener('click', function () {
        if (currentVariationId) {
            fetch(`delete_variation.php?id=${currentVariationId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message); // Mostrar mensaje de éxito
                        // Eliminar la fila de la variación del DOM
                        document.querySelector(`.deleteVariation[data-id="${currentVariationId}"]`).closest('tr').remove();
                    } else {
                        alert(data.message); // Mostrar mensaje de error
                    }
                })
                .catch(error => {
                    console.error('Error al eliminar la variación:', error);
                    alert('Hubo un error al intentar eliminar la variación.');
                })
                .finally(() => {
                    currentVariationId = null; // Reiniciar la variable
                    const modal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteVariationModal'));
                    modal.hide(); // Cerrar el modal
                });
        }
    });

    // Configurar el evento para eliminar productos
    function setupDeleteProduct() {
        document.querySelectorAll('.deleteProduct').forEach(button => {
            button.addEventListener('click', function () {
                currentProductId = this.getAttribute('data-id'); // Obtener la ID del producto
                const modal = new bootstrap.Modal(document.getElementById('confirmDeleteProductModal'));
                modal.show(); // Mostrar el modal de confirmación
            });
        });
    }

    // Configurar el botón de confirmación en el modal para productos
    document.getElementById('confirmDeleteProductButton').addEventListener('click', function () {
        if (currentProductId) {
            fetch('delete_product.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${encodeURIComponent(currentProductId)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message); // Mostrar mensaje de éxito
                        const row = document.querySelector(`.deleteProduct[data-id="${currentProductId}"]`).closest('tr');
                        const detailsRow = row.nextElementSibling; // Fila de detalles asociada
                        row.remove();
                        if (detailsRow && detailsRow.classList.contains('product-details')) {
                            detailsRow.remove(); // Eliminar la fila de detalles si existe
                        }
                    } else {
                        alert(data.message); // Mostrar mensaje de error
                    }
                })
                .catch(error => {
                    console.error('Error al eliminar el producto:', error);
                    alert('Hubo un error al intentar eliminar el producto.');
                })
                .finally(() => {
                    currentProductId = null; // Reiniciar la variable
                    const modal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteProductModal'));
                    modal.hide(); // Cerrar el modal
                });
        }
    });

    // Llamar la configuración inicial para los botones de eliminar
    setupDeleteProduct();

    // Actualizar la tabla de productos
    document.getElementById('refreshTable').addEventListener('click', () => {
        fetch('fetch_products.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tableBody = document.getElementById('productsTableBody');
                    tableBody.innerHTML = ''; // Limpiar la tabla
                    data.products.forEach(product => {
                        const tr = document.createElement('tr');
                        tr.setAttribute('data-id', product.id_shoe);
                        tr.classList.add('product-row');
                        tr.innerHTML = `
                            <td>${product.id_shoe}</td>
                            <td class="expandable" style="cursor: pointer;">${product.model_name}</td>
                            <td>${product.brand}</td>
                            <td>${product.gender}</td>
                            <td>$${product.price}</td>
                            <td>${product.description}</td>
                            <td>
                                <a href="editar.php?id=${product.id_shoe}" class="btn btn-sm btn-warning">Editar</a>
                                <button class="btn btn-sm btn-danger deleteProduct" data-id="${product.id_shoe}">Eliminar</button>
                            </td>
                        `;
                        tableBody.appendChild(tr);
                    });
                    setupDeleteProduct(); // Reconfigurar eliminar productos
                } else {
                    alert('Error al actualizar los productos.');
                }
            })
            .catch(error => console.error('Error al actualizar la tabla de productos:', error));
    });
</script>

<!-- Modal de Confirmación para Variaciones -->
<div class="modal fade" id="confirmDeleteVariationModal" tabindex="-1" aria-labelledby="confirmDeleteVariationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteVariationModalLabel">Confirmar Eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro de que quieres eliminar esta variación? Esta acción no se puede deshacer.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteVariationButton">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Confirmación para Productos -->
<div class="modal fade" id="confirmDeleteProductModal" tabindex="-1" aria-labelledby="confirmDeleteProductModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteProductModalLabel">Confirmar Eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro de que quieres eliminar este producto? Se eliminarán también todas las variaciones asociadas. Esta acción no se puede deshacer.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteProductButton">Eliminar</button>
      </div>
    </div>
  </div>
</div>
</body>
</html>
