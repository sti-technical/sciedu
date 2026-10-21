<?php require 'inc/auth.php'; require 'inc/layout.php';
$n = fn($sql) => (float)$pdo->query($sql)->fetchColumn();
admin_header('Dashboard'); ?>
<p>Welcome, <?= e($_SESSION['admin']['name']) ?>. Last login: <?= e($_SESSION['admin']['last_login'] ?? 'pehli baar') ?></p>
<div class="stats">
 <div class="card"><b><?= $n('SELECT COUNT(*) FROM students') ?></b>Total students</div>
 <div class="card"><b><?= $n("SELECT COUNT(*) FROM students WHERE status='Pending'") ?></b>Verify baaki</div>
 <div class="card"><b><?= $n("SELECT COUNT(*) FROM students WHERE status='Course Completed'") ?></b>Course completed</div>
 <div class="card"><b><?= $n('SELECT COUNT(*) FROM enquiries') ?></b>Enquiries</div>
 <div class="card"><b>Rs. <?= number_format($n("SELECT COALESCE(SUM(amount),0) FROM fees WHERE YEAR(paid_on)=YEAR(CURDATE()) AND MONTH(paid_on)=MONTH(CURDATE())")) ?></b>Is mahine ki fees</div>
 <div class="card"><b><?= $n('SELECT COUNT(*) FROM exams') ?></b>Exams</div>
</div>
<h3>Naye registration (verify baaki)</h3>
<table><tr><th>Reg no.</th><th>Naam</th><th>Mobile</th><th>Action</th></tr>
<?php $rows = $pdo->query("SELECT id,reg_no,name,mobile FROM students WHERE status='Pending' ORDER BY id DESC LIMIT 8")->fetchAll();
if (!$rows) echo '<tr><td colspan="4">Koi pending registration nahi.</td></tr>';
foreach ($rows as $r): ?><tr><td><?= e($r['reg_no']) ?></td><td><?= e($r['name']) ?></td><td><?= e($r['mobile']) ?></td>
<td><a href="students.php?act=verify&id=<?= $r['id'] ?>&k=<?= csrf() ?>">Verify</a></td></tr><?php endforeach; ?></table>
<h3 style="margin-top:1.5rem">Latest enquiries</h3>
<table><tr><th>Naam</th><th>Mobile</th><th>Course</th><th>Date</th></tr>
<?php foreach ($pdo->query('SELECT * FROM enquiries ORDER BY id DESC LIMIT 5') as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['mobile']) ?></td><td><?= e($r['course_interest']) ?></td><td><?= e($r['created_at']) ?></td></tr><?php endforeach; ?></table>
<?php admin_footer();
