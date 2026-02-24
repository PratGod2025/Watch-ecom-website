<?php
// php/includes/header.php
if (!isset($css_prefix)) $css_prefix = '../';
if (!isset($page_title))  $page_title  = 'Time-Hub';

// Cart count — safe even if cart table doesn't exist yet
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    try {
        $cc = $conn->query("SELECT COALESCE(SUM(quantity),0) AS total FROM cart WHERE user_id = $uid");
        if ($cc) $cart_count = (int)$cc->fetch_assoc()['total'];
    } catch (Exception $e) { $cart_count = 0; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> | Time-Hub</title>
    <link rel="stylesheet" href="<?= $css_prefix ?>css/style.css">
    <style>
        .header { background: #1a1a1a; border-bottom: 2px solid #c0392b; }
        .navbar { display:flex; align-items:center; padding:12px 24px; gap:16px; }
        .navbar a { text-decoration:none; }
        .logo { filter: brightness(0) invert(1); } /* Make logo white if it was dark */
        nav { flex:1; text-align:right; }
        nav ul { display:inline-block; list-style:none; margin:0; padding:0; }
        nav ul li { display:inline-block; margin-right:20px; }
        nav ul li a { color:#fff; font-size:15px; transition:color .2s; font-family: 'Outfit', sans-serif; }
        nav ul li a:hover { color:#c0392b; font-weight:600; }
        .nav-auth { display:flex; align-items:center; gap:8px; flex-shrink:0; }
        .nav-auth a { color:#c9a84c; border:1px solid #c9a84c; border-radius:20px; padding:5px 14px; font-size:13px; font-weight:600; transition:all .2s; font-family: 'Outfit', sans-serif; }
        .nav-auth a:hover { background:#c9a84c; color:#1a1a1a; }
        .cart-wrap { position:relative; margin-left:6px; }
        .cart-wrap img { cursor:pointer; display:block; filter: brightness(0) invert(1); }
        #cart-counter {
            position:absolute; top:-8px; right:-10px;
            background: #000080; color:#fff; border-radius:50%;
            width: 20px; height: 20px;
            display:<?= $cart_count > 0 ? 'flex' : 'none' ?>;
            align-items: center;
            justify-content: center;
            font-size: 11px; font-weight: bold;
            box-shadow: 0 0 5px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>
<div class="header">
    <div class="container">
        <div class="navbar">
            <a href="<?= $css_prefix ?>index.php">
                <img class="logo" src="<?= $css_prefix ?>images/logo.png" width="125" alt="Time-Hub">
            </a>
            <nav>
                <ul>
                    <li><a href="<?= $css_prefix ?>index.php">Home</a></li>
                    <li><a href="<?= $css_prefix ?>php/products.php">Products</a></li>
                    <li><a href="<?= $css_prefix ?>php/blog.php">Blog</a></li>
                    <li><a href="<?= $css_prefix ?>php/contact.php">Contact</a></li>
                </ul>
            </nav>
            <div class="nav-auth">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (!empty($_SESSION['is_admin'])): ?>
                        <a href="<?= $css_prefix ?>php/admin/dashboard.php">⚙ Admin</a>
                    <?php endif; ?>
                    <a href="<?= $css_prefix ?>php/orders.php">My Orders</a>
                    <a href="<?= $css_prefix ?>php/logout.php">Logout (<?= htmlspecialchars($_SESSION['username']) ?>)</a>
                <?php else: ?>
                    <a href="<?= $css_prefix ?>php/login.php">Login</a>
                    <a href="<?= $css_prefix ?>php/register.php">Register</a>
                <?php endif; ?>
                <div class="cart-wrap">
                    <a href="<?= $css_prefix ?>php/cart.php">
                        <img src="<?= $css_prefix ?>images/cart.png" alt="Cart" width="30" height="30">
                    </a>
                    <span id="cart-counter"><?= $cart_count ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
