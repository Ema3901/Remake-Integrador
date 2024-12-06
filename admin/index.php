<?php
// Incluir el archivo de conexión
include __DIR__ . '/../src/database/db.php';

session_start();

// Si no hay una sesión activa, redirigir a /sesion/sesion.php
if (!isset($_SESSION['user_id'])) {
    header('Location: /sesion/sesion.php');  // Cambia esto por la URL de tu página de sesión
    exit();
}

// Llamar al procedimiento almacenado para obtener los tickets
$sql = "CALL GetSalesTickets()"; // Aquí deberías usar el procedimiento almacenado adecuado
$stmt = $pdo->prepare($sql);

try {
    // Intentamos ejecutar la consulta
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Depuración: Verificamos si se obtuvieron resultados
    if (!$tickets) {
        throw new Exception("No se encontraron tickets en la base de datos.");
    }
    // Depuración: Mostrar los datos obtenidos
    error_log("Tickets obtenidos: " . print_r($tickets, true));

} catch (Exception $e) {
    // Capturamos cualquier error y lo mostramos
    error_log("Error al obtener los tickets: " . $e->getMessage());
    $tickets = [];  // Si ocurre un error, establecemos una variable vacía para evitar que el código se rompa
    $error_message = "Hubo un problema al obtener los tickets. Intenta más tarde.";
}
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

    <!-- Custom CSS -->
    <link rel="stylesheet" href="https://calzadojj.net/src/css/style.css">

</head>
<body>

    <!-- Header -->
    <?php include __DIR__ . '/../src/header.php'; ?>

    <main style="min-height: 53.6vh;">
        <div class="container mt-5">
            <h2>Gestión de Tickets de Venta</h2>

            <button class="btn btn-secondary mb-3" id="refreshTable">
                <i class="fas fa-sync-alt"></i> Actualizar
            </button>

            <!-- Mensaje de error si ocurre -->
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?= $error_message ?>
                </div>
            <?php endif; ?>

            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID Ticket</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Cliente</th>
                        <th>Producto</th>
                        <th>Tamaño</th>
                        <th>Color</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                    </tr>
                </thead>
                <tbody id="ticketsTableBody">
                    <?php if (!empty($tickets)): ?>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr data-id="<?= $ticket['id_ticket'] ?>">
                                <td><?= $ticket['id_ticket'] ?></td>
                                <td><?= $ticket['sale_date'] ?></td>
                                <td>$<?= number_format($ticket['total'], 2) ?></td>
                                <td><?= htmlspecialchars($ticket['client_name']) ?></td>
                                <td><?= htmlspecialchars($ticket['product_name']) ?></td>
                                <td><?= htmlspecialchars($ticket['size']) ?></td>
                                <td><?= htmlspecialchars($ticket['color']) ?></td>
                                <td><?= $ticket['quantity'] ?></td>
                                <td>$<?= number_format($ticket['price'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">No hay tickets para mostrar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../src/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setupRefreshTable() {
            document.getElementById("refreshTable").addEventListener("click", () => {
                fetch("fetch_sales_tickets.php")
                    .then((response) => response.json())
                    .then((data) => {
                        // Depuración: Verificar los datos recibidos
                        console.log("Datos recibidos de fetch_sales_tickets.php:", data);

                        if (data.success) {
                            const ticketsTableBody = document.getElementById("ticketsTableBody");
                            ticketsTableBody.innerHTML = "";

                            data.tickets.forEach((ticket) => {
                                const row = document.createElement("tr");
                                row.setAttribute("data-id", ticket.id_ticket);
                                row.innerHTML = `
                                    <td>${ticket.id_ticket}</td>
                                    <td>${ticket.sale_date}</td>
                                    <td>$${ticket.total.toFixed(2)}</td>
                                    <td>${ticket.client_name}</td>
                                    <td>${ticket.product_name}</td>
                                    <td>${ticket.size}</td>
                                    <td>${ticket.color}</td>
                                    <td>${ticket.quantity}</td>
                                    <td>$${ticket.price.toFixed(2)}</td>
                                `;
                                ticketsTableBody.appendChild(row);
                            });
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
            setupRefreshTable();
        });
    </script>
</body>
</html>
