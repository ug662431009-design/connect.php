<?php
include 'connect.php';

$id = $_GET['id'];

// ดึงข้อมูลสินค้าปัจจุบัน
$result = $conn->query("SELECT * FROM product WHERE ProductID = $id");
$product = $result->fetch_assoc();

// เมื่อกดบันทึก
if(isset($_POST['update'])){
    $name = $_POST['Name'];
    $desc = $_POST['Description'];
    $price = $_POST['Price'];
    $stock = $_POST['StockQuantity'];

    $update = $conn->prepare("UPDATE product SET Name=?, Description=?, Price=?, StockQuantity=? WHERE ProductID=?");
    $update->bind_param("ssdii", $name, $desc, $price, $stock, $id);
    if($update->execute()){
        header("Location: product_admin.php"); // กลับไปหน้าหลัก
        exit();
    } else {
        echo "❌ เกิดข้อผิดพลาด: " . $conn->error;
    }
}
?>
<form method="post">
  <input type="text" name="Name" value="<?= htmlspecialchars($product['Name']) ?>">
  <textarea name="Description"><?= htmlspecialchars($product['Description']) ?></textarea>
  <input type="number" step="0.01" name="Price" value="<?= $product['Price'] ?>">
  <input type="number" name="StockQuantity" value="<?= $product['StockQuantity'] ?>">
  <button type="submit" name="update">บันทึก</button>
</form>
