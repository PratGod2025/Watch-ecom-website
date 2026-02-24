<?php
// php/includes/footer.php
// Uses $css_prefix set by the including page
$cp = $css_prefix ?? '../';
?>
<footer class="footer" style="margin-top:0;">
    <div class="footer-container">
        <div class="footer-section about">
            <h3>⌚ Time-Hub</h3>
            <p>We're passionate about watches and believe they're more than just timepieces — they're a reflection of your style and a mark of time.</p>
        </div>
        <div class="footer-section links">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="<?= $cp ?>index.php">🏠 Home</a></li>
                <li><a href="<?= $cp ?>php/products.php">⌚ Products</a></li>
                <li><a href="<?= $cp ?>php/cart.php">🛒 Cart</a></li>
                <li><a href="<?= $cp ?>php/blog.php">📝 Blog</a></li>
                <li><a href="<?= $cp ?>php/contact.php">📬 Contact</a></li>
            </ul>
        </div>
        <div class="footer-section contact">
            <h3>Contact Us</h3>
            <p>📍 Balkumari, Lalitpur, Nepal</p>
            <p>📞 +977 9812345678</p>
            <p>📧 pratyushisneupane@gmail.com</p>
            <a href="<?= $cp ?>php/contact.php" style="color:#c9a84c;font-weight:600;">Send a Message →</a>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; <?= date('Y') ?> Time-Hub Watch Store &nbsp;|&nbsp; Crafted with ❤️ in Nepal
    </div>
</footer>
<!-- Antigravity cursor effects – loaded on every page -->
<script src="<?= $cp ?>js/cursor-effects.js"></script>
</body>
</html>
