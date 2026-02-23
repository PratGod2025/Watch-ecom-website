
<!DOCTYPE html>
<html lang="eng">

<head>
  <meta name="description" content="Products page of watch selling website">
  <meta name="keywords" content="Products, watches, Soura">
  <meta name="author" content="Oscar Kafle">
  <title>Products Page</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .video-container {
      position: relative;
      width: 100vw;
      height: 100vh;
      overflow: hidden;
    }

    .video-container video {
      width: 100vw;
      height: 100vh;
      object-fit: cover;
    }

    video {
      object-fit: cover;
      object-position: center;
      width: 100%;
      height: 100%;
    }

    .green {
      background: #202c2f;
    }

    .navbar {
      display: flex;
      align-items: center;
      padding: 10px;
    }

    nav {
      flex: 1;
      text-align: right;
    }

    nav ul {
      display: inline-block;
      list-style-type: none;
    }

    nav ul li {
      display: inline-block;
      margin-right: 20px;
    }

    p {
      color: rgb(246, 9, 9);
    }

    .product_image {
      height: 300px;
      width: 300px;
    }

    /* Image Slider Styles */
    .slider {
      width: 100%;
      overflow: hidden;
      position: relative;
      margin: 20px 0;
    }

    .slides {
      display: flex;
      transition: transform 0.5s ease-in-out;
    }

    .slide {
      min-width: 100%;
      box-sizing: border-box;
    }

    .slide img {
      width: 50%;
      display: block;
    }

    .slider-button {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background-color: rgba(0, 0, 0, 0.5);
      color: white;
      border: none;
      padding: 10px;
      cursor: pointer;
      z-index: 10;
    }

    .prev { left: 10px; }
    .next { right: 10px; }

    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      text-align: center;
      background-color: #f4f4f4;
    }

    header {
      background-color: #fff;
      padding: 20px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .tit {
      color: rgb(4, 98, 88);
    }

    .btn:hover {
      background-color: red;
    }

    .add-to-cart {
      padding: 10px;
      background-color: tomato;
      color: white;
    }

    .view-details {
      padding: 10px;
      background-color: tomato;
      color: white;
    }

    .product {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      padding: 20px;
      transition: transform 0.3s;
    }

    .product:hover {
      transform: scale(1.05);
    }

    .product_image {
      max-width: 100%;
      border-bottom: 1px solid #ddd;
      margin-bottom: 15px;
      padding: 25px;
    }

    .product h3 {
      font-size: 18px;
      margin: 10px 0;
    }

    .product p {
      color: #e67e22;
      font-weight: bold;
    }
  </style>
</head>

<body>

  <header class="green">
    <div class="header">
      <div class="container">
        <div class="navbar">
          <div class="logo">
            <a href="../index.php"><img src="../images/logo.png" width="125px"></a>
          </div>
          <nav>
            <ul>
              <li><a href="../index.php">Home</a></li>
              <li><a href="./products.php">Products</a></li>
              <li><a href="./blog.php">Blog</a></li>
              <li><a href="./contact.php">Contact</a></li>
            </ul>
          </nav>
          <div style="position: relative">
            <a href="./checkout.php"><img src="../images/cart.png" alt="cart" width="30px" height="30px"
                style="cursor: pointer;"></a>
            <span id="cart-counter"
              style="position: absolute; top: -10px; right: -10px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px;">0</span>
          </div>
        </div>
      </div>
    </div>
  </header>

  <div class="video-container">
    <video src="../images/watch.mp4" autoplay loop muted></video>
  </div><br>

  <h2 class="tit" style="margin: 20px 0 30px;">NEW RELEASES</h2>
  <table>
    <tr>
      <td class="product">
        <img class="product_image" src="../images/watch5.png" alt="Seiko Presage">
        <h3>Seiko Presage</h3>
        <p>Rs 180000</p>
        <div class="button-container">
          <button class="btn add-to-cart">Add to Cart</button>
          <a href="./watchdetails.php?id=1"><button class="btn view-details">View Details</button></a>
        </div>
      </td>
      <td class="product">
        <img class="product_image" src="../images/watch4.png" alt="Cartier Tank">
        <h3>Cartier Tank</h3>
        <p>Rs 530000</p>
        <div class="button-container">
          <button class="btn add-to-cart">Add to Cart</button>
          <a href="./watchdetails.php?id=2"><button class="btn view-details">View Details</button></a>
        </div>
      </td>
      <td class="product">
        <img class="product_image" src="../images/watch3.png" alt="Rolex Sea-Dweller">
        <h3>Rolex Sea-Dweller</h3>
        <p>Rs. 200000</p>
        <div class="button-container">
          <button class="btn add-to-cart">Add to Cart</button>
          <a href="./watchdetails.php?id=3"><button class="btn view-details">View Details</button></a>
        </div>
      </td>
      <td class="product">
        <img class="product_image" src="../images/rm2.png" alt="Richard Mille RM 53-02">
        <h3>Richard Mille RM 53-02 Tourbillon Blue Sapphire</h3>
        <p>Rs 420000000</p>
        <div class="button-container">
          <button class="btn add-to-cart">Add to Cart</button>
          <a href="./watchdetails.php?id=7"><button class="btn view-details">View Details</button></a>
        </div>
      </td>
    </tr>
  </table><br>

  <!-----Image Slider----->
  <div class="slider">
    <div class="slides">
      <div class="slide">
        <img src="../images/watchbanner1.jpeg" alt="Watchbanner 1" style="width: 100%;">
      </div>
      <div class="slide">
        <img src="../images/watchbanner2.jpeg" alt="Watchbanner 2" style="width: 100%;">
      </div>
      <div class="slide">
        <img src="../images/watchbanner3.jpeg" alt="Watchbanner 3" style="width: 100%;">
      </div>
      <div class="slide">
        <img src="../images/watchbanner4.jpeg" alt="Watchbanner 4" style="width: 100%;">
      </div>
    </div>
    <button class="slider-button prev" onclick="prevSlide()">&#10094;</button>
    <button class="slider-button next" onclick="nextSlide()">&#10095;</button>
  </div>

  <h2 class="tit" style="margin: 20px 0 30px;">OUR COLLECTION</h2>
  <table>
    <tr>
      <td class="product">
        <img class="product_image" src="../images/watch1.png" alt="Audemars Piguet Royal Oak">
        <h3>Audemars Piguet Royal Oak</h3>
        <p>Rs 1580000</p>
        <div class="button-container">
          <button class="btn add-to-cart">Add to Cart</button>
          <a href="./watchdetails.php?id=5"><button class="btn view-details">View Details</button></a>
        </div>
      </td>
      <td class="product">
        <img class="product_image" src="../images/rm1.png" alt="Richard Mille RM 11">
        <h3>Richard Mille RM 11 Chronograph</h3>
        <p>Rs 2100000</p>
        <div class="button-container">
          <button class="btn add-to-cart">Add to Cart</button>
          <a href="./watchdetails.php?id=6"><button class="btn view-details">View Details</button></a>
        </div>
      </td>
      <td class="product">
        <img class="product_image" src="../images/tag1.png" alt="TAG Heuer Aquaracer">
        <h3>TAG Heuer Aquaracer 300M Professional</h3>
        <p>Rs 420000</p>
        <div class="button-container">
          <button class="btn add-to-cart">Add to Cart</button>
          <a href="./watchdetails.php?id=11"><button class="btn view-details">View Details</button></a>
        </div>
      </td>
      <td class="product">
        <img class="product_image" src="../images/watch10.png" alt="Billionaire III">
        <h3>Billionaire III</h3>
        <p>Rs 1050000000</p>
        <div class="button-container">
          <button class="btn add-to-cart">Add to Cart</button>
          <a href="./watchdetails.php?id=8"><button class="btn view-details">View Details</button></a>
        </div>
      </td>
    </tr>
  </table><br>

  <h2 class="tit" style="margin: 20px 0 30px;">EXCEPTIONAL TIMEPIECE</h2>
  <table>
    <tr>
      <td class="product">
        <img class="product_image" src="../images/watch6.png" alt="Casio G-Shock">
        <h3>Casio G-Shock GA-2100</h3>
        <p>Rs 11500</p>
        <div class="button-container">
          <button class="btn add-to-cart">Add to Cart</button>
          <a href="./watchdetails.php?id=9"><button class="btn view-details">View Details</button></a>
        </div>
      </td>
      <td class="product">
        <img class="product_image" src="../images/watch8.png" alt="Seiko 5 Sports">
        <h3>Seiko 5 Sports</h3>
        <p>Rs 21000</p>
        <div class="button-container">
          <button class="btn add-to-cart">Add to Cart</button>
          <a href="./watchdetails.php?id=10"><button class="btn view-details">View Details</button></a>
        </div>
      </td>
      <td class="product">
        <img class="product_image" src="../images/watch2.png" alt="Privé Cloche de Cartier">
        <h3>Privé Cloche de Cartier</h3>
        <p>Rs 1000000</p>
        <div class="button-container">
          <button class="btn add-to-cart">Add to Cart</button>
          <a href="./watchdetails.php?id=4"><button class="btn view-details">View Details</button></a>
        </div>
      </td>
      <td class="product">
        <img class="product_image" src="../images/tag2.png" alt="TAG Heuer Carrera Seafarer">
        <h3>TAG Heuer Carrera Chronograph Seafarer × Hodinkee</h3>
        <p>Rs 840000</p>
        <div class="button-container">
          <button class="btn add-to-cart">Add to Cart</button>
          <a href="./watchdetails.php?id=12"><button class="btn view-details">View Details</button></a>
        </div>
      </td>
    </tr>
  </table><br>

  <!-------footer------>
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-section about">
        <h3>About Us</h3>
        <p>
          We're passionate about watches and believe they're more than just timepieces—they're a reflection of your
          style. Our collection combines timeless elegance with modern design, so you can find the perfect watch for any
          occasion.
        </p>
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
        <p>For Further Queries</p>
        <a href="./contact.php" style="color: gold;">Click Here!</a>
      </div>
    </div>
    <div class="footer-bottom">
      &copy; 2025 WatchStore |
    </div>
  </footer>

  <script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const totalSlides = slides.length;
    const sliderContainer = document.querySelector('.slides');

    const firstClone = slides[0].cloneNode(true);
    const lastClone = slides[totalSlides - 1].cloneNode(true);

    sliderContainer.appendChild(firstClone);
    sliderContainer.insertBefore(lastClone, slides[0]);

    let index = 1;
    const slideWidth = slides[0].clientWidth;

    sliderContainer.style.transform = `translateX(${-index * slideWidth}px)`;

    function moveSlide(direction) {
      if (direction === "next") {
        index++;
      } else {
        index--;
      }

      sliderContainer.style.transition = "transform 0.5s ease-in-out";
      sliderContainer.style.transform = `translateX(${-index * slideWidth}px)`;

      setTimeout(() => {
        if (index >= totalSlides + 1) {
          index = 1;
          sliderContainer.style.transition = "none";
          sliderContainer.style.transform = `translateX(${-index * slideWidth}px)`;
        } else if (index <= 0) {
          index = totalSlides;
          sliderContainer.style.transition = "none";
          sliderContainer.style.transform = `translateX(${-index * slideWidth}px)`;
        }
      }, 500);
    }

    function nextSlide() { moveSlide("next"); }
    function prevSlide() { moveSlide("prev"); }

    setInterval(nextSlide, 3000);
  </script>

</body>
</html>