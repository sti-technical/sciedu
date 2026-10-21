<?php $pageTitle = 'About us'; include 'includes/header.php'; ?>
<div class="wrap">
  <h1>About us</h1>
  <p><?= nl2br(e(setting('about_text', 'Yahan apne institute ke baare me likhein. Ye text admin panel ke Settings se badla ja sakega.'))) ?></p>
</div>
<?php include 'includes/footer.php'; ?>
