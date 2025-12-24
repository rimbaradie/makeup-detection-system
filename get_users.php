<?php
include 'connect.php';

try {
    $stmt = $conn->query("SELECT * FROM Users WHERE role IN ('User', 'admin')");

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr data-user-id='" . htmlspecialchars($row['user_id']) . "'>
                    <td>" . htmlspecialchars($row['user_id']) . "</td>
                    <td>" . htmlspecialchars($row['username']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>" . htmlspecialchars($row['skin_type']) . "</td>
                    <td>" . htmlspecialchars($row['role']) . "</td>
                    <td>
                      <button class='btn btn-sm btn-info view-user-btn'>View</button>
                      <button class='btn btn-sm btn-warning edit-user-btn'>Edit</button>
                      <button class='btn btn-sm btn-danger delete-user-btn'>Delete</button>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No users found</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='6'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}

$conn = null;
?>