<?php
require_once "../config/db.php";

$id=$_GET['id'];

$conn->query("
UPDATE import_receipts
SET status='cancelled'
WHERE id=$id
");

header("Location:adminipd.php");
exit;
?>