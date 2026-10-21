<?php $pageTitle = 'Courses'; include 'includes/header.php';
$cats = ['Computer Program'=>'computer','Vocational Program'=>'vocational-program','Vocational Training'=>'vocational-training'];
?>
<div class="wrap"><h1>Courses we provide</h1>
<?php foreach ($cats as $cat => $anchor):
  $st = $pdo->prepare('SELECT * FROM courses WHERE category=? AND is_active=1 ORDER BY name');
  $st->execute([$cat]); $rows = $st->fetchAll(); ?>
  <h2 id="<?= $anchor ?>"><?= e($cat) ?></h2>
  <?php if (!$rows): ?><p>Is category me abhi course nahi hai.</p><?php else: ?>
  <ul class="course-list"><?php foreach ($rows as $r): ?>
    <li><strong><?= e($r['name']) ?> (<?= e($r['short_name']) ?>)</strong><span><?= e($r['duration']) ?> &nbsp; Rs. <?= e(number_format($r['fee'])) ?></span></li>
  <?php endforeach; ?></ul><?php endif; ?>
<?php endforeach; ?>
</div>
<?php include 'includes/footer.php'; ?>
