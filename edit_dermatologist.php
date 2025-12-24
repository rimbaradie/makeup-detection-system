<?php
session_start();
require 'connect.php';

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get current user info
$stmt = $conn->prepare("SELECT * FROM Users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle errors
if (!$user) {
    echo "User not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Dermatologist Info</title>
  <style>
    body {
  font-family: Arial, sans-serif;
  background: #ffe6f0; /* Light pink */
  padding: 40px;
}

    form {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      width: 400px;
      margin: auto;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    input {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    button {
      padding: 10px 20px;
      background-color: #007BFF;
      border: none;
      color: white;
      cursor: pointer;
      border-radius: 4px;
    }
    .response {
      text-align: center;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
  <div style="background-color: #d4edda; color: #155724; padding: 10px; text-align: center; border-radius: 5px; margin-bottom: 20px;">
    ✅ Updated successfully.
  </div>
<?php endif; ?>


  <form action="update_dermatologist.php" method="POST" enctype="multipart/form-data">
    <h2>Edit Your Info</h2>

    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">

    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    <input type="password" name="password" placeholder="Leave blank to keep current password">
    <input type="tel" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>" required>
    <input type="text" name="specialty" value="<?= htmlspecialchars($user['specialty']) ?>" required>
    <input type="text" name="location" value="<?= htmlspecialchars($user['location']) ?>" required>
    <input type="date" name="next_available" value="<?= htmlspecialchars($user['next_available']) ?>" required>
    <input type="file" name="profile" accept="image/*">

    <button type="submit">Update Info</button>
  </form>

</body>
</html>
