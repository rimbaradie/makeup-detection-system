<?php
session_start();
include_once "config.php";

$fname = mysqli_real_escape_string($conn, $_POST['fname']);
$lname = mysqli_real_escape_string($conn, $_POST['lname']);
// جلب الإيميل والباسورد من السيشن
if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // جلب بيانات المستخدم من قاعدة البيانات
    $sql = mysqli_query($conn, "SELECT * FROM Users WHERE user_id = '{$user_id}'");
    if(mysqli_num_rows($sql) > 0){
        $user = mysqli_fetch_assoc($sql);
        $email = $user['email'];
        $password = $user['password']; // يمكنك استخدامه كما هو (مشفر)
    } else {
        echo "User not found.";
        exit();
    }
} else {
    echo "Session expired. Please login again.";
    exit();
}


if(!empty($fname) && !empty($lname) && !empty($email) && !empty($password)) {
    if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");

        // المستخدم لازم يكون موجود حتى نكمل
        if(mysqli_num_rows($sql) > 0){
            $unique_id = rand(time(), 1000000000);
           $encrypt_pass = $password;
            $img_name = "";

            if(isset($_FILES['image'])) {
                $img_name = $_FILES['image']['name'];
                $img_type = $_FILES['image']['type'];
                $tmp_name = $_FILES['image']['tmp_name'];

                $img_explode = explode('.', $img_name);
                $img_ext = strtolower(end($img_explode)); // لتحويل الامتداد لصغير

                $extensions = ["jpeg", "png", "jpg"];
                $types = ["image/jpeg", "image/jpg", "image/png"];

                if(in_array($img_ext, $extensions) && in_array($img_type, $types)) {
                    $time = time();
                    $new_img_name = $time . $img_name;

                    if(move_uploaded_file($tmp_name, "../pic/" . $new_img_name)) {
                        $img_name = $new_img_name;
                    } else {
                        echo "Failed to upload image.";
                        exit();
                    }
                } else {
                    echo "Please upload an image file - jpeg, png, jpg";
                    exit();
                }
            }

            // تحديث البيانات
            $update_query = "UPDATE users SET fname = '{$fname}', lname = '{$lname}', password = '{$encrypt_pass}', unique_id='{$unique_id}'";
            if(!empty($img_name)) {
                $update_query .= ", profile = '{$img_name}'";
            }
            $update_query .= " WHERE email = '{$email}'";

            if(mysqli_query($conn, $update_query)) {
    $sql_user = mysqli_query($conn, "SELECT unique_id FROM users WHERE email = '{$email}'");
    if(mysqli_num_rows($sql_user) > 0) {
        $user_data = mysqli_fetch_assoc($sql_user);
        $_SESSION['unique_id'] = $user_data['unique_id'];
echo "success";
exit();

    } else {
        echo "User updated but unique_id not found.";
    }
}
else {
                echo "Something went wrong while updating.";
            }

        } else {
            echo "No user found with this email. Update not possible.";
        }

    } else {
        echo "$email is not a valid email!";
    }
} else {
    echo "All input fields are required!";
}
?>
<script>
signupForm.onsubmit = (e) => {
  e.preventDefault();

  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php3/signup.php", true);
  xhr.onload = () => {
    if(xhr.readyState === XMLHttpRequest.DONE){
      if(xhr.status === 200){
        if(xhr.response === "success"){
          location.href = "users3.php"; // ✅ Do redirect here
        } else {
          errorText.textContent = xhr.response; // ❌ Show error if any
          errorText.style.display = "block";
        }
      }
    }
  }

  let formData = new FormData(signupForm);
  xhr.send(formData);
}

</script>
