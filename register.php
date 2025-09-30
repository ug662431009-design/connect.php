<?php
include 'connect.php';
session_start();

if(isset($_POST['submit'])){
    $FirstName=trim($_POST['FirstName']);
    $LastName=trim($_POST['LastName']);
    $Phone=trim($_POST['Phone']);
    $Address=trim($_POST['Address']);
    $Email=trim($_POST['Email']);
    $password=password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    $sql="INSERT INTO customer (FirstName,LastName,Phone,Address,Email,password) VALUES (?,?,?,?,?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ssssss",$FirstName,$LastName,$Phone,$Address,$Email,$password);

    if($stmt->execute()){
        $_SESSION['CustomerID']=$conn->insert_id;
        $_SESSION['FirstName']=$FirstName;
        header("Location: index.php");
    }else{
        $error=$stmt->error;
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
body{font-family:'Comic Neue',cursive;background:#fff0f5;}
.card{border-radius:15px;padding:20px;max-width:500px;margin:50px auto;}
</style>
</head>
<body>
<div class="card">
<h2 class="text-center mb-4">Register ✨</h2>
<?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
<form method="post">
<input class="form-control mb-2" type="text" name="FirstName" placeholder="First Name" required>
<input class="form-control mb-2" type="text" name="LastName" placeholder="Last Name" required>
<input class="form-control mb-2" type="text" name="Phone" placeholder="Phone">
<input class="form-control mb-2" type="text" name="Address" placeholder="Address">
<input class="form-control mb-2" type="email" name="Email" placeholder="Email" required>
<input class="form-control mb-2" type="password" name="password" placeholder="Password" required>
<button class="btn btn-primary w-100" type="submit" name="submit">Register</button>
</form>
<p class="text-center mt-3">Already have an account? <a href="login.php">Login</a></p>
</div>
</body>
</html>
