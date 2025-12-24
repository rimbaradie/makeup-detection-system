<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "SkinGlam");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Email and password are required"]);
    exit;
}

// تحضير الاستعلام للتحقق من المستخدم
$stmt = $conn->prepare("SELECT user_id, password, role FROM Users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // تحقق من كلمة المرور باستخدام password_verify
    if (password_verify($password, $user['password'])) {
        echo json_encode([
            "status" => "success",
            "message" => "Login successful",
            "user_id" => $user['user_id'],
            "role" => $user['role']
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Incorrect password"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "User not found"]);
}

$stmt->close();
$conn->close();
