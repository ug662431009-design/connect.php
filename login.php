<?php
include 'connect.php';
session_start();
if(isset($_POST['submit'])){
    $Email=trim($_POST['Email']);
    $password=trim($_POST['password']);
    $stmt=$conn->prepare("SELECT * FROM customer WHERE Email=? LIMIT 1");
    $stmt->bind_param("s",$Email);
    $stmt->execute();
    $result=$stmt->get_result();
    if($result->num_rows>0){
        $user=$result->fetch_assoc();
        if(password_verify($password,$user['password'])){
            $_SESSION['CustomerID']=$user['CustomerID'];
            $_SESSION['FirstName']=$user['FirstName'];
            header("Location: index.php");
        }else{$error="รหัสผ่านไม่ถูกต้อง";}
    }else{$error="ไม่พบผู้ใช้";}
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<style>body{font-family:'Comic Neue',cursive;background:#fff0f5;} .card{border-radius:15px;padding:20px;max-width:500px;margin:50px auto;}</style>
</head>
<body>
<div class="card">
<h2 class="text-center mb-4">Login 👋</h2>
<?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
<form method="post">
<input class="form-control mb-2" type="email" name="Email" placeholder="Email" required>
<input class="form-control mb-2" type="password" name="password" placeholder="Password" required>
<button class="btn btn-success w-100" type="submit" name="submit">Login</button>
</form>
<p class="text-center mt-3">Don't have an account? <a href="register.php">Register</a></p>
</div>
</body>
</html>
