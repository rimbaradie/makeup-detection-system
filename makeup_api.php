<?php
header('Content-Type: application/json');

// Get the raw POST data from JavaScript
$data = json_decode(file_get_contents("php://input"));
$faceWidth = $data->face_width ?? 0;

// Include your existing database connection
include 'connect.php';

// Set default recommendation filter
$filter = "";

// Decide the product type based on face width
if ($faceWidth < 100) {
    $filter = "small"; // for small faces
} elseif ($faceWidth < 180) {
    $filter = "medium"; // for medium faces
} else {
    $filter = "wide"; // for wide faces
}

try {
    // Query products that match the filter
    $stmt = $conn->prepare("SELECT name, description, image_url FROM products WHERE face_type = :face_type");
    $stmt->bindParam(':face_type', $filter);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "products" => $products
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
