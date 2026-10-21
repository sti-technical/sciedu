<?php require 'inc/auth.php';
$type = $_GET['type'] ?? 'id';
$ids = array_values(array_filter(array_map('intval', (array)($_GET['ids'] ?? []))));
if (isset($_GET['id'])) $ids[] = (int)$_GET['id'];
if (!$ids) die('Pehle kam se kam ek record chunein.');
$in = implode(',', array_fill(0, count($ids), '?'));
$inst = setting('institute_name', 'Institute'); $addr = setting('address'); $phone = setting('phone');
function photo($s) { return !empty($s['photo']) ? '<img src="' . BASE . '/uploads/students/' . e($s['photo']) . '" alt="">' : '<div class="nophoto">Photo</div>'; }
?><!DOCTYPE html><html lang="hi"><head><meta charset="utf-8"><title>Print</title><style>
body{font-family:system-ui,sans-serif;margin:0;padding:12px;color:#14213d;background:#eee}
.bar{margin-bottom:12px}.bar button{padding:.5rem 1.2rem;font-weight:700}
.card,.page{background:#fff;border:2px solid #14213d;page-break-inside:avoid;margin:0 0 14px}
.card{width:340px;padding:10px;display:inline-block;vertical-align:top;margin-right:12px}
.card h3{margin:0 0 6px;text-align:center;background:#14213d;color:#fff;padding:5px;font-size:15px}
.card .r{display:flex;gap:10px}.card img,.nophoto{width:80px;height:95px;object-fit:cover;border:1px solid #888}.nophoto{display:flex;align-items:center;justify-content:center;color:#888}
.card p{margin:2px 0;font-size:13px}.page{padding:22px;max-width:760px}.page h2{text-align:center;margin:0}
.cert{border:10px double #b8860b;padding:50px 40px;text-align:center;background:#fff;max-width:900px;margin:auto}
.cert h1{font-size:40px;color:#b8860b;margin:0 0 20px}.cert .big{font-size:28px;font-weight:700;margin:12px 0}.cert p{font-size:18px;line-height:1.7}
.sign{display:flex;justify-content:space-between;margin-top:60px;font-size:15px}
table{width:100%;border-collapse:collapse}td{padding:6px;border-bottom:1px solid #ccc}
@media print{body{background:#fff;padding:0}.bar{display:none}@page{margin:10mm}}
</style></head><body>
<div class="bar"><button onclick="window.print()">Print / PDF save karein</button></div>
<?php
if ($type === 'certificate') {
    $st = $pdo->prepare('SELECT ct.*, s.name, s.father_name, s.reg_no, c.name course, c.duration FROM certificates ct JOIN students s ON s.id=ct.student_id LEFT JOIN courses c ON c.id=s.course_id WHERE ct.id=?');
    $st->execute([$ids[0]]); $r = $st->fetch(); if (!$r) die('Certificate nahi mila.'); ?>
<div class="cert"><h1>Certificate of Completion</h1><p><b><?= e($inst) ?></b></p><p>This is to certify that</p>
<div class="big"><?= e($r['name']) ?></div><p>S/o D/o <?= e($r['father_name']) ?> (Reg. No. <?= e($r['reg_no']) ?>) has successfully completed the course</p>
<div class="big"><?= e($r['course']) ?></div><p>Duration: <?= e($r['duration']) ?> &nbsp;|&nbsp; Grade: <?= e($r['grade']) ?> (<?= e($r['percentage']) ?>%)</p>
<p>Certificate No: <b><?= e($r['certificate_no']) ?></b> &nbsp; Date: <?= e($r['issue_date']) ?></p>
<p style="font-size:14px">Verify: <?= e((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . BASE) ?>/student-zone/verify-certificate.php</p>
<div class="sign"><span>Controller of Examination</span><span>Director</span></div></div>
<?php } elseif ($type === 'receipt') {
    $st = $pdo->prepare('SELECT f.*, s.name, s.reg_no, c.name course FROM fees f JOIN students s ON s.id=f.student_id LEFT JOIN courses c ON c.id=s.course_id WHERE f.id=?');
    $st->execute([$ids[0]]); $r = $st->fetch(); if (!$r) die('Receipt nahi mili.'); ?>
<div class="page"><h2><?= e($inst) ?></h2><p style="text-align:center"><?= e($addr) ?> <?= e($phone) ?></p><h3 style="text-align:center">Fee Receipt</h3>
<table><tr><td>Receipt no.</td><td><b><?= e($r['receipt_no']) ?></b></td><td>Date</td><td><?= e($r['paid_on']) ?></td></tr>
<tr><td>Student</td><td><?= e($r['name']) ?></td><td>Reg no.</td><td><?= e($r['reg_no']) ?></td></tr>
<tr><td>Course</td><td colspan="3"><?= e($r['course']) ?></td></tr><tr><td>Amount</td><td><b>Rs. <?= number_format($r['amount'], 2) ?></b></td><td>Mode</td><td><?= e($r['payment_mode']) ?></td></tr>
<tr><td>Remark</td><td colspan="3"><?= e($r['remark']) ?></td></tr></table><div class="sign"><span>Student</span><span>Authorised signature</span></div></div>
<?php } else {
    $st = $pdo->prepare("SELECT s.*, c.name course, b.timing FROM students s LEFT JOIN courses c ON c.id=s.course_id LEFT JOIN batches b ON b.id=s.batch_id WHERE s.id IN ($in)");
    $st->execute($ids);
    foreach ($st->fetchAll() as $s):
        if ($type === 'admit') {
            $ex = $pdo->prepare('SELECT e.title, e.exam_date, e.duration_minutes FROM exam_enrolls ee JOIN exams e ON e.id=ee.exam_id WHERE ee.student_id=? ORDER BY ee.id DESC LIMIT 1');
            $ex->execute([$s['id']]); $x = $ex->fetch(); ?>
<div class="page"><h2><?= e($inst) ?></h2><h3 style="text-align:center">ADMIT CARD</h3><div class="r" style="display:flex;gap:16px"><?= photo($s) ?>
<table><tr><td>Roll / Reg no.</td><td><b><?= e($s['reg_no']) ?></b></td></tr><tr><td>Naam</td><td><?= e($s['name']) ?></td></tr><tr><td>Father</td><td><?= e($s['father_name']) ?></td></tr>
<tr><td>Course</td><td><?= e($s['course']) ?></td></tr><tr><td>Exam</td><td><?= e($x['title'] ?? 'Exam enroll nahi hua') ?></td></tr>
<tr><td>Exam date</td><td><?= e($x['exam_date'] ?? '-') ?> <?= $x ? '(' . (int)$x['duration_minutes'] . ' min)' : '' ?></td></tr></table></div>
<p style="font-size:13px">Instructions: Admit card aur photo ID saath layein. Exam shuru hone se 15 minute pehle pahunchein.</p><div class="sign"><span>Student signature</span><span>Controller of Examination</span></div></div>
<?php } else { ?>
<div class="card"><h3><?= e($inst) ?></h3><div class="r"><?= photo($s) ?><div><p><b><?= e($s['name']) ?></b></p><p>Reg: <?= e($s['reg_no']) ?></p><p>Course: <?= e($s['course']) ?></p>
<p>Batch: <?= e($s['timing']) ?></p><p>DOB: <?= e($s['dob']) ?></p><p>Mob: <?= e($s['mobile']) ?></p></div></div><p style="font-size:11px;text-align:center"><?= e($addr) ?> <?= e($phone) ?></p></div>
<?php }
    endforeach;
} ?></body></html>
