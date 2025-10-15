<?php
session_start();
include 'connect.php';

if(!isset($_SESSION['CustomerID'])){
    header("Location: login.php");
    exit;
}

$customer_id = $_SESSION['CustomerID'];
$customer_name = $_SESSION['CustomerName'];

// --- STEP 1: เพิ่มสินค้าลงตะกร้า ---
if(isset($_POST['add_to_cart'])){
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $stmt = $conn->prepare("SELECT * FROM product WHERE id=?");
    $stmt->bind_param("i",$product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    $cart_item = [
        'id'=>$product['id'],
        'name'=>$product['name'],
        'price'=>$product['price'],
        'quantity'=>$quantity
    ];

    $_SESSION['cart'][$product_id] = $cart_item;
    header("Location: shop.php");
    exit;
}

// --- STEP 2: สั่งซื้อ ---
if(isset($_POST['checkout'])){
    $cart = $_SESSION['cart'] ?? [];
    if(empty($cart)) { die("ตะกร้าว่าง"); }

    $conn->begin_transaction();
    try{
        $stmt = $conn->prepare("INSERT INTO orders (customer_id) VALUES (?)");
        $stmt->bind_param("i",$customer_id);
        $stmt->execute();
        $order_id = $conn->insert_id;

        foreach($cart as $item){
            $stmt_check = $conn->prepare("SELECT stock FROM product WHERE id=? FOR UPDATE");
            $stmt_check->bind_param("i",$item['id']);
            $stmt_check->execute();
            $product = $stmt_check->get_result()->fetch_assoc();

            if($product['stock'] < $item['quantity']){
                throw new Exception("สินค้าหมด: ".$item['name']);
            }

            $stmt_insert = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?,?,?,?)");
            $stmt_insert->bind_param("iiid",$order_id,$item['id'],$item['quantity'],$item['price']);
            $stmt_insert->execute();

            $stmt_update = $conn->prepare("UPDATE product SET stock=stock-? WHERE id=?");
            $stmt_update->bind_param("ii",$item['quantity'],$item['id']);
            $stmt_update->execute();
        }

        $conn->commit();
        unset($_SESSION['cart']);
        echo "สั่งซื้อสำเร็จ! Order ID: $order_id";

    }catch(Exception $e){
        $conn->rollback();
        echo "เกิดข้อผิดพลาด: ".$e->getMessage();
    }
}

// --- STEP 3: แสดงสินค้า ---
$products = $conn->query("SELECT * FROM product");
?>
<h2>สวัสดี <?= $customer_name ?> | <a href="logout.php">Logout</a></h2>
<h1>สินค้า</h1>
<?php while($row = $products->fetch_assoc()): ?>
    <div>
        <strong><?= $row['name'] ?></strong> - <?= $row['price'] ?> บาท
        (เหลือ <?= $row['stock'] ?> ชิ้น)
        <form method="post">
            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
            <input type="number" name="quantity" value="1" min="1" max="<?= $row['stock'] ?>">
            <button type="submit" name="add_to_cart">ใส่ตะกร้า</button>
        </form>
    </div>
<?php endwhile; ?>

<h2>ตะกร้าสินค้า</h2>
<?php if(!empty($_SESSION['cart'])): ?>
    <ul>
    <?php foreach($_SESSION['cart'] as $item): ?>
        <li><?= $item['name'] ?> x <?= $item['quantity'] ?> = <?= $item['price'] * $item['quantity'] ?> บาท</li>
    <?php endforeach; ?>
    </ul>
    <form method="post">
        <button type="submit" name="checkout">สั่งซื้อทั้งหมด</button>
    </form>
<?php else: ?>
    <p>ตะกร้าว่าง</p>
<?php endif; ?>
