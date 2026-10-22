<?php require_once '../includes/student.php';
$stu = student_required();
$eid = (int)($_GET['exam'] ?? 0);
$card = null;
if ($eid) {
    $st = $pdo->prepare('SELECT e.*, x.admit_card_no FROM exam_enrolls x JOIN exams e ON e.id=x.exam_id WHERE x.student_id=? AND x.exam_id=?');
    $st->execute([$stu['id'], $eid]); $card = $st->fetch() ?: null;
}
$list = $pdo->prepare('SELECT e.id, e.title, e.exam_date FROM exam_enrolls x JOIN exams e ON e.id=x.exam_id WHERE x.student_id=? ORDER BY x.id DESC');
$list->execute([$stu['id']]); $mine = $list->fetchAll();
$logo = media_ids('logo');
$photoOk = !empty($stu['photo']) && is_file(__DIR__ . '/../uploads/students/' . basename($stu['photo']));
$pageTitle = 'Admit card'; include '../includes/header.php'; ?>
<div class="wrap"><h1 class="noprint">Admit card</h1>
<div class="who"><span><b><?= e($stu['name']) ?></b> (<?= e($stu['reg_no']) ?>)</span><a href="logout.php">Logout</a></div>
<?php if ($eid && !$card): ?><div class="msg err noprint">Is exam me aap enroll nahi hue. Pehle <a href="exam-enroll.php">exam enroll</a> karein.</div><?php endif; ?>
<?php if ($card): ?>
<div class="admit">
  <?php if ($logo): ?><p style="text-align:center;margin:0"><img src="<?= media_url($logo[0]) ?>" alt="" style="height:70px"></p><?php endif; ?>
  <h2><?= e(setting('institute_name', 'Institute')) ?></h2><h3 style="text-align:center">ADMIT CARD</h3>
  <div class="r">
    <?php if ($photoOk): ?><img class="ph" src="<?= BASE ?>/uploads/students/<?= e(basename($stu['photo'])) ?>" alt=""><?php else: ?><div class="nophoto">Photo</div><?php endif; ?>
    <table>
      <tr><td>Admit card no.</td><td><b><?= e($card['admit_card_no'] ?: admit_no($stu, $eid)) ?></b></td></tr>
      <tr><td>Roll / Reg no.</td><td><b><?= e($stu['reg_no']) ?></b></td></tr>
      <tr><td>Naam</td><td><?= e($stu['name']) ?></td></tr>
      <tr><td>Father ka naam</td><td><?= e($stu['father_name']) ?></td></tr>
      <tr><td>Date of birth</td><td><?= e($stu['dob']) ?></td></tr>
      <tr><td>Course</td><td><?= e($stu['course_name']) ?></td></tr>
      <tr><td>Exam</td><td><?= e($card['title']) ?></td></tr>
      <tr><td>Exam date / Time</td><td><?= e($card['exam_date'] ?: '-') ?> &middot; <?= (int)$card['duration_minutes'] ?> min</td></tr>
    </table>
  </div>
  <p style="font-size:.85rem">Online exam ke liye website par Student Login karein (Roll number + Date of birth) aur Online Exam chunein.</p>
</div>
<p class="noprint"><button onclick="window.print()">Print / PDF save karein</button> <a class="btn" href="admit-card.php">Wapas</a></p>
<?php else: ?>
<?php if (!$mine): ?><p>Aap abhi kisi exam me enroll nahi hue. <a href="exam-enroll.php">Exam enroll form</a> bharein.</p>
<?php else: ?><table class="t"><tr><th>Exam</th><th>Date</th><th>Action</th></tr>
<?php foreach ($mine as $m): ?><tr><td><?= e($m['title']) ?></td><td><?= e($m['exam_date'] ?: '-') ?></td><td><a href="admit-card.php?exam=<?= (int)$m['id'] ?>">Admit card dekhein</a></td></tr><?php endforeach; ?></table><?php endif; ?>
<?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
