<?php
header("Content-Type: application/json; charset=UTF-8");
$conn = new mysqli("localhost", "root", "", "SkinGlam");

$sql = "SELECT username, email, phone_number, specialty, location, profile, next_available, fname, lname FROM Users WHERE role='dermatologist'";
$result = $conn->query($sql);

$dermatologists = [];

while ($row = $result->fetch_assoc()) {
    $dermatologists[] = [
        "username" => $row["username"],
        "email" => $row["email"],
        "phone_number" => $row["phone_number"],
        "specialty" => $row["specialty"],
        "location" => $row["location"],
        "profileImage" => "http://192.168.1.108/SkinGlamProject/pic/" . $row["profile"],
        "nextAvailable" => $row["next_available"],
        "fullName" => $row["fname"] . " " . $row["lname"]
    ];
}

echo json_encode($dermatologists);
?>
