<?php
session_start();
include 'connect.php';

if(isset($_POST['login'])){
    $type = $_POST['type']; // 'admin' หรือ 'customer'
    $email_or_username = $_POST['email_or_username'];
    $password = $_POST['password'];

    if($type == 'customer'){
        $stmt = $conn->prepare("SELECT * FROM customer WHERE email=?");
        $stmt->bind_param("s",$email_or_username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if($user && password_verify($password,$user['password'])){
            $_SESSION['CustomerID'] = $user['id'];
            $_SESSION['CustomerName'] = $user['name'];
            header("Location: shop.php");
            exit;
        }else{
            $error = "อีเมลหรือรหัสผ่านลูกค้าไม่ถูกต้อง";
        }
    }elseif($type == 'admin'){
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username=?");
        $stmt->bind_param("s",$email_or_username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if($user && password_verify($password,$user['password'])){
            $_SESSION['AdminID'] = $user['id'];
            $_SESSION['AdminName'] = $user['username'];
            header("Location: admin_dashboard.php");
            exit;
        }else{
            $error = "ชื่อผู้ใช้หรือรหัสผ่านแอดมินไม่ถูกต้อง";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
<h2>เข้าสู่ระบบ</h2>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
    <label>เลือกประเภท:</label>
    <select name="type">
        <option value="customer">ลูกค้า</option>
        <option value="admin">แอดมิน</option>
    </select><br><br>

    <label>Email หรือ Username:</label>
    <input type="text" name="email_or_username" required><br><br>

    <label>Password:</label>
    <input type="password" name="password" required><br><br>

    <button type="submit" name="login">Login</button>
</form>
</body>
</html>
