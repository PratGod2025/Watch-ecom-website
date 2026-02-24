<?php
session_start();
require_once '../config.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: products.php'); exit; }

$result = $conn->query("SELECT * FROM watches WHERE id = $id");
$watch  = $result ? $result->fetch_assoc() : null;
if (!$watch) { header('Location: products.php'); exit; }

// Average rating — safe if reviews table not yet created
$avg_rating = 0; $review_count = 0;
try {
    $avg_res = $conn->query("SELECT AVG(rating) AS avg, COUNT(*) AS total FROM reviews WHERE watch_id=$id");
    if ($avg_res) {
        $avg_row      = $avg_res->fetch_assoc();
        $avg_rating   = round((float)($avg_row['avg'] ?? 0), 1);
        $review_count = (int)($avg_row['total'] ?? 0);
    }
} catch (Exception $e) {}

$star_full  = str_repeat('★', min(5, (int)round($avg_rating)));
$star_empty = str_repeat('☆', max(0, 5 - (int)round($avg_rating)));

// Review form messages
$review_msg = '';
if (isset($_GET['review_success'])) $review_msg = 'success';
if (isset($_GET['review_error']))   $review_msg = $_GET['review_error'];

$page_title = htmlspecialchars($watch['name']);
$css_prefix = '../';

