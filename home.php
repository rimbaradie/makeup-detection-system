<?php
session_start();

// Allow only Admins
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header('Location: login.html');
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
