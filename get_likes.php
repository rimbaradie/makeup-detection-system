<?php
require 'connect.php';

$query = "
  SELECT l.like_id, u.username, p.name AS product_name, l.liked_at
  FROM likes l
  JOIN users u ON l.user_id = u.user_id
  JOIN products p ON l.product_id = p.product_id
";
$result = $conn->query($query);
$likes = [];
while ($row = $result->fetch_assoc()) {
  $likes[] = $row;
}
echo json_encode($likes);
?>