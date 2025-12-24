<?php
require 'connect.php';

$stmt = $conn->query("SELECT * FROM ProductSales ORDER BY sale_id DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row) {
    $status = (date('Y-m-d') >= $row['start_date'] && date('Y-m-d') <= $row['end_date']) ? "Active" : "Inactive";
    $code = "DSC" . $row['sale_id']; // Example discount code

    echo "<tr>
            <td>{$code}</td>
            <td>{$row['product_id']}</td>
            <td>{$row['discount_percentage']}%</td>
            <td>{$row['start_date']}</td>
            <td>{$row['end_date']}</td>
            <td><span class='status-{$status}'>{$status}</span></td>
            <td>
                <button class='btn btn-sm btn-warning edit-btn'
                data-id='{$row['sale_id']}'
                data-product='{$row['product_id']}'
                data-discount='{$row['discount_percentage']}'
                data-start='{$row['start_date']}'
                data-end='{$row['end_date']}'>
                <i class='fas fa-edit'></i>
                </button>
                <button class='btn btn-sm btn-danger' data-id='{$row['sale_id']}'>
                  <i class='fas fa-ban'></i>
                </button>
            </td>
          </tr>";
}
?>
