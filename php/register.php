<?php
session_start();
require_once '../config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = trim($_POST['username'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');

    if (empty($username) || strlen($username) < 3)
        $errors[] = 'Username must be at least 3 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'Please enter a valid email address.';
    if (strlen($password) < 6)
        $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm)
        $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=? OR username=?");
        $stmt->bind_param('ss', $email, $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'Email or username is already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $ins  = $conn->prepare(
                "INSERT INTO users (username, email, password, first_name, last_name, phone)
                 VALUES (?,?,?,?,?,?)"
            );
            $ins->bind_param('ssssss', $username, $email, $hash, $first_name, $last_name, $phone);
            if ($ins->execute()) {
                $success = 'Account created! You can now log in.';
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Time-Hub</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            background: #1a0a0a;
            font-family: 'Outfit', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        /* Ambient glows */
        body::before {
            content: '';
            position: fixed;
            top: -80px; left: -80px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(192,57,43,.25) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -80px; right: -80px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(201,168,76,.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .auth-wrap { width: 100%; max-width: 520px; padding: 20px; z-index: 1; }

        /* Glass card */
        .auth-box {
            background: rgba(42,14,14,.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(192,57,43,.3);
            border-radius: 18px;
            box-shadow: 0 24px 60px rgba(0,0,0,.5), 0 0 0 1px rgba(192,57,43,.1);
            padding: 40px 38px;
        }

        .logo-top {
            text-align: center;
            margin-bottom: 24px;
        }
        .logo-top a {
            font-size: 24px;
            font-weight: 700;
            color: #f0e0e0;
            letter-spacing: 2px;
            text-decoration: none;
        }
        .logo-top a span { color: #c0392b; }

        .auth-box h2 { color: #f0e0e0; font-size: 24px; margin-bottom: 4px; font-weight: 700; }
        .auth-box p.sub { color: #9a7070; font-size: 14px; margin-bottom: 24px; }

        /* Form fields */
        .form-row { display: flex; gap: 14px; }
        .form-group { margin-bottom: 16px; flex: 1; }
        .form-group label { display: block; font-size: 11px; font-weight: 700; color: #c0a0a0; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 1px; }
        .form-group input {
            width: 100%;
            padding: 11px 14px;
            background: rgba(26,10,10,.8);
            border: 1px solid rgba(192,57,43,.25);
            border-radius: 8px;
            font-size: 14px;
            color: #f0e0e0;
            font-family: 'Outfit', Arial, sans-serif;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-group input::placeholder { color: #664444; }
        .form-group input:focus {
            border-color: #c0392b;
            outline: none;
            box-shadow: 0 0 0 3px rgba(192,57,43,.2);
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #c0392b, #8b1a12);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 8px;
            font-family: 'Outfit', Arial, sans-serif;
            letter-spacing: .5px;
            transition: opacity .2s, transform .15s;
        }
        .btn-submit:hover { opacity: .88; transform: translateY(-1px); }

        /* Alerts */
        .alert { padding: 10px 14px; border-radius: 8px; margin-bottom: 14px; font-size: 13px; }
        .alert-error   { background: rgba(192,57,43,.15); color: #f0a0a0; border-left: 3px solid #c0392b; }
        .alert-success { background: rgba(39,174,96,.15);  color: #9af0b0; border-left: 3px solid #27ae60; }
        .alert-success a { color: #c9a84c; font-weight: 600; }

        .login-link { text-align: center; margin-top: 20px; font-size: 14px; color: #9a7070; }
        .login-link a { color: #c9a84c; font-weight: 600; text-decoration: none; }
        .login-link a:hover { color: #c0392b; }
        .divider { border: none; border-top: 1px solid rgba(192,57,43,.2); margin: 20px 0; }
    </style>
</head>
<body>
<div class="auth-wrap">
    <div class="auth-box">
        <div class="logo-top">
            <a href="../index.php">⌚ Time<span>Hub</span></a>
        </div>
        <h2>Create Account</h2>
        <p class="sub">Join Time-Hub and explore premium watches.</p>

        <?php foreach ($errors as $e): ?>
            <div class="alert alert-error"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?> <a href="login.php">Login now →</a></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" placeholder="Pratyush">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" placeholder="Neupane">
                </div>
            </div>
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" placeholder="pratyush123">
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="you@email.com">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" placeholder="+977 98XXXXXXXX">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required placeholder="Min 6 chars">
                </div>
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm_password" required placeholder="Repeat">
                </div>
            </div>
            <button type="submit" class="btn-submit">Create Account →</button>
        </form>
        <hr class="divider">
        <div class="login-link">Already have an account? <a href="login.php">Sign In</a></div>
    </div>
</div>
<script src="../js/cursor-effects.js"></script>
</body>
</html>
