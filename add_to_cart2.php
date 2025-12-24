<?php
$conn = new mysqli("localhost","root","","SkinGlam");
$user  = (int)$_POST['user_id'];
$prod  = (int)$_POST['product_id'];
$qty   = max(1, (int)$_POST['quantity']);

// هل العنصر موجود مسبقاً؟
$check = $conn->prepare("SELECT quantity FROM Cart_Items WHERE user_id=? AND product_id=?");
$check->bind_param("ii",$user,$prod);
$check->execute();
$res = $check->get_result();
if ($res->num_rows) {
    // حدّث الكمية
    $row = $res->fetch_assoc();
    $newQty = $row['quantity'] + $qty;
    $upd = $conn->prepare("UPDATE Cart_Items SET quantity=? WHERE user_id=? AND product_id=?");
    $upd->bind_param("iii",$newQty,$user,$prod);
    $upd->execute();
} else {
    // حطّ سجل جديد
    $ins = $conn->prepare("INSERT INTO Cart_Items(user_id,product_id,quantity) VALUES (?,?,?)");
    $ins->bind_param("iii",$user,$prod,$qty);
    $ins->execute();
}
echo "OK";
