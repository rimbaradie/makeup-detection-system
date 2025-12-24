<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $message = $_POST['message'];

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'baradiereem30@gmail.com';
        $mail->Password   = 'ronn lntr mqnc opem'; // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // From user to admin
        $mail->setFrom($email, $name);
        $mail->addAddress('baradiereem30@gmail.com', 'SkinGlam Admin');

        $mail->isHTML(true);
        $mail->Subject = '📩 New message from SkinGlam user';
        $mail->Body    = "<strong>Name:</strong> $name<br><strong>Email:</strong> $email<br><strong>Message:</strong><br>$message";

        $mail->send();
        echo "✅ Message sent to admin!";
    } catch (Exception $e) {
        echo "❌ Failed to send. Error: {$mail->ErrorInfo}";
    }
}
