<?php include '../config.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blog - Time Hub</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      color: #333;
    }

    /* Navbar */
    .header { background: #202c2f; }
    .navbar {
      display: flex;
      align-items: center;
      padding: 15px 20px;
    }
    nav { flex: 1; text-align: right; }
    nav ul { display: inline-block; list-style: none; }
    nav ul li { display: inline-block; margin-right: 20px; }
    nav ul li a { color: #fff; text-decoration: none; font-size: 15px; }
    nav ul li a:hover { color: #6EC6CA; }

    /* Hero */
    .blog-hero {
      background: linear-gradient(135deg, #202c2f, #34565c);
      color: white;
      text-align: center;
      padding: 80px 20px;
    }
    .blog-hero h1 {
      font-size: 48px;
      margin-bottom: 15px;
      letter-spacing: 2px;
    }
    .blog-hero p {
      font-size: 18px;
      color: #6EC6CA;
      max-width: 600px;
      margin: 0 auto;
    }

    /* Blog Grid */
    .blog-container {
      max-width: 1200px;
      margin: 60px auto;
      padding: 0 20px;
    }

    .section-title {
      font-size: 28px;
      color: #202c2f;
      margin-bottom: 40px;
      text-align: center;
      position: relative;
    }
    .section-title::after {
      content: '';
      display: block;
      width: 80px;
      height: 3px;
      background: #6EC6CA;
      margin: 10px auto 0;
      border-radius: 2px;
    }

    .blog-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }

    .blog-card {
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .blog-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    }

    .blog-card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
    }

    .blog-card-body {
      padding: 20px;
    }

    .blog-category {
      display: inline-block;
      background: #6EC6CA;
      color: white;
      font-size: 11px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 1px;
      padding: 4px 10px;
      border-radius: 20px;
      margin-bottom: 12px;
    }

    .blog-card-body h3 {
      font-size: 18px;
      color: #202c2f;
      margin-bottom: 10px;
      line-height: 1.4;
    }

    .blog-card-body p {
      font-size: 14px;
      color: #666;
      line-height: 1.6;
      margin-bottom: 15px;
    }

    .blog-meta {
      font-size: 12px;
      color: #999;
      margin-bottom: 15px;
    }

    .read-more {
      display: inline-block;
      background: #202c2f;
      color: white;
      padding: 8px 18px;
      border-radius: 20px;
      text-decoration: none;
      font-size: 13px;
      transition: background 0.3s;
    }
    .read-more:hover { background: #6EC6CA; }

    /* Featured Blog - big card on top */
    .featured-blog {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0;
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      margin-bottom: 60px;
    }
    .featured-blog img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      min-height: 350px;
    }
    .featured-blog-body {
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .featured-label {
      display: inline-block;
      background: #ffa000;
      color: white;
      font-size: 11px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 1px;
      padding: 4px 12px;
      border-radius: 20px;
      margin-bottom: 15px;
    }
    .featured-blog-body h2 {
      font-size: 28px;
      color: #202c2f;
      margin-bottom: 15px;
      line-height: 1.4;
    }
    .featured-blog-body p {
      font-size: 15px;
      color: #666;
      line-height: 1.7;
      margin-bottom: 20px;
    }

    /* Footer */
    .footer {
      background: #202c2f;
      color: #ccc;
      padding: 40px 20px 20px;
      margin-top: 60px;
    }
    .footer-container {
      display: flex;
      justify-content: space-between;
      max-width: 1200px;
      margin: 0 auto;
      gap: 30px;
      flex-wrap: wrap;
    }
    .footer-section h3 { color: #fff; margin-bottom: 15px; }
    .footer-section ul { list-style: none; }
    .footer-section ul li { margin-bottom: 8px; }
    .footer-section ul li a { color: #ccc; text-decoration: none; }
    .footer-section ul li a:hover { color: #6EC6CA; }
    .footer-bottom {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #444;
      font-size: 13px;
      color: #888;
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <div class="header">
    <div class="container">
      <div class="navbar">
        <a href="../index.php">
          <img class="logo" src="../images/logo.png" width="125px">
        </a>
        <nav>
          <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="./products.php">Products</a></li>
            <li><a href="./blog.php">Blog</a></li>
            <li><a href="./contact.php">Contact</a></li>
          </ul>
        </nav>
        <div style="position: relative">
          <a href="./checkout.php">
            <img src="../images/cart.png" alt="cart" width="30px" height="30px" style="cursor: pointer;">
          </a>
          <span id="cart-counter"
            style="position: absolute; top: -10px; right: -10px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px;">0</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Hero -->
  <div class="blog-hero">
    <h1>Time Hub Blog</h1>
    <p>Explore the latest trends, technology and stories from the world of watches</p>
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
        <span class="featured-label">⭐ Featured</span>
        <span class="blog-category"><?= htmlspecialchars($feat['category']) ?></span>
        <h2><?= htmlspecialchars($feat['title']) ?></h2>
        <p><?= htmlspecialchars($feat['excerpt']) ?></p>
        <div class="blog-meta">📅 <?= htmlspecialchars($feat['published_date']) ?> &nbsp;|&nbsp; ✍️ <?= htmlspecialchars($feat['author']) ?></div>
        <a href="./blogdetail.php?id=<?= $feat['id'] ?>" class="read-more">Read More →</a>
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
          <div class="blog-meta">📅 <?= htmlspecialchars($blog['published_date']) ?> &nbsp;|&nbsp; ✍️ <?= htmlspecialchars($blog['author']) ?></div>
          <p><?= htmlspecialchars($blog['excerpt']) ?></p>
          <a href="./blogdetail.php?id=<?= $blog['id'] ?>" class="read-more">Read More →</a>
        </div>
      </div>
      <?php endwhile; ?>
    </div>

  </div>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-section about">
        <h3>About Us</h3>
        <p>We're passionate about watches and believe they're more than just timepieces — they're a reflection of your style.</p>
      </div>
      <div class="footer-section links">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="../index.php">Home</a></li>
          <li><a href="./products.php">Products</a></li>
          <li><a href="./blog.php">Blog</a></li>
          <li><a href="./contact.php">Contact</a></li>
        </ul>
      </div>
      <div class="footer-section contact">
        <h3>Contact Us</h3>
        <p>📍 Balkumari, Lalitpur</p>
        <p>📞 +977 9812345678</p>
        <p>📧 pratyushisneupane@gmail.com</p>
        <a href="./contact.php" style="color: gold;">Click Here!</a>
      </div>
    </div>
    <div class="footer-bottom">
      &copy; 2025 WatchStore | Time Hub
    </div>
  </footer>

</body>
</html>




































