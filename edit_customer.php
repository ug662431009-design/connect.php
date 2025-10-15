<?php
include 'connect.php';

$id = $_GET['id'];

// ดึงข้อมูลลูกค้าปัจจุบัน
$result = $conn->query("SELECT * FROM customer WHERE CustomerID = $id");
$customer = $result->fetch_assoc();

// เมื่อกดบันทึก
if(isset($_POST['update'])){
    $FirstName = trim($_POST['FirstName']);
    $LastName  = trim($_POST['LastName']);
    $Phone     = trim($_POST['Phone']);
    $Address   = trim($_POST['Address']);
    $Email     = trim($_POST['Email']);

    // แก้ไข bind_param ให้ถูกประเภท
    $update = $conn->prepare("UPDATE customer SET FirstName=?, LastName=?, Phone=?, Address=?, Email=? WHERE CustomerID=?");
    $update->bind_param("sssssi", $FirstName, $LastName, $Phone, $Address, $Email, $id);

    if($update->execute()){
        header("Location: customer_admin.php"); // กลับไปหน้าหลัก
        exit();
    } else {
        echo "❌ เกิดข้อผิดพลาด: " . $conn->error;
    }

    // อัพเดตตัวแปร $customer เพื่อเติมค่าที่แก้ไขแล้วในฟอร์ม
    $customer['FirstName'] = $FirstName;
    $customer['LastName']  = $LastName;
    $customer['Phone']     = $Phone;
    $customer['Address']   = $Address;
    $customer['Email']     = $Email;
}
?>

<form method="post">
  <input class="form-control mb-2" type="text" name="FirstName" placeholder="First Name" required value="<?php echo htmlspecialchars($customer['FirstName']); ?>">
  <input class="form-control mb-2" type="text" name="LastName" placeholder="Last Name" required value="<?php echo htmlspecialchars($customer['LastName']); ?>">
  <input class="form-control mb-2" type="text" name="Phone" placeholder="Phone" value="<?php echo htmlspecialchars($customer['Phone']); ?>">
  <input class="form-control mb-2" type="text" name="Address" placeholder="Address" value="<?php echo htmlspecialchars($customer['Address']); ?>">
  <input class="form-control mb-2" type="email" name="Email" placeholder="Email" required value="<?php echo htmlspecialchars($customer['Email']); ?>">
  <button type="submit" name="update">บันทึก</button>
</form>
