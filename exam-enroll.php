<?php require_once '../includes/student.php';
$stu = student_required();
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_scsrf($_POST['k'] ?? '');
    $eid = (int)($_POST['exam_id'] ?? 0);
    $st = $pdo->prepare('SELECT id FROM exams WHERE id=? AND is_active=1 AND course_id=?');
    $st->execute([$eid, (int)$stu['course_id']]);
    if ($st->fetchColumn()) {
        $pdo->prepare('INSERT IGNORE INTO exam_enrolls (student_id, exam_id, admit_card_no) VALUES (?,?,?)')->execute([$stu['id'], $eid, admit_no($stu, $eid)]);
        header('Location: exam-enroll.php?ok=1'); exit;
    }
    $msg = 'Ye exam aapke course ka nahi hai.';
}
$st = $pdo->prepare('SELECT e.*, (SELECT COUNT(*) FROM exam_enrolls x WHERE x.student_id=? AND x.exam_id=e.id) AS enrolled FROM exams e WHERE e.is_active=1 AND e.course_id=? ORDER BY e.id DESC');
$st->execute([$stu['id'], (int)$stu['course_id']]); $exams = $st->fetchAll();
$pageTitle = 'Exam enroll'; include '../includes/header.php'; ?>
<div class="wrap"><h1>Exam enroll form</h1>
<div class="who"><span><b><?= e($stu['name']) ?></b> (<?= e($stu['reg_no']) ?>) &mdash; <?= e($stu['course_name']) ?></span><a href="logout.php">Logout</a></div>
<?php if (isset($_GET['ok'])): ?><div class="msg ok">Aap exam me enroll ho gaye. Ab admit card download kar sakte hain.</div><?php endif; ?>
<?php if ($msg): ?><div class="msg err"><?= e($msg) ?></div><?php endif; ?>
<?php if (!$exams): ?><p>Aapke course ka abhi koi exam open nahi hai.</p><?php else: ?>
<table class="t"><tr><th>Exam</th><th>Date</th><th>Time</th><th>Action</th></tr>
<?php foreach ($exams as $x): ?><tr><td><?= e($x['title']) ?></td><td><?= e($x['exam_date'] ?: '-') ?></td><td><?= (int)$x['duration_minutes'] ?> min</td><td>
<?php if ($x['enrolled']): ?><span class="pass">Enrolled</span> &middot; <a href="admit-card.php?exam=<?= (int)$x['id'] ?>">Admit card</a>
<?php else: ?><form method="post" style="margin:0"><input type="hidden" name="k" value="<?= scsrf() ?>"><input type="hidden" name="exam_id" value="<?= (int)$x['id'] ?>"><button type="submit">Enroll karein</button></form><?php endif; ?>
</td></tr><?php endforeach; ?></table><?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
