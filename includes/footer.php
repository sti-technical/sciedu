</main>
<?php if (!isset($tel)) { require_once __DIR__ . '/site.php'; $tel = first_phone(setting('phone', '')); $wa = first_phone(setting('whatsapp', '')) ?: $tel; } ?>
<footer class="site-footer">
  <div>
    <strong><?= e(setting('institute_name', 'Your Institute Name')) ?></strong>
    <p><?= e(setting('address', 'Address add karein (admin settings me)')) ?></p>
  </div>
  <div>
    <a href="<?= BASE ?>/courses.php">Courses</a>
    <a href="<?= BASE ?>/student-zone/verify-certificate.php">Verify certificate</a>
    <a href="<?= BASE ?>/contact.php">Contact</a>
    <a href="<?= BASE ?>/apply-job.php">Apply for job</a>
  </div>
  <small>&copy; <?= date('Y') ?> <?= e(setting('institute_name', 'Your Institute Name')) ?>. All rights reserved.</small>
</footer>
<?php if ($wa): ?><a class="fab wa" href="https://wa.me/91<?= e($wa) ?>?text=<?= rawurlencode('Namaste, mujhe course ki jaankari chahiye.') ?>" target="_blank" rel="noopener">WhatsApp</a><?php endif; ?>
<?php if ($tel): ?><a class="fab call" href="tel:+91<?= e($tel) ?>">Call karein</a><?php endif; ?>
<script>
(function () {
  var c = document.getElementById('clock');
  function t() { if (c) c.textContent = new Date().toLocaleString('en-IN', {timeZone: 'Asia/Kolkata', day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true}) + ' IST'; }
  t(); setInterval(t, 1000);
  var s = document.querySelectorAll('.slider img');
  if (s.length > 1) { var i = 0; setInterval(function () { s[i].classList.remove('on'); i = (i + 1) % s.length; s[i].classList.add('on'); }, 4000); }
})();
</script>
</body>
</html>
