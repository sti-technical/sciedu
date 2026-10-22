<?php require 'inc/auth.php'; require 'inc/layout.php';
$id = (int)($_GET['id'] ?? 0); $s = [];
if ($id) { $st = $pdo->prepare('SELECT * FROM students WHERE id=?'); $st->execute([$id]); $s = $st->fetch() ?: []; if (!$s) go('students.php'); }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf($_POST['k'] ?? '');
    $name = trim($_POST['name'] ?? ''); $mobile = trim($_POST['mobile'] ?? '');
    if ($name === '' || !preg_match('/^[0-9]{10}$/', $mobile)) $err = 'Naam aur 10 digit mobile zaroori hai.';
    $photo = upload_image('photo', __DIR__ . '/../uploads/students');
    if ($photo === false) $err = 'Photo sirf JPG/PNG/WEBP aur 2 MB se chhoti honi chahiye.';
    if (!$err) {
        $v = [$name, trim($_POST['father_name'] ?? ''), $_POST['dob'] ?: null, $_POST['doj'] ?: null, $_POST['gender'] ?? null, $mobile, trim($_POST['email'] ?? ''),
              trim($_POST['address'] ?? ''), (int)$_POST['course_id'] ?: null, (int)$_POST['batch_id'] ?: null, $_POST['status']];
        if ($id) {
            $pdo->prepare('UPDATE students SET name=?,father_name=?,dob=?,doj=?,gender=?,mobile=?,email=?,address=?,course_id=?,batch_id=?,status=? WHERE id=?')->execute([...$v, $id]);
            if ($photo) { if ($s['photo']) @unlink(__DIR__ . '/../uploads/students/' . basename($s['photo'])); $pdo->prepare('UPDATE students SET photo=? WHERE id=?')->execute([$photo, $id]); }
        } else {
            $pdo->prepare('INSERT INTO students (name,father_name,dob,doj,gender,mobile,email,address,course_id,batch_id,status,reg_no,photo) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)')->execute([...$v, uniqid('T'), $photo]);
            $nid = $pdo->lastInsertId();
            $pdo->prepare('UPDATE students SET reg_no=? WHERE id=?')->execute([setting('reg_prefix', 'SCI') . date('Y') . str_pad($nid, 3, '0', STR_PAD_LEFT), $nid]);
        }
        flash('Student save ho gaya.'); go('students.php');
    }
    $s = $_POST + $s;
}
$courses = $pdo->query('SELECT id,name FROM courses ORDER BY name')->fetchAll(); $batches = $pdo->query('SELECT id,timing FROM batches')->fetchAll();
admin_header($id ? 'Student edit: ' . ($s['reg_no'] ?? '') : 'Naya student'); ?>
<?php if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="card"><input type="hidden" name="k" value="<?= csrf() ?>"><div class="row">
 <div><label>Naam</label><input name="name" value="<?= e($s['name'] ?? '') ?>" required></div>
 <div><label>Father ka naam</label><input name="father_name" value="<?= e($s['father_name'] ?? '') ?>"></div>
 <div><label>DOB</label><input type="date" name="dob" value="<?= e($s['dob'] ?? '') ?>"></div>
 <div><label>Date of joining</label><input type="date" name="doj" value="<?= e($s['doj'] ?? date('Y-m-d')) ?>"></div>
 <div><label>Gender</label><select name="gender"><?php foreach (['Male', 'Female', 'Other'] as $g): ?><option <?= ($s['gender'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option><?php endforeach; ?></select></div>
 <div><label>Mobile</label><input name="mobile" maxlength="10" value="<?= e($s['mobile'] ?? '') ?>" required></div>
 <div><label>Email</label><input type="email" name="email" value="<?= e($s['email'] ?? '') ?>"></div>
 <div><label>Course</label><select name="course_id"><option value="0">-</option><?php foreach ($courses as $c): ?><option value="<?= $c['id'] ?>" <?= ($s['course_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
 <div><label>Batch</label><select name="batch_id"><option value="0">-</option><?php foreach ($batches as $b): ?><option value="<?= $b['id'] ?>" <?= ($s['batch_id'] ?? 0) == $b['id'] ? 'selected' : '' ?>><?= e($b['timing']) ?></option><?php endforeach; ?></select></div>
 <div><label>Status</label><select name="status"><?php foreach (['Pending', 'Verified', 'Course Completed', 'Inactive'] as $x): ?><option <?= ($s['status'] ?? 'Verified') === $x ? 'selected' : '' ?>><?= $x ?></option><?php endforeach; ?></select></div>
 <div><label>Photo (JPG/PNG, max 2 MB)</label><input type="file" name="photo" accept="image/jpeg,image/png,image/webp"></div>
</div><label>Address</label><textarea name="address" rows="2"><?= e($s['address'] ?? '') ?></textarea>
<p><button>Save</button><a class="btn grey" href="students.php">Cancel</a></p></form>
<?php admin_footer();
