<?php
function admin_header($title) {
    $cur = basename($_SERVER['PHP_SELF']);
    $nav = ['index.php'=>'Dashboard','students.php'=>'Students','students_edit.php'=>'Add student','fees.php'=>'Fees','certificates.php'=>'Certificates',
      'enquiries.php'=>'Enquiry record','jobs.php'=>'Applied job record','exam_enrolls.php'=>'Exam enroll record','exams.php'=>'Exams / Paper','results.php'=>'Exam results',
      'news.php'=>'News','courses.php'=>'Courses','batches.php'=>'Batches','gallery.php'=>'Gallery','settings.php'=>'Settings'];
    ?><!DOCTYPE html><html lang="hi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> | Admin</title><link rel="stylesheet" href="<?= BASE ?>/Admin/admin.css"></head><body>
<aside><div class="logo"><?= e(setting('institute_name', 'Admin')) ?></div>
<?php foreach ($nav as $f => $l): ?><a href="<?= BASE ?>/Admin/<?= $f ?>" class="<?= $cur === $f ? 'on' : '' ?>"><?= e($l) ?></a><?php endforeach; ?>
<a href="<?= BASE ?>/index.php" target="_blank">Go to website</a><a href="<?= BASE ?>/Admin/logout.php">Logout</a></aside>
<section><h1><?= e($title) ?></h1>
<?php if ($m = flash()): ?><div class="flash"><?= e($m) ?></div><?php endif;
}
function admin_footer() { echo '</section></body></html>'; }
