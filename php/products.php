<?php
session_start();
require_once '../config.php';

$page_title = 'Collection';
$css_prefix = '../';

// ── Search & filter ──────────────────────────────────────────
$search    = trim($conn->real_escape_string($_GET['search'] ?? ''));
$brand     = $conn->real_escape_string($_GET['brand'] ?? '');
$min_price = (float)($_GET['min'] ?? 0);
$max_price = (float)($_GET['max'] ?? 0);
$sort      = $_GET['sort'] ?? 'default';

$where = [];
if ($search)    $where[] = "(name LIKE '%$search%' OR brand LIKE '%$search%' OR description LIKE '%$search%')";
if ($brand)     $where[] = "brand = '$brand'";
if ($min_price) $where[] = "price >= $min_price";
if ($max_price) $where[] = "price <= $max_price";
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$order_sql = match ($sort) {
    'price_asc'  => 'ORDER BY price ASC',
    'price_desc' => 'ORDER BY price DESC',
    'name'       => 'ORDER BY name ASC',
    'rating'     => 'ORDER BY rating DESC',
    default      => 'ORDER BY id ASC',
};

$brands_res = $conn->query("SELECT DISTINCT brand FROM watches ORDER BY brand");
$brands     = $brands_res ? $brands_res->fetch_all(MYSQLI_ASSOC) : [];
$watches    = $conn->query("SELECT * FROM watches $where_sql $order_sql");
$count      = $watches ? $watches->num_rows : 0;

