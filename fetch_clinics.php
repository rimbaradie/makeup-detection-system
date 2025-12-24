<?php
header('Content-Type: application/json');

// Include your database connection
require 'connect.php'; // This file should define $conn (PDO instance)

try {
    // Query all clinic data
    $stmt = $conn->query("SELECT clinic_id, name, address, contact, hours FROM Clinics");
    $clinics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Send JSON response
    echo json_encode($clinics);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch clinics: ' . $e->getMessage()
    ]);
}
?>
