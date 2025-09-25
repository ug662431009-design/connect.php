<?php
include 'connect.php';
session_start();

// รับ ProductID จาก query string
$product_id = $_GET['ProductID'] ?? 0;

// ดึงข้อมูลสินค้าจากฐานข้อมูล
$sql = "SELECT * FROM product WHERE ProductID = ? LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    die("❌ ไม่พบสินค้า");
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>รายละเอียดสินค้า</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<style>
body {
    background-color: #fff8f0;
    font-family: 'Poppins', sans-serif;
}
.card { 
    border-radius: 15px; 
    box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
    background-color: #fff5f7; 
}
.bottom-buttons {
    position: fixed;
    bottom: 10px;
    width: 100%;
    text-align: center;
}
.card p span {
    font-weight: bold;
}
</style>
</head>
<body>
<div class="container mt-4">
    <div class="card p-4 col-md-6 mx-auto">
        <h2 class="mb-3"><?= htmlspecialchars($product['Name']) ?></h2>
        <p><span>ProductID:</span> <?= $product['ProductID'] ?></p>
        <p><span>SKU:</span> <?= htmlspecialchars($product['SKU']) ?></p>
        <p><span>Description:</span> <?= htmlspecialchars($product['Description']) ?></p>
        <p><span>Price:</span> <?= number_format($product['Price'],2) ?> บาท</p>
        <p><span>StockQuantity:</span> <?= $product['StockQuantity'] ?></p>
        <p><span>CategoryID:</span> <?= $product['CategoryID'] ?></p>
        <p><span>SupplierID:</span> <?= $product['SupplierID'] ?></p>
    </div>
</div>

<!-- ปุ่มด้านล่าง -->
<div class="bottom-buttons">
    <a href="index.php" class="btn btn-info btn-lg mx-2">🏠 หน้าแรก</a>
</div>

</body>
</html>
