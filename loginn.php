<?php
session_start();
$signup_error = isset($_SESSION['signup_error']) ? $_SESSION['signup_error'] : '';
unset($_SESSION['signup_error']); 
$login_error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login & Sign Up</title>
  <style>
    /* ... نفس CSS الموجود عندك تماماً ... */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    #bgVideo {
      position: fixed;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: -2;
    }

    #videoOverlay {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: rgba(0, 0, 0, 0.4);
      z-index: -1;
    }

    body {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      width: 380px;
      min-height: 420px; 
      background: rgba(255, 255, 255, 0.3);
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      overflow: hidden;
      position: relative;
      backdrop-filter: blur(5px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .form-box {
      width: 200%;
      display: flex;
      transition: 0.5s ease-in-out;
    }

    .form-container {
      width: 50%;
      padding: 10px 40px 40px 40px;
    }

    h2 {
      color: #ff1493;
      margin-bottom: 20px;
      text-align: center;
      font-size: 32px;
      font-weight: bold;
    }

    input, select {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 10px;
      background: #f9f9f9;
      font-size: 16px;
      color: #333;
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-image: none;
    }

    select {
      background-image: url("data:image/svg+xml;utf8,<svg fill='hotpink' height='20' viewBox='0 0 24 24' width='20' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
      background-repeat: no-repeat;
      background-position: right 12px center;
      background-size: 18px;
    }

    input:focus, select:focus {
      border-color: #ff69b4;
      outline: none;
    }

    button {
      width: 100%;
      padding: 12px;
      background: #ff69b4;
      color: white;
      border: none;
      border-radius: 10px;
      font-weight: bold;
      margin-top: 10px;
      cursor: pointer;
      font-size: 16px;
    }

    button:hover {
      background: #ff1493;
    }

    .toggle-box {
      width: 100%;
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-bottom: 20px;
      padding: 20px;
      background-color: #f0f0f0;
    }

    .toggle-box button {
      flex: 1;
      padding: 10px 0;
      background: white;
      border: 2px solid #ff69b4;
      border-radius: 30px;
      color: #ff69b4;
      font-weight: bold;
      cursor: pointer;
      font-size: 16px;
      transition: 0.3s ease-in-out;
    }

    .toggle-box button:hover {
      background: #ffe6f0;
    }

    .toggle-box .active-btn {
      background: #ff1493;
      border-color: #ff1493;
      color: white;
    }

    .move-left {
      transform: translateX(0%);
    }

    .move-right {
      transform: translateX(-50%);
    }

    @media (max-width: 500px) {
      .container {
        width: 90%;
      }
    }

    .input-row {
      display: flex;
      gap: 10px;
    }

    .input-row input,
    .input-row select {
      flex: 1;
    }
  </style>
</head>
<body>

  <video autoplay muted loop id="bgVideo">
    <source src="pictures/videoo2.mov" type="video/mp4" />
    Your browser does not support the video tag.
  </video>

  <div id="videoOverlay"></div>

  <div class="container">
    <div class="toggle-box">
      <button id="loginBtn" class="active-btn">Login</button>
      <button id="signupBtn">Sign Up</button>
    </div>
    <div class="form-box move-left" id="formBox">
      <!-- Login Form -->
      <form class="form-container" action="login.php" method="POST">
        <h2>Login</h2>
        <?php if (!empty($login_error)): ?>
          <p style="color: hotpink; font-weight: bold; margin-bottom: 10px;"><?php echo htmlspecialchars($login_error); ?></p>
        <?php endif; ?>

        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Login</button>
      </form>

      <!-- Sign Up Form -->
      <form id="signupForm" class="form-container" action="signup.php" method="POST" enctype="multipart/form-data">
        <h2>Sign Up</h2>
        <?php if (!empty($signup_error)): ?>
          <p style="color: hotpink; margin-top: 10px; font-weight: bold;"><?php echo htmlspecialchars($signup_error); ?></p>
        <?php endif; ?>
        <div class="input-row">
          <input type="text" name="fname" placeholder="First Name" required />
          <input type="text" name="lname" placeholder="Last Name" required />
        </div>
        <div class="input-row">
          <input type="text" name="username" placeholder="Username" required />
          <input type="email" name="email" placeholder="Email" required />
        </div>
        <div class="input-row">
          <input type="password" name="password" placeholder="Password" required />
          <select name="skin_type" required>
            <option value="" disabled selected>Select Skin Type</option>
            <option value="Normal">Normal</option>
            <option value="Oily">Oily</option>
            <option value="Dry">Dry</option>
            <option value="Combination">Combination</option>
            <option value="Sensitive">Sensitive</option>
          </select>
        </div>
        <input type="file" name="profile" accept="image/*" required />
        <button type="submit">Sign Up</button>
      </form>
    </div>
  </div>

  <script>
    const loginBtn = document.getElementById('loginBtn');
    const signupBtn = document.getElementById('signupBtn');
    const formBox = document.getElementById('formBox');

    loginBtn.addEventListener('click', () => {
      formBox.classList.add('move-left');
      formBox.classList.remove('move-right');
      loginBtn.classList.add('active-btn');
      signupBtn.classList.remove('active-btn');
    });

    signupBtn.addEventListener('click', () => {
      formBox.classList.add('move-right');
      formBox.classList.remove('move-left');
      signupBtn.classList.add('active-btn');
      loginBtn.classList.remove('active-btn');
    });
  </script>

  <?php if (!empty($signup_error)): ?>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // افتح تبويب Sign Up عند وجود خطأ
      formBox.classList.add("move-right");
      formBox.classList.remove("move-left");

      signupBtn.classList.add("active-btn");
      loginBtn.classList.remove("active-btn");
    });
  </script>
  <?php endif; ?>

</body>
</html>
