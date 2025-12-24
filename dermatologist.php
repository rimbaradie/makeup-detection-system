<?php
session_start();
// يمكنك تعديل اسم المستخدم هنا حسب الحاجة
$dermatologistName = "Dr. Dya";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dermatologist Dashboard</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #ffe6f0; /* زهري فاتح */
      margin: 0;
    }

    nav {
      background-color: #ff69b4; /* Hot pink */
      padding: 20px 40px;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    nav h1 {
      margin: 0;
      font-size: 26px;
      font-weight: bold;
    }

    nav ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      gap: 25px;
    }

    nav ul li a {
      color: white;
      text-decoration: none;
      font-size: 17px;
      font-weight: 500;
      transition: all 0.3s;
    }

    nav ul li a:hover {
      text-decoration: underline;
      color: #fffbe6;
    }

    .container {
      padding: 40px;
    }

    .page {
      background-color: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      max-width: 800px;
      margin: 20px auto;
    }

    .page h2 {
      color: #ff69b4;
      font-size: 24px;
      margin-bottom: 10px;
    }

    .page p {
      font-size: 16px;
      color: #444;
    }

    .hidden {
      display: none;
    }
  </style>
</head>
<body>

  <nav>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>
    <ul>
      <li><a href="login3.php" onclick="showPage('chat')">Chat With Users</a></li>
      <li><a href="edit_dermatologist.php" onclick="showPage('info')">Edit My Info</a></li>
    </ul>
  </nav>

  <div class="container">
    <!-- Chat Page -->
    <div id="chat" class="page">
      <h2>Chat With Users</h2>
      <p>This section will show messages and allow you to reply to users (to be developed).</p>
    </div>

    <div id="chat" class="page">
      <h2>Edit My Info</h2>
      <p>This section will allow you to update your profile.</p>
    </div>

    <!-- Edit Info Page -->
    <div id="info" class="page hidden">
      <h2>Edit My Info</h2>
      <p>Coming soon: Form to update dermatologist profile.</p>
    </div>
  </div>

</body>
</html>
