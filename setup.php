<?php require_once __DIR__ . '/../includes/config.php';
// Ye page sirf pehla admin banane ke liye hai
if ((int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn() > 0) die('Admin pehle se bana hua hai. <a href="login.php">Login karein</a>. Is setup.php file ko delete kar dein.');
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? ''); $p = $_POST['password'] ?? '';
    if ($u === '' || strlen($p) < 8) $err = 'Username likhein aur password kam se kam 8 characters ka rakhein.';
    else { $pdo->prepare('INSERT INTO admins (name,username,password_hash,role) VALUES (?,?,?,"admin")')->execute([$u, $u, password_hash($p, PASSWORD_DEFAULT)]);
           header('Location: login.php'); exit; }
}
?><!DOCTYPE html><html lang="hi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin setup</title><link rel="stylesheet" href="admin.css"></head><body style="display:block">
<form method="post" class="login"><h1>Pehla admin banayein</h1><?php if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
<label>Username</label><input name="username" required><label>Password (min 8 characters)</label><input name="password" type="password" required>
<p><button>Admin banayein</button></p></form></body></html>
