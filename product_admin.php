<?php
session_start();
include 'connect.php';

// ตรวจสอบว่าเป็น admin
if(!isset($_SESSION['AdminID'])){
    header("Location: admin_login.php");
    exit();
}

// เพิ่มสินค้า
if(isset($_POST['add_product'])){
    $sku = $_POST['SKU'];
    $name = $_POST['Name'];
    $desc = $_POST['Description'];
    $price = $_POST['Price'];
    $stock = $_POST['StockQuantity'];
    $category = $_POST['CategoryID'];
    $supplier = $_POST['SupplierID'];

    $stmt = $conn->prepare("INSERT INTO product (SKU, Name, Description, Price, StockQuantity, CategoryID, SupplierID) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdiii", $sku, $name, $desc, $price, $stock, $category, $supplier);

    if($stmt->execute()){
        $msg = "✅ เพิ่มสินค้าเรียบร้อย!";
    } else {
        $msg = "❌ เกิดข้อผิดพลาด: " . $stmt->error;
    }
}

// ดึงรายการสินค้า
$result = $conn->query("SELECT * FROM product ORDER BY ProductID ASC");
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Admin - Products</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<style>
body { font-family:'Comic Neue', cursive; background:linear-gradient(135deg,#ffe6f0,#e6f7ff); min-height:100vh; }
.container{margin-top:30px;}
.card{border-radius:15px; padding:20px; background-color:#fff5f7; box-shadow:0 4px 6px rgba(0,0,0,0.1);}
input, select{border-radius:10px; border:1px solid #ffc0cb; padding:5px 10px; margin-bottom:10px;}
.btn-primary{background-color:#ffb6b9;border:none;}
</style>
</head>
<body>
<div class="container">
<h2>🛒 จัดการสินค้า</h2>

<?php if(isset($msg)) echo "<p>$msg</p>"; ?>

<!-- ฟอร์มเพิ่มสินค้า -->
<div class="card mb-4 col-md-6">
    <h4>เพิ่มสินค้าใหม่</h4>
    <form method="post">
        <input type="text" name="SKU" placeholder="SKU" class="form-control" required>
        <input type="text" name="Name" placeholder="ชื่อสินค้า" class="form-control" required>
        <textarea name="Description" placeholder="รายละเอียด" class="form-control" required></textarea>
        <input type="number" step="0.01" name="Price" placeholder="ราคา" class="form-control" required>
        <input type="number" name="StockQuantity" placeholder="จำนวนสต็อก" class="form-control" required>
        <input type="number" name="CategoryID" placeholder="CategoryID" class="form-control" required>
        <input type="number" name="SupplierID" placeholder="SupplierID" class="form-control" required>
        <button type="submit" name="add_product" class="btn btn-primary w-100">เพิ่มสินค้า</button>
    </form>
</div>

<!-- ตารางรายการสินค้า -->
<div class="card">
    <h4>รายการสินค้า</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ProductID</th><th>SKU</th><th>Name</th><th>Description</th><th>Price</th><th>Stock</th><th>CategoryID</th><th>SupplierID</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row=$result->fetch_assoc()): ?>
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
</div>
<div class="bottom-buttons">
<a href="index.php" class="btn btn-info btn-lg mx-2">🏠 หน้าแรก</a>
</div>
</body>
</html>
