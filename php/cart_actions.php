<?php
// php/cart_actions.php – handles both AJAX (JSON) and regular form POST (redirect)
session_start();
require_once '../config.php';

$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_POST['ajax']) && $_POST['ajax'] === '1');

function jsonOut($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
function redirectOut($url) {
    header("Location: $url");
    exit;
}

if (!isset($_SESSION['user_id'])) {
    if ($is_ajax) jsonOut(['success'=>false,'message'=>'Please login first.','redirect'=>'login.php']);
    else redirectOut('login.php');
}

$uid    = (int)$_SESSION['user_id'];
$action = $_POST['action'] ?? '';

function cartCount($conn, $uid) {
    try {
        $r = $conn->query("SELECT COALESCE(SUM(quantity),0) AS t FROM cart WHERE user_id=$uid");
        return (int)($r->fetch_assoc()['t'] ?? 0);
    } catch (Exception $e) { return 0; }
}

try {
    switch ($action) {

        // ── ADD ─────────────────────────────────────────────
        case 'add':
            $watch_id = (int)($_POST['watch_id'] ?? 0);
            $qty      = max(1, (int)($_POST['quantity'] ?? 1));
            if ($watch_id <= 0) {
                if ($is_ajax) jsonOut(['success'=>false,'message'=>'Invalid product.']);
                else redirectOut('products.php');
            }
            $stmt = $conn->prepare(
                "INSERT INTO cart (user_id, watch_id, quantity) VALUES (?,?,?)
                 ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)"
            );
            $stmt->bind_param('iii', $uid, $watch_id, $qty);
            $stmt->execute();
            $stmt->close();

            if ($is_ajax) {
                jsonOut(['success'=>true,'message'=>'Added to cart!','count'=>cartCount($conn,$uid)]);
            } else {
                // non-AJAX: redirect based on button
                $dest = ($_POST['buy_now'] ?? '0') === '1' ? 'checkout.php' : 'cart.php';
                redirectOut($dest);
            }
            break;

        // ── UPDATE ──────────────────────────────────────────
        case 'update':
            $watch_id = (int)($_POST['watch_id'] ?? 0);
            $qty      = (int)($_POST['quantity'] ?? 1);
            if ($qty <= 0) {
                $conn->query("DELETE FROM cart WHERE user_id=$uid AND watch_id=$watch_id");
            } else {
                $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE user_id=? AND watch_id=?");
                $stmt->bind_param('iii', $qty, $uid, $watch_id);
                $stmt->execute();
                $stmt->close();
            }
            if ($is_ajax) jsonOut(['success'=>true,'count'=>cartCount($conn,$uid)]);
            else redirectOut('cart.php');
            break;

        // ── REMOVE ──────────────────────────────────────────
        case 'remove':
            $watch_id = (int)($_POST['watch_id'] ?? 0);
            $conn->query("DELETE FROM cart WHERE user_id=$uid AND watch_id=$watch_id");
            if ($is_ajax) jsonOut(['success'=>true,'message'=>'Removed.','count'=>cartCount($conn,$uid)]);
            else redirectOut('cart.php');
            break;

        default:
            if ($is_ajax) jsonOut(['success'=>false,'message'=>'Unknown action.']);
            else redirectOut('products.php');
    }
} catch (Exception $e) {
    if ($is_ajax) jsonOut(['success'=>false,'message'=>'DB error: '.$e->getMessage()]);
    else redirectOut('products.php?error=cart_failed');
}
