<?php
include 'connect.php';
$id = $_GET['id'];
$conn->query("DELETE FROM customer WHERE CustomerID = $id");
header("Location: customer_admin.php");
exit();
?>
