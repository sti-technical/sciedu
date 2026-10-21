<?php $pageTitle = 'Home'; include 'includes/header.php';
$courses = $pdo->query("SELECT name, short_name, duration FROM courses WHERE is_active=1 ORDER BY id LIMIT 6")->fetchAll();
$news = $pdo->query("SELECT title, posted_on FROM news WHERE is_active=1 ORDER BY posted_on DESC LIMIT 5")->fetchAll();
?>
<section class="hero"><div class="hero-in">
  <div>
    <h1>Computer ki padhai, jo naukri tak le jaaye.</h1>
    <p>ISO certified institute me computer, vocational aur job-oriented courses. Online registration karein aur apna certificate kabhi bhi verify karein.</p>
    <a class="btn" href="<?= BASE ?>/student-zone/register.php">Online registration</a>
  </div>
  <div class="check-box">
    <h2>Certificate verify karein</h2>
    <form action="<?= BASE ?>/student-zone/verify-certificate.php" method="get">
      <label for="no">Certificate number</label>
      <input id="no" name="no" required>
      <p><button type="submit">Verify</button></p>
    </form>
  </div>
</div></section>

<div class="wrap">
  <h2>Hamare courses</h2>
  <ul class="course-list">
  <?php foreach ($courses as $c): ?>
    <li><strong><?= e($c['name']) ?> (<?= e($c['short_name']) ?>)</strong><span><?= e($c['duration']) ?></span></li>
  <?php endforeach; ?>
  </ul>
  <a class="btn" href="<?= BASE ?>/courses.php">Saare courses dekhein</a>

  <h2 style="margin-top:2.5rem">Naye notice</h2>
  <?php if (!$news): ?><p>Abhi koi notice nahi hai.</p><?php else: ?>
  <ul class="news"><?php foreach ($news as $n): ?>
    <li><?= e($n['title']) ?> <small>(<?= e($n['posted_on']) ?>)</small></li>
  <?php endforeach; ?></ul><?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
