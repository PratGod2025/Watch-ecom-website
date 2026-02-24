<?php
session_start();
require_once '../config.php';

$id = intval($_GET['id'] ?? 0);
$result = $conn->query("SELECT * FROM blogs WHERE id = $id");
$blog = $result ? $result->fetch_assoc() : null;

if (!$blog) { header('Location: blog.php'); exit; }

$page_title = htmlspecialchars($blog['title']) . " - Time Hub Blog";
$css_prefix = '../';
include 'includes/header.php';
?>
<style>
    body { background: #1a0a0a; color: #f0e0e0; font-family: 'Outfit', Arial, sans-serif; margin: 0; }
    
    .blog-detail-container {
        max-width: 1000px;
        margin: 50px auto;
        padding: 0 20px;
    }

    .back-btn {
        display: inline-block;
        margin-bottom: 30px;
        background: rgba(42,14,14,0.6);
        color: #c0a0a0;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        border: 1px solid rgba(192,57,43,0.3);
        transition: all 0.3s;
    }
    .back-btn:hover { background: #c0392b; color: #fff; box-shadow: 0 0 15px rgba(192,57,43,0.4); }

    .blog-detail-card {
        background: rgba(42,14,14,0.8);
        backdrop-filter: blur(12px);
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(192,57,43,0.3);
        box-shadow: 0 25px 60px rgba(0,0,0,0.6);
    }

    .blog-detail-card img {
        width: 100%;
        height: 500px;
        object-fit: cover;
        border-bottom: 2px solid #c0392b;
    }

    .blog-detail-body { padding: 60px; }

    .blog-category {
        display: inline-block;
        background: #c0392b;
        color: white;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        padding: 6px 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .blog-detail-body h1 {
        font-size: 42px;
        color: #fff;
        margin-bottom: 20px;
        line-height: 1.2;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .blog-meta {
        font-size: 15px;
        color: #9a7070;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 1px solid rgba(192,57,43,0.2);
        display: flex;
        gap: 20px;
    }

    .blog-content {
        font-size: 18px;
        line-height: 1.9;
        color: #c0a0a0;
        white-space: pre-line;
    }

    @media (max-width: 768px) {
        .blog-detail-body { padding: 30px; }
        .blog-detail-body h1 { font-size: 32px; }
        .blog-detail-card img { height: 300px; }
    }
</style>

<div class="blog-detail-container">
    <a href="./blog.php" class="back-btn">← Back to Stories</a>
    <div class="blog-detail-card">
        <img src="../images/blog/<?= htmlspecialchars($blog['image']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>">
        <div class="blog-detail-body">
            <span class="blog-category"><?= htmlspecialchars($blog['category']) ?></span>
            <h1><?= htmlspecialchars($blog['title']) ?></h1>
            <div class="blog-meta">
                <span>📅 <?= date('M d, Y', strtotime($blog['published_date'])) ?></span>
                <span>✍️ <?= htmlspecialchars($blog['author']) ?></span>
            </div>
            <div class="blog-content"><?= nl2br(htmlspecialchars($blog['content'])) ?></div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>