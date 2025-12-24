<?php
session_start();
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname     = trim($_POST['fname']);
    $lname     = trim($_POST['lname']);
    $username  = trim($_POST['username']);
    $email     = trim($_POST['email']);
    $password  = $_POST['password'];
    $skin_type = $_POST['skin_type'];

    // Validate inputs basic check
    if (strlen($password) < 6) {
        $_SESSION['signup_error'] = "Password must be at least 6 characters.";
        header("Location: loginn.php");
        exit;
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['signup_error'] = "Email already exists.";
        header("Location: loginn.php");
        exit;
    }

    // Generate unique_id and ensure uniqueness (loop)
    do {
        $unique_id = rand(1000000000, 2147483647);
        $checkStmt = $conn->prepare("SELECT * FROM users WHERE unique_id = ?");
        $checkStmt->execute([$unique_id]);
    } while ($checkStmt->rowCount() > 0);

    // Handle profile picture upload
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile']['tmp_name'];
        $fileName = $_FILES['profile']['name'];
        $fileSize = $_FILES['profile']['size'];
        $fileType = $_FILES['profile']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExtension, $allowedfileExtensions)) {
            $_SESSION['signup_error'] = "Allowed image types: jpg, jpeg, png, gif.";
            header("Location: loginn.php");
            exit;
        }

        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = './pic/';

        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }

        $dest_path = $uploadFileDir . $newFileName;

        if (!move_uploaded_file($fileTmpPath, $dest_path)) {
            $_SESSION['signup_error'] = "Error uploading profile picture.";
            header("Location: loginn.php");
            exit;
        }

        $profile_path = $newFileName; // store filename only
    } else {
        $_SESSION['signup_error'] = "Please upload a profile picture.";
        header("Location: loginn.php");
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into DB
    $insertStmt = $conn->prepare("INSERT INTO users (unique_id, fname, lname, username, email, password, skin_type, profile) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
   if ($insertStmt->execute([$unique_id, $fname, $lname, $username, $email, $hashed_password, $skin_type, $profile_path])) {
    // Get the newly inserted user_id
    $user_id = $conn->lastInsertId();

    // Store session variables
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['unique_id'] = $unique_id;

    // Redirect to success page
    header("Location: test.html");
    exit;
}
else {
    $_SESSION['signup_error'] = "Error creating account.";
    header("Location: loginn.php");
}
exit;

}
?>
