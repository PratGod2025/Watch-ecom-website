<?php include 'config.php'; ?> 
<!DOCTYPE html> 
<html lang="en">

<head>
    <meta name="description" content="ecommerce website selling watches">
    <meta name="keywords" content="Watches, Website, ecommerce, HomePage, soura">
    <title>Home Page</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .view_more {
            max-width: 15%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: #343434;
            color: white;
            padding: 8px 30px;
            margin: 30px 0;
            border-radius: 30px;
        }

        .view_more:hover {
            background: grey;
        }

        .article-section {
            background-color: #ffffff;
            color: rgb(255, 255, 255);
            padding: 85px;
            font-family: Arial, sans-serif;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
            background-image: url("https://luxurywatchbuyer.com/wp-content/uploads/2015/11/banner-1_1200x360.jpg");
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="container">
            <div class="navbar">
                <a href="./index.php"><img class="logo" src="./images/logo.png" width="125px"></a>
                <nav>
                    <ul>
                        <li><a href="index.php" class="a_color">Home</a></li>
                        <li><a href="./php/products.php " class="a_color">Products</a></li>
                        <li><a href="./php/blog.php" class="a_color">Blog</a></li>
                        <li><a href="./php/contact.php" class="a_color">Contact</a></li>
                    </ul>
                </nav>
                <div style="position: relative">
                    <a href="./php/checkout.php"><img src="./images/cart.png" alt="cart" width="30px" height="30px"
                            style="cursor: pointer;"></a>
                    <span id="cart-counter"
                        style="position: absolute; top: -10px; right: -10px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px;">0</span>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <h1>Mementos That Capture Every Moment.</h1>
                    <a href="./php/products.php" class="btn">Explore Now &#8594;</a>
                </div>
            </div>
        </div>
    </div>

    <!----featured products---->
   <div class="small-container">
    <h2 class="title">Featured Products</h2>
    <table>
        <tr class="row">
            <?php
            $featured = $conn->query("SELECT * FROM watches WHERE is_featured = 1");
            while($watch = $featured->fetch_assoc()):
            ?>
            <td class="col-4">
                <a href="./php/watchdetails.php?id=<?= $watch['id'] ?>">
                    <img src="./images/<?= $watch['image'] ?>">
                </a>
                <h4><?= htmlspecialchars($watch['name']) ?></h4>
                <p>Rs. <?= number_format($watch['price']) ?></p>
            </td>
            <?php endwhile; ?>
        </tr>
    </table>

        <!-----Latest Products---->
       <h2 class="title">Latest Products</h2>
<table>
    <tr class="row">
        <?php
        $latest = $conn->query("SELECT * FROM watches WHERE is_latest = 1");
        while($watch = $latest->fetch_assoc()):
        ?>
        <td class="col-4">
            <a href="./php/watchdetails.php?id=<?= $watch['id'] ?>">
                <img src="./images/<?= $watch['image'] ?>">
            </a>
            <h4><?= htmlspecialchars($watch['name']) ?></h4>
            <p>Rs. <?= number_format($watch['price']) ?></p>
        </td>
        <?php endwhile; ?>
    </tr>
</table>
        <a href="./php/products.php" class="view_more">View More</a>
    </div>

    <!-------Article Section------>
    <section class="article-section">
        <div class="container" style="margin-left: 500px;">
            <h2>The Timeless Elegance of Watches</h2>
            <p style="color: #ffffff;">
                Watches are more than just timekeeping devices; they are a statement of style, a symbol of status, and a
                reflection of one's personality. From the classic designs of Rolex and Cartier to the modern innovations
                of Seiko and Casio, watches have evolved to become an essential accessory for both men and women.
            </p>
            <p style="color: #ffffff;">
                Whether you're looking for a luxury timepiece to complement your formal attire or a durable sports watch
                for your outdoor adventures, our collection offers a wide range of options to suit every taste and
                occasion. Explore our featured and latest products to find the perfect watch that resonates with your
                style and needs.
            </p>
            <a href="./php/blog.php" class="btn">view more</a>
        </div>
    </section>

    <!-------footer------>
    <footer class="footer" style="margin-top: 0;">
        <div class="footer-container">
            <!-- About Section -->
            <div class="footer-section about">
                <h3>About Us</h3>
                <p>
                    We're passionate about watches and believe they're more than just timepieces—they're a reflection of
                    your style. Our collection combines timeless elegance with modern design, so you can find the
                    perfect watch for any occasion.
                </p>
            </div>

            <!-- Links Section -->
            <div class="footer-section links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="./index.php">Home</a></li>
                    <li><a href="./php/products.php">Products</a></li>
                    <li><a href="./php/blog.php">Blog</a></li>
                    <li><a href="./php/contact.php">Contact</a></li>
                </ul>
            </div>

            <!-- Contact Section -->
            <div class="footer-section contact">
                <h3>Contact Us</h3>
                <p>📍 Balkumari, Lalitpur</p>
                <p>📞 +977 9812345678</p>
                <p>📧 pratyushisneupane@gmail.com</p>
                <p>🌐 </p>
                <p>For Further Queries</p>
                <a href="./php/contact.php" style="color: gold;">Click Here! </a>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2025 WatchStore |
        </div>
    </footer>
</body>

</html>