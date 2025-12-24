<?php
header("Content-Type: application/json");
include "connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents("log.txt", json_encode($_POST) . "\n", FILE_APPEND);

    if (!isset($_POST['user_id'])) {
        echo json_encode(["success" => false, "message" => "Missing user_id"]);
        exit;
    }

    $user_id = intval($_POST['user_id']);

    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $success = $stmt->execute([$user_id]);

        if ($success && $stmt->rowCount() > 0) {
            echo json_encode(["success" => true, "message" => "Account deleted"]);
        } else {
            echo json_encode(["success" => false, "message" => "No account deleted, user may not exist"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Only POST requests are allowed"]);
}
?>