include 'includes/header.php';
?>
<style>
    body { background: #1a0a0a; color: #f0e0e0; font-family: 'Outfit', Arial, sans-serif; margin: 0; }
    a { text-decoration: none; }

    .back-btn { display: inline-block; margin: 20px 24px 0; background: #2a0e0e; color: #c0a0a0; padding: 10px 20px; border-radius: 8px; font-size: 14px; border: 1px solid #c0392b; transition: all 0.3s; }
    .back-btn:hover { background: #c0392b; color: #fff; box-shadow: 0 0 15px rgba(192,57,43,0.4); }

    /* Product detail layout */
    .product-detail { display: flex; gap: 40px; max-width: 1200px; margin: 30px auto 50px; background: rgba(42,14,14,0.8); backdrop-filter: blur(10px); padding: 40px; border-radius: 20px; border: 1px solid rgba(192,57,43,0.3); box-shadow: 0 20px 50px rgba(0,0,0,0.5); flex-wrap: wrap; }
    /* Zoom effect */
    .product-image {
        flex: 1; min-width: 300px; text-align: center;
        background: radial-gradient(circle, #3d0b0b 0%, #1a0a0a 100%);
        border-radius: 15px; padding: 20px;
        position: relative; overflow: hidden;
        cursor: zoom-in;
    }
    .product-image img {
        max-width: 100%; max-height: 400px; object-fit: contain;
        filter: drop-shadow(0 15px 30px rgba(0,0,0,0.6));
        transition: transform 0.5s ease-out;
        pointer-events: none;
    }
    .product-image:hover img { transform: scale(1.5); }

    /* Video section */
    .video-section { margin-top: 40px; text-align: center; }
    .video-container {
        max-width: 800px; margin: 0 auto;
        background: #000; border-radius: 15px;
        overflow: hidden; border: 1px solid rgba(192,57,43,0.3);
        box-shadow: 0 15px 40px rgba(0,0,0,0.8);
    }
    .video-container video { width: 100%; display: block; }
    .video-title { font-size: 18px; color: #c9a84c; margin-bottom: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; }

    .product-info { flex: 2; min-width: 300px; }
    .product-info h1 { font-size: 32px; color: #fff; margin: 0 0 10px; line-height: 1.2; font-weight: 700; }
    .brand  { font-size: 14px; color: #c0392b; text-transform: uppercase; letter-spacing: 3px; font-weight: 700; margin-bottom: 15px; }
    .price  { font-size: 34px; font-weight: 700; color: #c9a84c; margin: 15px 0 20px; }
    .description { font-size: 16px; line-height: 1.8; color: #c0a0a0; border-top: 1px solid #4a1515; border-bottom: 1px solid #4a1515; padding: 20px 0; margin-bottom: 25px; }

    /* Specs */
    .specs-box   { background: rgba(26,10,10,0.6); border: 1px solid #4a1515; border-radius: 12px; padding: 20px 25px; margin-bottom: 25px; }
    .specs-title { font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; border-bottom: 2px solid #c0392b; padding-bottom: 10px; margin: 0 0 15px; color: #fff; }
    .specifications { list-style: none; padding: 0; margin: 0; }
    .specifications li { display: flex; gap: 15px; padding: 10px 0; border-bottom: 1px solid #3d0b0b; font-size: 14px; }
    .specifications li:last-child { border-bottom: none; }
    .spec-key   { font-weight: 700; color: #c0392b; min-width: 160px; }
    .spec-value { color: #f0e0e0; }

    /* Quantity */
    .qty-row { display: flex; align-items: center; gap: 20px; margin-bottom: 25px; }
    .qty-row label { font-weight: 700; font-size: 15px; color: #c0a0a0; text-transform: uppercase; letter-spacing: 1px; }
    .qty-ctrl { display: flex; border: 1px solid #4a1515; border-radius: 10px; overflow: hidden; background: #1a0a0a; }
    .qty-ctrl button { width: 45px; height: 45px; border: none; background: #2a0e0e; color: #fff; font-size: 20px; font-weight: 700; cursor: pointer; transition: all 0.3s; }
    .qty-ctrl button:hover { background: #c0392b; }
    .qty-ctrl input  { width: 60px; height: 45px; border: none; background: transparent; color: #fff; text-align: center; font-size: 16px; font-weight: 700; -moz-appearance: textfield; }
    .qty-ctrl input::-webkit-inner-spin-button { display: none; }

    /* Buttons */
    .action-forms  { display: flex; gap: 15px; flex-wrap: wrap; }
    .btn-cart { flex: 1; min-width: 180px; background: linear-gradient(135deg, #c0392b, #8b1a12); color: #fff; border: none; padding: 15px 30px; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.3s; text-transform: uppercase; letter-spacing: 1px; }
    .btn-cart:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(192,57,43,0.4); }
    .btn-buy  { flex: 1; min-width: 180px; background: #c9a84c; color: #1a0a0a; border: none; padding: 15px 30px; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.3s; text-transform: uppercase; letter-spacing: 1px; }
    .btn-buy:hover  { background: #e0c060; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(201,168,76,0.4); }

    /* Review section UI */
    .reviews-wrap { max-width: 1200px; margin: 0 auto 60px; padding: 0 20px; }
    .reviews-box  { background: rgba(42,14,14,0.8); backdrop-filter: blur(10px); border-radius: 20px; border: 1px solid rgba(192,57,43,0.3); padding: 40px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
    .reviews-box h2 { color: #fff; font-size: 24px; margin: 0 0 25px; border-bottom: 2px solid #4a1515; padding-bottom: 10px; }
    .review-item { border-bottom: 1px solid #3d0b0b; padding: 25px 0; }
    .review-item:last-child { border-bottom: none; }
    .rev-header  { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .rev-author  { font-weight: 700; color: #c9a84c; font-size: 16px; }
    .rev-stars   { color: #c0392b; font-size: 16px; }
    .rev-comment { color: #c0a0a0; font-size: 15px; line-height: 1.8; }
    .no-reviews  { color: #664444; text-align: center; padding: 40px 0; font-style: italic; font-size: 16px; }

    /* Review form */
    .review-form-box { background: rgba(26,10,10,0.6); border-radius: 15px; padding: 30px; margin-top: 40px; border: 1px solid #4a1515; }
    .review-form-box h3 { color: #fff; margin: 0 0 20px; font-size: 18px; }
    .star-picker { display: flex; flex-direction: row-reverse; gap: 5px; margin-bottom: 20px; width: fit-content; }
    .star-picker input { display: none; }
    .star-picker label { font-size: 36px; color: #4a1515; cursor: pointer; transition: color 0.3s; }
    .star-picker input:checked ~ label, .star-picker label:hover, .star-picker label:hover ~ label { color: #c0392b; }
    .rf-group { margin-bottom: 20px; }
    .rf-group label { display: block; font-size: 13px; font-weight: 700; color: #c0a0a0; margin-bottom: 8px; text-transform: uppercase; }
    .rf-group input, .rf-group textarea { width: 100%; padding: 12px 15px; background: #1a0a0a; border: 1px solid #4a1515; border-radius: 8px; font-size: 15px; color: #fff; }
    .btn-review { background: #c0392b; color: #fff; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-size: 15px; font-weight: 700; transition: all 0.3s; }
    .btn-review:hover { background: #8b1a12; transform: translateY(-2px); }
</style>

<a href="products.php" class="back-btn">← Back to Products</a>

<div class="product-detail">
    <!-- Image -->
    <div class="product-image">
        <img src="../images/<?= htmlspecialchars($watch['image']) ?>" alt="<?= htmlspecialchars($watch['name']) ?>">
    </div>

    <!-- Info -->
    <div class="product-info">
        <h1><?= htmlspecialchars($watch['name']) ?></h1>
        <div class="brand"><?= htmlspecialchars($watch['brand']) ?></div>

        <?php if ($review_count > 0): ?>
            <div style="color:#ffa000;font-size:14px;margin-bottom:6px;">
                <?= $star_full . $star_empty ?>
                <span style="color:#777;font-size:12px;"> <?= $avg_rating ?>/5 — <?= $review_count ?> review<?= $review_count !== 1 ? 's' : '' ?></span>
            </div>
        <?php endif; ?>

        <div class="price">Rs. <?= number_format($watch['price']) ?></div>
        <div class="description"><?= htmlspecialchars($watch['description'] ?? '') ?></div>

        <!-- Specs -->
        <?php if (!empty($watch['specifications'])): ?>
        <div class="specs-box">
            <div class="specs-title">Specifications</div>
            <ul class="specifications">
                <?php foreach (explode(' | ', $watch['specifications']) as $spec):
                    if (!trim($spec)) continue;
                    $parts = explode(': ', $spec, 2);
                ?>
                <li>
                    <?php if (count($parts) === 2): ?>
                        <span class="spec-key"><?= htmlspecialchars($parts[0]) ?>:</span>
                        <span class="spec-value"><?= htmlspecialchars($parts[1]) ?></span>
                    <?php else: ?>
                        <span><?= htmlspecialchars($spec) ?></span>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Quantity row -->
        <div class="qty-row">
            <label for="qty">Quantity:</label>
            <div class="qty-ctrl">
                <button type="button" onclick="changeQty(-1)">−</button>
                <input type="number" id="qty" value="1" min="1" max="99">
                <button type="button" onclick="changeQty(1)">+</button>
            </div>
        </div>

        <!-- Action buttons — regular forms, work without JS -->
        <div class="action-forms">
            <!-- Add to Cart -->
            <form method="POST" action="cart_actions.php" id="form-cart">
                <input type="hidden" name="action"   value="add">
                <input type="hidden" name="watch_id" value="<?= $watch['id'] ?>">
                <input type="hidden" name="buy_now"  value="0">
                <input type="hidden" name="quantity" id="qty-cart" value="1">
                <button type="submit" class="btn-cart">🛒 Add to Cart</button>
            </form>
            <!-- Buy Now → goes straight to checkout -->
            <form method="POST" action="cart_actions.php" id="form-buy">
                <input type="hidden" name="action"   value="add">
                <input type="hidden" name="watch_id" value="<?= $watch['id'] ?>">
                <input type="hidden" name="buy_now"  value="1">
                <input type="hidden" name="quantity" id="qty-buy" value="1">
                <button type="submit" class="btn-buy">⚡ Buy Now</button>
            </form>
        </div>
    </div>
</div><!-- end product-detail -->

<!-- ── Customer Reviews ── -->
<div class="reviews-wrap">
    <div class="reviews-box">
        <h2>Customer Reviews</h2>

        <!-- Average -->
        <div class="avg-stars-row">
            <span class="star-display"><?= $review_count > 0 ? $star_full . $star_empty : '☆☆☆☆☆' ?></span>
            <span class="avg-val"><?= $review_count > 0 ? "$avg_rating / 5" : 'No ratings yet' ?></span>
            <?php if ($review_count > 0): ?>
                <span class="avg-count">Based on <?= $review_count ?> review<?= $review_count !== 1 ? 's' : '' ?></span>
            <?php endif; ?>
        </div>

        <!-- Feedback from review submit -->
        <?php if ($review_msg === 'success'): ?>
            <div class="alert alert-success">✓ Your review has been submitted. Thank you!</div>
        <?php elseif ($review_msg === 'duplicate'): ?>
            <div class="alert alert-error">You have already reviewed this watch.</div>
        <?php elseif ($review_msg === '1' || $review_msg): ?>
            <div class="alert alert-error">Please fill in your rating and comment.</div>
        <?php endif; ?>

        <!-- Reviews list -->
        <?php
        $reviews_list = null;
        try {
            $reviews_list = $conn->query("SELECT * FROM reviews WHERE watch_id=$id ORDER BY created_at DESC");
        } catch (Exception $e) { /* table not created yet — show nothing */ }
        ?>
        <?php if ($reviews_list && $reviews_list->num_rows > 0): ?>
            <?php while ($rev = $reviews_list->fetch_assoc()):
                $r_stars = str_repeat('★', (int)$rev['rating']) . str_repeat('☆', 5 - (int)$rev['rating']);
            ?>
            <div class="review-item">
                <div class="rev-header">
                    <span class="rev-author"><?= htmlspecialchars($rev['username']) ?></span>
                    <span class="rev-stars"><?= $r_stars ?></span>
                </div>
                <?php if (!empty($rev['title'])): ?>
                    <div class="rev-title">"<?= htmlspecialchars($rev['title']) ?>"</div>
                <?php endif; ?>
                <div class="rev-comment"><?= nl2br(htmlspecialchars($rev['comment'])) ?></div>
                <div class="rev-date"><?= date('d M Y', strtotime($rev['created_at'])) ?></div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-reviews">No reviews yet — be the first to review this watch!</p>
        <?php endif; ?>

        <!-- Write a review -->
        <div class="review-form-box">
            <h3>✍ Write a Review</h3>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="login-prompt">
                    Please <a href="login.php?redirect=watchdetails.php?id=<?= $id ?>">login</a> to write a review.
                </div>
            <?php else: ?>
                <form method="POST" action="review_submit.php">
                    <input type="hidden" name="watch_id" value="<?= $id ?>">
                    <div class="rf-group">
                        <label>Your Rating *</label>
                        <div class="star-picker">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="s<?= $i ?>" name="rating" value="<?= $i ?>" <?= $i===5?'checked':'' ?>>
                                <label for="s<?= $i ?>">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="rf-group">
                        <label>Title (optional)</label>
                        <input type="text" name="title" placeholder="e.g. Beautiful craftsmanship" maxlength="150">
                    </div>
                    <div class="rf-group">
                        <label>Review *</label>
                        <textarea name="comment" rows="4" placeholder="Share your thoughts on this watch…" required></textarea>
                    </div>
                    <button type="submit" class="btn-review">Submit Review</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Image Zoom Follow Cursor
const zoomBox = document.getElementById('zoom-box');
const zoomImg = document.getElementById('zoom-img');

if (zoomBox && zoomImg) { // Check if elements exist before adding listeners
    zoomBox.addEventListener('mousemove', (e) => {
        const x = e.clientX - zoomBox.offsetLeft;
        const y = e.clientY - zoomBox.offsetTop - window.scrollY; // Adjust for scroll
        
        const xPerc = (x / zoomBox.offsetWidth) * 100;
        const yPerc = (y / zoomBox.offsetHeight) * 100;
        
        zoomImg.style.transformOrigin = `${xPerc}% ${yPerc}%`;
    });

    zoomBox.addEventListener('mouseleave', () => {
        zoomImg.style.transformOrigin = 'center center';
    });
}

// Sync qty input → hidden form fields
function changeQty(delta) {
    const inp = document.getElementById('qty');
    let v = parseInt(inp.value) + delta;
    if (isNaN(v) || v < 1) v = 1;
    if (v > 99) v = 99;
    inp.value = v;
    document.getElementById('qty-cart').value = v;
    document.getElementById('qty-buy').value  = v;
}
// Also keep in sync when user types directly
document.getElementById('qty').addEventListener('input', function() {
    let v = parseInt(this.value) || 1;
    if (v < 1) v = 1; if (v > 99) v = 99;
    this.value = v;
    document.getElementById('qty-cart').value = v;
    document.getElementById('qty-buy').value  = v;
});
</script>

<?php include 'includes/footer.php'; ?>