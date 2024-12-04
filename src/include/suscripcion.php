<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../../vendor/autoload.php'; // Incluye el autoloader de Composer

$mensajeExito = ''; // Variable para mostrar el mensaje de éxito

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email']; // Recibe el correo desde el formulario

    // Validar el correo electrónico
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com'; // Servidor SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'info@calzadojj.net'; // Usuario SMTP (correo completo)
            $mail->Password = 'calzadoJJ_marsopa69'; // Contraseña SMTP
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Cifrado
            $mail->Port = 465; // Puerto SMTP

            // Configuración del correo
            $mail->setFrom('info@calzadojj.net', 'Calzado JJ'); // Remitente
            $mail->addAddress($email); // Correo del suscriptor
            $mail->isHTML(true); // Permitir contenido HTML en el correo
            $mail->Subject = 'Gracias por suscribirte al boletín de ofertas'; // Asunto

            // Agregar imagen incrustada utilizando __DIR__
            $rutaImagen = __DIR__ . '/../images/promos/promo1.jpg'; // Cambia 'carpeta_imagenes' por el nombre de tu carpeta
            $mail->addEmbeddedImage($rutaImagen, 'imagenPromocion'); // Etiqueta 'imagenPromocion' para usar en HTML

            // Cuerpo del correo con la imagen como enlace
            $mail->Body = '<h1>¡Gracias por suscribirte!</h1>
                           <p>Aquí tienes nuestra última promoción:</p>
                           <a href="https://calzadojj.net" target="_blank">
                               <img src="cid:imagenPromocion" alt="Promoción" style="max-width: 100%; border: none;">
                           </a>
                           <p>Haz clic en la imagen para conocer más de nuestros productos.</p>';

            // Enviar correo
            $mail->send();

            // Asignar el mensaje de éxito a la variable para mostrarlo en la página
            $mensajeExito = 'El correo se mandó con éxito.';
        } catch (Exception $e) {
            $mensajeExito = "Error al enviar el correo: {$mail->ErrorInfo}";
        }
    } else {
        $mensajeExito = 'Correo inválido.';
    }
}
?>

<!-- El formulario de suscripción -->
<form method="POST" action="">
    <label for="email">Correo Electrónico:</label>
    <input type="email" name="email" id="email" required>
    <button type="submit">Suscribirse</button>
</form>

<!-- Mostrar el mensaje de éxito o error si existe -->
<?php if ($mensajeExito): ?>
    <div id="mensajeExito" style="position: fixed; top: 20px; right: 20px; background-color: #4CAF50; color: white; padding: 10px 20px; border-radius: 5px; display: block;">
        <?php echo $mensajeExito; ?>
    </div>
    <script>
        // El mensaje desaparece después de 3 segundos
        setTimeout(function() {
            document.getElementById("mensajeExito").style.display = "none";
        }, 3000); 
    </script>
<?php endif; ?>
