<?php
// Enable error reporting (for development)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=UTF-8');

$conn = new mysqli("localhost", "root", "", "SkinGlam");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

if (isset($_POST['comment_id'], $_POST['user_id'])) {
    $comment_id = intval($_POST['comment_id']);
    $user_id    = intval($_POST['user_id']);

    // Only delete the comment if it belongs to the user
    $stmt = $conn->prepare("DELETE FROM Comments WHERE comment_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $comment_id, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "You cannot delete this comment or it does not exist"]);
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
}
?>
