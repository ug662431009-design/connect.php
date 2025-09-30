<?php
session_start();
if(!isset($_SESSION['AdminID'])){
    header("Location: admin_login.php");
    exit;
}
include 'connect.php';

// messages
$msg_customer = '';
$msg_product = '';

// เพิ่มลูกค้า
if(isset($_POST['add_customer'])){
    $FirstName = trim($_POST['FirstName']);
    $LastName  = trim($_POST['LastName']);
    $Phone     = trim($_POST['Phone']);
    $Address   = trim($_POST['Address']);
    $Email     = trim($_POST['Email']);
    $Password  = password_hash(trim($_POST['Password']), PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO customer (FirstName, LastName, Phone, Address, Email, Password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $FirstName, $LastName, $Phone, $Address, $Email, $Password);
    if($stmt->execute()){
        $msg_customer = "เพิ่มลูกค้าสำเร็จ";
    } else {
        $msg_customer = "Error: " . $stmt->error;
    }
}

// เพิ่มสินค้า
if(isset($_POST['add_product'])){
    $SKU = trim($_POST['SKU']);
    $Name = trim($_POST['Name']);
    $Description = trim($_POST['Description']);
    $Price = floatval($_POST['Price']);
    $StockQuantity = intval($_POST['StockQuantity']);
    $CategoryID = isset($_POST['CategoryID']) && $_POST['CategoryID'] !== '' ? intval($_POST['CategoryID']) : null;
    $SupplierID = isset($_POST['SupplierID']) && $_POST['SupplierID'] !== '' ? intval($_POST['SupplierID']) : null;

    $stmt = $conn->prepare("INSERT INTO product (SKU, Name, Description, Price, StockQuantity, CategoryID, SupplierID) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdiis", $SKU, $Name, $Description, $Price, $StockQuantity, $CategoryID, $SupplierID);
    if($stmt->execute()){
        $msg_product = "เพิ่มสินค้าสำเร็จ";
    } else {
        $msg_product = "Error: " . $stmt->error;
    }
}

// ดึงข้อมูล
$customers = $conn->query("SELECT * FROM customer ORDER BY CustomerID ASC");
$products = $conn->query("SELECT * FROM product ORDER BY ProductID ASC");
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@400;700&display=swap" rel="stylesheet">
<style>
body{font-family:'Comic Neue',cursive;background:linear-gradient(135deg,#fff0f5,#e6f7ff);min-height:100vh;}
.container{margin-top:30px;}
.card{padding:18px;margin-bottom:18px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,0.06);background:#fff;}
.btn-primary{background:#ffb6b9;border:none;}
.btn-success{background:#caffbf;border:none;color:black;}
.btn-warning{background:#ffd3b6;border:none;}
.table-responsive{margin-top:12px;}
</style>
</head>
<body>
<div class="container">
  <h2 class="text-center mb-3">👑 Admin Panel — <?= htmlspecialchars($_SESSION['AdminName']) ?></h2>
  <div class="text-end mb-3">
    <a href="logout.php" class="btn btn-warning">Logout</a>
    <a href="product.php" class="btn btn-info">View Products</a>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="card">
        <h5>เพิ่มลูกค้า</h5>
        <?php if($msg_customer) echo "<div class='alert alert-info'>$msg_customer</div>"; ?>
        <form method="post">
          <input type="text" name="FirstName" class="form-control mb-2" placeholder="FirstName" required>
          <input type="text" name="LastName" class="form-control mb-2" placeholder="LastName" required>
          <input type="text" name="Phone" class="form-control mb-2" placeholder="Phone">
          <input type="text" name="Address" class="form-control mb-2" placeholder="Address">
          <input type="email" name="Email" class="form-control mb-2" placeholder="Email" required>
          <input type="password" name="Password" class="form-control mb-2" placeholder="Password" required>
          <button type="submit" name="add_customer" class="btn btn-success w-100">เพิ่มลูกค้า</button>
        </form>
      </div>

      <div class="card">
        <h5>รายชื่อลูกค้า</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead><tr><th>ID</th><th>First</th><th>Last</th><th>Phone</th><th>Address</th><th>Email</th></tr></thead>
            <tbody>
              <?php while($c = $customers->fetch_assoc()): ?>
              <tr>
                <td><?= $c['CustomerID'] ?></td>
                <td><?= htmlspecialchars($c['FirstName']) ?></td>
                <td><?= htmlspecialchars($c['LastName']) ?></td>
                <td><?= htmlspecialchars($c['Phone']) ?></td>
                <td><?= htmlspecialchars($c['Address']) ?></td>
                <td><?= htmlspecialchars($c['Email']) ?></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <h5>เพิ่มสินค้า</h5>
        <?php if($msg_product) echo "<div class='alert alert-info'>$msg_product</div>"; ?>
        <form method="post">
          <input type="text" name="SKU" class="form-control mb-2" placeholder="SKU" required>
          <input type="text" name="Name" class="form-control mb-2" placeholder="Name" required>
          <input type="text" name="Description" class="form-control mb-2" placeholder="Description">
          <input type="number" step="0.01" name="Price" class="form-control mb-2" placeholder="Price" required>
          <input type="number" name="StockQuantity" class="form-control mb-2" placeholder="StockQuantity" required>
          <input type="number" name="CategoryID" class="form-control mb-2" placeholder="CategoryID">
          <input type="number" name="SupplierID" class="form-control mb-2" placeholder="SupplierID">
          <button type="submit" name="add_product" class="btn btn-primary w-100">เพิ่มสินค้า</button>
        </form>
      </div>

      <div class="card">
        <h5>รายการสินค้า</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead><tr><th>ID</th><th>SKU</th><th>Name</th><th>Description</th><th>Price</th><th>Stock</th><th>Category</th><th>Supplier</th></tr></thead>
            <tbody>
              <?php while($p = $products->fetch_assoc()): ?>
              <tr>
                <td><?= $p['ProductID'] ?></td>
                <td><?= htmlspecialchars($p['SKU']) ?></td>
                <td><?= htmlspecialchars($p['Name']) ?></td>
                <td><?= htmlspecialchars($p['Description']) ?></td>
                <td><?= $p['Price'] ?></td>
                <td><?= $p['StockQuantity'] ?></td>
                <td><?= $p['CategoryID'] ?></td>
                <td><?= $p['SupplierID'] ?></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>
</body>
</html>
