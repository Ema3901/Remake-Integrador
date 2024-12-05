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