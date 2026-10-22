<?php require_once __DIR__ . '/includes/site.php'; $pageTitle = 'Gallery'; include 'includes/header.php';
$type = $_GET['type'] ?? '';
if ($type === 'photo' || $type === 'video') {
    $st = $pdo->prepare('SELECT * FROM gallery WHERE type=? ORDER BY id DESC'); $st->execute([$type]); $items = $st->fetchAll();
} else { $items = $pdo->query('SELECT * FROM gallery ORDER BY id DESC')->fetchAll(); }
$head = $type === 'video' ? 'Video Gallery' : ($type === 'photo' ? 'Photo Gallery' : 'Gallery'); ?>
<div class="wrap"><h1><?= e($head) ?></h1>
<?php if (!$items): ?><p>Abhi koi photo ya video nahi hai.</p><?php else: ?>
<div class="grid"><?php foreach ($items as $g):
  if ($g['type'] === 'photo'): ?>
    <img src="<?= e(gallery_src($g['file_or_url'])) ?>" alt="<?= e($g['title']) ?>" loading="lazy">
  <?php else: $yt = youtube_id($g['file_or_url']);
    if ($yt): ?>
    <div><iframe width="100%" height="170" src="https://www.youtube-nocookie.com/embed/<?= e($yt) ?>" title="<?= e($g['title']) ?>" loading="lazy" allowfullscreen style="border:0"></iframe><small><?= e($g['title']) ?></small></div>
    <?php else: ?>
    <a href="<?= e($g['file_or_url']) ?>" target="_blank" rel="noopener">Video: <?= e($g['title']) ?></a>
  <?php endif; endif; endforeach; ?></div><?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
