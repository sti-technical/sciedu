<?php require 'inc/auth.php'; require 'inc/layout.php';
$reg = trim($_GET['reg'] ?? '');
if (isset($_GET['del'])) { check_csrf($_GET['k'] ?? ''); $pdo->prepare('DELETE FROM certificates WHERE id=?')->execute([(int)$_GET['del']]); flash('Certificate delete ho gaya.'); go('certificates.php'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf($_POST['k'] ?? '');
    $st = $pdo->prepare('SELECT id FROM students WHERE reg_no=?'); $st->execute([trim($_POST['reg_no'] ?? '')]); $sid = $st->fetchColumn();
    if (!$sid) flash('Ye registration number nahi mila.');
    else {
        $pdo->prepare('INSERT INTO certificates (student_id,certificate_no,issue_date,grade,percentage) VALUES (?,?,?,?,?)')
            ->execute([$sid, uniqid('T'), $_POST['issue_date'] ?: date('Y-m-d'), trim($_POST['grade']), (float)$_POST['percentage']]);
        $cid = $pdo->lastInsertId();
        $pdo->prepare('UPDATE certificates SET certificate_no=? WHERE id=?')->execute(['CERT' . date('Y') . str_pad($cid, 4, '0', STR_PAD_LEFT), $cid]);
        $pdo->prepare("UPDATE students SET status='Course Completed' WHERE id=?")->execute([$sid]);
        flash('Certificate ban gaya aur student Course Completed ho gaya.');
    }
    go('certificates.php');
}
admin_header('Certificates'); ?>
<form method="post" class="card"><h3>Naya certificate</h3><input type="hidden" name="k" value="<?= csrf() ?>"><div class="row">
 <div><label>Registration no.</label><input name="reg_no" value="<?= e($reg) ?>" required></div>
 <div><label>Issue date</label><input type="date" name="issue_date" value="<?= date('Y-m-d') ?>"></div>
 <div><label>Grade</label><input name="grade" placeholder="A"></div>
 <div><label>Percentage</label><input type="number" step="0.01" name="percentage"></div></div><button>Certificate banayein</button></form>
<table><tr><th>Certificate no.</th><th>Reg no.</th><th>Naam</th><th>Course</th><th>Grade</th><th>Date</th><th>Action</th></tr>
<?php foreach ($pdo->query('SELECT ct.*, s.name, s.reg_no, c.short_name course FROM certificates ct JOIN students s ON s.id=ct.student_id LEFT JOIN courses c ON c.id=s.course_id ORDER BY ct.id DESC LIMIT 200') as $r): ?>
<tr><td><?= e($r['certificate_no']) ?></td><td><?= e($r['reg_no']) ?></td><td><?= e($r['name']) ?></td><td><?= e($r['course']) ?></td><td><?= e($r['grade']) ?> (<?= e($r['percentage']) ?>%)</td><td><?= e($r['issue_date']) ?></td>
<td><a href="print.php?type=certificate&id=<?= $r['id'] ?>" target="_blank">Print</a><a style="color:#c0392b" href="?del=<?= $r['id'] ?>&k=<?= csrf() ?>" onclick="return confirm('Pakka delete?')">Delete</a></td></tr><?php endforeach; ?></table>
<?php admin_footer();
