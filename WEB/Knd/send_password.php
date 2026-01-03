<?php
require_once 'config/database.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $pdo->prepare("SELECT per_nombre, per_apellido, per_password FROM perfiles WHERE per_email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $nombre = $user['per_nombre'];
            $apellido = $user['per_apellido'];
            $password = $user['per_password'];
            $to = $email;
            $subject = "Recuperación de Contraseña";
            $message = "Señor(a) $nombre $apellido,\n\nPlusvalíaBLK le ha enviado su contraseña de recuperación. Si usted no solicitó este mensaje, por favor comuníquese con nosotros a través de nuestro sitio web: PlusvaliaBLK.com gracias por preferirnos.\n\nSu contraseña es: $password";
            
            $smtp_server = "smtp.gmail.com";
            $smtp_port = 587;
            $smtp_username = "uffthevear@gmail.com";
            $smtp_password = "iuix petm glgz jjqz";

            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->isSMTP();
            $mail->Host = $smtp_server;
            $mail->SMTPAuth = true;
            $mail->Username = $smtp_username;
            $mail->Password = $smtp_password;
            $mail->SMTPSecure = 'tls';
            $mail->Port = $smtp_port;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($smtp_username, 'Sistema de Recuperación');
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body = $message;

            if ($mail->send()) {
                echo "<script>alert('Se ha enviado su contraseña al correo ingresado.'); window.location.href='index.php';</script>";
            } else {
                echo "<script>alert('Error al enviar el correo.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Correo electrónico no encontrado.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Correo electrónico inválido.'); window.history.back();</script>";
    }
}
?>
