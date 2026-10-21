<?php $pageTitle = 'Verify certificate'; include '../includes/header.php';
$no = trim($_GET['no'] ?? ''); $row = null;
if ($no !== '') {
    $st = $pdo->prepare('SELECT ct.certificate_no,ct.issue_date,ct.grade,ct.percentage,s.name,s.reg_no,c.name AS course
        FROM certificates ct JOIN students s ON s.id=ct.student_id LEFT JOIN courses c ON c.id=s.course_id WHERE ct.certificate_no=?');
    $st->execute([$no]); $row = $st->fetch();
}
?>
<div class="wrap"><h1>Verify certificate</h1>
<form method="get"><label for="no">Certificate number</label><input id="no" name="no" value="<?= e($no) ?>" required>
<p><button type="submit">Verify</button></p></form>
<?php if ($no !== '' && !$row): ?><div class="msg err">Is number ka certificate nahi mila. Number dobara check karein.</div>
<?php elseif ($row): ?><div class="msg ok">Certificate asli hai.</div><dl class="result">
 <dt>Naam</dt><dd><?= e($row['name']) ?></dd>
 <dt>Registration no.</dt><dd><?= e($row['reg_no']) ?></dd>
 <dt>Course</dt><dd><?= e($row['course']) ?></dd>
 <dt>Issue date</dt><dd><?= e($row['issue_date']) ?></dd>
 <dt>Grade</dt><dd><?= e($row['grade']) ?> (<?= e($row['percentage']) ?>%)</dd></dl><?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
