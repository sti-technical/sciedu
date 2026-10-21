<?php require 'inc/auth.php'; require 'inc/layout.php';
// ---- single actions ----
if (isset($_GET['act'])) {
    check_csrf($_GET['k'] ?? ''); $id = (int)($_GET['id'] ?? 0);
    $map = ['verify' => 'Verified', 'complete' => 'Course Completed', 'uncomplete' => 'Verified'];
    if (isset($map[$_GET['act']])) { $pdo->prepare('UPDATE students SET status=? WHERE id=?')->execute([$map[$_GET['act']], $id]); flash('Status badal gaya.'); }
    if ($_GET['act'] === 'delete') {
        $ph = $pdo->prepare('SELECT photo FROM students WHERE id=?'); $ph->execute([$id]); $photo = $ph->fetchColumn();
        $pdo->prepare('DELETE FROM students WHERE id=?')->execute([$id]);
        if ($photo) @unlink(__DIR__ . '/../uploads/students/' . basename($photo));
        flash('Student delete ho gaya.');
    }
    go('students.php');
}
// ---- bulk: course completed ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['bulk'] ?? '') !== '') {
    check_csrf($_POST['k'] ?? ''); $ids = array_map('intval', (array)($_POST['ids'] ?? []));
    if ($ids) {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $status = $_POST['bulk'] === 'complete' ? 'Course Completed' : 'Verified';
        $pdo->prepare("UPDATE students SET status=? WHERE id IN ($in)")->execute([$status, ...$ids]);
        flash(count($ids) . ' student update hue.');
    }
    go('students.php');
}
// ---- search / filter ----
$kw = trim($_GET['kw'] ?? ''); $course = (int)($_GET['course'] ?? 0); $batch = (int)($_GET['batch'] ?? 0); $status = $_GET['status'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1)); $per = 50;
$w = []; $p = [];
if ($kw !== '') { $w[] = '(s.name LIKE ? OR s.reg_no LIKE ? OR s.mobile LIKE ?)'; array_push($p, "%$kw%", "%$kw%", "%$kw%"); }
if ($course) { $w[] = 's.course_id=?'; $p[] = $course; }
if ($batch) { $w[] = 's.batch_id=?'; $p[] = $batch; }
if ($status !== '') { $w[] = 's.status=?'; $p[] = $status; }
$where = $w ? ' WHERE ' . implode(' AND ', $w) : '';
$cnt = $pdo->prepare("SELECT COUNT(*) FROM students s $where"); $cnt->execute($p); $total = (int)$cnt->fetchColumn();
$st = $pdo->prepare("SELECT s.*, c.short_name course, c.fee course_fee, b.timing,
   (SELECT COALESCE(SUM(amount),0) FROM fees f WHERE f.student_id=s.id) paid
   FROM students s LEFT JOIN courses c ON c.id=s.course_id LEFT JOIN batches b ON b.id=s.batch_id $where
   ORDER BY s.id DESC LIMIT $per OFFSET " . (($page - 1) * $per));
$st->execute($p); $rows = $st->fetchAll();
$courses = $pdo->query('SELECT id,name FROM courses ORDER BY name')->fetchAll(); $batches = $pdo->query('SELECT id,timing FROM batches')->fetchAll();
$qs = $_GET; unset($qs['page']);
admin_header('Student record'); ?>
<form method="get" class="card"><div class="row">
 <div><label>Naam / Reg no. / Mobile</label><input name="kw" value="<?= e($kw) ?>"></div>
 <div><label>Course</label><select name="course"><option value="0">Sab</option><?php foreach ($courses as $c): ?><option value="<?= $c['id'] ?>" <?= $course == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
 <div><label>Batch</label><select name="batch"><option value="0">Sab</option><?php foreach ($batches as $b): ?><option value="<?= $b['id'] ?>" <?= $batch == $b['id'] ? 'selected' : '' ?>><?= e($b['timing']) ?></option><?php endforeach; ?></select></div>
 <div><label>Status</label><select name="status"><option value="">Sab</option><?php foreach (['Pending', 'Verified', 'Course Completed', 'Inactive'] as $s): ?><option <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select></div>
</div><button>Search</button><a class="btn grey" href="students.php">Reset</a></form>

<form method="get" action="print.php" target="_blank">
 <input type="hidden" name="k" value="<?= csrf() ?>">
 <button name="type" value="id" class="amber">Print ID card</button>
 <button name="type" value="admit" class="amber">Generate admit card</button>
 <button type="submit" formaction="students.php" formmethod="post" formtarget="_self" name="bulk" value="complete">Course completed</button>
 <button type="submit" formaction="students.php" formmethod="post" formtarget="_self" name="bulk" value="remove" class="red">Remove course completed</button>
 <p><?= $total ?> student mile.</p>
<table><tr><th><input type="checkbox" onclick="document.querySelectorAll('.ck').forEach(c=>c.checked=this.checked)" aria-label="Sab chunein"></th>
 <th>Sr.</th><th>Photo</th><th>Reg / Naam</th><th>DOB / DOJ</th><th>Mobile</th><th>Batch</th><th>Course</th><th>Fee</th><th>Status</th><th>Action</th></tr>
<?php if (!$rows) echo '<tr><td colspan="11">Koi student nahi mila.</td></tr>';
foreach ($rows as $i => $r): $due = max(0, $r['course_fee'] - $r['paid']); $k = csrf(); ?>
<tr><td><input type="checkbox" class="ck" name="ids[]" value="<?= $r['id'] ?>"></td><td><?= ($page - 1) * $per + $i + 1 ?></td>
 <td><?php if ($r['photo']): ?><img class="th" src="<?= BASE ?>/uploads/students/<?= e($r['photo']) ?>" alt=""><?php endif; ?></td>
 <td><b><?= e($r['reg_no']) ?></b><br><?= e($r['name']) ?></td><td><?= e($r['dob']) ?><br><?= e($r['doj']) ?></td><td><?= e($r['mobile']) ?></td>
 <td><?= e($r['timing']) ?></td><td><?= e($r['course']) ?></td>
 <td><?= $due > 0 ? '<span class="due">Due ' . number_format($due) . '</span>' : '<span class="ok">Paid</span>' ?></td>
 <td><span class="tag"><?= e($r['status']) ?></span></td>
 <td><a href="students_edit.php?id=<?= $r['id'] ?>">Edit</a><a href="fees.php?reg=<?= urlencode($r['reg_no']) ?>">Fee</a>
  <?php if ($r['status'] === 'Pending'): ?><a href="?act=verify&id=<?= $r['id'] ?>&k=<?= $k ?>">Verify</a><?php endif; ?>
  <a href="certificates.php?reg=<?= urlencode($r['reg_no']) ?>">Certificate</a>
  <a style="color:#c0392b" href="?act=delete&id=<?= $r['id'] ?>&k=<?= $k ?>" onclick="return confirm('Student aur uski fee record delete ho jayegi. Pakka?')">Delete</a></td></tr>
<?php endforeach; ?></table></form>
<p><?php if ($page > 1): ?><a class="btn grey" href="?<?= e(http_build_query($qs + ['page' => $page - 1])) ?>">Pichla</a><?php endif;
 if ($page * $per < $total): ?><a class="btn grey" href="?<?= e(http_build_query($qs + ['page' => $page + 1])) ?>">Agla</a><?php endif; ?></p>
<?php admin_footer();
