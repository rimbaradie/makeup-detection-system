<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'connect.php';

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT username, email, role, skin_type, created_at FROM Users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #fff0f5, #fdfdfd);
            color: #333;
            margin: 0;
            padding: 0;
            animation: fadeIn 1s ease-in-out;
        }

        h2 {
            text-align: center;
            color: #c94f7c;
            margin-top: 30px;
            font-size: 28px;
        }

        .profile-container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            max-width: 600px;
            margin: 30px auto;
            box-shadow: 0 8px 20px rgba(201, 79, 124, 0.2);
            transition: transform 0.3s ease;
        }

        .profile-container:hover {
            transform: translateY(-5px);
        }

        .profile-container p {
            margin: 12px 0;
            font-size: 18px;
        }

        .profile-container strong {
            color: #c94f7c;
        }

        a {
            display: block;
            width: fit-content;
            margin: 20px auto;
            text-align: center;
            text-decoration: none;
            color: #fff;
            background: #c94f7c;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        a:hover {
            background: #a83861;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <h2><i class="fas fa-user-circle"></i> Admin Profile</h2>

    <div class="profile-container">
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
        <p><strong>Skin Type:</strong> <?= htmlspecialchars($user['skin_type']) ?></p>
        <p><strong>Joined On:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
    </div>

    <a href="admin.html"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

</body>
</html>
