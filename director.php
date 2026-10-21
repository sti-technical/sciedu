<?php $pageTitle = 'Director message'; include 'includes/header.php'; ?>
<div class="wrap">
  <h1>Director message</h1>
  <p><?= nl2br(e(setting('director_message', 'Director ka sandesh yahan aayega.'))) ?></p>
</div>
<?php include 'includes/footer.php'; ?>
