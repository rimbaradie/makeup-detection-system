<?php
require 'connect.php';

// Get the search term from query parameter if set
$searchTerm = '';
if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $searchTerm = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM Products WHERE name LIKE :search ORDER BY name ASC");
    $stmt->execute(['search' => "%$searchTerm%"]);
} else {
    $stmt = $conn->query("SELECT * FROM Products ORDER BY name ASC");
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($products as $p) {
    $name = htmlspecialchars($p['name']);
    $brand = htmlspecialchars($p['brand']);
    $category = htmlspecialchars($p['category']);
    $skin_type = htmlspecialchars($p['skin_type']);
    $price = number_format((float)$p['price'], 2);
    $stock_quantity = (int)$p['stock_quantity'];
    $image_product = !empty($p['image_product']) ? htmlspecialchars($p['image_product']) : 'https://via.placeholder.com/100';
    $image2_product = !empty($p['image2_product']) ? htmlspecialchars($p['image2_product']) : '';
    $description = htmlspecialchars($p['description']);

    echo '<div class="product-card">';
    echo    '<img src="' . $image_product . '" alt="' . $name . '" class="product-img">';
    echo    '<div class="product-details">';
    echo        '<h4>' . $name . '</h4>';
    echo        '<p>Brand: ' . $brand . '</p>';
    echo        '<p>Category: ' . $category . '</p>';
    echo        '<p>Skin Type: ' . $skin_type . '</p>';
    echo        '<p>Price: $' . $price . '</p>';
    echo        '<p>Stock: ' . $stock_quantity . '</p>';
    if ($description) {
        echo '<p>Description: ' . $description . '</p>';
    }
    echo        '<div class="product-actions">';
    echo          '<button class="btn btn-sm btn-info view-product-btn" data-id="' . $p['product_id'] . '"><i class="fas fa-eye"></i> View</button>';
    echo          '<button class="btn btn-sm btn-warning edit-product-btn" data-id="' . $p['product_id'] . '"><i class="fas fa-edit"></i> Edit</button>';
    echo          '<button class="btn btn-sm btn-danger delete-product-btn" data-id="' . $p['product_id'] . '"><i class="fas fa-trash"></i> Delete</button>';
    echo        '</div>';
    echo    '</div>';
    echo '</div>';
}
?>
