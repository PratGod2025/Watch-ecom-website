<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

$page_title = 'Admin Dashboard';
$css_prefix = '../../';

// Stats — safe if tables don't exist yet
$total_orders = $total_revenue = $total_users = $total_products = $pending_orders = 0;
$recent = null;
try {
    $total_orders   = (int)$conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];
    $total_revenue  = (float)($conn->query("SELECT COALESCE(SUM(total_amount),0) AS s FROM orders WHERE order_status != 'cancelled'")->fetch_assoc()['s']);
    $total_users    = (int)$conn->query("SELECT COUNT(*) AS c FROM users WHERE is_admin=0")->fetch_assoc()['c'];
    $total_products = (int)$conn->query("SELECT COUNT(*) AS c FROM watches")->fetch_assoc()['c'];
    $pending_orders = (int)$conn->query("SELECT COUNT(*) AS c FROM orders WHERE order_status='pending'")->fetch_assoc()['c'];
    $recent = $conn->query(
        "SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC LIMIT 10"
    );
} catch (Exception $e) {}

include '../includes/header.php';
?>
<style>
    body { background: #1a0a0a; color: #f0e0e0; font-family: 'Outfit', Arial, sans-serif; margin: 0; }
    .admin-wrap  { max-width:1240px; margin:40px auto; padding:0 20px; }
    .admin-wrap h1 { color: #fff; margin-bottom: 25px; font-weight: 700; font-size: 32px; letter-spacing: 1px; }
    
    .admin-nav { display: flex; gap: 15px; margin-bottom: 35px; flex-wrap: wrap; }
    .admin-nav a { 
        padding: 12px 24px; 
        border-radius: 10px; 
        text-decoration: none; 
        font-size: 14px; 
        font-weight: 700; 
        background: rgba(42,14,14,0.6); 
        color: #c0a0a0; 
        border: 1px solid rgba(192,57,43,0.3); 
        transition: all 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .admin-nav a:hover, .admin-nav a.active { 
        background: #c0392b; 
        color: #fff; 
        border-color: #c0392b; 
        box-shadow: 0 0 15px rgba(192,57,43,0.4);
    }
    .admin-nav a.view-store { background: transparent; border-color: #c9a84c; color: #c9a84c; }
    .admin-nav a.view-store:hover { background: #c9a84c; color: #1a0a0a; }

    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px; }
    .stat-card { 
        background: rgba(42,14,14,0.8); 
        backdrop-filter: blur(10px);
        border-radius: 15px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.4); 
        padding: 25px; 
        border: 1px solid rgba(192,57,43,0.2);
        transition: transform 0.3s;
    }
    .stat-card:hover { transform: translateY(-5px); border-color: #c0392b; }
    .stat-card .stat-val { font-size: 32px; font-weight: 700; color: #fff; line-height: 1; margin-top: 10px; }
    .stat-card .stat-label { font-size: 12px; color: #c0a0a0; margin-top: 10px; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700; }
    .stat-card .stat-icon { font-size: 32px; float: right; color: #c0392b; opacity: 0.8; }
    
    table.data-table { 
        width: 100%; 
        border-collapse: collapse; 
        background: rgba(42,14,14,0.8); 
        backdrop-filter: blur(10px);
        border-radius: 15px; 
        overflow: hidden; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        border: 1px solid rgba(192,57,43,0.3);
    }
    table.data-table th { background: #2a0e0e; color: #c0392b; padding: 18px 16px; text-align: left; font-size: 13px; text-transform: uppercase; letter-spacing: 2px; border-bottom: 1px solid rgba(192,57,43,0.2); }
    table.data-table td { padding: 15px 16px; border-bottom: 1px solid rgba(192,57,43,0.1); font-size: 14px; color: #f0e0e0; }
    
    .badge { display: inline-block; padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
    .badge-pending    { background: rgba(192,57,43,0.2); color: #f0a0a0; border: 1px solid #c0392b; }
    .badge-processing { background: rgba(52,152,219,0.2); color: #a0d4f0; border: 1px solid #3498db; }
    .badge-shipped    { background: rgba(201,168,76,0.2); color: #f0e0a0; border: 1px solid #c9a84c; }
    .badge-delivered  { background: rgba(39,174,96,0.2); color: #a0f0b0; border: 1px solid #27ae60; }
    .badge-cancelled  { background: rgba(149,165,166,0.2); color: #f0f0f0; border: 1px solid #95a5a6; }
    
    .section-title { font-size: 22px; color: #fff; font-weight: 700; margin: 0 0 20px; letter-spacing: 1px; }
    .manage-link { color: #c9a84c; font-weight: 700; text-decoration: none; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; }
    .manage-link:hover { color: #fff; }
</style>

<div class="admin-wrap">
    <h1>⚙️ Admin Dashboard</h1>
    <div class="admin-nav">
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="orders.php">Manage Orders</a>
        <a href="products.php">Manage Products</a>
        <a href="../products.php" class="view-store">← View Store</a>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-icon">📦</span>
            <div class="stat-val"><?= $total_orders ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
            <span class="stat-icon" style="color:#27ae60;">💰</span>
            <div class="stat-val">Rs. <?= number_format($total_revenue) ?></div>
            <div class="stat-label">Total Revenue</div>
        </div>
        <div class="stat-card">
            <span class="stat-icon" style="color:#3498db;">👥</span>
            <div class="stat-val"><?= $total_users ?></div>
            <div class="stat-label">Registered Users</div>
        </div>
        <div class="stat-card">
            <span class="stat-icon" style="color:#c9a84c;">⌚</span>
            <div class="stat-val"><?= $total_products ?></div>
            <div class="stat-label">Products</div>
        </div>
        <div class="stat-card">
            <span class="stat-icon" style="color:#e74c3c;">🔔</span>
            <div class="stat-val"><?= $pending_orders ?></div>
            <div class="stat-label">Pending Orders</div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="section-title">Recent Orders</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!$recent || $recent->num_rows === 0): ?>
            <tr><td colspan="7" style="text-align:center;color:#664444;padding:40px;font-style:italic;">No orders yet.</td></tr>
        <?php endif; ?>
        <?php while ($recent && $ord = $recent->fetch_assoc()): ?>
            <tr>
                <td><strong><?= htmlspecialchars($ord['order_number']) ?></strong></td>
                <td><?= htmlspecialchars($ord['username']) ?></td>
                <td><span style="color:#c9a84c;font-weight:700;">Rs. <?= number_format($ord['total_amount']) ?></span></td>
                <td style="font-weight:700;"><?= strtoupper($ord['payment_method']) ?></td>
                <td><span class="badge badge-<?= $ord['order_status'] ?>"><?= $ord['order_status'] ?></span></td>
                <td><?= date('d M Y', strtotime($ord['created_at'])) ?></td>
                <td><a href="orders.php?id=<?= $ord['id'] ?>" class="manage-link">Manage →</a></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
