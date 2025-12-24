<?php 
  session_start();
  if(isset($_SESSION['unique_id'])){
    header("location: users2.php");
    exit;
  }
?>

<?php include_once "header2.php"; ?>
<body>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: url('pic/faces.png') no-repeat center center fixed;
      background-size: cover;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .wrapper {
      background-color: rgba(255, 255, 255, 0.5);
      padding: 20px 30px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.3);
      max-width: 400px;
      width: 90%;
    }

    section.form.signup header {
      font-size: 28px;
      font-weight: 700;
      text-align: center;
      margin-bottom: 30px;
      color: #333;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .error-text {
      color: red;
      font-size: 14px;
      margin-bottom: 10px;
      min-height: 18px;
      text-align: center;
    }

    .field {
      display: flex;
      flex-direction: column;
      width: 100%;
    }

    .field input[type="text"],
    .field input[type="file"] {
      padding: 10px 12px;
      border: 1.5px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
      transition: border-color 0.3s ease;
    }

    .field input[type="text"]:focus,
    .field input[type="file"]:focus {
      border-color: #ff69b4;
      outline: none;
    }

    .field.button input[type="submit"] {
      background-color: #ff69b4;
      color: white;
      font-size: 18px;
      font-weight: 700;
      border: none;
      border-radius: 50px;
      padding: 15px 0;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .field.button input[type="submit"]:hover {
      background-color: #ff1493;
      transform: scale(1.05);
    }

    .link {
      text-align: center;
      margin-top: 20px;
      font-size: 15px;
      color: #555;
    }

    .link a {
      color: #ff69b4;
      text-decoration: none;
      font-weight: 600;
    }

    .link a:hover {
      text-decoration: underline;
    }

    html, body {
      height: 100%;
    }

    label {
      font-weight: 600;
      color: #555;
      margin-bottom: 5px;
    }
  </style>

  <div class="wrapper">
    <section class="form signup">
      <header>Let's start</header>
      <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="error-text"></div>

        <div class="field input">
          <input type="text" name="fname" placeholder="First name" required>
        </div>

        <div class="field input">
          <input type="text" name="lname" placeholder="Last name" required>
        </div>

        <div class="field image">
          <label for="image">Select Image</label>
          <input type="file" id="image" name="image" accept="image/x-png,image/gif,image/jpeg,image/jpg" required>
        </div>

        <div class="field button">
          <input type="submit" name="submit" value="Continue to Chat">
        </div>
      </form>
      <div class="link">Already signed up? <a href="login2.php">Login now</a></div>
    </section>
  </div>

  <script src="javascript/pass-show-hide.js"></script>
  <script src="javascript/signup.js"></script>
</body>
</html>
