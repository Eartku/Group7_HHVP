<?php
require_once "db.php";

if (!$isLogin) {
    header("Location: login.php");
    exit();
}
?>