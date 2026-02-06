<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = new mysqli("localhost", "root", "", "bonsai");
if ($conn->connect_error) {
    die("Kết nối DB thất bại");
}
$conn->set_charset("utf8mb4");

$isLogin = isset($_SESSION['user']) && !empty($_SESSION['user']);
?>