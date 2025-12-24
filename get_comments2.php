<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "SkinGlam");

if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    $stmt = $conn->prepare("
        SELECT c.comment_id, c.user_id, CONCAT(u.fname, ' ', u.lname) AS username, c.comment_text AS comment, c.comment_date
        FROM Comments c 
        JOIN Users u ON c.user_id = u.user_id 
        WHERE c.product_id = ? 
        ORDER BY c.comment_date DESC
    ");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }

    echo json_encode($comments);
} else {
    echo json_encode(["error" => "product_id not set"]);
}
?>
