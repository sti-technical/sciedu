<?php $pageTitle = 'Gallery'; include 'includes/header.php';
$items = $pdo->query('SELECT * FROM gallery ORDER BY id DESC')->fetchAll(); ?>
<div class="wrap"><h1>Gallery</h1>
<?php if (!$items): ?><p>Abhi koi photo ya video nahi hai.</p><?php else: ?>
<div class="grid"><?php foreach ($items as $g):
  if ($g['type'] === 'photo'): ?>
    <img src="<?= BASE ?>/uploads/<?= e($g['file_or_url']) ?>" alt="<?= e($g['title']) ?>">
  <?php else: ?>
    <a href="<?= e($g['file_or_url']) ?>" target="_blank" rel="noopener">Video: <?= e($g['title']) ?></a>
  <?php endif; endforeach; ?></div><?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
