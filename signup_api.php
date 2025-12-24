<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "SkinGlam");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$fname     = $_POST['fname']     ?? '';
$lname     = $_POST['lname']     ?? '';
$username  = $_POST['username']  ?? '';
$email     = $_POST['email']     ?? '';
$password  = $_POST['password']  ?? '';
$skin_type = $_POST['skin_type'] ?? '';
$imageBase64 = $_POST['profile'] ?? '';

if (empty($fname) || empty($lname) || empty($username) || empty($email) || empty($password) || empty($skin_type)) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

$stmt = $conn->prepare("SELECT user_id FROM Users WHERE email = ? OR username = ?");
$stmt->bind_param("ss", $email, $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email or Username already exists"]);
    exit;
}
$stmt->close();

// Decode and save Base64 image
$profilePath = null;
if (!empty($imageBase64)) {
    $imageName = uniqid() . ".jpg";
    $imagePath = "pic/" . $imageName;

    $imageData = base64_decode($imageBase64);
    if ($imageData === false) {
        echo json_encode(["status" => "error", "message" => "Invalid image data"]);
        exit;
    }

    if (file_put_contents($imagePath, $imageData)) {
        $profilePath = $imageName; // ✅ فقط اسم الصورة
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to save image"]);
        exit;
    }
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO Users (fname, lname, username, email, password, skin_type, profile) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $fname, $lname, $username, $email, $hashed_password, $skin_type, $profilePath);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id; // ✅ Get inserted user ID
    echo json_encode([
        "status" => "success",
        "message" => "Account created successfully",
        "user_id" => $user_id
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Signup failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
