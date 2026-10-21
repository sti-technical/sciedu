<?php $pageTitle = 'Verify registration'; include '../includes/header.php';
$reg = trim($_GET['reg'] ?? ''); $row = null;
if ($reg !== '') {
    $st = $pdo->prepare('SELECT s.name,s.reg_no,s.doj,s.status,c.name AS course FROM students s LEFT JOIN courses c ON c.id=s.course_id WHERE s.reg_no=?');
    $st->execute([$reg]); $row = $st->fetch();
}
?>
<div class="wrap"><h1>Verify registration</h1>
<form method="get"><label for="reg">Registration number</label><input id="reg" name="reg" value="<?= e($reg) ?>" required>
<p><button type="submit">Verify</button></p></form>
<?php if ($reg !== '' && !$row): ?><div class="msg err">Ye registration number nahi mila. Number dobara check karein.</div>
<?php elseif ($row): ?><dl class="result">
 <dt>Naam</dt><dd><?= e($row['name']) ?></dd>
 <dt>Registration no.</dt><dd><?= e($row['reg_no']) ?></dd>
 <dt>Course</dt><dd><?= e($row['course']) ?></dd>
 <dt>Joining date</dt><dd><?= e($row['doj']) ?></dd>
 <dt>Status</dt><dd><?= e($row['status']) ?></dd></dl><?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
