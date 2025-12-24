<?php
include 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['userId'];
$name = $data['username'];
$email = $data['email'];
$role = $data['role'];

$sql = "UPDATE Users SET name = ?, email = ?, role = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $name, $email, $role, $id);

if ($stmt->execute()) {
    echo json_encode(["status" => "updated"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}

$conn->close();
?>
