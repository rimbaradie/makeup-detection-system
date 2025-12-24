<?php
header("Content-Type: application/json; charset=UTF-8");
include "connect.php";
$conn->exec("SET NAMES utf8mb4");

try {
    if (isset($_GET['product_id'])) {
        $product_id = intval($_GET['product_id']);

        $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->execute([$product_id]);

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // ضمان وجود رابط الصورة الكامل
            if (!empty($row['image_product'])) {
                $row['image_product'] = "http://192.168.1.108/SkinGlamProject/" . ltrim($row['image_product'], '/');
            }

            // إرسال JSON طبيعي مع الحفاظ على علامات مثل (Paula’s)
            echo json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
            echo json_encode(["error" => "Product not found"], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(["error" => "No ID provided"], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    echo json_encode(["error" => "Server error", "details" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