include 'includes/header.php';
?>
<style>
/* ── Page base ── */
body { background: #1a0a0a; color: #f0e0e0; font-family: 'Outfit', Arial, sans-serif; margin: 0; }

/* ── Hero banner over products ── */
.collection-hero {
    background: linear-gradient(135deg, #1a0a0a 0%, #3d0b0b 50%, #1a0a0a 100%);
    text-align: center;
    padding: 64px 20px 48px;
    border-bottom: 2px solid #4a1515;
    position: relative;
    overflow: hidden;
}
.collection-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at center, rgba(192,57,43,.18) 0%, transparent 70%);
}
.collection-hero h1 {
    font-size: 48px;
    font-weight: 700;
    color: #fff;
    letter-spacing: 4px;
    text-transform: uppercase;
    margin-bottom: 10px;
    position: relative;
}
.collection-hero h1 span { color: #c0392b; }
.collection-hero p  { color: #c0a0a0; font-size: 16px; position: relative; }
.hero-line {
    width: 80px; height: 3px;
    background: linear-gradient(90deg, #c0392b, #c9a84c);
    margin: 14px auto 0;
    border-radius: 2px;
}

/* ── Filter bar ── */
.filter-wrap { max-width: 1200px; margin: -28px auto 0; padding: 0 24px; position: relative; z-index: 10; }
.filter-bar {
    background: #2a0e0e;
    border: 1px solid #4a1515;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0,0,0,.4);
    padding: 20px 24px;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: flex-end;
}
.filter-bar label { font-size: 11px; font-weight: 600; color: #c0a0a0; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 5px; }
.filter-bar input,
.filter-bar select {
    background: #1a0a0a;
    border: 1px solid #4a1515;
    color: #f0e0e0;
    border-radius: 6px;
    padding: 9px 12px;
    font-size: 14px;
    transition: border .2s;
}
.filter-bar input:focus,
.filter-bar select:focus { border-color: #c0392b; outline: none; }
.filter-bar select option { background: #1a0a0a; }
.search-input { flex: 2; min-width: 180px; }
.search-input input { width: 100%; box-sizing: border-box; }
.btn-search {
    background: linear-gradient(135deg, #c0392b, #8b1a12);
    color: #fff;
    border: none;
    padding: 10px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    height: 40px;
    letter-spacing: .5px;
    transition: opacity .2s;
}
.btn-search:hover { opacity: .85; }
.btn-clear {
    background: transparent;
    color: #c0a0a0;
    border: 1px solid #4a1515;
    padding: 9px 16px;
    border-radius: 8px;
    font-size: 13px;
    cursor: pointer;
    height: 40px;
    text-decoration: none;
    display: flex;
    align-items: center;
    transition: background .2s;
}
.btn-clear:hover { background: #3d0b0b; color: #fff; }

/* ── Results info ── */
.results-info {
    max-width: 1200px; margin: 28px auto 14px; padding: 0 24px;
    font-size: 13px; color: #c0a0a0;
}
.results-info strong { color: #c0392b; }

/* ── Product Grid ── */
.products-grid {
    max-width: 1200px;
    margin: 0 auto 60px;
    padding: 0 24px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 24px;
}

/* ── Product Card — premium dark design ── */
.product-card {
    background: #2a0e0e;
    border: 1px solid #3d1515;
    border-radius: 14px;
    overflow: hidden;
    transition: transform .3s, box-shadow .3s, border-color .3s;
    display: flex;
    flex-direction: column;
    position: relative;
}
.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 16px 48px rgba(192,57,43,.25);
    border-color: #c0392b;
}

/* Image container */
.card-img-wrap {
    background: linear-gradient(145deg, #1a0a0a, #3d0b0b);
    height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}
.card-img-wrap::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 40px;
    background: linear-gradient(to top, #2a0e0e, transparent);
}
.card-img {
    max-height: 190px;
    max-width: 90%;
    object-fit: contain;
    transition: transform .35s ease;
    filter: drop-shadow(0 8px 16px rgba(0,0,0,.5));
}
.product-card:hover .card-img { transform: scale(1.08); }

/* Badge */
.card-badge {
    position: absolute;
    top: 12px; left: 12px;
    background: linear-gradient(135deg, #c0392b, #8b1a12);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Body */
.card-body {
    padding: 18px 18px 14px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.card-brand {
    font-size: 10px;
    color: #c0392b;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-weight: 700;
    margin-bottom: 5px;
}
.card-name {
    font-size: 15px;
    font-weight: 600;
    color: #f0e0e0;
    line-height: 1.35;
    min-height: 42px;
    margin-bottom: 8px;
}
.card-rating { font-size: 13px; color: #c9a84c; margin-bottom: 8px; }
.card-price {
    font-size: 18px;
    font-weight: 700;
    color: #c9a84c;
    letter-spacing: .5px;
    margin-bottom: 14px;
}
.card-divider { border: none; border-top: 1px solid #3d1515; margin: 0 0 14px; }

/* Action buttons */
.card-actions { display: flex; gap: 8px; margin-top: auto; }
.btn-cart-card {
    flex: 1;
    background: linear-gradient(135deg, #c0392b, #8b1a12);
    color: #fff;
    border: none;
    padding: 10px 0;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: opacity .2s, transform .15s;
    font-family: 'Outfit', Arial, sans-serif;
}
.btn-cart-card:hover { opacity: .85; transform: scale(1.02); }
.btn-detail-card {
    flex: 1;
    background: transparent;
    color: #c0a0a0;
    border: 1px solid #4a1515;
    padding: 10px 0;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-align: center;
    transition: background .2s, color .2s;
    display: block;
}
.btn-detail-card:hover { background: #3d0b0b; color: #f0e0e0; }

/* No results */
.no-products {
    grid-column: 1/-1;
    text-align: center;
    padding: 70px 20px;
    background: #2a0e0e;
    border-radius: 14px;
    border: 1px solid #3d1515;
}
.no-products h3 { color: #c0a0a0; font-size: 20px; font-weight: 400; margin-bottom: 10px; }
.no-products a  { color: #c0392b; }

/* Toast */
.toast {
    position: fixed; bottom: 24px; right: 24px;
    background: #c0392b; color: #fff;
    padding: 12px 22px; border-radius: 8px;
    font-size: 14px; font-weight: 600;
    box-shadow: 0 4px 20px rgba(0,0,0,.3);
    display: none; z-index: 9999;
    animation: slideup .3s ease;
}
.toast.ok { background: #27ae60; }
@keyframes slideup { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
</style>

<!-- ── Hero ── -->
<div class="collection-hero">
    <h1>Our <span>Collection</span></h1>
    <p><?= $count ?> luxury timepiece<?= $count !== 1 ? 's' : '' ?> available</p>
    <div class="hero-line"></div>
</div>

<!-- ── Filter Bar ── -->
<div class="filter-wrap">
    <form method="GET" class="filter-bar">
        <div class="search-input">
            <label>🔍 Search</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Brand, name or keyword…">
        </div>
        <div>
            <label>Brand</label>
            <select name="brand">
                <option value="">All Brands</option>
                <?php foreach ($brands as $b): ?>
                    <option value="<?= htmlspecialchars($b['brand']) ?>" <?= $brand===$b['brand']?'selected':'' ?>>
                        <?= htmlspecialchars($b['brand']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Min Rs.</label>
            <input type="number" name="min" value="<?= $min_price ?: '' ?>" placeholder="0" style="width:100px;">
        </div>
        <div>
            <label>Max Rs.</label>
            <input type="number" name="max" value="<?= $max_price ?: '' ?>" placeholder="Any" style="width:100px;">
        </div>
        <div>
            <label>Sort</label>
            <select name="sort">
                <option value="default"    <?= $sort==='default'   ?'selected':'' ?>>Default</option>
                <option value="price_asc"  <?= $sort==='price_asc' ?'selected':'' ?>>Price ↑</option>
                <option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>Price ↓</option>
                <option value="name"       <?= $sort==='name'      ?'selected':'' ?>>Name A–Z</option>
                <option value="rating"     <?= $sort==='rating'    ?'selected':'' ?>>Top Rated</option>
            </select>
        </div>
        <button type="submit" class="btn-search">Search</button>
        <a href="products.php" class="btn-clear">✕</a>
    </form>
</div>

<!-- Results info -->
<div class="results-info">
    Showing <strong><?= $count ?></strong> watch<?= $count !== 1 ? 'es' : '' ?>
    <?php if ($search): ?> for "<strong style="color:#f0e0e0"><?= htmlspecialchars($search) ?></strong>"<?php endif; ?>
</div>

<!-- ── Product Grid ── -->
<div class="products-grid">
    <?php if (!$watches || $count === 0): ?>
        <div class="no-products">
            <h3>No watches found</h3>
            <p><a href="products.php">Browse all products</a></p>
        </div>
    <?php else: ?>
        <?php while ($w = $watches->fetch_assoc()):
            $stars = round((float)($w['rating'] ?? 0));
            $star_str = str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
        ?>
        <div class="product-card">
            <?php if (!empty($w['is_featured'])): ?>
                <div class="card-badge">Featured</div>
            <?php elseif (!empty($w['is_latest'])): ?>
                <div class="card-badge" style="background:linear-gradient(135deg,#c9a84c,#8b6914);">New</div>
            <?php endif; ?>

            <a href="watchdetails.php?id=<?= $w['id'] ?>">
                <div class="card-img-wrap">
                    <img class="card-img"
                         src="../images/<?= htmlspecialchars($w['image']) ?>"
                         alt="<?= htmlspecialchars($w['name']) ?>">
                </div>
            </a>
            <div class="card-body">
                <div class="card-brand"><?= htmlspecialchars($w['brand']) ?></div>
                <div class="card-name"><?= htmlspecialchars($w['name']) ?></div>
                <?php if ($stars > 0): ?>
                    <div class="card-rating"><?= $star_str ?> <span style="color:#888;font-size:11px;"><?= number_format((float)$w['rating'], 1) ?></span></div>
                <?php endif; ?>
                <div class="card-price">Rs. <?= number_format($w['price']) ?></div>
                <hr class="card-divider">
                <div class="card-actions">
                    <form method="POST" action="cart_actions.php" style="flex:1">
                        <input type="hidden" name="action"   value="add">
                        <input type="hidden" name="watch_id" value="<?= $w['id'] ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn-cart-card">🛒 Add to Cart</button>
                    </form>
                    <a href="watchdetails.php?id=<?= $w['id'] ?>" class="btn-detail-card">Details</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<div class="toast" id="toast"></div>

<?php include 'includes/footer.php'; ?>