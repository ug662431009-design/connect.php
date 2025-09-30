<?php
session_start();
include 'connect.php';

// ตรวจสอบว่าเป็น admin
if(!isset($_SESSION['AdminID'])){
    header("Location: admin_login.php");
    exit();
}

// เพิ่มลูกค้า
if(isset($_POST['add_customer'])){
    $fname = $_POST['FirstName'];
    $lname = $_POST['LastName'];
    $phone = $_POST['Phone'];
    $address = $_POST['Address'];
    $email = $_POST['Email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO customer (FirstName, LastName, Phone, Address, Email, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $fname, $lname, $phone, $address, $email, $password);

    if($stmt->execute()){
        $msg = "✅ เพิ่มลูกค้าเรียบร้อย!";
    } else {
        $msg = "❌ เกิดข้อผิดพลาด: " . $stmt->error;
    }
}

// ดึงรายการลูกค้า
$result = $conn->query("SELECT * FROM customer ORDER BY CustomerID ASC");
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Admin - Customers</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<style>
body { font-family:'Comic Neue', cursive; background:linear-gradient(135deg,#ffe6f0,#e6f7ff); min-height:100vh; }
.container{margin-top:30px;}
.card{border-radius:15px; padding:20px; background-color:#fff5f7; box-shadow:0 4px 6px rgba(0,0,0,0.1);}
input{border-radius:10px; border:1px solid #ffc0cb; padding:5px 10px; margin-bottom:10px;}
.btn-primary{background-color:#ffb6b9;border:none;}
</style>
</head>
<body>
<div class="container">
<h2>👥 จัดการลูกค้า</h2>

<?php if(isset($msg)) echo "<p>$msg</p>"; ?>

<!-- ฟอร์มเพิ่มลูกค้า -->
<div class="card mb-4 col-md-6">
    <h4>เพิ่มลูกค้าใหม่</h4>
    <form method="post">
        <input type="text" name="FirstName" placeholder="ชื่อ" class="form-control" required>
        <input type="text" name="LastName" placeholder="นามสกุล" class="form-control" required>
        <input type="text" name="Phone" placeholder="เบอร์โทร" class="form-control">
        <input type="text" name="Address" placeholder="ที่อยู่" class="form-control">
        <input type="email" name="Email" placeholder="อีเมล" class="form-control" required>
        <input type="password" name="password" placeholder="รหัสผ่าน" class="form-control" required>
        <button type="submit" name="add_customer" class="btn btn-primary w-100">เพิ่มลูกค้า</button>
    </form>
</div>

<!-- ตารางรายการลูกค้า -->
<div class="card">
    <h4>รายการลูกค้า</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>CustomerID</th><th>FirstName</th><th>LastName</th><th>Phone</th><th>Address</th><th>Email</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row=$result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['CustomerID'] ?></td>
                <td><?= htmlspecialchars($row['FirstName']) ?></td>
                <td><?= htmlspecialchars($row['LastName']) ?></td>
                <td><?= htmlspecialchars($row['Phone']) ?></td>
                <td><?= htmlspecialchars($row['Address']) ?></td>
                <td><?= htmlspecialchars($row['Email']) ?></td>
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
