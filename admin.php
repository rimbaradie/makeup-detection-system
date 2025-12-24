<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Home</title>
</head>
<body>
  <h1>Welcome Admin: <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
  <p>This is the admin-only page.</p>
</body>
</html>
