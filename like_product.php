<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to like products',
        'error' => 'unauthorized'
    ]);
    exit;
}

// Validate product ID
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
if ($product_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid product ID',
        'error' => 'invalid_input'
    ]);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Database connection
$conn = new mysqli("localhost", "root", "", "SkinGlam");
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed',
        'error' => 'db_error'
    ]);
    exit;
}

// Check if like exists
$check_sql = "SELECT like_id FROM Likes WHERE user_id = ? AND product_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $user_id, $product_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    // Unlike the product
    $delete_sql = "DELETE FROM Likes WHERE user_id = ? AND product_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $user_id, $product_id);
    
    if ($delete_stmt->execute()) {
        $response = [
            'success' => true,
            'liked' => false,
            'message' => 'Product unliked successfully'
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'Failed to unlike product',
            'error' => 'db_error'
        ];
    }
    $delete_stmt->close();
} else {
    // Like the product
    $insert_sql = "INSERT INTO Likes (user_id, product_id) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ii", $user_id, $product_id);
    
    if ($insert_stmt->execute()) {
        $response = [
            'success' => true,
            'liked' => true,
            'message' => 'Product liked successfully'
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'Failed to like product',
            'error' => 'db_error'
        ];
    }
    $insert_stmt->close();
}

$check_stmt->close();
$conn->close();

echo json_encode($response);
?>