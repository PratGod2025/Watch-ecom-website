<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

$page_title = 'Manage Orders';
$css_prefix = '../../';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $oid           = (int)$_POST['order_id'];
    $order_status  = $conn->real_escape_string($_POST['order_status']);
    $pay_status    = $conn->real_escape_string($_POST['payment_status']);
    $tracking      = $conn->real_escape_string(trim($_POST['tracking_number'] ?? ''));
    $conn->query("UPDATE orders SET order_status='$order_status', payment_status='$pay_status', tracking_number='$tracking' WHERE id=$oid");
    header('Location: orders.php?updated=1');
    exit;
}

// Filter
$filter = $conn->real_escape_string($_GET['status'] ?? '');
$where  = $filter ? "WHERE o.order_status='$filter'" : '';

$orders = $conn->query(
    "SELECT o.*, u.username, u.email FROM orders o
     JOIN users u ON o.user_id=u.id
     $where ORDER BY o.created_at DESC"
);

// Detail view
$detail = null;
if (isset($_GET['id'])) {
    $oid  = (int)$_GET['id'];
    $res  = $conn->query("SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id=u.id WHERE o.id=$oid");
    $detail = $res->fetch_assoc();
    $detail_items = $conn->query(
        "SELECT oi.*, w.name, w.image FROM order_items oi JOIN watches w ON oi.watch_id=w.id WHERE oi.order_id=$oid"
    );
}

