<?php
session_start();

$fixed_username = 'admin';
$fixed_password = '1234'; // รหัสตายตัว

if(isset($_POST['login'])){
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if($username === $fixed_username && $password === $fixed_password){
        $_SESSION['AdminID'] = 1;
        $_SESSION['AdminName'] = 'Super Admin';
        header("Location: index.php");
        exit();
    } else {
        $error = "❌ ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<style>
body { font-family:'Comic Neue', cursive; background:linear-gradient(135deg,#ffe6f0,#e6f7ff); min-height:100vh; }
.card{border-radius:15px; padding:20px; background-color:#fff5f7; box-shadow:0 4px 6px rgba(0,0,0,0.1);}
input{border-radius:10px; border:1px solid #ffc0cb; padding:5px 10px; margin-bottom:10px;}
.btn-primary{background-color:#ffb6b9;border:none;}
.container{margin-top:50px;}
</style>
</head>
<body>
<div class="container d-flex justify-content-center">
<div class="card col-md-4">
<h2 class="text-center mb-4">Admin Login</h2>
<?php if(isset($error)) echo "<p>$error</p>"; ?>
<form method="post">
<input type="text" name="username" placeholder="Username" class="form-control" required>
<input type="password" name="password" placeholder="Password" class="form-control" required>
<button type="submit" name="login" class="btn btn-primary w-100">Login</button>
</form>
</div>
</div>
</body>
</html>
