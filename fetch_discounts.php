<?php
// Include your database connection
require_once 'connect.php';

try {
    // Prepare the SQL query with a JOIN to get product name
    $stmt = $conn->prepare("
        SELECT ps.sale_id,  ps.product_id, p.name AS product_name, 
               ps.discount_percentage, ps.start_date, ps.end_date
        FROM ProductSales ps
        LEFT JOIN Products p ON ps.product_id = p.product_id
        ORDER BY ps.sale_id DESC
    ");

    $stmt->execute();

    // Start outputting table rows
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Determine status based on dates
        $today = date('Y-m-d');
        if ($row['start_date'] <= $today && $row['end_date'] >= $today) {
            $status = '<span class="status-active">Active</span>';
        } else {
            $status = '<span class="status-inactive">Inactive</span>';
        }

     echo "<tr>";
echo "<td>" . htmlspecialchars($row['sale_id']) . "</td>";
echo "<td>" . htmlspecialchars($row['product_name'] ?: 'Unknown') . "</td>";
echo "<td>" . htmlspecialchars($row['discount_percentage']) . "%</td>";
echo "<td>" . htmlspecialchars($row['start_date']) . "</td>";
echo "<td>" . htmlspecialchars($row['end_date']) . "</td>";
echo "<td>" . $status . "</td>";
echo "<td>
        <button class='btn btn-sm btn-warning tooltip' data-tooltip='Edit' data-id='" . $row['sale_id'] . "'>
            <i class='fas fa-edit'></i>
        </button>
        <button class='btn btn-sm btn-danger tooltip' data-tooltip='Deactivate' data-id='" . $row['sale_id'] . "'>
            <i class='fas fa-ban'></i>
        </button>
      </td>";
echo "</tr>";

    }
} catch (PDOException $e) {
    echo "<tr><td colspan='7'>Error fetching discounts: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>
