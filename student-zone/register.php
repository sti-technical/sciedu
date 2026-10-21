<?php $pageTitle = 'Online registration'; include '../includes/header.php';
$courses = $pdo->query('SELECT id,name FROM courses WHERE is_active=1 ORDER BY name')->fetchAll();
$batches = $pdo->query('SELECT id,timing FROM batches WHERE is_active=1')->fetchAll();
$msg = ''; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? ''); $mobile = trim($_POST['mobile'] ?? '');
    $dob = $_POST['dob'] ?? ''; $course = (int)($_POST['course_id'] ?? 0); $batch = (int)($_POST['batch_id'] ?? 0);
    if ($name === '' || !preg_match('/^[0-9]{10}$/', $mobile) || $dob === '' || !$course || !$batch) {
        $err = 'Naam, DOB, 10 digit mobile, course aur batch zaroori hain.';
    } else {
        $pdo->beginTransaction();
        $pdo->prepare('INSERT INTO students (reg_no,name,father_name,dob,doj,gender,mobile,email,address,course_id,batch_id,status)
                       VALUES (?,?,?,?,CURDATE(),?,?,?,?,?,?,"Pending")')
            ->execute([uniqid('T'), $name, trim($_POST['father_name'] ?? ''), $dob, $_POST['gender'] ?? null,
                       $mobile, trim($_POST['email'] ?? ''), trim($_POST['address'] ?? ''), $course, $batch]);
        $id = $pdo->lastInsertId();
        $reg = setting('reg_prefix', 'SCI') . date('Y') . str_pad($id, 3, '0', STR_PAD_LEFT);
        $pdo->prepare('UPDATE students SET reg_no=? WHERE id=?')->execute([$reg, $id]);
        $pdo->commit();
        $msg = "Registration ho gaya. Aapka Registration No. hai: $reg (isse sambhal kar rakhein)";
    }
}
?>
<div class="wrap"><h1>Online registration</h1>
<?php if ($msg): ?><div class="msg ok"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="msg err"><?= e($err) ?></div><?php endif; ?>
<form method="post">
 <div class="form-grid">
  <div><label for="name">Student ka naam</label><input id="name" name="name" required></div>
  <div><label for="father_name">Father ka naam</label><input id="father_name" name="father_name"></div>
  <div><label for="dob">Date of birth</label><input id="dob" name="dob" type="date" required></div>
  <div><label for="gender">Gender</label><select id="gender" name="gender"><option>Male</option><option>Female</option><option>Other</option></select></div>
  <div><label for="mobile">Mobile (10 digit)</label><input id="mobile" name="mobile" maxlength="10" required></div>
  <div><label for="email">Email</label><input id="email" name="email" type="email"></div>
  <div><label for="course_id">Course</label><select id="course_id" name="course_id" required><option value="">Chunein</option>
    <?php foreach ($courses as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
  <div><label for="batch_id">Batch</label><select id="batch_id" name="batch_id" required><option value="">Chunein</option>
    <?php foreach ($batches as $b): ?><option value="<?= $b['id'] ?>"><?= e($b['timing']) ?></option><?php endforeach; ?></select></div>
 </div>
 <label for="address">Address</label><textarea id="address" name="address"></textarea>
 <p><button type="submit">Registration karein</button></p>
</form></div>
<?php include '../includes/footer.php'; ?>
