<?php $pageTitle = 'Apply for job'; include 'includes/header.php';
$msg = ''; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? ''); $mobile = trim($_POST['mobile'] ?? '');
    if ($name === '' || !preg_match('/^[0-9]{10}$/', $mobile)) $err = 'Naam aur 10 digit mobile number zaroori hai.';
    else { $pdo->prepare('INSERT INTO job_applications (name,mobile,email,position) VALUES (?,?,?,?)')->execute([$name, $mobile, trim($_POST['email'] ?? ''), trim($_POST['position'] ?? '')]); $msg = 'Aapki application mil gayi.'; }
}
?><div class="wrap"><h1>Apply for job</h1>
<?php if ($msg): ?><div class="msg ok"><?= e($msg) ?></div><?php endif; if ($err): ?><div class="msg err"><?= e($err) ?></div><?php endif; ?>
<form method="post"><div class="form-grid"><div><label for="name">Naam</label><input id="name" name="name" required></div>
<div><label for="mobile">Mobile (10 digit)</label><input id="mobile" name="mobile" maxlength="10" required></div>
<div><label for="email">Email</label><input id="email" name="email" type="email"></div>
<div><label for="position">Position</label><input id="position" name="position"></div></div><p><button type="submit">Apply karein</button></p></form></div>
<?php include 'includes/footer.php'; ?>
