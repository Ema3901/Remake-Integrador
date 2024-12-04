<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // Ajusta la ruta si es necesario

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email']; // Correo del remitente
    $message = $_POST['message']; // Mensaje del usuario

    // Validar los campos del formulario
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($message)) {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com'; // Servidor SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'info@calzadojj.net'; // Usuario SMTP (correo completo)
            $mail->Password = 'calzadoJJ_marsopa69'; // Contraseña SMTP
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Configuración del correo
            $mail->setFrom('info@calzadojj.net', 'Formulario Calzado JJ'); // Usa noreply como remitente
            $mail->addAddress('info@calzadojj.net'); // Donde recibirás el mensaje
            $mail->addReplyTo($email, 'Usuario que envió el mensaje'); // Respuestas dirigidas al remitente
            $mail->Subject = 'Nuevo mensaje del formulario de contacto';
            $mail->Body = "Has recibido un nuevo mensaje:\n\nCorreo: $email\nMensaje:\n$message";

            // Enviar correo
            $mail->send();
            echo 'Mensaje enviado con éxito.';
        } catch (Exception $e) {
            echo "Error al enviar el mensaje: {$mail->ErrorInfo}";
        }
    } else {
        echo 'Por favor, completa todos los campos correctamente.';
    }
}
?>
