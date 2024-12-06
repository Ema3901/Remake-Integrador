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
                <th>Modelo</th>
                <th>Marca</th>
                <th>Género</th>
                <th>Precio</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="productsTableBody">
            <?php 
                // Agrupar productos por el id del zapato, mostrando las variaciones solo cuando se expanden
                $lastProductId = null;
                foreach ($products as $product): 
                    // Solo mostrar una fila por producto
                    if ($lastProductId !== $product['id_shoe']) :
                        $lastProductId = $product['id_shoe'];
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
                                    <!-- Aquí se cargarán las variaciones con JS -->
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            <?php endif; endforeach; ?>
        </tbody>
    </table>
</div>
</main>

<!-- Footer -->
<?php include __DIR__ . '/../src/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Expandir detalles y cargar variaciones dinámicamente
    document.querySelectorAll('.expandable').forEach(cell => {
        cell.addEventListener('click', () => {
            const row = cell.closest('tr');
            const detailsRow = row.nextElementSibling;

            // Verificar si la fila de detalles está oculta o visible
            if (detailsRow.style.display === 'none') {
                detailsRow.style.display = 'table-row'; // Mostrar detalles del producto
                const productId = row.getAttribute('data-id'); // Obtener el ID del producto
                const variationsTable = document.getElementById(`variationsTable-${productId}`);

                // Solo cargar las variaciones si no se han cargado aún
                if (variationsTable.querySelector('tbody').innerHTML.trim() === '') {
                    fetch(`fetch_variations.php?id=${productId}`)  // Corregir la URL para incluir el ID
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.variations.length > 0) {
                                variationsTable.querySelector('tbody').innerHTML = ''; // Limpiar tabla de variaciones antes de agregar nuevos datos
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
                                setupDeleteVariation(); // Configurar el evento de eliminación de variación
                            } else {
                                variationsTable.querySelector('tbody').innerHTML = `<tr><td colspan="5" class="text-center">No hay variaciones disponibles</td></tr>`;
                            }
                        })
                        .catch(error => console.error('Error al cargar las variaciones:', error));
                }
            } else {
                detailsRow.style.display = 'none'; // Ocultar los detalles del producto
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
                        button.closest('tr').remove(); // Eliminar la fila de variación de la tabla
                    } else {
                        alert('Error al eliminar la variación.');
                    }
                });
            });
        });
    }

    // Configurar botón de eliminar producto
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
                        button.closest('tr').remove(); // Eliminar el producto de la tabla
                    } else {
                        alert('Error al eliminar el producto.');
                    }
                });
            }
        });
    });

    // Función para actualizar la tabla de productos
    document.getElementById('refreshTable').addEventListener('click', () => {
        fetch('fetch_products.php')  // Asegúrate de que este archivo devuelva los productos en formato JSON
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tableBody = document.getElementById('productsTableBody');
                    tableBody.innerHTML = '';  // Limpiar la tabla existente

                    data.products.forEach(product => {
                        const tr = document.createElement('tr');
                        tr.setAttribute('data-id', product.id_shoe);
                        tr.classList.add('product-row');
                        tr.innerHTML = `
                            <td>${product.id_shoe}</td>
                            <td class="expandable" style="cursor: pointer;">
                                ${product.model_name}
                            </td>
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

                    // Reconfigurar el comportamiento de eliminar productos
                    setupDeleteProduct();
                } else {
                    alert('Error al actualizar los productos.');
                }
            })
            .catch(error => console.error('Error al actualizar la tabla de productos:', error));
    });

    // Configurar el comportamiento de eliminar productos
    function setupDeleteProduct() {
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
                            button.closest('tr').remove(); // Eliminar el producto
                        } else {
                            alert('Error al eliminar el producto.');
                        }
                    });
                }
            });
        });
    }
</script>
</body>
</html>
