<?php
include __DIR__ . '/../../src/database/db.php';

// Fetch users with their ranges
$sql = "SELECT u.id_user, u.user_namee, u.namee, u.last_name, u.email_address, r.rangee 
        FROM users u
        JOIN ranges r ON u.id_range = r.id_range";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Zapatos | Calzado JJ</title>

    <link rel="icon" type="image/x-icon" href="/src/images/logo/favicon.png">
    <meta name="description" content="Tienda de calzado JJ. Venta de zapatos para hombre, mujer y unisex en Reynosa, Tamaulipas. Descubre nuestras colecciones.">
    <meta name="keywords" content="zapatos, calzado, venta de calzado, tienda de zapatos, Reynosa, hombre, mujer, unisex">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/src/css/style.css">
    <link rel="stylesheet" href="/src/css/admin.css">
    <link rel="stylesheet" href="/src/css/crud.css">
</head>
<body>

    <!-- Header -->
    <?php include __DIR__ . '/src/header.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Gestión de Usuarios</h2>
            <div>
                <button class="btn btn-primary" id="addUser" onclick="window.location.href='agregar_usuario.php'">
                    <i class="fas fa-plus"></i> Añadir Usuario
                </button>
                <button class="btn btn-secondary" id="refreshTable">
                    <i class="fas fa-sync-alt"></i> Actualizar
                </button>
                <a href="/
panel.php" class="btn btn-danger">Regresar</a>
            </div>
        </div>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Correo</th>
                    <th>Rango</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <?php foreach ($users as $user): ?>
                    <tr data-id="<?= $user['id_user'] ?>">
                        <td><?= $user['id_user'] ?></td>
                        <td><?= htmlspecialchars($user['user_namee']) ?></td>
                        <td><?= htmlspecialchars($user['namee']) ?></td>
                        <td><?= htmlspecialchars($user['last_name']) ?></td>
                        <td><?= htmlspecialchars($user['email_address']) ?></td>
                        <td><?= htmlspecialchars($user['rangee']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning editUser">Editar</button>
                            <button class="btn btn-sm btn-danger deleteUser">Eliminar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para Editar Usuario -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <input type="hidden" id="editUserId">
                        <div class="mb-3">
                            <label for="editUserName" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="editUserName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editName" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editLastName" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="editLastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="editEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="editRange" class="form-label">Rango</label>
                            <select class="form-select" id="editRange" required></select>
                        </div>
                        <div class="mb-3">
                            <label for="editPassword" class="form-label">Nueva Contraseña (opcional)</label>
                            <input type="password" class="form-control" id="editPassword" placeholder="Dejar en blanco para mantener la contraseña actual">
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="confirmPassword" placeholder="Repite la nueva contraseña">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveUserChanges">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

        <!-- Footer -->
        <?php include __DIR__ . '/src/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setupDeleteUser() {
            document.querySelectorAll(".deleteUser").forEach((button) => {
                button.addEventListener("click", (event) => {
                    const userId = event.target.closest("tr").getAttribute("data-id");

                    if (confirm(`¿Seguro que deseas eliminar al usuario con ID: ${userId}?`)) {
                        fetch(`delete_user.php`, {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json",
                            },
                            body: JSON.stringify({ id: userId }),
                        })
                            .then((response) => response.json())
                            .then((data) => {
                                if (data.success) {
                                    alert(data.message);
                                    event.target.closest("tr").remove();
                                } else {
                                    alert(data.message || "Error al eliminar el usuario");
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

        function setupEditUser() {
            document.querySelectorAll(".editUser").forEach((button) => {
                button.addEventListener("click", (event) => {
                    const userId = event.target.closest("tr").getAttribute("data-id");

                    fetch(`fetch_user.php?id=${userId}`)
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.success) {
                                const user = data.user;

                                document.getElementById("editUserId").value = user.id_user;
                                document.getElementById("editUserName").value = user.user_namee;
                                document.getElementById("editName").value = user.namee;
                                document.getElementById("editLastName").value = user.last_name;
                                document.getElementById("editEmail").value = user.email_address;

                                document.getElementById("editPassword").value = "";
                                document.getElementById("confirmPassword").value = "";

                                const rangeSelect = document.getElementById("editRange");
                                rangeSelect.innerHTML = "";
                                fetch("fetch_ranges.php")
                                    .then((response) => response.json())
                                    .then((ranges) => {
                                        ranges.forEach((range) => {
                                            const option = document.createElement("option");
                                            option.value = range.id_range;
                                            option.text = range.rangee;
                                            if (range.id_range === user.id_range) {
                                                option.selected = true;
                                            }
                                            rangeSelect.appendChild(option);
                                        });
                                    });
                            } else {
                                alert(data.message || "Error al cargar los datos del usuario");
                            }
                        })
                        .catch((error) => {
                            console.error("Error al cargar el usuario:", error);
                        });

                    const modal = new bootstrap.Modal(document.getElementById("editUserModal"));
                    modal.show();
                });
            });
        }

        function setupSaveUserChanges() {
            document.getElementById("saveUserChanges").addEventListener("click", () => {
                const userId = document.getElementById("editUserId").value;
                const userName = document.getElementById("editUserName").value;
                const name = document.getElementById("editName").value;
                const lastName = document.getElementById("editLastName").value;
                const email = document.getElementById("editEmail").value;
                const idRange = document.getElementById("editRange").value;
                const password = document.getElementById("editPassword").value;
                const confirmPassword = document.getElementById("confirmPassword").value;

                if (password && password !== confirmPassword) {
                    alert("Las contraseñas no coinciden. Por favor, verifica e inténtalo de nuevo.");
                    return;
                }

                fetch("edit_user.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        id: userId,
                        user_name: userName,
                        name: name,
                        last_name: lastName,
                        id_range: idRange,
                        email: email,
                        password: password,
                    }),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message || "Error al actualizar el usuario");
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
                fetch("fetch_users.php")
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            const usersTableBody = document.getElementById("usersTableBody");
                            usersTableBody.innerHTML = "";

                            data.users.forEach((user) => {
                                const row = document.createElement("tr");
                                row.setAttribute("data-id", user.id_user);
                                row.innerHTML = `
                                    <td>${user.id_user}</td>
                                    <td>${user.user_namee}</td>
                                    <td>${user.namee}</td>
                                    <td>${user.last_name}</td>
                                    <td>${user.email_address}</td>
                                    <td>${user.rangee}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning editUser">Editar</button>
                                        <button class="btn btn-sm btn-danger deleteUser">Eliminar</button>
                                    </td>
                                `;
                                usersTableBody.appendChild(row);
                            });

                            setupEditUser();
                            setupDeleteUser();
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
            setupDeleteUser();
            setupEditUser();
            setupSaveUserChanges();
            setupRefreshTable();
        });
    </script>
</body>
</html>
