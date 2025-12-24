<?php
header('Content-Type: application/json');
require 'connect.php';

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($data['userId']) || empty($data['userId'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing or empty userId']);
    exit;
}

$id = $data['userId'];

try {
    $stmt = $conn->prepare("DELETE FROM Users WHERE user_id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'deleted']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found or already deleted']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>