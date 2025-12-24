<?php
session_start();
include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM Users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

   if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $email;
    $_SESSION['role'] = $user['role'];
    $_SESSION['unique_id'] = $user['unique_id'];

    // ✅ أضف هذين السطرين:
    $_SESSION['fname'] = $user['fname'];
    $_SESSION['lname'] = $user['lname'];

    switch (strtolower($user['role'])) {
        case 'admin':
            header("Location: admin.html");
            break;
        case 'user':
            header("Location: test.html");
            break;
        default:
            header("Location: dermatologist.php");
            break;
    }
    exit();
}
 else {
        $_SESSION['login_error'] = "Invalid email or password.";
        header("Location: loginn.php");
        exit();
    }
}
?>
