<?php
session_start();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>School Store</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<style>
html, body {
    height: 100%;
    margin: 0;
}

.center-screen {
    display: flex;
    flex-direction: column; /* เรียงเนื้อหาแนวตั้ง */
    justify-content: center; /* แนวตั้ง */
    align-items: center;     /* แนวนอน */
    height: 100%;
    text-align: center;
    background: linear-gradient(135deg, #ffe6f0, #e6f7ff);
    font-family: 'Comic Neue', cursive;
}
h1 { color: #ff6f91; text-shadow: 1px 1px #fff; margin-bottom: 40px; }
.btn-primary{background-color:#ffb6b9;border:none;}
.btn-success{background-color:#caffbf;border:none;color:black;}
.btn-warning{background-color:#ffd3b6;border:none;}
.btn-info{background-color:#caffbf;border:none;color:black;}
</style>
</head>
<body>

<div class="center-screen">
    <h1>🎒 Welcome to School Store 🎨</h1>

<?php if(isset($_SESSION['AdminID'])): ?>
<p>Hello Admin, <strong><?= $_SESSION['AdminName'] ?></strong> 👋 | <a href="logout.php">Logout</a></p>
<a href="product_admin.php" class="btn btn-warning m-2">Manage Products 🛒</a>
<a href="customer_admin.php" class="btn btn-info m-2">Manage Customers 👥</a>
<?php elseif(isset($_SESSION['CustomerID'])): ?>
<p>Hello, <strong><?= $_SESSION['FirstName'] ?></strong> 👋 | <a href="logout.php">Logout</a></p>
<?php else: ?>
<a href="register.php" class="btn btn-primary m-2">Register</a>
<a href="login.php" class="btn btn-success m-2">Login</a>
<a href="admin_login.php" class="btn btn-warning m-2">Admin Login</a>
<?php endif; ?>

</div>
</body>
</html>
