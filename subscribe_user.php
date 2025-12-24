<?php
header('Content-Type: application/json');
require 'connect.php'; // فيه الاتصال بقاعدة البيانات (PDO)

$input = json_decode(file_get_contents("php://input"), true);

$email = isset($input['email']) ? trim($input['email']) : '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email']);
    exit;
}

// Check if email already subscribed
$stmt = $conn->prepare("SELECT COUNT(*) FROM Subscribers WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already subscribed']);
    exit;
}

// Insert
$insert = $conn->prepare("INSERT INTO Subscribers (email, subscribed_at) VALUES (?, NOW())");
if ($insert->execute([$email])) {
    echo json_encode(['success' => true, 'message' => 'Subscribed successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
