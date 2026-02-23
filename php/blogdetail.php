<?php include '../config.php'; ?>
<?php
$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM blogs WHERE id = $id");
$blog = $result->fetch_assoc();
if (!$blog) { echo "Blog not found!"; exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($blog['title']) ?> - Time Hub Blog</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; background: #f4f4f4; }

    .header { background: #202c2f; }
    .navbar { display: flex; align-items: center; padding: 15px 20px; }
    nav { flex: 1; text-align: right; }
    nav ul { display: inline-block; list-style: none; }
    nav ul li { display: inline-block; margin-right: 20px; }
    nav ul li a { color: #fff; text-decoration: none; }
    nav ul li a:hover { color: #6EC6CA; }

    .blog-detail-container {
      max-width: 900px;
      margin: 50px auto;
      padding: 0 20px;
    }

    .blog-detail-card {
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .blog-detail-card img {
      width: 100%;
      height: 400px;
      object-fit: cover;
    }

    .blog-detail-body { padding: 40px; }

    .blog-category {
      display: inline-block;
      background: #6EC6CA;
      color: white;
      font-size: 11px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 1px;
      padding: 4px 12px;
      border-radius: 20px;
      margin-bottom: 15px;
    }

    .blog-detail-body h1 {
      font-size: 32px;
      color: #202c2f;
      margin-bottom: 15px;
      line-height: 1.4;
    }

    .blog-meta {
      font-size: 13px;
      color: #999;
      margin-bottom: 25px;
      padding-bottom: 25px;
      border-bottom: 1px solid #eee;
    }

    .blog-content {
      font-size: 16px;
      line-height: 1.9;
      color: #555;
      white-space: pre-line;
    }

    .back-btn {
      display: inline-block;
      margin-bottom: 25px;
      background: #202c2f;
      color: white;
      padding: 8px 18px;
      border-radius: 20px;
      text-decoration: none;
      font-size: 13px;
      transition: background 0.3s;
    }
    .back-btn:hover { background: #6EC6CA; }

    .footer {
      background: #202c2f;
      color: #ccc;
      padding: 40px 20px 20px;
      margin-top: 60px;
    }
    .footer-bottom {
      text-align: center;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px solid #444;
      font-size: 13px;
      color: #888;
    }
  </style>
</head>
<body>

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
      </div>
    </div>
  </div>

  <div class="blog-detail-container">
    <a href="./blog.php" class="back-btn">← Back to Blog</a>
    <div class="blog-detail-card">
      <img src="../images/blog/<?= htmlspecialchars($blog['image']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>">
      <div class="blog-detail-body">
        <span class="blog-category"><?= htmlspecialchars($blog['category']) ?></span>
        <h1><?= htmlspecialchars($blog['title']) ?></h1>
        <div class="blog-meta">
          📅 <?= htmlspecialchars($blog['published_date']) ?> &nbsp;|&nbsp; ✍️ <?= htmlspecialchars($blog['author']) ?>
        </div>
        <div class="blog-content"><?= nl2br(htmlspecialchars($blog['content'])) ?></div>
      </div>
    </div>
  </div>

  <footer class="footer">
    <div class="footer-bottom">
      &copy; 2025 WatchStore | Time Hub
    </div>
  </footer>

</body>
</html>