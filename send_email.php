<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the PHPMailer library files
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// Create an instance of PHPMailer
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';  // SMTP server
    $mail->SMTPAuth   = true;               // Enable SMTP authentication
    $mail->Username   = 'baradiereem30@gmail.com'; // Your Gmail address
    $mail->Password   = 'ronn lntr mqnc opem';   // Your Gmail app password (NOT your regular Gmail password)
    $mail->SMTPSecure = 'tls';              // Enable TLS encryption
    $mail->Port       = 587;                // TCP port to connect to

    // Recipients
    $mail->setFrom('baradiereem30@gmail.com', 'SkinGlam Admin');
    $mail->addAddress('baradiehanin@gmail.com', 'Client Name'); // Add a recipient

    // Content
    $mail->isHTML(true);                    // Set email format to HTML
    $mail->Subject = '🎉 Welcome to SkinGlam!';
    $mail->Body    = '
        <h2>Hello from SkinGlam!</h2>
        <p>Your journey to dermatologist-approved beauty starts here ✨</p>
    ';

    $mail->send();
    echo '✅ Email sent successfully!';
} catch (Exception $e) {
    echo "❌ Email failed. Error: {$mail->ErrorInfo}";
}
