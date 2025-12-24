<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "SkinGlam");

if (isset($_POST['user_id'], $_POST['product_id'], $_POST['comment'])) {
    $user_id = intval($_POST['user_id']);
    $product_id = intval($_POST['product_id']);
    $comment = trim($_POST['comment']);

    $stmt = $conn->prepare("INSERT INTO Comments (user_id, product_id, comment_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $product_id, $comment);
    $stmt->execute();

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
}
?>
