<?php
// Start the session to store messages or any other session data
session_start();

// Include database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "SkinGlam";
$conn = mysqli_connect($host, $username, $password, $database);

// Check if connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email from the form
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if the email exists in the database
    $sql = "SELECT * FROM Users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        // If email exists, generate a unique reset token
        $token = bin2hex(random_bytes(50)); // Generate a 100-character token
        
        // Store the token in the database for that user (with an expiration date)
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token expires in 1 hour
        $sql = "UPDATE Users SET reset_token = '$token', reset_expiry = '$expiry' WHERE email = '$email'";
        if (mysqli_query($conn, $sql)) {
            // Send the reset link to the user's email
            $resetLink = "http://yourdomain.com/resetPasswordForm.php?token=$token"; // Replace with actual domain
            $subject = "Password Reset Request";
            $message = "Hello, \n\nWe received a request to reset your password. Click the link below to reset it: \n\n$resetLink\n\nIf you didn't request this, please ignore this email.";
            $headers = "From: no-reply@yourdomain.com"; // Replace with your email

            // Send email (You can also use PHPMailer or another library for more advanced email handling)
            if (mail($email, $subject, $message, $headers)) {
                $_SESSION['message'] = "A password reset link has been sent to your email address.";
                header("Location: forgotPasswordMessage.php"); // Redirect to a message page
                exit();
            } else {
                $_SESSION['error'] = "There was an error sending the email. Please try again.";
            }
        } else {
            $_SESSION['error'] = "There was an error generating the reset token. Please try again.";
        }
    } else {
        $_SESSION['error'] = "The email address doesn't exist in our system.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Password Reset</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }
        .container {
            width: 400px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        h2 {
            color: #ff1493;
            text-align: center;
            margin-bottom: 20px;
        }
        .message {
            text-align: center;
            font-size: 18px;
        }
        .message.success {
            color: green;
        }
        .message.error {
            color: red;
        }
        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }
        .back-to-login a {
            color: #ff1493;
            font-size: 16px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Password Reset Request</h2>
        <div class="message <?php echo isset($_SESSION['message']) ? 'success' : 'error'; ?>">
            <?php
            if (isset($_SESSION['message'])) {
                echo $_SESSION['message'];
                unset($_SESSION['message']);
            }
            if (isset($_SESSION['error'])) {
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            }
            ?>
        </div>
        <div class="back-to-login">
            <a href="login.html">Back to Login</a>
        </div>
    </div>
</body>
</html>