<?php
header('Content-Type: application/json');
require 'connect.php';

// Check if user_id is provided
if (!isset($_POST['user_id']) || empty($_POST['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing user ID']);
    exit();
}

$userId = $_POST['user_id'];

try {
    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT * FROM Users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Optionally prepend profile path if needed
        if (!empty($user['profile'])) {
            $user['profile'] = 'pic/' . $user['profile']; // adjust path if different
        }

        echo json_encode(['status' => 'success', 'user' => $user]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>