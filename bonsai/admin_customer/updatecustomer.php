<?php
session_start();
require "../config/db.php";

/* kiểm tra admin */
if (!isset($_SESSION['admin'])) {
    $_SESSION['admin_timeout'] = "Bạn đã thoát khỏi trang này quá lâu, để chắc bạn là admin hãy đăng nhập lại.";
    header("Location: ../adminlogin/admin_login.php");
    exit();
}

$id = $_POST['id'];
$fullname = $_POST['fullname'];
$username = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$status = $_POST['status'];
$password = $_POST['password'];

/* nếu có nhập password mới thì update */
if(!empty($password)){

    $password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "UPDATE users SET
        fullname='$fullname',
        username='$username',
        email='$email',
        phone='$phone',
        address='$address',
        status='$status',
        password='$password'
        WHERE id=$id";

}else{

    $sql = "UPDATE users SET
        fullname='$fullname',
        username='$username',
        email='$email',
        phone='$phone',
        address='$address',
        status='$status'
        WHERE id=$id";

}

mysqli_query($conn,$sql);

/* quay về danh sách khách hàng */
header("Location: customermanage.php");
exit();
?>