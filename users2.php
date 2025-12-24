<?php 
  session_start();
  include_once "php/config.php";
  if(!isset($_SESSION['unique_id'])){
    header("location: login2.php");
  }
?>
<?php include_once "header2.php"; ?>
<body>
  <style>
    body {
  background-color: #ffe4e1; /* MistyRose Pink */
}

.wrapper {
  border: 2px solid #ff69b4; /* HotPink border */
  border-radius: 15px;
  padding: 10px;
}

.logout {
  background-color: #ffb6c1; /* LightPink */
  color: white;
  padding: 8px 12px;
  border-radius: 20px;
  text-decoration: none;
}

.logout:hover {
  background-color: #ff69b4; /* HotPink */
}
.logout {
  background: linear-gradient(135deg, #ff9acb, #ff69b4);
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 30px;
  font-size: 16px;
  font-weight: bold;
  text-transform: uppercase;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 4px 10px rgba(255, 105, 180, 0.5);
}

.logout:hover {
  background: linear-gradient(135deg, #ff69b4, #e75480);
  transform: scale(1.05);
  box-shadow: 0 6px 14px rgba(255, 105, 180, 0.6);
}

  </style>
  
  <div class="wrapper">
    <section class="users">
      <header>
        <div class="content">
          <?php 
            $sql = mysqli_query($conn, "SELECT * FROM Users WHERE unique_id = {$_SESSION['unique_id']}");
            if(mysqli_num_rows($sql) > 0){
              $row = mysqli_fetch_assoc($sql);
            }
          ?>
         <img src="<?php echo 'pic/' . $row['profile']; ?>" alt="">

          <div class="details">
            <span><?php echo $row['fname']. " " . $row['lname'] ?></span>
            <p><?php echo $row['status']; ?></p>
          </div>
        </div>
       <button class="logout" id="btn-close">&times;</button>
<script>
  document.getElementById('btn-close').addEventListener('click', () => {
    window.location.href = 'test.html';
  });
</script>

      </header>
      <div class="search">
        <span class="text">Select an user to start chat</span>
        <input type="text" placeholder="Enter name to search...">
        <button><i class="fas fa-search"></i></button>
      </div>
      <div class="users-list">
  
      </div>
    </section>
  </div>

  <script src="javascript/users.js"></script>

</body>
</html>
