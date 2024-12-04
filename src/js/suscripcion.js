document.getElementById("formSuscripcion").addEventListener("submit", function(e) {
    e.preventDefault(); // Evitar que el formulario se envíe de la manera tradicional

    var formData = new FormData(this); // Recoger los datos del formulario

    // Realizar la petición AJAX
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/src/include/suscripcion.php", true); // Cambia la ruta al archivo PHP donde procesas el envío de correo
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Si la respuesta es 'success', mostramos el popup
            if (xhr.responseText === 'success') {
                document.getElementById("mensajeExito").style.display = "block"; // Mostrar el mensaje de éxito
                setTimeout(function() {
                    document.getElementById("mensajeExito").style.display = "none"; // Ocultar el mensaje después de 3 segundos
                }, 3000);
            } else {
                // Si hay error, puedes manejarlo aquí (puedes mostrar un mensaje diferente si lo deseas)
                alert('Hubo un problema al enviar el correo.');
            }
        }
    };
    xhr.send(formData); // Enviar los datos del formulario por AJAX
});
