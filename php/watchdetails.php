<?php 
include '../config.php';

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM watches WHERE id = $id");
$watch = $result->fetch_assoc();

if (!$watch) {
    echo "Watch not found!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($watch['name']) ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .product-detail {
            display: flex;
            max-width: 1200px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .product-image {
            flex: 1;
            text-align: center;
        }
        .product-image img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .product-info {
            flex: 2;
            padding: 20px;
        }
        .product-info h1 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #333;
        }
        .product-info .brand {
            font-size: 16px;
            color: #777;
            margin-bottom: 20px;
        }
        .product-info .price {
            font-size: 24px;
            color: #d9534f;
            margin-bottom: 20px;
        }
        .product-info .description {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
            color: #555;
        }
        .specifications {
            list-style: none;
            padding: 0;
            margin-bottom: 20px;
        }
        .specifications li {
            margin: 5px 0;
            font-size: 16px;
            color: #555;
        }
        .quantity {
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .quantity input {
            width: 50px;
            text-align: center;
        }
        .action-buttons {
            margin-top: 20px;
        }
        .action-buttons button {
            background-color: #ffa000;
            color: white;
            border: none;
            margin-bottom: 5px;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .action-buttons button:hover {
            background-color: #7a4f06;
        }
        .buy-now {
            background-color: #5cb85c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 5px;
        }
        .buy-now:hover {
            background-color: #053105;
        }
        .back-button {
            position: absolute;
            top: 100px;
            left: 30px;
            background-color: #606060;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: #ababab;
        }
        .ratings-reviews {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .ratings-reviews h2 {
            font-size: 22px;
            margin-bottom: 15px;
        }
        .average-rating {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .rating-stars { color: #ffa000; }
        .rating-value { font-weight: bold; margin: 0 10px; }
        .total-reviews { color: #777; }
        .reviews-list { margin-top: 20px; }
        .review {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .review-author { font-weight: bold; }
        .review-rating { color: #ffa000; }
        .review-text { font-size: 16px; color: #555; }
        .review-date { font-size: 14px; color: #777; }

        /* Navbar */
        .navbar {
            display: flex;
            align-items: center;
            padding: 20px;
        }
        nav { flex: 1; text-align: right; }
        nav ul { display: inline-block; list-style-type: none; }
        nav ul li { display: inline-block; margin-right: 20px; }
        a { text-decoration: none; }
        .a_color { color: #ffffff; }
        .header { background: #202c2f; }
        a:hover { color: tomato; font-weight: 600; }

        /* Specs Box */
.specs-box {
    background-color: #f9f9f9;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px 20px;
    margin: 20px 0;
}

.specs-title {
    font-size: 16px;
    font-weight: bold;
    color: #333;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 2px solid #ffa000;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.specifications {
    list-style: none;
    padding: 0;
    margin: 0;
}

.specifications li {
    display: flex;
    gap: 10px;
    padding: 7px 0;
    border-bottom: 1px solid #eee;
    font-size: 15px;
    color: #555;
}

.specifications li:last-child {
    border-bottom: none;
}

.spec-key {
    font-weight: 600;
    color: #333;
    min-width: 160px;
}

.spec-value {
    color: #555;
}

/* Description */
.product-info .description {
    font-size: 15px;
    line-height: 1.7;
    color: #666;
    margin-bottom: 10px;
    padding: 12px 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}

/* Price */
.product-info .price {
    font-size: 26px;
    font-weight: bold;
    color: #d9534f;
    margin: 10px 0 15px 0;
}

/* Brand */
.product-info .brand {
    font-size: 15px;
    color: #999;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 5px;
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
                        <li><a href="../index.php" class="a_color">Home</a></li>
                        <li><a href="./products.php" class="a_color">Products</a></li>
                        <li><a href="./blog.php" class="a_color">Blog</a></li>
                        <li><a href="./contact.php" class="a_color">Contact</a></li>
                    </ul>
                </nav>
                <div style="position: relative">
                    <img src="../images/cart.png" alt="cart" width="30px" height="30px" style="cursor: pointer;">
                    <span id="cart-counter"
                        style="position: absolute; top: -10px; right: -10px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px;">0</span>
                </div>
            </div>
        </div>
    </div>

   <div class="product-detail">
    <a href="./products.php"><button class="back-button">&#8592; Back</button></a>

    <!-- LEFT SIDE - Image -->
    <div class="product-image">
        <img src="../images/<?= htmlspecialchars($watch['image']) ?>" 
             alt="<?= htmlspecialchars($watch['name']) ?>">
    </div>

    <!-- RIGHT SIDE - Info -->
    <div class="product-info">
        <h1><?= htmlspecialchars($watch['name']) ?></h1>
        <p class="brand"><?= htmlspecialchars($watch['brand']) ?></p>
        <p class="price">Rs. <?= number_format($watch['price']) ?></p>

        <p class="description"><?= htmlspecialchars($watch['description']) ?></p>

        <div class="specs-box">
            <h4 class="specs-title">Specifications</h4>
            <ul class="specifications">
                <?php
                $specs = explode(' | ', $watch['specifications']);
                foreach($specs as $spec):
                    $parts = explode(': ', $spec, 2);
                ?>
                    <li>
                        <?php if(count($parts) == 2): ?>
                            <span class="spec-key"><?= htmlspecialchars($parts[0]) ?>:</span>
                            <span class="spec-value"><?= htmlspecialchars($parts[1]) ?></span>
                        <?php else: ?>
                            <?= htmlspecialchars($spec) ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="quantity">
            <label>Quantity:</label>
            <input type="number" value="1" min="1">
        </div>

        <div class="action-buttons">
            <button class="add-to-cart">Add to Cart</button>
        </div>
        <button class="buy-now">Buy Now</button>
    </div>

</div><!-- end product-detail -->
   <!-- Customer Reviews -->
<div class="ratings-reviews">
    <h2>Customer Reviews</h2>
    <div class="average-rating">
        <span class="rating-stars">★★★★★</span>
        <span class="rating-value">4.7 out of 5</span>
        <span class="total-reviews">(Based on 123 reviews)</span>
    </div>
    <div class="reviews-list">
        <?php
        $reviews = $conn->query("SELECT * FROM reviews WHERE watch_id = $id");
        while($review = $reviews->fetch_assoc()):
            $stars = str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']);
        ?>
        <div class="review">
            <div class="review-header">
                <span class="review-author"><?= htmlspecialchars($review['author']) ?></span>
                <span class="review-rating"><?= $stars ?></span>
            </div>
            <p class="review-text"><?= htmlspecialchars($review['review_text']) ?></p>
            <span class="review-date">Reviewed on <?= htmlspecialchars($review['review_date']) ?></span>
        </div>
        <?php endwhile; ?>
    </div>
</div>


    <!-- Footer -->
    <footer class="footer" style="margin-top: 0;">
        <div class="footer-bottom">
            &copy; 2025 WatchStore
        </div>
    </footer>

</body>
</html>