<?php
include 'connect.php';
$id = $_GET['id'];
$conn->query("DELETE FROM product WHERE ProductID = $id");
header("Location: product_admin.php");
exit();
?>
