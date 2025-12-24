<?php
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['comment_id']) && is_numeric($_POST['comment_id']) && $_POST['comment_id'] > 0) {
        $comment_id = intval($_POST['comment_id']);
        $stmt = $conn->prepare("DELETE FROM Comments WHERE comment_id = ?");
        if ($stmt->execute([$comment_id])) {
            header("Location: admin.html");
            exit;
        } else {
            echo "Error deleting comment.";
            exit;
        }
    } else {
        echo "Invalid comment ID.";
        exit;
    }
} else {
    echo "Invalid request method.";
    exit;
}
?>