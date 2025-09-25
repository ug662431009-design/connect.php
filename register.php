<?php
include 'connect.php';

if(isset($_POST['submit'])){
    $FirstName = trim($_POST['FirstName']);
    $LastName  = trim($_POST['LastName']);
    $Phone     = trim($_POST['Phone']);
    $Address   = trim($_POST['Address']);
    $Email     = trim($_POST['Email']);
    $password  = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // เข้ารหัส

    $sql = "INSERT INTO customer (FirstName, LastName, Phone, Address, Email, `password`) VALUES (?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    if(!$stmt){
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssss", $FirstName, $LastName, $Phone, $Address, $Email, $password);

    if($stmt->execute()){
        echo "<p style='color:green;'>สมัครสมาชิกสำเร็จ ✅</p>";
    } else {
        echo "<p style='color:red;'>เกิดข้อผิดพลาด: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Register</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<style>
body { background-color: #fff8f0; font-family: 'Poppins', sans-serif; }
.card { border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 20px; background-color: #fff5f7; margin-top:50px;}
input { border-radius:10px; border:1px solid #ffc0cb; padding:5px 10px; }
.btn-primary { background-color: #ffb6b9; border-color:#ffb6b9;}
</style>
</head>
<body>
<div class="container d-flex justify-content-center">
    <div class="card col-md-5">
        <h2 class="text-center mb-4">Register ✨</h2>
         <a href="index.php" class="btn btn-info btn-lg mx-2">🏠 กลับหน้าหลัก</a>
        <form method="post">
            <input class="form-control mb-2" type="text" name="FirstName" placeholder="First Name" required>
            <input class="form-control mb-2" type="text" name="LastName" placeholder="Last Name" required>
            <input class="form-control mb-2" type="text" name="Phone" placeholder="Phone">
            <input class="form-control mb-2" type="text" name="Address" placeholder="Address">
            <input class="form-control mb-2" type="email" name="Email" placeholder="Email" required>
            <input class="form-control mb-2" type="password" name="password" placeholder="Password" required>
            <button class="btn btn-primary w-100" type="submit" name="submit">Register</button>
        </form>
    </div>
</div>
</body>
</html>
