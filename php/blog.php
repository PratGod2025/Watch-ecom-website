<?php
session_start();
require_once '../config.php';
$page_title = 'Blog - Time Hub';
$css_prefix = '../';
include 'includes/header.php';
?>
<style>
    body { background: #1a0a0a; color: #f0e0e0; font-family: 'Outfit', Arial, sans-serif; margin: 0; }

    /* Hero */
    .blog-hero {
        background: linear-gradient(rgba(26,10,10,0.7), rgba(26,10,10,0.85)), url('https://images.unsplash.com/photo-1547996160-81dfa63595dd?q=80&w=2000&auto=format&fit=crop') center/cover no-repeat;
        color: white;
        text-align: center;
        padding: 100px 20px;
        border-bottom: 2px solid #c0392b;
    }
    .blog-hero h1 { font-size: 56px; margin-bottom: 15px; font-weight: 700; letter-spacing: 2px; }
    .blog-hero p { font-size: 20px; color: #c0a0a0; max-width: 700px; margin: 0 auto; letter-spacing: 1px; }

    /* Blog Grid */
    .blog-container { max-width: 1240px; margin: 60px auto; padding: 0 20px; }

    .section-title { font-size: 32px; color: #fff; margin-bottom: 50px; text-align: center; font-weight: 700; letter-spacing: 1px; }
    .section-title::after { content: ''; display: block; width: 60px; height: 3px; background: #c0392b; margin: 15px auto 0; border-radius: 2px; }

    .blog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 40px; }

    .blog-card {
        background: rgba(42,14,14,0.8);
        backdrop-filter: blur(10px);
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid rgba(192,57,43,0.3);
        box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .blog-card:hover { transform: translateY(-12px); border-color: #c0392b; box-shadow: 0 20px 50px rgba(192,57,43,0.25); }

    .blog-card img { width: 100%; height: 240px; object-fit: cover; border-bottom: 1px solid rgba(192,57,43,0.2); }

    .blog-card-body { padding: 30px; }

    .blog-category {
        display: inline-block;
        background: #c0392b;
        color: white;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding: 5px 12px;
        border-radius: 5px;
        margin-bottom: 15px;
    }

    .blog-card-body h3 { font-size: 22px; color: #fff; margin-bottom: 15px; line-height: 1.3; font-weight: 600; }
    .blog-card-body p { font-size: 15px; color: #c0a0a0; line-height: 1.7; margin-bottom: 20px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

    .blog-meta { font-size: 13px; color: #9a7070; margin-bottom: 15px; font-weight: 500; }

    .read-more {
        display: inline-flex;
        align-items: center;
        color: #c9a84c;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: gap 0.3s;
    }
    .read-more:hover { color: #f0e0e0; gap: 8px; }

    /* Featured Blog */
    .featured-blog {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        background: rgba(42,14,14,0.9);
        backdrop-filter: blur(15px);
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(192,57,43,0.4);
        box-shadow: 0 25px 70px rgba(0,0,0,0.6);
        margin-bottom: 80px;
    }
    .featured-blog img { width: 100%; height: 100%; object-fit: cover; min-height: 450px; }
    .featured-blog-body { padding: 60px; display: flex; flex-direction: column; justify-content: center; background: radial-gradient(circle at top right, rgba(192,57,43,0.1), transparent); }
    .featured-label { display: inline-block; background: #c9a84c; color: #1a0a0a; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; padding: 6px 15px; border-radius: 4px; margin-bottom: 20px; width: fit-content; }
    .featured-blog-body h2 { font-size: 38px; color: #fff; margin-bottom: 20px; line-height: 1.2; font-weight: 700; }
    .featured-blog-body p { font-size: 17px; color: #c0a0a0; line-height: 1.8; margin-bottom: 30px; }

    @media (max-width: 992px) {
        .featured-blog { grid-template-columns: 1fr; }
        .featured-blog img { min-height: 300px; }
    }
</style>

<div class="blog-hero">
    <h1>Watch Stories</h1>
    <p>Discover the heritage, precision, and soul behind the world's finest timepieces.</p>
</div>

<div class="blog-container">

    <!-- Featured Blog -->
    <?php
    $featured = $conn->query("SELECT * FROM blogs WHERE is_featured = 1 LIMIT 1");
    $feat = $featured->fetch_assoc();
    if($feat):
    ?>
    <div class="featured-blog">
        <img src="../images/blog/<?= htmlspecialchars($feat['image']) ?>" alt="<?= htmlspecialchars($feat['title']) ?>">
        <div class="featured-blog-body">
            <span class="featured-label">⭐ Spotlight</span>
            <span class="blog-category"><?= htmlspecialchars($feat['category']) ?></span>
            <h2><?= htmlspecialchars($feat['title']) ?></h2>
            <p><?= htmlspecialchars($feat['excerpt']) ?></p>
            <div class="blog-meta">📅 <?= date('M d, Y', strtotime($feat['published_date'])) ?> &nbsp;|&nbsp; ✍️ <?= htmlspecialchars($feat['author']) ?></div>
            <a href="./blogdetail.php?id=<?= $feat['id'] ?>" class="read-more">Dive Deeper →</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- All Blogs -->
    <h2 class="section-title">Latest Articles</h2>
    <div class="blog-grid">
        <?php
        $blogs = $conn->query("SELECT * FROM blogs WHERE is_featured = 0 ORDER BY id DESC");
        while($blog = $blogs->fetch_assoc()):
        ?>
        <div class="blog-card">
            <img src="../images/blog/<?= htmlspecialchars($blog['image']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>">
            <div class="blog-card-body">
                <span class="blog-category"><?= htmlspecialchars($blog['category']) ?></span>
                <h3><?= htmlspecialchars($blog['title']) ?></h3>
                <div class="blog-meta">📅 <?= date('M d, Y', strtotime($blog['published_date'])) ?> &nbsp;|&nbsp; ✍️ <?= htmlspecialchars($blog['author']) ?></div>
                <p><?= htmlspecialchars($blog['excerpt']) ?></p>
                <a href="./blogdetail.php?id=<?= $blog['id'] ?>" class="read-more">Read More →</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

</div>

<?php include 'includes/footer.php'; ?>




































