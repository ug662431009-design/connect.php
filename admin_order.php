<?php
session_start();
include 'connect.php';

// ตรวจสอบว่าเป็น admin
if(!isset($_SESSION['AdminID'])){
    header("Location: login.php");
    exit();
}

// ฟังก์ชันช่วย sanitize
function e($str){
    return htmlspecialchars($str);
}

// ดึงออร์เดอร์ทั้งหมด พร้อมรายการสินค้า
$stmt_orders = $conn->prepare("SELECT * FROM orders ORDER BY OrderDate DESC");
$stmt_orders->execute();
$orders_result = $stmt_orders->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายการออร์เดอร์ย้อนหลัง</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { margin-top: 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
        .order-block { margin-bottom: 40px; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <h1>รายการออร์เดอร์ย้อนหลัง</h1>

    <?php while($order = $orders_result->fetch_assoc()): ?>
        <div class="order-block">
            <h2>Order ID: <?= e($order['OrderID']) ?> | ลูกค้า: <?= e($order['CustomerName']) ?> | วันที่: <?= $order['OrderDate'] ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>รหัสสินค้า</th>
                        <th>ชื่อสินค้า</th>
                        <th>จำนวน</th>
                        <th>ราคา/ชิ้น</th>
                        <th>รวม</th>
                        <th>Stock ปัจจุบัน</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt_items = $conn->prepare("
                        SELECT oi.ProductID, p.Name, oi.Quantity, oi.Price, p.StockQuantity
                        FROM order_items oi
                        JOIN product p ON oi.ProductID = p.ProductID
                        WHERE oi.OrderID = ?
                    ");
                    $stmt_items->bind_param("i", $order['OrderID']);
                    $stmt_items->execute();
                    $items_result = $stmt_items->get_result();
                    $order_total = 0;
                    while($item = $items_result->fetch_assoc()):
                        $line_total = $item['Quantity'] * $item['Price'];
                        $order_total += $line_total;
                    ?>
                    <tr>
                        <td><?= e($item['ProductID']) ?></td>
                        <td><?= e($item['Name']) ?></td>
                        <td><?= $item['Quantity'] ?></td>
                        <td><?= number_format($item['Price'],2) ?></td>
                        <td><?= number_format($line_total,2) ?></td>
                        <td><?= $item['StockQuantity'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="4" class="total">รวมทั้งหมด:</td>
                        <td colspan="2" class="total"><?= number_format($order_total,2) ?> บาท</td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php endwhile; ?>

</body>
</html>