include '../includes/header.php';
?>
<style>
    body { background: #1a0a0a; color: #f0e0e0; font-family: 'Outfit', Arial, sans-serif; margin: 0; }
    .admin-wrap { max-width:1240px; margin:40px auto; padding:0 20px; }
    .admin-wrap h1 { color: #fff; margin-bottom: 25px; font-weight: 700; font-size: 32px; letter-spacing: 1px; }

    .admin-nav { display: flex; gap: 15px; margin-bottom: 35px; flex-wrap: wrap; }
    .admin-nav a { 
        padding: 12px 24px; border-radius: 10px; text-decoration: none; 
        font-size: 14px; font-weight: 700; background: rgba(42,14,14,0.6); 
        color: #c0a0a0; border: 1px solid rgba(192,57,43,0.3); transition: all 0.3s;
        text-transform: uppercase; letter-spacing: 1px;
    }
    .admin-nav a:hover, .admin-nav a.active { 
        background: #c0392b; color: #fff; border-color: #c0392b; 
        box-shadow: 0 0 15px rgba(192,57,43,0.4);
    }
    .admin-nav a.view-store { background: transparent; border-color: #c9a84c; color: #c9a84c; }
    .admin-nav a.view-store:hover { background: #c9a84c; color: #1a0a0a; }

    .filter-bar { display:flex; gap:10px; margin-bottom:25px; flex-wrap:wrap; }
    .filter-bar a { 
        padding:8px 18px; border-radius:20px; border:1px solid rgba(192,57,43,0.3); 
        text-decoration:none; font-size:13px; color:#c0a0a0; background:rgba(42,14,14,0.4);
        transition: all 0.3s;
    }
    .filter-bar a.active, .filter-bar a:hover { background:#c0392b; color:#fff; border-color:#c0392b; }

    table.data-table { 
        width: 100%; border-collapse: collapse; background: rgba(42,14,14,0.8); 
        backdrop-filter: blur(10px); border-radius: 15px; overflow: hidden; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.4); border: 1px solid rgba(192,57,43,0.3);
        margin-bottom: 30px;
    }
    table.data-table th { background: #2a0e0e; color: #c0392b; padding: 18px 16px; text-align: left; font-size: 13px; text-transform: uppercase; letter-spacing: 2px; border-bottom: 1px solid rgba(192,57,43,0.2); }
    table.data-table td { padding: 15px 16px; border-bottom: 1px solid rgba(192,57,43,0.1); font-size: 14px; color: #f0e0e0; }

    .badge { display: inline-block; padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
    .badge-pending    { background: rgba(192,57,43,0.2); color: #f0a0a0; border: 1px solid #c0392b; }
    .badge-processing { background: rgba(52,152,219,0.2); color: #a0d4f0; border: 1px solid #3498db; }
    .badge-shipped    { background: rgba(201,168,76,0.2); color: #f0e0a0; border: 1px solid #c9a84c; }
    .badge-delivered  { background: rgba(39,174,96,0.2); color: #a0f0b0; border: 1px solid #27ae60; }
    .badge-cancelled  { background: rgba(149,165,166,0.2); color: #f0f0f0; border: 1px solid #95a5a6; }
    .badge-paid       { background: rgba(39,174,96,0.2); color: #a0f0b0; border: 1px solid #27ae60; }
    .badge-failed     { background: rgba(192,57,43,0.2); color: #f0a0a0; border: 1px solid #c0392b; }

    .detail-card { 
        background: rgba(42,14,14,0.9); backdrop-filter: blur(15px);
        border-radius: 20px; border: 1px solid rgba(192,57,43,0.4);
        padding: 40px; margin-bottom: 40px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    }
    .detail-card h3 { color: #fff; border-bottom: 2px solid #c0392b; padding-bottom: 15px; margin: 0 0 25px; font-size: 24px; }
    
    .detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px 40px; margin-bottom: 30px; }
    .d-item label { font-size: 11px; color: #9a7070; display: block; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700; margin-bottom: 5px; }
    .d-item span { font-size: 15px; font-weight: 600; color: #f0e0e0; }

    .item-row { display: flex; align-items: center; gap: 15px; padding: 15px 0; border-bottom: 1px solid rgba(192,57,43,0.1); }
    .item-row img { width: 60px; height: 60px; object-fit: contain; background: radial-gradient(circle, #3d0b0b 0%, #1a0a0a 100%); border-radius: 10px; border: 1px solid rgba(192,57,43,0.2); }
    
    .update-form select, .update-form input[type=text] { 
        padding: 10px 15px; background: rgba(26,10,10,0.8); 
        border: 1px solid rgba(192,57,43,0.3); border-radius: 8px; 
        color: #fff; font-family: 'Outfit', sans-serif; font-size: 14px;
    }
    .update-form select:focus, .update-form input:focus { border-color: #c0392b; outline: none; }
    
    .btn-update { 
        background: linear-gradient(135deg, #c0392b, #8b1a12); color: #fff; 
        border: none; padding: 12px 25px; border-radius: 8px; 
        cursor: pointer; font-size: 14px; font-weight: 700; 
        text-transform: uppercase; letter-spacing: 1px; transition: all 0.3s;
    }
    .btn-update:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(192,57,43,0.4); }
    
    .alert-success { background: rgba(39,174,96,0.2); color: #a0f0b0; padding: 15px 20px; border-radius: 10px; border: 1px solid #27ae60; margin-bottom: 25px; font-weight: 600; }
    .manage-link { color: #c9a84c; font-weight: 700; text-decoration: none; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; }
    .manage-link:hover { color: #fff; }
</style>

<div class="admin-wrap">
    <h1>📦 Manage Orders</h1>
    <div class="admin-nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="orders.php" class="active">Manage Orders</a>
        <a href="products.php">Manage Products</a>
        <a href="../products.php" class="view-store">← View Store</a>
    </div>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert-success">✓ Order updated successfully.</div>
    <?php endif; ?>

    <!-- Detail view -->
    <?php if ($detail): ?>
        <div class="detail-card">
            <h3>Order: <?= htmlspecialchars($detail['order_number']) ?></h3>
            <div class="detail-grid">
                <div class="d-item"><label>Customer</label><span><?= htmlspecialchars($detail['username']) ?> (<?= htmlspecialchars($detail['email']) ?>)</span></div>
                <div class="d-item"><label>Phone</label><span><?= htmlspecialchars($detail['phone_number'] ?? '-') ?></span></div>
                <div class="d-item"><label>Total</label><span style="color:#c9a84c;">Rs. <?= number_format($detail['total_amount']) ?></span></div>
                <div class="d-item"><label>Payment</label><span><?= strtoupper($detail['payment_method']) ?> (<?= strtoupper($detail['payment_status']) ?>)</span></div>
                <div class="d-item"><label>Shipping</label><span><?= htmlspecialchars($detail['shipping_address'] . ', ' . $detail['shipping_city']) ?></span></div>
                <div class="d-item"><label>Date</label><span><?= date('d M Y, h:i A', strtotime($detail['created_at'])) ?></span></div>
            </div>
            <?php while ($it = $detail_items->fetch_assoc()): ?>
                <div class="item-row">
                    <img src="<?= $css_prefix ?>images/<?= htmlspecialchars($it['image']) ?>" alt="">
                    <span><?= htmlspecialchars($it['name']) ?> ×<?= $it['quantity'] ?></span>
                    <span style="margin-left:auto;color:#c9a84c;font-weight:700;">Rs. <?= number_format($it['price'] * $it['quantity']) ?></span>
                </div>
            <?php endwhile; ?>

            <!-- Update form -->
            <form method="POST" action="" class="update-form" style="margin-top:30px;display:flex;gap:15px;flex-wrap:wrap;align-items:flex-end;">
                <input type="hidden" name="order_id" value="<?= $detail['id'] ?>">
                <div>
                    <label style="font-size:11px;font-weight:700;display:block;margin-bottom:8px;text-transform:uppercase;color:#9a7070;">Order Status</label>
                    <select name="order_status">
                        <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                            <option value="<?= $s ?>" <?= $detail['order_status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="font-size:11px;font-weight:700;display:block;margin-bottom:8px;text-transform:uppercase;color:#9a7070;">Payment Status</label>
                    <select name="payment_status">
                        <?php foreach (['pending','paid','failed'] as $s): ?>
                            <option value="<?= $s ?>" <?= $detail['payment_status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="font-size:11px;font-weight:700;display:block;margin-bottom:8px;text-transform:uppercase;color:#9a7070;">Tracking Number</label>
                    <input type="text" name="tracking_number" value="<?= htmlspecialchars($detail['tracking_number'] ?? '') ?>" placeholder="e.g. NPC123456">
                </div>
                <button type="submit" name="update_order" class="btn-update">Update Order</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Filter bar -->
    <div class="filter-bar">
        <a href="orders.php" <?= !$filter ? 'class="active"' : '' ?>>All Orders</a>
        <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
            <a href="orders.php?status=<?= $s ?>" <?= $filter===$s?'class="active"':'' ?>><?= ucfirst($s) ?></a>
        <?php endforeach; ?>
    </div>

    <table class="data-table">
        <thead><tr><th>Order #</th><th>Customer</th><th>Amount</th><th>Payment</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
        <tbody>
        <?php while ($ord = $orders->fetch_assoc()): ?>
            <tr>
                <td><strong><?= htmlspecialchars($ord['order_number']) ?></strong></td>
                <td><?= htmlspecialchars($ord['username']) ?></td>
                <td><span style="color:#c9a84c;font-weight:700;">Rs. <?= number_format($ord['total_amount']) ?></span></td>
                <td><span class="badge badge-<?= $ord['payment_status'] ?>"><?= $ord['payment_status'] ?></span></td>
                <td><span class="badge badge-<?= $ord['order_status'] ?>"><?= $ord['order_status'] ?></span></td>
                <td><?= date('d M Y', strtotime($ord['created_at'])) ?></td>
                <td><a href="orders.php?id=<?= $ord['id'] ?>" class="manage-link">Manage →</a></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
