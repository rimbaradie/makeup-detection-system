<?php
header('Content-Type: application/json');
require 'connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing ID']);
    exit;
}

$id = (int)$_GET['id'];

try {
    $stmt = $conn->prepare("
        SELECT 
            user_id, fname, lname, username, email,
            phone_number, specialty, location, profile,
            next_available, status 
        FROM Users WHERE user_id = ?
    ");
    $stmt->execute([$id]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        foreach ($user as $key => $value) {
            $user[$key] = htmlspecialchars($value);
        }

        $user['profile_url'] = !empty($user['profile']) ? 'pic/' . $user['profile'] : 'pic/default.png';

        echo json_encode(['status' => 'success', 'data' => $user]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>