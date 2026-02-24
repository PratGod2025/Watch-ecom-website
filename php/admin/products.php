<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

$page_title = 'Manage Products';
$css_prefix = '../../';

// Toggle featured / latest
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $wid   = (int)$_POST['watch_id'];
    $field = in_array($_POST['field'], ['is_featured','is_latest']) ? $_POST['field'] : null;
    if ($field) {
        $conn->query("UPDATE watches SET $field = 1 - $field WHERE id=$wid");
    }
    header('Location: products.php?updated=1');
    exit;
}

$watches = $conn->query("SELECT * FROM watches ORDER BY id DESC");

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

    table.data-table { 
        width: 100%; border-collapse: collapse; background: rgba(42,14,14,0.8); 
        backdrop-filter: blur(10px); border-radius: 15px; overflow: hidden; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.4); border: 1px solid rgba(192,57,43,0.3);
        margin-bottom: 30px;
    }
    table.data-table th { background: #2a0e0e; color: #c0392b; padding: 18px 16px; text-align: left; font-size: 13px; text-transform: uppercase; letter-spacing: 2px; border-bottom: 1px solid rgba(192,57,43,0.2); }
    table.data-table td { padding: 15px 16px; border-bottom: 1px solid rgba(192,57,43,0.1); font-size: 14px; color: #f0e0e0; vertical-align: middle; }

    .thumb { width: 60px; height: 60px; object-fit: contain; background: radial-gradient(circle, #3d0b0b 0%, #1a0a0a 100%); border-radius: 10px; border: 1px solid rgba(192,57,43,0.2); }
    
    .toggle-btn { border: none; padding: 6px 15px; border-radius: 20px; cursor: pointer; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; transition: all 0.3s; }
    .toggle-on  { background: rgba(39,174,96,0.2); color: #a0f0b0; border: 1px solid #27ae60; }
    .toggle-on:hover { background: #27ae60; color: #fff; }
    .toggle-off { background: rgba(192,57,43,0.2); color: #f0a0a0; border: 1px solid #c0392b; }
    .toggle-off:hover { background: #c0392b; color: #fff; }
    
    .alert-success { background: rgba(39,174,96,0.2); color: #a0f0b0; padding: 15px 20px; border-radius: 10px; border: 1px solid #27ae60; margin-bottom: 25px; font-weight: 600; }
</style>

<div class="admin-wrap">
    <h1>⌚ Manage Products</h1>
    <div class="admin-nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="orders.php">Manage Orders</a>
        <a href="products.php" class="active">Manage Products</a>
        <a href="../products.php">← View Store</a>
    </div>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert-success">✓ Product updated.</div>
    <?php endif; ?>

    <table class="data-table">
        <thead><tr><th>Image</th><th>Name</th><th>Brand</th><th>Price</th><th>Rating</th><th>Featured</th><th>Latest</th></tr></thead>
        <tbody>
        <?php while ($w = $watches->fetch_assoc()): ?>
            <tr>
                <td><img class="thumb" src="<?= $css_prefix ?>images/<?= htmlspecialchars($w['image']) ?>" alt=""></td>
                <td><?= htmlspecialchars($w['name']) ?></td>
                <td><?= htmlspecialchars($w['brand']) ?></td>
                <td>Rs. <?= number_format($w['price']) ?></td>
                <td>⭐ <?= number_format($w['rating'] ?? 0, 1) ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="watch_id" value="<?= $w['id'] ?>">
                        <input type="hidden" name="field" value="is_featured">
                        <button type="submit" class="toggle-btn <?= $w['is_featured'] ? 'toggle-on' : 'toggle-off' ?>">
                            <?= $w['is_featured'] ? '✓ Featured' : '✗ Not Featured' ?>
                        </button>
                    </form>
                </td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="watch_id" value="<?= $w['id'] ?>">
                        <input type="hidden" name="field" value="is_latest">
                        <button type="submit" class="toggle-btn <?= $w['is_latest'] ? 'toggle-on' : 'toggle-off' ?>">
                            <?= $w['is_latest'] ? '✓ Latest' : '✗ Not Latest' ?>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
