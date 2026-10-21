</main>
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
</body>
</html>
