<?php require_once __DIR__ . '/../includes/config.php';
if ((int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn() === 0) { header('Location: setup.php'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $st = $pdo->prepare('SELECT * FROM admins WHERE username=?'); $st->execute([trim($_POST['username'] ?? '')]); $a = $st->fetch();
    if ($a && password_verify($_POST['password'] ?? '', $a['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin'] = ['id' => $a['id'], 'name' => $a['name'], 'last_login' => $a['last_login']];
        $pdo->prepare('UPDATE admins SET last_login=NOW() WHERE id=?')->execute([$a['id']]);
        header('Location: index.php'); exit;
    }
    sleep(1); $err = 'Username ya password galat hai.';
}
?><!DOCTYPE html><html lang="hi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin login</title><link rel="stylesheet" href="admin.css"></head><body style="display:block">
<form method="post" class="login"><h1>Admin login</h1><?php if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
<label for="u">Username</label><input id="u" name="username" required autofocus><label for="p">Password</label><input id="p" name="password" type="password" required>
<p><button>Login</button></p></form></body></html>
