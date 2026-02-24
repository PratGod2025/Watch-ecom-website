<?php
session_start();
require_once '../config.php';

$page_title = 'Checkout';
$css_prefix = '../';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=checkout.php');
    exit;
}

$uid = (int)$_SESSION['user_id'];

// Pre-fill user details
$user_res = $conn->prepare("SELECT * FROM users WHERE id=?");
$user_res->bind_param('i', $uid);
$user_res->execute();
$user = $user_res->get_result()->fetch_assoc();
$user_res->close();

// Fetch cart
$cart_items = [];
$subtotal   = 0;
try {
    $result = $conn->query(
        "SELECT c.watch_id, c.quantity, w.name, w.price, w.image
         FROM cart c JOIN watches w ON c.watch_id = w.id
         WHERE c.user_id = $uid"
    );
    while ($row = $result->fetch_assoc()) {
        $row['line_total'] = $row['price'] * $row['quantity'];
        $subtotal += $row['line_total'];
        $cart_items[] = $row;
    }
} catch (Exception $e) {}

if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $shipping_address = trim($_POST['address']  ?? '');
    $shipping_city    = trim($_POST['city']      ?? '');
    $shipping_phone   = trim($_POST['phone']     ?? '');
    $shipping_country = trim($_POST['country']   ?? 'Nepal');
    $payment_method   = in_array($_POST['payment_method'] ?? '', ['cod','esewa','khalti'])
                        ? $_POST['payment_method'] : 'cod';

    if (empty($shipping_address) || empty($shipping_city) || empty($shipping_phone)) {
        $error = 'Please fill in your complete shipping address and phone number.';
    } else {
        $order_number = 'TH-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $conn->begin_transaction();
        try {
            $ins = $conn->prepare(
                "INSERT INTO orders
                    (user_id, order_number, total_amount, payment_method,
                     payment_status, shipping_address, shipping_city, shipping_country, phone_number)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            // payment_status: cod=pending (collect on delivery), online=pending (admin to verify)
            $pay_status = 'pending';
            $ins->bind_param(
                'isdssssss',
                $uid, $order_number, $subtotal, $payment_method,
                $pay_status, $shipping_address, $shipping_city, $shipping_country, $shipping_phone
            );
            $ins->execute();
            $order_id = $conn->insert_id;
            $ins->close();

            $item_ins = $conn->prepare(
                "INSERT INTO order_items (order_id, watch_id, quantity, price) VALUES (?,?,?,?)"
            );
            foreach ($cart_items as $item) {
                $item_ins->bind_param('iiid', $order_id, $item['watch_id'], $item['quantity'], $item['price']);
                $item_ins->execute();
            }
            $item_ins->close();

            $conn->query("DELETE FROM cart WHERE user_id = $uid");
            $conn->commit();

            header("Location: order_confirmation.php?order=" . urlencode($order_number));
            exit;

        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Order failed: ' . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>
<style>
    body { background: #1a0a0a; color: #f0e0e0; font-family: 'Outfit', Arial, sans-serif; margin: 0; }

    /* Layout */
    .checkout-wrap { max-width:1100px; margin:40px auto; padding:0 20px; display:flex; gap:30px; flex-wrap:wrap; align-items:flex-start; }
    .checkout-left { flex:2; min-width:300px; }
    .checkout-left h2 { color:#fff; margin:0 0 25px; font-size: 28px; font-weight: 700; letter-spacing: 1px; }
    
    .section-box { 
        background: rgba(42,14,14,0.8); 
        backdrop-filter: blur(12px);
        border-radius: 20px; 
        box-shadow: 0 15px 40px rgba(0,0,0,0.5); 
        padding: 30px; 
        margin-bottom: 25px;
        border: 1px solid rgba(192,57,43,0.3);
    }
    .section-box h3 { color: #fff; margin: 0 0 20px; font-size: 18px; border-bottom: 2px solid #c0392b; padding-bottom: 10px; font-weight: 700; letter-spacing: 1px; }

    /* Form */
    .form-row    { display:flex; gap:15px; }
    .form-group  { margin-bottom:18px; flex:1; }
    .form-group label { display:block; font-size:11px; font-weight:700; color:#9a7070; margin-bottom:6px; text-transform:uppercase; letter-spacing: 1px; }
    .form-group input { 
        width:100%; padding:12px 15px; background: rgba(26,10,10,0.8); 
        border: 1px solid rgba(192,57,43,0.3); border-radius: 10px; 
        color: #fff; font-family: 'Outfit', sans-serif; font-size: 14px; box-sizing:border-box; transition:all 0.3s;
    }
    .form-group input:focus { border-color: #c0392b; outline:none; box-shadow: 0 0 10px rgba(192,57,43,0.2); }

    /* Payment cards */
    .payment-opts { display:flex; gap:15px; flex-wrap:wrap; }
    .pay-card { 
        flex:1; min-width:140px; border:2px solid rgba(192,57,43,0.2); border-radius:15px; 
        padding:20px 15px; cursor:pointer; text-align:center; transition:all 0.3s;
        background: rgba(26,10,10,0.4);
    }
    .pay-card:hover { border-color: #c0392b; transform: translateY(-3px); }
    .pay-card input { display:none; }
    .pay-card.picked { border-color:#c0392b; background: rgba(192,57,43,0.1); box-shadow:0 10px 20px rgba(0,0,0,0.3); }
    .pay-icon    { font-size:32px; margin-bottom:8px; }
    .pay-label   { font-size:14px; font-weight:700; color:#fff; }
    .pay-sublabel{ font-size:11px; color:#9a7070; margin-top:4px; }

    /* QR modal */
    .qr-box { 
        display:none; margin-top:25px; background: rgba(26,10,10,0.6); 
        border: 1px solid rgba(192,57,43,0.3); border-radius: 15px; 
        padding:30px; text-align:center; animation:fadeIn .3s ease; 
    }
    .qr-box.show { display:block; }
    .qr-box h4   { color:#fff; margin:0 0 15px; font-size:18px; font-weight: 700; }
    .qr-box p    { color:#c0a0a0; font-size:14px; margin:8px 0; }
    .qr-img      { width:220px; height:220px; border:8px solid #fff; border-radius:15px; box-shadow:0 15px 30px rgba(0,0,0,0.5); margin:20px auto; display:block; }
    .qr-amount   { font-size:28px; font-weight:bold; color:#c9a84c; margin:10px 0; }
    .qr-steps    { text-align:left; background: rgba(42,14,14,0.5); border-radius:12px; padding:20px 25px; margin:20px 0 0; font-size:14px; color:#f0e0e0; border: 1px solid rgba(192,57,43,0.2); }
    .qr-steps li { margin-bottom:8px; }
    .qr-note     { font-size:11px; color:#9a7070; margin-top:15px; font-style:italic; }

    /* Place order button */
    .btn-place { 
        width:100%; padding:16px; background: linear-gradient(135deg, #c0392b, #8b1a12); 
        color:#fff; border:none; border-radius:12px; font-size:18px; font-weight:700; 
        cursor:pointer; margin-top:10px; transition:all 0.3s; text-transform: uppercase; letter-spacing: 1px;
        box-shadow: 0 10px 20px rgba(192,57,43,0.3);
    }
    .btn-place:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(192,57,43,0.4); }

    /* Right: Order summary */
    .checkout-right { flex:1; min-width:300px; }
    .checkout-right h2 { color:#fff; margin:0 0 25px; font-size: 24px; font-weight: 700; }
    .sum-item    { display:flex; align-items:center; gap:15px; padding:15px 0; border-bottom:1px solid rgba(192,57,43,0.1); }
    .sum-item:last-child { border-bottom:none; }
    .sum-item img { width:60px; height:60px; object-fit:contain; background: radial-gradient(circle, #3d0b0b 0%, #1a0a0a 100%); border-radius:10px; border: 1px solid rgba(192,57,43,0.2); }
    .si-name     { font-size:14px; font-weight:700; color:#fff; }
    .si-qty      { font-size:12px; color:#9a7070; margin-top:3px; }
    .si-price    { margin-left:auto; color:#c9a84c; font-weight:700; font-size:14px; white-space:nowrap; }
    .sum-total   { display:flex; justify-content:space-between; margin-top:20px; font-size:22px; font-weight:700; color:#fff; padding-top:20px; border-top:2px solid rgba(192,57,43,0.2); }
    .sum-total span:last-child { color: #c9a84c; }

    .alert-error { background: rgba(192,57,43,0.2); color: #f0a0a0; border-left:4px solid #c0392b; padding:15px 20px; border-radius:10px; margin-bottom:25px; font-size:15px; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
</style>

<div class="checkout-wrap">

    <!-- ── Left: Checkout Form ── -->
    <div class="checkout-left">
        <h2>Checkout</h2>

        <?php if ($error): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" id="checkout-form">
            <input type="hidden" name="place_order" value="1">

            <!-- Shipping Address -->
            <div class="section-box">
                <h3>📦 Shipping Address</h3>
                <div class="form-group">
                    <label>Street / Area *</label>
                    <input type="text" name="address" required
                           value="<?= htmlspecialchars($user['address'] ?? '') ?>"
                           placeholder="e.g. Thamel, Putalisadak…">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>City *</label>
                        <input type="text" name="city" required
                               value="<?= htmlspecialchars($user['city'] ?? '') ?>"
                               placeholder="Kathmandu">
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="text" name="phone" required
                               value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                               placeholder="98XXXXXXXX">
                    </div>
                </div>
                <div class="form-group">
                    <label>Country</label>
                    <input type="text" name="country"
                           value="<?= htmlspecialchars($user['country'] ?? 'Nepal') ?>">
                </div>
            </div>

            <!-- Payment Method -->
            <div class="section-box">
                <h3>💳 Payment Method</h3>

                <div class="payment-opts">
                    <!-- Cash on Delivery -->
                    <label class="pay-card picked" id="opt-cod" onclick="selectPayment('cod')">
                        <input type="radio" name="payment_method" value="cod" checked id="pay-cod">
                        <div class="pay-icon">💵</div>
                        <div class="pay-label">Cash on Delivery</div>
                        <div class="pay-sublabel">Pay when you receive</div>
                    </label>

                    <!-- eSewa -->
                    <label class="pay-card" id="opt-esewa" onclick="selectPayment('esewa')">
                        <input type="radio" name="payment_method" value="esewa" id="pay-esewa">
                        <div class="pay-icon">🟢</div>
                        <div class="pay-label">eSewa</div>
                        <div class="pay-sublabel">Scan QR to pay</div>
                    </label>

                    <!-- Khalti -->
                    <label class="pay-card" id="opt-khalti" onclick="selectPayment('khalti')">
                        <input type="radio" name="payment_method" value="khalti" id="pay-khalti">
                        <div class="pay-icon">🟣</div>
                        <div class="pay-label">Khalti</div>
                        <div class="pay-sublabel">Scan QR to pay</div>
                    </label>
                </div>

                <!-- eSewa QR Section -->
                <div class="qr-box" id="qr-esewa">
                    <h4>🟢 Pay via eSewa</h4>
                    <div class="qr-amount">Rs. <?= number_format($subtotal) ?></div>
                    <p>Scan the QR code below using your <strong>eSewa app</strong></p>
                    <img class="qr-img"
                         src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&color=1a8000&data=<?=
                             urlencode("eSewa Payment\nMerchant: Time-Hub Watch Store\nAmount: NPR " . number_format($subtotal, 2) . "\nePay ID: timehub@esewa\n\nPlease note your Order # after placing the order.")
                         ?>"
                         alt="eSewa QR Code" id="img-esewa">
                    <ol class="qr-steps">
                        <li>Open your <strong>eSewa</strong> app → tap <strong>Scan</strong></li>
                        <li>Scan the QR code above</li>
                        <li>Confirm the amount: <strong>Rs. <?= number_format($subtotal) ?></strong></li>
                        <li>Complete the payment in eSewa</li>
                        <li>Click <strong>"Place Order"</strong> below — we will verify your payment</li>
                    </ol>
                    <p class="qr-note">⚠ Your order status will show "Payment Pending" until verified by our team (usually within 1 hour).</p>
                </div>

                <!-- Khalti QR Section -->
                <div class="qr-box" id="qr-khalti">
                    <h4>🟣 Pay via Khalti</h4>
                    <div class="qr-amount">Rs. <?= number_format($subtotal) ?></div>
                    <p>Scan the QR code below using your <strong>Khalti app</strong></p>
                    <img class="qr-img"
                         src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&color=5c2d91&data=<?=
                             urlencode("Khalti Payment\nMerchant: Time-Hub Watch Store\nAmount: NPR " . number_format($subtotal, 2) . "\nKhalti ID: timehub@khalti\n\nPlease note your Order # after placing the order.")
                         ?>"
                         alt="Khalti QR Code" id="img-khalti">
                    <ol class="qr-steps">
                        <li>Open your <strong>Khalti</strong> app → tap <strong>QR Scan</strong></li>
                        <li>Scan the QR code above</li>
                        <li>Enter amount: <strong>Rs. <?= number_format($subtotal) ?></strong> manually</li>
                        <li>Complete the payment in Khalti</li>
                        <li>Click <strong>"Place Order"</strong> below — we will verify your payment</li>
                    </ol>
                    <p class="qr-note">⚠ Your order will be processed once payment is confirmed by our team.</p>
                </div>
            </div>

            <button type="submit" class="btn-place" id="btn-place">✓ Place Order</button>
        </form>
    </div>

    <!-- ── Right: Order Summary ── -->
    <div class="checkout-right">
        <h2>Order Summary</h2>
        <div class="section-box">
            <?php foreach ($cart_items as $item): ?>
                <div class="sum-item">
                    <img src="../images/<?= htmlspecialchars($item['image']) ?>" alt="">
                    <div>
                        <div class="si-name"><?= htmlspecialchars($item['name']) ?></div>
                        <div class="si-qty">Qty: <?= $item['quantity'] ?></div>
                    </div>
                    <div class="si-price">Rs. <?= number_format($item['line_total']) ?></div>
                </div>
            <?php endforeach; ?>
            <div class="sum-total">
                <span>Total</span>
                <span>Rs. <?= number_format($subtotal) ?></span>
            </div>
        </div>
    </div>

</div>

<script>
function selectPayment(method) {
    // Update radio
    document.getElementById('pay-' + method).checked = true;

    // Update card highlight
    ['cod','esewa','khalti'].forEach(m => {
        document.getElementById('opt-' + m).classList.toggle('picked', m === method);
    });

    // Show/hide QR boxes
    document.getElementById('qr-esewa').classList.toggle('show', method === 'esewa');
    document.getElementById('qr-khalti').classList.toggle('show', method === 'khalti');

    // Update button text
    const btn = document.getElementById('btn-place');
    if (method === 'cod') {
        btn.textContent = '✓ Place Order (Cash on Delivery)';
    } else {
        btn.textContent = '✓ I Have Paid — Place Order';
    }
}
</script>

<?php include 'includes/footer.php'; ?>
