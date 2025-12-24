<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "SkinGlam");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get email from form
$email = trim($_POST['email']);

// Basic validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format.";
    exit;
}

// Check if already subscribed
$stmt = $conn->prepare("SELECT * FROM Subscribers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "You're already subscribed!";
} else {
    // Insert new subscriber
    $stmt = $conn->prepare("INSERT INTO Subscribers (email) VALUES (?)");
    $stmt->bind_param("s", $email);
    
    if ($stmt->execute()) {
        echo "Thank you for subscribing!";
    } else {
        echo "Error: Could not subscribe.";
    }
}

$stmt->close();
$conn->close();
?>
