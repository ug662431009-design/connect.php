<?php
session_start();
include 'connect.php';

// ตรวจสอบว่าเป็น admin
if(!isset($_SESSION['AdminID'])){
    header("Location: login.php");
    exit();
}

// ฟังก์ชันช่วย
function e($str){
    return htmlspecialchars($str);
}

$errors = [];

// ลบ Order หากมีการส่ง GET delete_order
if(isset($_GET['delete_order'])){
    $orderID = (int)$_GET['delete_order'];
    $conn->begin_transaction();
    try {
        $conn->query("DELETE FROM order_items WHERE OrderID=$orderID");
        $conn->query("DELETE FROM orders WHERE OrderID=$orderID");
        $conn->commit();
        $_SESSION['success'] = "ลบ Order $orderID เรียบร้อยแล้ว";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } catch(Exception $e){
        $conn->rollback();
        $errors[] = "เกิดข้อผิดพลาด: ".$e->getMessage();
    }
}

// บันทึกออร์เดอร์
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart'])) {
    $customerName = trim($_POST['CustomerName'] ?? '');
    $cart = $_POST['cart'] ?? [];

    if($customerName === '') {
        $errors[] = "กรุณากรอกชื่อลูกค้า";
    } else {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO orders (CustomerName, OrderDate) VALUES (?, NOW())");
            if(!$stmt) throw new Exception("Prepare failed: ".$conn->error);
            $stmt->bind_param("s", $customerName);
            $stmt->execute();
            $OrderID = $conn->insert_id;

            $total = 0;
            foreach($cart as $ProductID => $Quantity) {
                $Quantity = (int)$Quantity;
                if($Quantity <= 0) continue;

                $stmtP = $conn->prepare("SELECT Price, StockQuantity FROM product WHERE ProductID=?");
                if(!$stmtP) throw new Exception("Prepare failed: ".$conn->error);
                $stmtP->bind_param("i",$ProductID);
                $stmtP->execute();
                $res = $stmtP->get_result()->fetch_assoc();
                $Price = $res['Price'];
                $Stock = $res['StockQuantity'];

                if($Quantity > $Stock) {
                    throw new Exception("จำนวนสินค้า {$ProductID} เกินสต็อก");
                }

                $subtotal = $Quantity * $Price;
                $total += $subtotal;

                $stmtItem = $conn->prepare("
                    INSERT INTO order_items (OrderID, ProductID, Quantity, Price, StockAtOrder) 
                    VALUES (?,?,?,?,?)
                ");
                if(!$stmtItem) throw new Exception("Prepare failed: ".$conn->error);
                $stmtItem->bind_param("iiidd",$OrderID,$ProductID,$Quantity,$Price,$Stock);
                $stmtItem->execute();

                $stmtUpd = $conn->prepare("UPDATE product SET StockQuantity = StockQuantity - ? WHERE ProductID=?");
                if(!$stmtUpd) throw new Exception("Prepare failed: ".$conn->error);
                $stmtUpd->bind_param("ii",$Quantity,$ProductID);
                $stmtUpd->execute();
            }

            $conn->commit();
            $_SESSION['success'] = "บันทึกออร์เดอร์สำเร็จ! ยอดรวม: ".number_format($total,2)." บาท";
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();

        } catch(Exception $e) {
            $conn->rollback();
            $errors[] = "เกิดข้อผิดพลาด: ".$e->getMessage();
        }
    }
}

// ดึงสินค้าทั้งหมด
$stmt = $conn->prepare("SELECT ProductID, Name, Price, StockQuantity FROM product");
$stmt->execute();
$result = $stmt->get_result();

