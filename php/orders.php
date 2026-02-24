<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'My Orders';
$css_prefix = '../';
$uid = (int)$_SESSION['user_id'];

// Fetch orders
$orders_res = $conn->query(
    "SELECT * FROM orders WHERE user_id = $uid ORDER BY created_at DESC"
);

include 'includes/header.php';
?>
<style>
    body { background: #1a0a0a; color: #f0e0e0; font-family: 'Outfit', Arial, sans-serif; margin: 0; }
    .orders-wrap { max-width: 1000px; margin: 60px auto; padding: 0 20px; }
    .orders-wrap h1 { color: #fff; margin-bottom: 30px; font-weight: 700; font-size: 32px; letter-spacing: 1px; text-align: center; }

    .order-item { 
        background: rgba(42,14,14,0.7); 
        backdrop-filter: blur(12px);
        border-radius: 20px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.4); 
        padding: 30px; 
        margin-bottom: 25px;
        border: 1px solid rgba(192,57,43,0.2);
        transition: transform 0.3s;
    }
    .order-item:hover { transform: translateY(-5px); border-color: rgba(192,57,43,0.5); }

    .order-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
    .oh-left h3 { margin: 0 0 5px; color: #fff; font-size: 18px; }
    .oh-left p { margin: 0; font-size: 13px; color: #9a7070; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
    
    .oh-right { text-align: right; }
    .order-total { display: block; font-size: 20px; font-weight: 700; color: #c9a84c; margin-bottom: 8px; }
    
    .badge { display: inline-block; padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
    .badge-pending    { background: rgba(192,57,43,0.2); color: #f0a0a0; border: 1px solid #c0392b; }
    .badge-processing { background: rgba(52,152,219,0.2); color: #a0d4f0; border: 1px solid #3498db; }
    .badge-shipped    { background: rgba(201,168,76,0.2); color: #f0e0a0; border: 1px solid #c9a84c; }
    .badge-delivered  { background: rgba(39,174,96,0.2); color: #a0f0b0; border: 1px solid #27ae60; }
    .badge-cancelled  { background: rgba(149,165,166,0.2); color: #f0f0f0; border: 1px solid #95a5a6; }

    .order-details { margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(192,57,43,0.1); }
    .prod-row { display: flex; align-items: center; gap: 15px; padding: 10px 0; border-bottom: 1px solid rgba(192,57,43,0.05); }
    .prod-row:last-child { border-bottom: none; }
    .prod-row img { width: 50px; height: 50px; object-fit: contain; background: rgba(42,14,14,0.5); border-radius: 8px; border: 1px solid rgba(192,57,43,0.1); }
    .prod-info { flex: 1; }
    .prod-name { font-size: 14px; font-weight: 600; color: #f0e0e0; }
    .prod-meta { font-size: 12px; color: #9a7070; margin-top: 2px; }

    .empty-msg { text-align: center; padding: 80px 20px; background: rgba(42,14,14,0.4); border-radius: 30px; border: 1px dashed rgba(192,57,43,0.3); }
    .empty-msg h2 { color: #888; margin-bottom: 20px; }
    .btn-shop { 
        display: inline-block; padding: 14px 35px; background: linear-gradient(135deg, #c0392b, #8b1a12); 
        color: #fff; text-decoration: none; border-radius: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
    }
</style>

<div class="orders-wrap">
    <h1>Your Order History</h1>

    <?php if ($orders_res && $orders_res->num_rows > 0): ?>
        <?php while ($ord = $orders_res->fetch_assoc()): 
            $oid = $ord['id'];
            $items = $conn->query(
                "SELECT oi.*, w.name, w.image FROM order_items oi 
                 JOIN watches w ON oi.watch_id = w.id 
                 WHERE oi.order_id = $oid"
            );
        ?>
            <div class="order-item">
                <div class="order-header">
                    <div class="oh-left">
                        <h3>Order #<?= htmlspecialchars($ord['order_number']) ?></h3>
                        <p><?= date('d M Y, h:i A', strtotime($ord['created_at'])) ?></p>
                    </div>
                    <div class="oh-right">
                        <span class="order-total">Rs. <?= number_format($ord['total_amount']) ?></span>
                        <span class="badge badge-<?= $ord['order_status'] ?>"><?= ucfirst($ord['order_status']) ?></span>
                    </div>
                </div>

                <div class="order-details">
                    <?php while ($it = $items->fetch_assoc()): ?>
                        <div class="prod-row">
                            <img src="../images/<?= htmlspecialchars($it['image']) ?>" alt="">
                            <div class="prod-info">
                                <div class="prod-name"><?= htmlspecialchars($it['name']) ?></div>
                                <div class="prod-meta">Qty: <?= $it['quantity'] ?> × Rs. <?= number_format($it['price']) ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-msg">
            <h2>No orders found.</h2>
            <p style="color:#9a7070; margin-bottom:30px;">Looks like you haven't placed any orders yet.</p>
            <a href="products.php" class="btn-shop">Start Shopping →</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
