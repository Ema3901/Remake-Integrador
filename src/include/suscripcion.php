<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../../vendor/autoload.php'; // Incluye el autoloader de Composer

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
            echo 'Correo enviado con éxito.';
        } catch (Exception $e) {
            echo "Error al enviar el correo: {$mail->ErrorInfo}";
        }
    } else {
        echo 'Correo inválido.';
    }
}
?>
