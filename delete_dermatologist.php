<?php
header('Content-Type: application/json');
require 'connect.php';

if (!empty($_POST['dermatologist_id'])) {
    $id = $_POST['dermatologist_id'];

    try {
        // Optionally: Fetch profile filename to delete image file too (if you want)
        $stmtSelect = $conn->prepare("SELECT profile FROM Users WHERE user_id = ?");
        $stmtSelect->execute([$id]);
        $profile = $stmtSelect->fetchColumn();

        // Delete the dermatologist
        $stmt = $conn->prepare("DELETE FROM Users WHERE user_id = ?");
        $stmt->execute([$id]);

        // Delete profile image file if exists and not default
        if ($profile && file_exists('pic/' . $profile)) {
            unlink('pic/' . $profile);
        }

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing dermatologist ID.']);
}
?>