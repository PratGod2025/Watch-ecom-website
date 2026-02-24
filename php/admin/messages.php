<?php
session_start();
require_once '../../config.php';

if (empty($_SESSION['is_admin'])) {
    header('Location: ../login.php');
    exit;
}

$page_title = 'Contact Messages';
$css_prefix = '../../';

// Handle deletion
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $conn->query("DELETE FROM contact_messages WHERE id = $did");
    header("Location: messages.php");
    exit;
}

$messages = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");

include '../includes/header.php';
?>
<style>
    body { background: #1a0a0a; color: #f0e0e0; font-family: 'Outfit', Arial, sans-serif; margin: 0; }
    .admin-wrap { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
    .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .admin-header h1 { margin: 0; color: #fff; font-size: 32px; font-weight: 700; }
    
    .msg-table-wrap { 
        background: rgba(42,14,14,0.7); 
        backdrop-filter: blur(12px);
        border-radius: 20px; 
        border: 1px solid rgba(192,57,43,0.3);
        padding: 30px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.5);
    }
    
    table { width: 100%; border-collapse: collapse; color: #f0e0e0; }
    th { text-align: left; padding: 15px; border-bottom: 2px solid #c0392b; color: #9a7070; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; }
    td { padding: 15px; border-bottom: 1px solid rgba(192,57,43,0.1); font-size: 14px; vertical-align: top; }
    tr:hover td { background: rgba(192,57,43,0.05); }

    .msg-text { max-width: 400px; line-height: 1.5; color: #c0a0a0; }
    .badge-gender { padding: 4px 8px; border-radius: 4px; font-size: 11px; text-transform: uppercase; font-weight: 700; }
    .male { background: rgba(52,152,219,0.2); color: #a0d4f0; }
    .female { background: rgba(192,57,43,0.2); color: #f0a0a0; }

    .btn-del { color: #e74c3c; text-decoration: none; font-weight: 700; font-size: 13px; transition: 0.3s; }
    .btn-del:hover { color: #ff0000; text-shadow: 0 0 10px rgba(231,76,60,0.5); }
    
    .back-dash { color: #c9a84c; text-decoration: none; font-size: 14px; font-weight: 700; }
    .back-dash:hover { text-decoration: underline; }
</style>

<div class="admin-wrap">
    <div class="admin-header">
        <h1>Contact Inquiries</h1>
        <a href="dashboard.php" class="back-dash">← Back to Dashboard</a>
    </div>

    <div class="msg-table-wrap">
        <?php if ($messages && $messages->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User Info</th>
                        <th>Message</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($m = $messages->fetch_assoc()): ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($m['created_at'])) ?><br><span style="font-size:11px; color:#666;"><?= date('h:i A', strtotime($m['created_at'])) ?></span></td>
                            <td>
                                <strong><?= htmlspecialchars($m['name']) ?></strong><br>
                                <span style="font-size:12px; color:#c9a84c;"><?= htmlspecialchars($m['email']) ?></span><br>
                                <span style="font-size:12px;"><?= htmlspecialchars($m['phone']) ?></span><br>
                                <span class="badge-gender <?= $m['gender'] ?>"><?= $m['gender'] ?></span>
                            </td>
                            <td><div class="msg-text"><?= nl2br(htmlspecialchars($m['message'])) ?></div></td>
                            <td>
                                <a href="?delete=<?= $m['id'] ?>" class="btn-del" onclick="return confirm('Delete this message?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align:center; padding:50px; color:#666;">No messages found.</div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
