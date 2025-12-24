<?php
header("Content-Type: text/html; charset=UTF-8");
// الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = ""; // أضيفي الباسورد إذا لزم
$dbname = "SkinGlam"; // غيّري اسم قاعدة البيانات

$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// جلب بيانات الأطباء بدون location
$sql = "SELECT username, specialty, email, phone_number, profile FROM Users WHERE role='dermatologist'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

        // صورة البروفايل (نص مسار)
        $profileImg = 'pic/' . htmlspecialchars($row["profile"]);


        echo '<div class="derm-card">';
        echo '<img src="' . $profileImg . '" alt="' . htmlspecialchars($row["profile"]) . '" class="derm-photo">';
        echo '<h3>' . htmlspecialchars($row["username"]) . '</h3>';
        echo '<p class="derm-specialty">' . htmlspecialchars($row["specialty"]) . '</p>';
        echo '<p class="derm-bio">Contact: ' 
            . htmlspecialchars($row["email"]) . ' / ' . htmlspecialchars($row["phone_number"]) . '</p>';
        echo '<div class="derm-social">';
        echo '<a href="#"><i class="fab fa-twitter"></i></a>';
        echo '<a href="#"><i class="fab fa-linkedin-in"></i></a>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo "<p>No dermatologists found.</p>";
}

$conn->close();
?>
