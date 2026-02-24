<?php
session_start();
require_once '../config.php';
$page_title = 'Contact Us';
$css_prefix = '../';
include 'includes/header.php';
?>
<style>
    body { background: #1a0a0a; color: #f0e0e0; font-family: 'Outfit', Arial, sans-serif; margin: 0; }
    
    .contact-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        padding: 60px 20px;
    }

    .contact-card {
        display: flex;
        width: 100%;
        max-width: 1100px;
        background: rgba(42,14,14,0.8);
        backdrop-filter: blur(12px);
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
        border-radius: 20px;
        border: 1px solid rgba(192,57,43,0.3);
        overflow: hidden;
        flex-wrap: wrap;
    }

    .form-section {
        flex: 1.5;
        min-width: 320px;
        background: linear-gradient(135deg, #2a0e0e, #1a0a0a);
        padding: 50px;
        border-right: 1px solid rgba(192,57,43,0.2);
    }

    .form-section h2 {
        margin-bottom: 30px;
        color: #fff;
        font-size: 28px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 13px; font-weight: 700; color: #c0a0a0; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }

    .form-section input,
    .form-section textarea {
        width: 100%;
        padding: 12px 15px;
        background: rgba(26,10,10,0.8);
        border: 1px solid rgba(192,57,43,0.3);
        border-radius: 8px;
        font-size: 15px;
        color: #fff;
        font-family: 'Outfit', sans-serif;
        transition: all 0.3s;
        resize: none;
    }

    .form-section input:focus,
    .form-section textarea:focus {
        border-color: #c0392b;
        outline: none;
        box-shadow: 0 0 10px rgba(192,57,43,0.3);
    }

    .gender-group { display: flex; gap: 20px; margin: 10px 0; }
    .gender-option { display: flex; align-items: center; gap: 8px; color: #f0e0e0; cursor: pointer; font-size: 15px; }
    .gender-option input { width: auto; margin: 0; }

    .btn-send {
        display: block;
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #c0392b, #8b1a12);
        border: none;
        color: #fff;
        font-size: 16px;
        font-weight: 700;
        border-radius: 10px;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 2px;
        transition: all 0.3s;
        margin-top: 25px;
    }

    .btn-send:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(192,57,43,0.4);
    }

    .info-section {
        flex: 1;
        min-width: 300px;
        padding: 50px;
        background: rgba(42,14,14,0.4);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .info-section h2 {
        margin-bottom: 30px;
        color: #c9a84c;
        font-size: 24px;
        font-weight: 700;
    }

    .info-section p {
        margin-bottom: 20px;
        font-size: 16px;
        color: #c0a0a0;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .info-section p span { color: #c0392b; font-size: 20px; }

    @media (max-width: 768px) {
        .form-section { border-right: none; border-bottom: 1px solid rgba(192,57,43,0.2); }
    }
</style>

<?php
    $success = false;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name    = trim($_POST['name']);
        $email   = trim($_POST['email']);
        $phone   = trim($_POST['phone']);
        $gender  = $_POST['gender'];
        $message = trim($_POST['message']);

        if ($name && $email && $phone && $message) {
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, gender, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $phone, $gender, $message);
            if ($stmt->execute()) {
                $success = true;
            }
        }
    }
?>

<div class="contact-container">
    <div class="contact-card">
        <div class="form-section">
            <h2>Send us a Message</h2>
            
            <?php if ($success): ?>
                <div style="background: rgba(39,174,96,0.2); color: #a0f0b0; border-left: 4px solid #27ae60; padding: 15px; border-radius: 8px; margin-bottom: 25px;">
                    ✓ Thank you! Your message has been sent successfully.
                </div>
            <?php endif; ?>

            <form name="contactForm" method="POST" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Full name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Your email address" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="10-digit number" required>
                </div>

                <div class="form-group">
                    <label>Gender</label>
                    <div class="gender-group">
                        <label class="gender-option">
                            <input type="radio" name="gender" value="male" checked> Male
                        </label>
                        <label class="gender-option">
                            <input type="radio" name="gender" value="female"> Female
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" placeholder="Leave a message here." required></textarea>
                </div>

                <button type="submit" class="btn-send">Send Message →</button>
            </form>
        </div>

        <div class="info-section">
            <h2>Contact Us</h2>
            <p><span>📍</span> Balkumari, Lalitpur, Nepal</p>
            <p><span>📞</span> +977 9812345678</p>
            <p><span>📧</span> pratyushisneupane@gmail.com</p>
            <p><span>🌐</span> www.timehub.com.np</p>
        </div>
    </div>
</div>

<script>
    function validateForm() {
        // Form is handled by PHP now, but we keep JS for instant feedback
        const name = document.forms["contactForm"]["name"].value.trim();
        const email = document.forms["contactForm"]["email"].value.trim();
        const phone = document.forms["contactForm"]["phone"].value.trim();
        const message = document.forms["contactForm"]["message"].value.trim();

        if (!name || !email || !phone || !message) {
            alert("Please fill in all required fields");
            return false;
        }

        if (isNaN(phone) || phone.length !== 10) {
            alert("Please enter a valid 10-digit phone number");
            return false;
        }

        if (!email.includes("@")) {
            alert("Please enter a valid email address");
            return false;
        }

        return true; 
    }
</script>

<?php include 'includes/footer.php'; ?>