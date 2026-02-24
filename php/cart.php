<?php
session_start();
require_once '../config.php';

$page_title = 'My Cart';
$css_prefix = '../';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=cart.php');
    exit;
}

$uid = (int)$_SESSION['user_id'];

// Fetch cart items with watch info
$cart_items = [];
$subtotal   = 0;
$result = $conn->query(
    "SELECT c.watch_id, c.quantity, w.name, w.brand, w.price, w.image
     FROM cart c
     JOIN watches w ON c.watch_id = w.id
     WHERE c.user_id = $uid"
);
while ($row = $result->fetch_assoc()) {
    $row['line_total'] = $row['price'] * $row['quantity'];
    $subtotal += $row['line_total'];
    $cart_items[] = $row;
}

include 'includes/header.php';
?>
<style>
    body { background: #1a0a0a; color: #f0e0e0; font-family: 'Outfit', Arial, sans-serif; margin: 0; }
    .cart-page { max-width:1200px; margin:40px auto; padding:0 20px; }
    .cart-page h1 { color: #fff; margin-bottom: 24px; font-size: 32px; font-weight: 700; letter-spacing: 1px; }
    .cart-table { width:100%; border-collapse:collapse; background: rgba(42,14,14,0.8); backdrop-filter: blur(10px); border-radius: 15px; overflow:hidden; border: 1px solid rgba(192,57,43,0.3); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
    .cart-table th { background: #2a0e0e; color: #c0392b; padding: 18px 16px; text-align: left; font-size: 14px; text-transform: uppercase; letter-spacing: 2px; border-bottom: 1px solid rgba(192,57,43,0.2); }
    .cart-table td { padding: 20px 16px; border-bottom: 1px solid rgba(192,57,43,0.1); vertical-align: middle; }
    .cart-table tr:last-child td { border-bottom: none; }
    .cart-table img { width: 80px; height: 80px; object-fit: contain; border-radius: 10px; background: radial-gradient(circle, #3d0b0b 0%, #1a0a0a 100%); padding: 5px; }
    .item-name { font-weight: 700; color: #fff; font-size: 16px; margin-bottom: 4px; }
    .item-brand { font-size: 12px; color: #c0392b; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; }
    .qty-controls { display:flex; align-items:center; gap:10px; }
    .qty-controls button { width:32px; height:32px; border:1px solid #c0392b; border-radius:6px; background:transparent; color:#fff; cursor:pointer; font-size:18px; font-weight:700; transition: all 0.2s; }
    .qty-controls button:hover { background:#c0392b; }
    .qty-val { width: 40px; text-align: center; font-weight: 700; color: #fff; font-size: 16px; }
    .btn-remove { background:none; border:none; color:#c0392b; cursor:pointer; font-size:24px; transition: transform 0.2s; }
    .btn-remove:hover { transform: scale(1.2); color: #e74c3c; }
    .price-col { font-weight: 700; color: #c9a84c; font-size: 16px; }
    .cart-summary { background: rgba(42,14,14,0.8); backdrop-filter: blur(10px); border-radius: 15px; border: 1px solid rgba(192,57,43,0.3); padding: 30px; margin-top: 30px; display: flex; justify-content: flex-end; align-items: center; gap: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
    .cart-summary .total-label { font-size: 18px; color: #c0a0a0; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
    .cart-summary .total-value { font-size: 30px; font-weight: 700; color: #c9a84c; }
    .btn-checkout { background: linear-gradient(135deg, #c0392b, #8b1a12); color: #fff; border: none; padding: 15px 40px; border-radius: 10px; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; text-decoration: none; display: inline-block; transition: all 0.3s; }
    .btn-checkout:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(192,57,43,0.4); }
    .empty-cart { text-align: center; padding: 80px 40px; background: rgba(42,14,14,0.8); backdrop-filter: blur(10px); border-radius: 15px; border: 1px solid rgba(192,57,43,0.3); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
    .empty-cart h2 { color: #fff; margin-bottom: 16px; font-size: 24px; }
    .empty-cart a { background: #c0392b; color: #fff; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 700; transition: all 0.3s; }
    .empty-cart a:hover { background: #8b1a12; }
    .cart-msg { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 600; display: none; }
</style>

<div class="cart-page">
    <h1>🛒 Shopping Cart</h1>
    <div id="cart-msg" class="cart-msg"></div>

    <?php if (empty($cart_items)): ?>
        <div class="empty-cart">
            <h2>Your cart is empty</h2>
            <p style="color:#aaa;margin-bottom:20px;">Discover our premium watch collection.</p>
            <a href="products.php">Shop Now</a>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody id="cart-body">
            <?php foreach ($cart_items as $item): ?>
                <tr id="row-<?= $item['watch_id'] ?>">
                    <td><img src="../images/<?= htmlspecialchars($item['image']) ?>" alt=""></td>
                    <td>
                        <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                        <div class="item-brand"><?= htmlspecialchars($item['brand']) ?></div>
                    </td>
                    <td class="price-col">Rs. <?= number_format($item['price']) ?></td>
                    <td>
                        <div class="qty-controls">
                            <button onclick="updateQty(<?= $item['watch_id'] ?>, -1)">−</button>
                            <span class="qty-val" id="qty-<?= $item['watch_id'] ?>"><?= $item['quantity'] ?></span>
                            <button onclick="updateQty(<?= $item['watch_id'] ?>, 1)">+</button>
                        </div>
                    </td>
                    <td class="price-col" id="line-<?= $item['watch_id'] ?>">Rs. <?= number_format($item['line_total']) ?></td>
                    <td>
                        <button class="btn-remove" onclick="removeItem(<?= $item['watch_id'] ?>)" title="Remove">✕</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <span class="total-label">Subtotal:</span>
            <span class="total-value" id="cart-total">Rs. <?= number_format($subtotal) ?></span>
            <a href="checkout.php" class="btn-checkout">Proceed to Checkout →</a>
        </div>
    <?php endif; ?>
</div>

<script>
const prices = {
    <?php foreach ($cart_items as $item): ?>
    <?= $item['watch_id'] ?>: <?= $item['price'] ?>,
    <?php endforeach; ?>
};

function showMsg(text, ok) {
    const m = document.getElementById('cart-msg');
    m.textContent = text;
    m.style.display = 'block';
    m.style.background = ok ? '#eafaf1' : '#fdecea';
    m.style.color = ok ? '#1e8449' : '#c0392b';
    m.style.borderLeft = `4px solid ${ok ? '#27ae60' : '#c0392b'}`;
    setTimeout(() => m.style.display = 'none', 3000);
}

function updateCartCounter(count) {
    const el = document.getElementById('cart-counter');
    if (el) { el.textContent = count; el.style.display = count > 0 ? 'block' : 'none'; }
}

function recalcTotal() {
    let total = 0;
    document.querySelectorAll('[id^="qty-"]').forEach(el => {
        const wid = el.id.replace('qty-', '');
        const qty = parseInt(el.textContent);
        const lineEl = document.getElementById('line-' + wid);
        if (lineEl && prices[wid]) {
            const line = prices[wid] * qty;
            total += line;
            lineEl.textContent = 'Rs. ' + line.toLocaleString('en-IN');
        }
    });
    document.getElementById('cart-total').textContent = 'Rs. ' + total.toLocaleString('en-IN');
}

function updateQty(watchId, delta) {
    const qEl = document.getElementById('qty-' + watchId);
    let qty = parseInt(qEl.textContent) + delta;
    if (qty < 1) { removeItem(watchId); return; }
    qEl.textContent = qty;
    recalcTotal();
    fetch('cart_actions.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: `action=update&watch_id=${watchId}&quantity=${qty}&ajax=1`
    }).then(r => r.json()).then(d => { updateCartCounter(d.count); });
}

function removeItem(watchId) {
    if (!confirm('Remove this item from your cart?')) return;
    fetch('cart_actions.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: `action=remove&watch_id=${watchId}&ajax=1`
    }).then(r => r.json()).then(d => {
        const row = document.getElementById('row-' + watchId);
        if (row) row.remove();
        updateCartCounter(d.count);
        showMsg('Item removed from cart.', true);
        delete prices[watchId];
        recalcTotal();
        if (document.querySelectorAll('[id^="row-"]').length === 0) location.reload();
    });
}
</script>

<?php include 'includes/footer.php'; ?>
