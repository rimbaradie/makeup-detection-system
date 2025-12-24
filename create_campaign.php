<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'connect.php'; // Connect to the database
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $status = $_POST['status'];
    $last_sent = $_POST['last_sent'];
    $opens = $_POST['opens_percentage'];
    $recipient = $_POST['recipient'];

    try {
        // Insert campaign into the database
        $stmt = $conn->prepare("INSERT INTO email_campaigns (title, status, last_sent, opens_percentage, recipient) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $status, $last_sent, $opens, $recipient]);

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        // Email configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'baradiereem30@gmail.com'; // your Gmail
        $mail->Password   = 'ronn lntr mqnc opem';     // your app password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('baradiereem30@gmail.com', 'SkinGlam Campaigns');
        $mail->addAddress($recipient);

        $mail->isHTML(true);
        $mail->Subject = "📢 $title";
        $mail->Body    = "
            <h3>SkinGlam Campaign: $title</h3>
            <p>Status: $status</p>
            <p>Last Sent: $last_sent</p>
            <p>Open Rate: $opens%</p>
            <p>✨ Stay beautiful with SkinGlam!</p>
        ";

        $mail->send();

        // Success message
        echo "<script>alert('✅ Campaign saved and email sent successfully!'); window.location.href='admin.html';</script>";
    } catch (Exception $e) {
        echo "❌ Email failed: {$mail->ErrorInfo}";
    } catch (PDOException $e) {
        echo "❌ Database error: " . $e->getMessage();
    }
}
?>
