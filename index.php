<?php
session_start();
require_once 'config.php';

$page_title = 'Home';
$css_prefix = '';   // root-level

include 'php/includes/header.php';
?>
<style>
/* ── Eliminate any gap between navbar and content ── */
html, body { margin: 0; padding: 0; }

/* Dark red page background */
body { background: #1a0a0a; color: #f0e0e0; }

/* ── Hero section – flush against header ── */
.hero-section {
    background: linear-gradient(135deg, #1a0a0a 0%, #2a0e0e 40%, #1a0a0a 100%),
                url('./images/banner.jpg') center/cover no-repeat;
    background-blend-mode: multiply;
    padding: 90px 40px;
    display: flex;
    align-items: center;
    min-height: 70vh;
    margin: 0;
}
.hero-content { max-width: 600px; }
.hero-content h1 {
    font-size: 52px;
    font-weight: 700;
    color: #fff;
    line-height: 1.15;
    margin-bottom: 20px;
    letter-spacing: -1px;
}
.hero-content h1 em {
    font-style: normal;
    background: linear-gradient(90deg, #c0392b, #c9a84c);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.hero-content p { color: #c0a0a0; font-size: 17px; margin-bottom: 32px; max-width: 480px; }
.hero-btn {
    display: inline-block;
    background: linear-gradient(135deg, #c0392b, #8b1a12);
    color: #fff;
    padding: 14px 36px;
    border-radius: 32px;
    font-size: 16px;
    font-weight: 600;
    letter-spacing: .5px;
    transition: all .25s;
    border: 2px solid transparent;
}
.hero-btn:hover {
    background: transparent;
    border-color: #c0392b;
    color: #c0392b;
}

/* ── Section wrapper ── */
.section-wrap { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

/* ── Section heading ── */
.sec-heading {
    text-align: center;
    padding: 56px 0 8px;
    font-size: 26px;
    font-weight: 700;
    color: #f0e0e0;
    letter-spacing: 2px;
    text-transform: uppercase;
}
.sec-heading span { color: #c0392b; }
.sec-line {
    width: 60px; height: 3px;
    background: linear-gradient(90deg, #c0392b, #c9a84c);
    margin: 8px auto 36px;
    border-radius: 2px;
}

/* ── Home product cards (Featured / Latest) ── */
.cards-row { display: flex; gap: 20px; flex-wrap: wrap; justify-content: center; }
.home-card {
    background: #2a0e0e;
    border: 1px solid #3d1515;
    border-radius: 14px;
    width: 230px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: transform .3s, box-shadow .3s, border-color .3s;
    display: block;
}
.home-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 16px 40px rgba(192,57,43,.3);
    border-color: #c0392b;
}
.home-card-img-wrap {
    height: 180px;
    background: linear-gradient(145deg, #1a0a0a, #3d0b0b);
    display: flex;
    align-items: center;
    justify-content: center;
}
.home-card-img-wrap img {
    max-height: 160px;
    max-width: 90%;
    object-fit: contain;
    filter: drop-shadow(0 6px 12px rgba(0,0,0,.5));
    transition: transform .3s;
}
.home-card:hover .home-card-img-wrap img { transform: scale(1.06); }
.home-card-body { padding: 14px 16px 16px; }
.home-card-brand { font-size: 10px; color: #c0392b; text-transform: uppercase; letter-spacing: 2px; font-weight: 700; margin-bottom: 4px; }
.home-card-name  { font-size: 14px; font-weight: 600; color: #f0e0e0; line-height: 1.3; margin-bottom: 8px; min-height: 40px; }
.home-card-price { font-size: 15px; font-weight: 700; color: #c9a84c; }

/* View more */
.view-more-wrap { text-align: center; padding: 24px 0 50px; }
.view-more-btn {
    display: inline-block;
    border: 2px solid #c0392b;
    color: #c0392b;
    padding: 11px 36px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: .5px;
    transition: all .25s;
}
.view-more-btn:hover { background: #c0392b; color: #fff; }

/* ── Article/Blog banner ── */
.article-section {
    background: linear-gradient(rgba(26,10,10,.7), rgba(26,10,10,.8)),
                url("https://luxurywatchbuyer.com/wp-content/uploads/2015/11/banner-1_1200x360.jpg") center/cover no-repeat fixed;
    padding: 80px 60px;
    color: #fff;
    display: flex;
    justify-content: flex-end;
}
.article-content { max-width: 520px; }
.article-content h2 { font-size: 28px; color: #fff; margin-bottom: 14px; }
.article-content p  { color: #c0a0a0; font-size: 15px; line-height: 1.7; margin-bottom: 14px; }
</style>

<!-- ── Hero ── -->
<div class="hero-section">
    <div class="hero-content">
        <h1>Mementos That<br>Capture <em>Every Moment.</em></h1>
        <p>Luxury timepieces, crafted for those who understand that time is the most precious gift.</p>
        <a href="./php/products.php" class="hero-btn">Explore Collection →</a>
    </div>
</div>

<!-- ── Featured Products ── -->
<div class="section-wrap">
    <div class="sec-heading">Featured <span>Products</span></div>
    <div class="sec-line"></div>
    <div class="cards-row">
        <?php
        $featured = $conn->query("SELECT * FROM watches WHERE is_featured = 1");
        if (!$featured || $featured->num_rows === 0)
            $featured = $conn->query("SELECT * FROM watches LIMIT 8");
        while ($w = $featured->fetch_assoc()):
        ?>
        <a class="home-card" href="./php/watchdetails.php?id=<?= $w['id'] ?>">
            <div class="home-card-img-wrap">
                <img src="./images/<?= htmlspecialchars($w['image']) ?>" alt="<?= htmlspecialchars($w['name']) ?>">
            </div>
            <div class="home-card-body">
                <div class="home-card-brand"><?= htmlspecialchars($w['brand']) ?></div>
                <div class="home-card-name"><?= htmlspecialchars($w['name']) ?></div>
                <div class="home-card-price">Rs. <?= number_format($w['price']) ?></div>
            </div>
        </a>
        <?php endwhile; ?>
    </div>

    <!-- ── Latest Products ── -->
    <div class="sec-heading">Latest <span>Arrivals</span></div>
    <div class="sec-line"></div>
    <div class="cards-row">
        <?php
        $latest = $conn->query("SELECT * FROM watches WHERE is_latest = 1");
        if (!$latest || $latest->num_rows === 0)
            $latest = $conn->query("SELECT * FROM watches ORDER BY id DESC LIMIT 8");
        while ($w = $latest->fetch_assoc()):
        ?>
        <a class="home-card" href="./php/watchdetails.php?id=<?= $w['id'] ?>">
            <div class="home-card-img-wrap">
                <img src="./images/<?= htmlspecialchars($w['image']) ?>" alt="<?= htmlspecialchars($w['name']) ?>">
            </div>
            <div class="home-card-body">
                <div class="home-card-brand"><?= htmlspecialchars($w['brand']) ?></div>
                <div class="home-card-name"><?= htmlspecialchars($w['name']) ?></div>
                <div class="home-card-price">Rs. <?= number_format($w['price']) ?></div>
            </div>
        </a>
        <?php endwhile; ?>
    </div>

    <div class="view-more-wrap">
        <a href="./php/products.php" class="view-more-btn">View Full Collection →</a>
    </div>
</div>

<!-- ── Article Section ── -->
<div class="article-section">
    <div class="article-content">
        <h2>The Timeless Elegance of Watches</h2>
        <p>
            Watches are more than just timekeeping devices — they are a statement of style, a symbol of
            status, and a reflection of one's personality. From the classic designs of Rolex and Cartier
            to the modern innovations of Seiko and Casio, watches have evolved into essential accessories
            for those who value craftsmanship.
        </p>
        <p>Browse our curated blog for stories about watchmaking, heritage, and horology.</p>
        <a href="./php/blog.php" class="hero-btn">Read Our Blog →</a>
    </div>
</div>

<?php include 'php/includes/footer.php'; ?>