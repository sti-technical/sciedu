<?php $pageTitle = 'Contact'; include 'includes/header.php';
$msg = ''; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? ''); $mobile = trim($_POST['mobile'] ?? '');
    if ($name === '' || !preg_match('/^[0-9]{10}$/', $mobile)) {
        $err = 'Naam likhein aur 10 digit ka mobile number daalein.';
    } else {
        $pdo->prepare('INSERT INTO enquiries (name,mobile,email,course_interest,message) VALUES (?,?,?,?,?)')
            ->execute([$name, $mobile, trim($_POST['email'] ?? ''), trim($_POST['course'] ?? ''), trim($_POST['message'] ?? '')]);
        $msg = 'Aapki enquiry mil gayi. Hum jald sampark karenge.';
    }
}
?>
<div class="wrap"><h1>Contact us</h1>
<p><?= e(setting('address')) ?> <br> <?= e(setting('phone')) ?> | <?= e(setting('email')) ?></p>
<?php if ($msg): ?><div class="msg ok"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="msg err"><?= e($err) ?></div><?php endif; ?>
<form method="post">
  <div class="form-grid">
    <div><label for="name">Naam</label><input id="name" name="name" required></div>
    <div><label for="mobile">Mobile (10 digit)</label><input id="mobile" name="mobile" required maxlength="10"></div>
    <div><label for="email">Email</label><input id="email" name="email" type="email"></div>
    <div><label for="course">Kaunsa course?</label><input id="course" name="course"></div>
  </div>
  <label for="message">Message</label><textarea id="message" name="message"></textarea>
  <p><button type="submit">Enquiry bhejein</button></p>
</form></div>
<?php include 'includes/footer.php'; ?>
