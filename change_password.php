<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'connect.php';

$userId = $_SESSION['user_id'];
$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (empty($current) || empty($new) || empty($confirm)) {
        $error = "All fields are required.";
    } elseif ($new !== $confirm) {
        $error = "New passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM Users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($current, $user['password'])) {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE Users SET password = ? WHERE user_id = ?");
            $update->execute([$hashed, $userId]);
            $success = "Password updated successfully.";
        } else {
            $error = "Current password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #ffe4ec, #ffffff);
            padding: 0;
            margin: 0;
            animation: fadeIn 0.6s ease-in-out;
        }

        h2 {
            text-align: center;
            margin-top: 30px;
            color: #c94f7c;
        }

        .form-container {
            max-width: 500px;
            margin: 30px auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(201, 79, 124, 0.15);
        }

        .form-container input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        .form-container button {
            background-color: #c94f7c;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-container button:hover {
            background-color: #a83861;
        }

        .message {
            text-align: center;
            margin-top: 15px;
            font-weight: bold;
        }

        .message.success {
            color: #28a745;
        }

        .message.error {
            color: #e74c3c;
        }

        a.back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #c94f7c;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <h2><i class="fas fa-key"></i> Change Password</h2>

    <div class="form-container">
        <form method="POST" action="">
            <input type="password" name="current_password" placeholder="Current Password" required>
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <button type="submit"><i class="fas fa-lock"></i> Update Password</button>
        </form>

        <?php if ($success): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php elseif ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </div>

    <a class="back-link" href="admin_profile.php"><i class="fas fa-arrow-left"></i> Back to Profile</a>

</body>
</html>
