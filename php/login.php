<?php
session_start();
require_once '../config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $conn->prepare(
            "SELECT id, username, password, is_admin FROM users WHERE email=? OR username=? LIMIT 1"
        );
        $stmt->bind_param('ss', $login, $login);
        $stmt->execute();
        $stmt->bind_result($id, $username, $hash, $is_admin);
        $stmt->fetch();
        $stmt->close();

        if ($id && password_verify($password, $hash)) {
            $_SESSION['user_id']  = $id;
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = $is_admin;

            if ($is_admin) {
                header('Location: admin/dashboard.php');
            } else {
                $redirect = $_GET['redirect'] ?? '../index.php';
                header('Location: ' . $redirect);
            }
            exit;
        } else {
            $error = 'Invalid email/username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Time-Hub</title>
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
        body::before {
            content: '';
            position: fixed;
            top: -100px; right: -100px;
            width: 450px; height: 450px;
            background: radial-gradient(circle, rgba(192,57,43,.2) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -100px; left: -100px;
            width: 450px; height: 450px;
            background: radial-gradient(circle, rgba(201,168,76,.12) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* Clock decorative lines */
        .clock-bg {
            position: fixed;
            inset: 0;
            background-image:
                repeating-linear-gradient(0deg, transparent, transparent 60px, rgba(192,57,43,.04) 60px, rgba(192,57,43,.04) 61px),
                repeating-linear-gradient(90deg, transparent, transparent 60px, rgba(192,57,43,.04) 60px, rgba(192,57,43,.04) 61px);
            pointer-events: none;
        }

        .auth-wrap { width: 100%; max-width: 420px; padding: 20px; z-index: 1; }
        .auth-box {
            background: rgba(42,14,14,.9);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(192,57,43,.3);
            border-radius: 20px;
            box-shadow: 0 28px 70px rgba(0,0,0,.6), 0 0 0 1px rgba(192,57,43,.1);
            padding: 48px 40px;
        }

        .logo-top { text-align: center; margin-bottom: 30px; }
        .logo-icon { font-size: 42px; display: block; margin-bottom: 6px; }
        .logo-name { font-size: 22px; font-weight: 700; color: #f0e0e0; letter-spacing: 3px; text-transform: uppercase; }
        .logo-name span { color: #c0392b; }

        .auth-box h2 { color: #f0e0e0; font-size: 22px; font-weight: 700; margin-bottom: 4px; text-align: center; }
        .auth-box p.sub { color: #9a7070; font-size: 13px; margin-bottom: 28px; text-align: center; }

        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 11px; font-weight: 700; color: #c0a0a0; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 1px; }
        .form-group input {
            width: 100%;
            padding: 13px 16px;
            background: rgba(26,10,10,.8);
            border: 1px solid rgba(192,57,43,.25);
            border-radius: 10px;
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

        .btn-submit {
            width: 100%;
            padding: 14px;
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
            position: relative;
            overflow: hidden;
        }
        .btn-submit::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.1), transparent);
        }
        .btn-submit:hover { opacity: .86; transform: translateY(-1px); }

        .alert-error {
            background: rgba(192,57,43,.12);
            color: #f0a0a0;
            border-left: 3px solid #c0392b;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 13px;
        }
        .divider { border: none; border-top: 1px solid rgba(192,57,43,.2); margin: 24px 0; }
        .reg-link { text-align: center; font-size: 14px; color: #9a7070; }
        .reg-link a { color: #c9a84c; font-weight: 600; text-decoration: none; }
        .reg-link a:hover { color: #c0392b; }
    </style>
</head>
<body>
<div class="clock-bg"></div>
<div class="auth-wrap">
    <div class="auth-box">
        <div class="logo-top">
            <span class="logo-icon">⌚</span>
            <div class="logo-name">Time<span>Hub</span></div>
        </div>
        <h2>Welcome Back</h2>
        <p class="sub">Sign in to your Time-Hub account</p>

        <?php if ($error): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Email or Username</label>
                <input type="text" name="login" required autofocus
                    value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
                    placeholder="you@email.com or username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Your password">
            </div>
            <button type="submit" class="btn-submit">Sign In →</button>
        </form>
        <hr class="divider">
        <div class="reg-link">Don't have an account? <a href="register.php">Create one →</a></div>
    </div>
</div>
<script src="../js/cursor-effects.js"></script>
</body>
</html>
