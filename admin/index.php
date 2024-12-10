<?php
// Incluir el archivo de conexión
include __DIR__ . '/../src/database/db.php';

session_start();

// Si no hay una sesión activa, redirigir a /sesion/sesion.php
if (!isset($_SESSION['user_id'])) {
    header('Location: /sesion/sesion.php');
    exit();
}

// Obtener los tickets (tabla `orders`)
$sql = "SELECT id_order, user_id, total_price, created_at FROM orders ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error al obtener los tickets: " . $e->getMessage());
    $tickets = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio | Calzado JJ</title>

    <link rel="icon" type="image/x-icon" href="https://calzadojj.net/src/images/logo/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://calzadojj.net/src/css/style.css">
</head>
<body>

    <!-- Header -->
    <?php include __DIR__ . '/../src/header.php'; ?>

    <!-- Main Content -->
    <main class="container mt-5">
        <h2 class="mb-4">Gestión de Tickets</h2>
        
        <!-- Tabla de Tickets -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Ticket</th>
                    <th>Usuario</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="ticketsTableBody">
                <?php foreach ($tickets as $ticket): ?>
                    <tr data-id="<?= $ticket['id_order'] ?>">
                        <td><?= $ticket['id_order'] ?></td>
                        <td><?= htmlspecialchars($ticket['user_id']) ?></td>
                        <td>$<?= number_format($ticket['total_price'], 2) ?></td>
                        <td><?= $ticket['created_at'] ?></td>
                        <td>
                            <button class="btn btn-danger btn-sm delete-ticket-btn" data-id="<?= $ticket['id_order'] ?>">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3 class="mt-5">Detalles del Ticket</h3>
        <!-- Tabla de Detalles del Ticket -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Item</th>
                    <th>Variación</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Tamaño</th>
                    <th>Color</th>
                </tr>
            </thead>
            <tbody id="orderItemsTableBody">
                <tr>
                    <td colspan="6" class="text-center">Seleccione un ticket para ver los detalles.</td>
                </tr>
            </tbody>
        </table>
    </main>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="deleteTicketModal" tabindex="-1" aria-labelledby="deleteTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTicketModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar este ticket?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/src/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            let ticketToDelete = null;

            // Abrir modal para confirmar eliminación
            $('#ticketsTableBody').on('click', '.delete-ticket-btn', function (e) {
                e.stopPropagation(); // Evitar que se dispare el evento de clic en la fila
                ticketToDelete = $(this).data('id');
                $('#deleteTicketModal').modal('show');
            });

            // Confirmar eliminación
            $('#confirmDeleteButton').on('click', function () {
                if (ticketToDelete) {
                    $.ajax({
                        url: 'delete_ticket.php',
                        type: 'POST',
                        data: { id_order: ticketToDelete },
                        success: function (response) {
                            if (response.success) {
                                $(`tr[data-id="${ticketToDelete}"]`).remove();
                                $('#deleteTicketModal').modal('hide');
                            } else {
                                $('#deleteTicketModal .modal-body').text('Error al eliminar el ticket. Por favor, inténtalo de nuevo.');
                            }
                        },
                        error: function () {
                            $('#deleteTicketModal .modal-body').text('Error al procesar la solicitud. Verifica tu conexión.');
                        }
                    });
                }
            });

            // Cargar los detalles de un ticket al hacer clic en la fila
            $('#ticketsTableBody').on('click', 'tr', function () {
                const orderId = $(this).data('id');

                $.ajax({
                    url: 'get_order_items.php',
                    type: 'GET',
                    data: { id_order: orderId },
                    dataType: 'json',
                    success: function (data) {
                        const tableBody = $('#orderItemsTableBody');
                        tableBody.empty();

                        if (data.success) {
                            data.items.forEach(item => {
                                const row = `
                                    <tr>
                                        <td>${item.id_item}</td>
                                        <td>${item.id_variation}</td>
                                        <td>$${item.price}</td>
                                        <td>${item.quantity}</td>
                                        <td>${item.sizeMX}</td>
                                        <td>${item.color}</td>
                                    </tr>
                                `;
                                tableBody.append(row);
                            });
                        } else {
                            tableBody.append('<tr><td colspan="6" class="text-center">No se encontraron detalles para este ticket.</td></tr>');
                        }
                    },
                    error: function () {
                        $('#orderItemsTableBody').html('<tr><td colspan="6" class="text-center">Error al obtener los detalles del ticket.</td></tr>');
                    }
                });
            });
        });
    </script>
</body>
</html>
