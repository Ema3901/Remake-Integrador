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

    <!-- Exclusive Footer CSS -->
    <link rel="stylesheet" href="https://calzadojj.net/src/css/footer.css">
</head>
<body>

    <!-- Header -->
    <?php include __DIR__ . '/../src/header.php'; ?>
    <main style="min-height: 53.6vh;">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Gestión de Órdenes</h2>
            <div>
                <button class="btn btn-primary" id="addOrder" onclick="window.location.href='agregar_orden.php'">
                    <i class="fas fa-plus"></i> Añadir Orden
                </button>
                <button class="btn btn-secondary" id="refreshTable">
                    <i class="fas fa-sync-alt"></i> Actualizar
                </button>
            </div>
        </div>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID Orden</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Producto</th>
                    <th>Tamaño</th>
                    <th>Color</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="ordersTableBody">
                <?php foreach ($orders as $order): ?>
                    <tr data-id="<?= $order['id_order'] ?>">
                        <td><?= $order['id_order'] ?></td>
                        <td><?= $order['order_date'] ?></td>
                        <td>$<?= number_format($order['total'], 2) ?></td>
                        <td><?= htmlspecialchars($order['model_name']) ?></td>
                        <td><?= htmlspecialchars($order['sizeMX']) ?></td>
                        <td><?= htmlspecialchars($order['color']) ?></td>
                        <td><?= $order['quantity'] ?></td>
                        <td>$<?= number_format($order['price'], 2) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning editOrder">Editar</button>
                            <button class="btn btn-sm btn-danger deleteOrder">Eliminar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para Editar Orden -->
    <div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editOrderModalLabel">Editar Orden</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editOrderForm">
                        <input type="hidden" id="editOrderId">
                        <div class="mb-3">
                            <label for="editOrderDate" class="form-label">Fecha de Orden</label>
                            <input type="text" class="form-control" id="editOrderDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="editOrderTotal" class="form-label">Total</label>
                            <input type="number" class="form-control" id="editOrderTotal" required>
                        </div>
                        <div class="mb-3">
                            <label for="editOrderProduct" class="form-label">Producto</label>
                            <select class="form-select" id="editOrderProduct" required></select>
                        </div>
                        <div class="mb-3">
                            <label for="editOrderQuantity" class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="editOrderQuantity" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveOrderChanges">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>
    </main>
        <!-- Footer -->
        <?php include __DIR__ . '/../src/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setupDeleteOrder() {
            document.querySelectorAll(".deleteOrder").forEach((button) => {
                button.addEventListener("click", (event) => {
                    const orderId = event.target.closest("tr").getAttribute("data-id");

                    if (confirm(`¿Seguro que deseas eliminar la orden con ID: ${orderId}?`)) {
                        fetch(`delete_order.php`, {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json",
                            },
                            body: JSON.stringify({ id: orderId }),
                        })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.success) {
                                alert(data.message);
                                event.target.closest("tr").remove();
                            } else {
                                alert(data.message || "Error al eliminar la orden");
                            }
                        })
                        .catch((error) => {
                            console.error("Error:", error);
                            alert("Ocurrió un error al procesar la solicitud");
                        });
                    }
                });
            });
        }

        function setupEditOrder() {
            document.querySelectorAll(".editOrder").forEach((button) => {
                button.addEventListener("click", (event) => {
                    const orderId = event.target.closest("tr").getAttribute("data-id");

                    fetch(`fetch_order.php?id=${orderId}`)
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.success) {
                                const order = data.order;

                                document.getElementById("editOrderId").value = order.id_order;
                                document.getElementById("editOrderDate").value = order.order_date;
                                document.getElementById("editOrderTotal").value = order.total;

                                const productSelect = document.getElementById("editOrderProduct");
                                productSelect.innerHTML = "";
                                fetch("fetch_products.php")
                                    .then((response) => response.json())
                                    .then((products) => {
                                        products.forEach((product) => {
                                            const option = document.createElement("option");
                                            option.value = product.id_product;
                                            option.text = product.model_name;
                                            if (product.id_product === order.id_product) {
                                                option.selected = true;
                                            }
                                            productSelect.appendChild(option);
                                        });
                                    });
                            } else {
                                alert(data.message || "Error al cargar los datos de la orden");
                            }
                        })
                        .catch((error) => {
                            console.error("Error al cargar la orden:", error);
                        });

                    const modal = new bootstrap.Modal(document.getElementById("editOrderModal"));
                    modal.show();
                });
            });
        }

        function setupSaveOrderChanges() {
            document.getElementById("saveOrderChanges").addEventListener("click", () => {
                const orderId = document.getElementById("editOrderId").value;
                const orderDate = document.getElementById("editOrderDate").value;
                const total = document.getElementById("editOrderTotal").value;
                const productId = document.getElementById("editOrderProduct").value;
                const quantity = document.getElementById("editOrderQuantity").value;

                fetch("edit_order.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        id: orderId,
                        order_date: orderDate,
                        total: total,
                        product_id: productId,
                        quantity: quantity
                    }),
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || "Error al actualizar la orden");
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("Ocurrió un error al procesar la solicitud");
                });
            });
        }

        function setupRefreshTable() {
            document.getElementById("refreshTable").addEventListener("click", () => {
                fetch("fetch_orders.php")
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            const ordersTableBody = document.getElementById("ordersTableBody");
                            ordersTableBody.innerHTML = "";

                            data.orders.forEach((order) => {
                                const row = document.createElement("tr");
                                row.setAttribute("data-id", order.id_order);
                                row.innerHTML = `
                                    <td>${order.id_order}</td>
                                    <td>${order.order_date}</td>
                                    <td>$${order.total.toFixed(2)}</td>
                                    <td>${order.model_name}</td>
                                    <td>${order.sizeMX}</td>
                                    <td>${order.color}</td>
                                    <td>${order.quantity}</td>
                                    <td>$${order.price.toFixed(2)}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning editOrder">Editar</button>
                                        <button class="btn btn-sm btn-danger deleteOrder">Eliminar</button>
                                    </td>
                                `;
                                ordersTableBody.appendChild(row);
                            });

                            setupEditOrder();
                            setupDeleteOrder();
                        } else {
                            alert("Error al actualizar la tabla: " + data.message);
                        }
                    })
                    .catch((error) => {
                        console.error("Error al actualizar la tabla:", error);
                        alert("Ocurrió un error al intentar actualizar la tabla.");
                    });
            });
        }

        document.addEventListener("DOMContentLoaded", () => {
            setupDeleteOrder();
            setupEditOrder();
            setupSaveOrderChanges();
            setupRefreshTable();
        });
    </script>
</body>
</html>
