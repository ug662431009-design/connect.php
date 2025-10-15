<?php
header('Content-Type: application/json');
include 'connect.php';

$sku = trim($_POST['SKU'] ?? '');
$name = trim($_POST['Name'] ?? '');
$desc = trim($_POST['Description'] ?? '');
$price = floatval($_POST['Price'] ?? 0);
$stock = intval($_POST['StockQuantity'] ?? 0);
$categoryID = intval($_POST['CategoryID'] ?? 0);
$supplierID = intval($_POST['SupplierID'] ?? 0);

// ถ้า SKU ว่าง
if ($sku == '') {
    echo json_encode(['status'=>false,'message'=>'❌ กรุณาใส่ SKU']);
    exit;
}

// ตรวจสอบ SKU ว่ามีอยู่ไหม
$sql = "SELECT id, StockQuantity FROM product WHERE BINARY SKU = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $sku);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // ถ้ามี SKU เดิมอยู่แล้ว ให้เพิ่ม stock
    $row = $result->fetch_assoc();
    $newStock = $row['StockQuantity'] + $stock;

    $update = $conn->prepare("UPDATE product SET StockQuantity = ? WHERE id = ?");
    $update->bind_param("ii", $newStock, $row['id']);
    $update->execute();

    echo json_encode(['status'=>true, 'message'=>'✅ อัปเดตจำนวนสต็อกของ SKU เดิมเรียบร้อยแล้ว']);
} else {
    // ถ้ายังไม่มี SKU เดิม → เพิ่มสินค้าใหม่
    $insert = $conn->prepare("INSERT INTO product (SKU, Name, Description, Price, StockQuantity, CategoryID, SupplierID)
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insert->bind_param("sssdiis", $sku, $name, $desc, $price, $stock, $categoryID, $supplierID);

    if ($insert->execute()) {
        echo json_encode(['status'=>true, 'message'=>'✅ เพิ่มสินค้าใหม่เรียบร้อยแล้ว']);
    } else {
        echo json_encode(['status'=>false, 'message'=>'❌ เกิดข้อผิดพลาด: '.$conn->error]);
    }
}
?>
