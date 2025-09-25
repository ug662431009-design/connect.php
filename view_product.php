<?php
include 'connect.php'; // เชื่อมฐานข้อมูล
session_start();

// ดึงข้อมูลสินค้าทั้งหมด เรียงตาม ProductID ขึ้นก่อน
$sql = "SELECT * FROM product ORDER BY ProductID ASC";
$result = $conn->query($sql);
if(!$result){
    die("เกิดข้อผิดพลาด: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>View Products</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<style>
body { 
    background-color: #fff8f0; 
    font-family:'Poppins', sans-serif; 
}
h2 { color:#ff6f91; margin-top:30px; }
.table { background-color:#fff5f7; border-radius:10px; }
.container { margin-top:30px; }
.btn-primary { background-color:#ffb6b9; border-color:#ffb6b9; }
</style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Product List 🛒</h2>

    <div class="text-center mb-3">
        <a href="index.php" class="btn btn-primary btn-lg">🏡 กลับหน้าหลัก</a>
    </div>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ProductID</th>
                <th>SKU</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>StockQuantity</th>
                <th>CategoryID</th>
                <th>SupplierID</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['ProductID'] ?></td>
                <td><?= htmlspecialchars($row['SKU']) ?></td>
                <td><?= htmlspecialchars($row['Name']) ?></td>
                <td><?= htmlspecialchars($row['Description']) ?></td>
                <td><?= number_format($row['Price'],2) ?></td>
                <td><?= $row['StockQuantity'] ?></td>
                <td><?= $row['CategoryID'] ?></td>
                <td><?= $row['SupplierID'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
