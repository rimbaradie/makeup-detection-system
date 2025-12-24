<?php
header('Content-Type: application/json');

require 'connect.php'; // ✅ Make sure this file sets up your $conn or $pdo

// Check required fields
if (isset($_POST['name'], $_POST['address'], $_POST['contact'], $_POST['hours'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $hours = $_POST['hours'];

    try {
        $stmt = $conn->prepare("INSERT INTO Clinics (name, address, contact, hours) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $address, $contact, $hours]);

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
}
?>
