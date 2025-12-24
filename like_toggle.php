<?php
header('Content-Type: application/json');
file_put_contents("debug_like.txt", json_encode($_POST));

// Validate inputs
if (!isset($_POST['user_id']) || !isset($_POST['product_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing user_id or product_id',
        'error' => 'invalid_input'
    ]);
    exit;
}

$user_id = (int)$_POST['user_id'];
$product_id = (int)$_POST['product_id'];

// Validate values
if ($user_id <= 0 || $product_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid user_id or product_id',
        'error' => 'invalid_input'
    ]);
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "SkinGlam");
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed',
        'error' => 'db_error'
    ]);
    exit;
}

// Check if already liked
$check_sql = "SELECT like_id FROM Likes WHERE user_id = ? AND product_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $user_id, $product_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    // Unlike
    $delete_sql = "DELETE FROM Likes WHERE user_id = ? AND product_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $user_id, $product_id);
    if ($delete_stmt->execute()) {
        $response = [
            'success' => true,
            'liked' => false,
            'message' => 'Product unliked'
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'Failed to unlike',
            'error' => 'db_error'
        ];
    }
    $delete_stmt->close();
} else {
    // Like
    $insert_sql = "INSERT INTO Likes (user_id, product_id) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ii", $user_id, $product_id);
    if ($insert_stmt->execute()) {
        $response = [
            'success' => true,
            'liked' => true,
            'message' => 'Product liked'
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'Failed to like',
            'error' => 'db_error'
        ];
    }
    $insert_stmt->close();
}

$check_stmt->close();
$conn->close();

echo json_encode($response);
?>
