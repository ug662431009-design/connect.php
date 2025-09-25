<?php
include 'connect.php';

if(isset($_POST['submit'])){
    $SKU = trim($_POST['SKU']);
    $Name = trim($_POST['Name']);
    $Description = trim($_POST['Description']);
    $Price = trim($_POST['Price']);
    $StockQuantity = trim($_POST['StockQuantity']);
    $CategoryID = trim($_POST['CategoryID']);
    $SupplierID = trim($_POST['SupplierID']);

    // ตรวจสอบ SKU ซ้ำ
    $check = $conn->prepare("SELECT * FROM product WHERE SKU=?");
    $check->bind_param("s", $SKU);
    $check->execute();
    $res = $check->get_result();
    if($res->num_rows > 0){
        echo "<p style='color:red;'>SKU นี้มีอยู่แล้ว!</p>";
    } else {
        $sql = "INSERT INTO product (SKU, Name, Description, Price, StockQuantity, CategoryID, SupplierID) VALUES (?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        if(!$stmt) die("Prepare failed: ".$conn->error);
        $stmt->bind_param("sssdiis", $SKU, $Name, $Description, $Price, $StockQuantity, $CategoryID, $SupplierID);
        if($stmt->execute()){
            echo "<p style='color:green;'>เพิ่มสินค้าสำเร็จ ✅</p>";
        } else {
            echo "<p style='color:red;'>เกิดข้อผิดพลาด: ".$stmt->error."</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Add Product</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<style>
body { background-color: #fff8f0; font-family: 'Poppins', sans-serif; }
.card { border-radius:15px; box-shadow:0 4px 6px rgba(0,0,0,0.1); padding:20px; margin-top:50px; background-color:#fff5f7;}
input, textarea { border-radius:10px; border:1px solid #ffc0cb; padding:5px 10px; }
.btn-success { background-color:#ffdac1; border-color:#ffdac1; color:#000;}
</style>
</head>
<body>
<div class="container d-flex justify-content-center">
    <div class="card col-md-6">
        <h2 class="text-center mb-4">Add Product 🛒</h2>
         <a href="index.php" class="btn btn-info btn-lg mx-2">🏠 กลับหน้าหลัก</a>
        <form method="post">
            <input class="form-control mb-2" type="text" name="SKU" placeholder="SKU" required>
            <input class="form-control mb-2" type="text" name="Name" placeholder="Product Name" required>
            <textarea class="form-control mb-2" name="Description" placeholder="Description"></textarea>
            <input class="form-control mb-2" type="number" step="0.01" name="Price" placeholder="Price" required>
            <input class="form-control mb-2" type="number" name="StockQuantity" placeholder="Stock Quantity" required>
            <input class="form-control mb-2" type="number" name="CategoryID" placeholder="CategoryID" value="1" required>
            <input class="form-control mb-2" type="number" name="SupplierID" placeholder="SupplierID" value="1" required>
            <button class="btn btn-success w-100" type="submit" name="submit">Add Product</button>
        </form>
    </div>
</div>
</body>
</html>
