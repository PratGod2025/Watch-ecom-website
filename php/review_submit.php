<?php
// php/review_submit.php – handles review form submission (POST only)
session_start();
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: products.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$uid      = (int)$_SESSION['user_id'];
$username = $_SESSION['username'];
$watch_id = (int)($_POST['watch_id'] ?? 0);
$rating   = (int)($_POST['rating']   ?? 0);
$title    = trim($_POST['title']     ?? '');
$comment  = trim($_POST['comment']   ?? '');

if ($watch_id <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
    header("Location: watchdetails.php?id=$watch_id&review_error=1");
    exit;
}

// Check watch exists
$chk = $conn->query("SELECT id FROM watches WHERE id = $watch_id");
if ($chk->num_rows === 0) {
    header('Location: products.php');
    exit;
}

// Prevent duplicate review from same user for same watch
$dup = $conn->prepare("SELECT id FROM reviews WHERE watch_id=? AND user_id=?");
$dup->bind_param('ii', $watch_id, $uid);
$dup->execute();
$dup->store_result();
if ($dup->num_rows > 0) {
    header("Location: watchdetails.php?id=$watch_id&review_error=duplicate");
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO reviews (watch_id, user_id, username, rating, title, comment) VALUES (?,?,?,?,?,?)"
);
$stmt->bind_param('iiisss', $watch_id, $uid, $username, $rating, $title, $comment);
$stmt->execute();

// Update average rating in watches table
$conn->query(
    "UPDATE watches SET rating = (SELECT AVG(rating) FROM reviews WHERE watch_id=$watch_id) WHERE id=$watch_id"
);

header("Location: watchdetails.php?id=$watch_id&review_success=1");
exit;
