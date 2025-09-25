<?php
session_start();
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>School Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #fff8f0; /* พื้นหลังพาสเทล */
    }
    h1, h2 {
      color: #ff6f91;
    }
    .btn-primary { background-color: #ffb6b9; border-color: #ffb6b9; }
    .btn-success { background-color: #ffdac1; border-color: #ffdac1; color: #000; }
    .btn-info { background-color: #caffbf; border-color: #caffbf; color: #000; }
    .btn-warning { background-color: #ffd3b6; border-color: #ffd3b6; color: #000; }
    .card {
      border-radius: 15px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      background-color: #fff5f7;
    }
  </style>
</head>
<body>

<!-- 🔹 Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold text-danger" href="index.php">🎒 School Store</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if(isset($_SESSION['FirstName'])): ?>
          <li class="nav-item"><a class="nav-link">👋 สวัสดี <?= $_SESSION['FirstName'] ?></a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">🚪 ออกจากระบบ</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="register.php">📝 สมัครสมาชิก</a></li>
          <li class="nav-item"><a class="nav-link" href="login.php">🔑 เข้าสู่ระบบ</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- 🔹 Content -->
<div class="container text-center mt-5">
  <h1 class="mb-4">🎨 Welcome to School Store 🛍️</h1>
  <p class="fs-5">เลือกซื้ออุปกรณ์การเรียน น่ารัก ๆ ได้เลย!</p>

  <div class="row mt-4">
    <div class="col-12 col-md-6 col-lg-3 mb-3">
      <a href="view_product.php" class="btn btn-info w-100 py-3">📚 ดูสินค้า</a>
    </div>
    <div class="col-12 col-md-6 col-lg-3 mb-3">
      <a href="add_product.php" class="btn btn-warning w-100 py-3">➕ เพิ่มสินค้า</a>
    </div>
    <div class="col-12 col-md-6 col-lg-3 mb-3">
      <a href="register.php" class="btn btn-primary w-100 py-3">📝 สมัครสมาชิก</a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