// ดึงออร์เดอร์ย้อนหลัง
$orders = $conn->query("
    SELECT OrderID, CustomerName, OrderDate
    FROM orders
    ORDER BY OrderDate DESC
");
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Admin POS</title>
<style>
    body { font-family: Arial; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background-color: #f0f0f0; }
    input[type=number] { width: 60px; }
    .alert-success { color: green; margin: 10px 0; }
    .alert-error { color: red; margin: 10px 0; }
</style>
<script>
function calculateTotal(){
    let total = 0;
    document.querySelectorAll('input[data-price]').forEach(input=>{
        total += parseFloat(input.value||0) * parseFloat(input.dataset.price);
    });
    document.getElementById('total').innerText = total.toFixed(2);
}
</script>
</head>
<body>
<h1>🛒 ระบบ POS (Admin)</h1>

<?php 
if(isset($_SESSION['success'])){
    echo '<div class="alert-success">'.e($_SESSION['success']).'</div>';
    unset($_SESSION['success']);
}

if(!empty($errors)){
    echo '<div class="alert-error">';
    foreach($errors as $e) echo "<p>".e($e)."</p>";
    echo '</div>';
}
?>

<form method="POST">
    <label>ชื่อลูกค้า: <input type="text" name="CustomerName" required></label>

    <table>
        <thead>
            <tr><th>รหัสสินค้า</th><th>ชื่อสินค้า</th><th>ราคา</th><th>คงเหลือ</th><th>จำนวน</th></tr>
        </thead>
        <tbody>
        <?php while($row=$result->fetch_assoc()): ?>
            <tr>
                <td><?= e($row['ProductID']) ?></td>
                <td><?= e($row['Name']) ?></td>
                <td><?= number_format($row['Price'],2) ?></td>
                <td><?= e($row['StockQuantity']) ?></td>
                <td><input type="number" name="cart[<?= $row['ProductID'] ?>]" value="0" min="0" max="<?= $row['StockQuantity'] ?>" data-price="<?= $row['Price'] ?>" onchange="calculateTotal()"></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <p>รวมทั้งหมด: <span id="total">0.00</span> บาท</p>
    <button type="submit">บันทึกออร์เดอร์</button>
</form>

<h2>📜 รายการออร์เดอร์ย้อนหลัง</h2>
<?php while($order = $orders->fetch_assoc()): ?>
    <h3>
        Order ID: <?= e($order['OrderID']) ?> | 
        ลูกค้า: <?= e($order['CustomerName']) ?> | 
        วันที่: <?= $order['OrderDate'] ?> | 
        <a href="?delete_order=<?= $order['OrderID'] ?>" onclick="return confirm('ลบ Order <?= $order['OrderID'] ?> จริงหรือไม่?')">[ลบ]</a>
    </h3>
    <table>
        <thead>
            <tr><th>สินค้า</th><th>จำนวน</th><th>ราคา/ชิ้น</th><th>รวม</th><th>สต็อกตอนขาย</th></tr>
        </thead>
        <tbody>
        <?php
        $stmt_items = $conn->prepare("
            SELECT oi.Quantity, oi.Price, oi.StockAtOrder, p.Name
            FROM order_items oi
            JOIN product p ON oi.ProductID = p.ProductID
            WHERE oi.OrderID = ?
        ");
        if(!$stmt_items){
            echo "<tr><td colspan='5' style='color:red;'>Prepare failed: ".$conn->error."</td></tr>";
            continue;
        }
        $stmt_items->bind_param("i",$order['OrderID']);
        $stmt_items->execute();
        $items = $stmt_items->get_result();
        $totalOrder = 0;
        while($item = $items->fetch_assoc()):
            $sum = $item['Quantity'] * $item['Price'];
            $totalOrder += $sum;
        ?>
            <tr>
                <td><?= e($item['Name']) ?></td>
                <td><?= $item['Quantity'] ?></td>
                <td><?= number_format($item['Price'],2) ?></td>
                <td><?= number_format($sum,2) ?></td>
                <td><?= $item['StockAtOrder'] ?></td>
            </tr>
        <?php endwhile; ?>
        <tr><td colspan="3"><strong>รวมทั้งหมด:</strong></td><td colspan="2"><strong><?= number_format($totalOrder,2) ?> บาท</strong></td></tr>
        </tbody>
    </table>
    <div class="bottom-buttons">
<a href="index.php" class="btn btn-info btn-lg mx-2">🏠 หน้าแรก</a>
</div>
<?php endwhile; ?>
</body>

</html>
