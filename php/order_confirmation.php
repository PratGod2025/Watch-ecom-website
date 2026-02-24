<?php
session_start();
require_once '../config.php';

$page_title = 'Order Confirmed';
$css_prefix = '../';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$order_number = trim($_GET['order'] ?? '');
if (empty($order_number)) {
    header('Location: ../index.php');
    exit;
}

$uid = (int)$_SESSION['user_id'];

// Fetch order
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_number=? AND user_id=?");
$stmt->bind_param('si', $order_number, $uid);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header('Location: ../index.php');
    exit;
}

// Fetch order items with watch info
$items_res = $conn->query(
    "SELECT oi.*, w.name, w.image, w.brand
     FROM order_items oi JOIN watches w ON oi.watch_id = w.id
     WHERE oi.order_id = {$order['id']}"
);
$items = $items_res->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>
<style>
    body { background: #1a0a0a; color: #f0e0e0; font-family: 'Outfit', Arial, sans-serif; margin: 0; }
    .confirm-wrap { max-width:800px; margin:60px auto; padding:0 20px; text-align:center; }
    .confirm-icon { font-size:80px; margin-bottom:20px; text-shadow: 0 0 20px rgba(39,174,96,0.3); }
    .confirm-wrap h1 { color:#27ae60; font-size:36px; margin-bottom:10px; font-weight:700; letter-spacing: 1px; }
    .confirm-wrap p.sub { color:#c0a0a0; font-size:17px; margin-bottom:40px; }
    
    .order-card { 
        background: rgba(42,14,14,0.8); 
        backdrop-filter: blur(12px);
        border-radius: 20px; 
        box-shadow: 0 15px 40px rgba(0,0,0,0.5); 
        padding: 40px; 
        text-align: left; 
        margin-bottom: 30px;
        border: 1px solid rgba(192,57,43,0.3);
    }
    .order-card h3 { color: #fff; border-bottom: 2px solid #c0392b; padding-bottom: 15px; margin: 0 0 25px; font-size: 20px; font-weight: 700; letter-spacing: 1px; }
    
    .order-meta { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px 30px; margin-bottom: 10px; }
    .meta-item label { font-size: 11px; color: #9a7070; display: block; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700; margin-bottom: 5px; }
    .meta-item span { font-size: 16px; font-weight: 600; color: #f0e0e0; }
    
    .status-badge { display: inline-block; padding: 5px 15px; border-radius: 6px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
    .status-pending { background: rgba(201,168,76,0.2); color: #f0e0a0; border: 1px solid #c9a84c; }
    
    .item-row { display: flex; align-items: center; gap: 20px; padding: 15px 0; border-bottom: 1px solid rgba(192,57,43,0.1); }
    .item-row:last-child { border-bottom: none; }
    .item-row img { width: 70px; height: 70px; object-fit: contain; background: radial-gradient(circle, #3d0b0b 0%, #1a0a0a 100%); border-radius: 12px; border: 1px solid rgba(192,57,43,0.2); }
    .item-row .i-name { font-weight: 700; color: #fff; font-size: 16px; margin-bottom: 3px; }
    .item-row .i-brand { font-size: 13px; color: #c0392b; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; }
    .item-row .i-price { margin-left: auto; color: #c9a84c; font-weight: 700; font-size: 16px; }
    
    .order-total { 
        display: flex; justify-content: space-between; margin-top: 20px; 
        font-size: 22px; font-weight: 700; color: #fff; 
        padding-top: 20px; border-top: 2px solid rgba(192,57,43,0.2); 
    }
    .order-total span:last-child { color: #c9a84c; }
    
    .cta-buttons { display: flex; gap: 20px; justify-content: center; margin-top: 30px; }
    .cta-buttons a { padding: 15px 35px; border-radius: 10px; text-decoration: none; font-size: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; transition: all 0.3s; }
    .btn-primary { background: linear-gradient(135deg, #c0392b, #8b1a12); color: #fff; box-shadow: 0 10px 20px rgba(192,57,43,0.3); }
    .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(192,57,43,0.4); }
    .btn-outline { border: 2px solid #c9a84c; color: #c9a84c; }
    .btn-outline:hover { background: #c9a84c; color: #1a0a0a; }
</style>

<div class="confirm-wrap">
    <div class="confirm-icon">✅</div>
    <h1>Order Placed Successfully!</h1>
    <p class="sub">Thank you, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>! Your order has been received.</p>

    <div class="order-card">
        <h3>Order Details</h3>
        <div class="order-meta">
            <div class="meta-item">
                <label>Order Number</label>
                <span><?= htmlspecialchars($order['order_number']) ?></span>
            </div>
            <div class="meta-item">
                <label>Date</label>
                <span><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></span>
            </div>
            <div class="meta-item">
                <label>Payment Method</label>
                <span><?= $order['payment_method'] === 'cod' ? 'Cash on Delivery' : 'eSewa' ?></span>
            </div>
            <div class="meta-item">
                <label>Order Status</label>
                <span class="status-badge status-pending"><?= ucfirst($order['order_status']) ?></span>
            </div>
            <div class="meta-item">
                <label>Shipping To</label>
                <span><?= htmlspecialchars($order['shipping_address'] . ', ' . $order['shipping_city'] . ', ' . $order['shipping_country']) ?></span>
            </div>
        </div>
    </div>

    <div class="order-card">
        <h3>Items Ordered</h3>
        <?php foreach ($items as $item): ?>
            <div class="item-row">
                <img src="../images/<?= htmlspecialchars($item['image']) ?>" alt="">
                <div>
                    <div class="i-name"><?= htmlspecialchars($item['name']) ?></div>
                    <div class="i-brand"><?= htmlspecialchars($item['brand']) ?> &nbsp;×<?= $item['quantity'] ?></div>
                </div>
                <div class="i-price">Rs. <?= number_format($item['price'] * $item['quantity']) ?></div>
            </div>
        <?php endforeach; ?>
        <div class="order-total">
            <span>Total Paid</span>
            <span>Rs. <?= number_format($order['total_amount']) ?></span>
        </div>
    </div>

    <div class="cta-buttons">
        <a href="products.php" class="cta-buttons__btn btn-outline">Continue Shopping</a>
        <a href="../index.php" class="cta-buttons__btn btn-primary">Back to Home</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
