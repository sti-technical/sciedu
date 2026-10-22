<?php require_once '../includes/student.php';
if (student()) { header('Location: ' . BASE . '/student-zone/online-exam.php'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_scsrf($_POST['k'] ?? '');
    $reg = trim($_POST['reg_no'] ?? ''); $dob = parse_dob($_POST['dob'] ?? '');
    $locked = false;
    try {
        $pdo->exec("DELETE FROM login_fails WHERE tried_at < (NOW() - INTERVAL 1 DAY)");
        $c = $pdo->prepare('SELECT COUNT(*) FROM login_fails WHERE reg_no=? AND tried_at > (NOW() - INTERVAL 15 MINUTE)');
        $c->execute([$reg]); $locked = (int)$c->fetchColumn() >= 5;
    } catch (Throwable $ex) {}
    if ($locked) $err = 'Bahut baar galat koshish hui. 15 minute baad dobara try karein.';
    elseif ($reg === '' || $dob === '') $err = 'Roll number aur date of birth dono bharein.';
    else {
        $st = $pdo->prepare("SELECT id FROM students WHERE reg_no=? AND dob=? AND status<>'Inactive'");
        $st->execute([$reg, $dob]); $id = $st->fetchColumn();
        if ($id) {
            session_regenerate_id(true);
            $_SESSION['student_id'] = (int)$id;
            try { $pdo->prepare('DELETE FROM login_fails WHERE reg_no=?')->execute([$reg]); } catch (Throwable $ex) {}
            header('Location: ' . BASE . '/student-zone/online-exam.php'); exit;
        }
        try { $pdo->prepare('INSERT INTO login_fails (reg_no, ip) VALUES (?,?)')->execute([mb_substr($reg, 0, 30), $_SERVER['REMOTE_ADDR'] ?? '']); } catch (Throwable $ex) {}
        $err = 'Roll number ya date of birth galat hai.';
    }
}
$pageTitle = 'Student login'; include '../includes/header.php'; ?>
<div class="wrap" style="max-width:520px"><h1>Student login</h1>
<?php if ($err): ?><div class="msg err"><?= e($err) ?></div><?php endif; ?>
<form method="post"><input type="hidden" name="k" value="<?= scsrf() ?>">
<label for="reg_no">Roll number / Registration number</label><input id="reg_no" name="reg_no" required autocomplete="username" value="<?= e($_POST['reg_no'] ?? '') ?>">
<label for="dob">Date of birth</label><input id="dob" name="dob" type="date" required>
<p><button type="submit">Login</button></p></form>
<p><small>Roll number wahi hai jo aapki registration slip / admit card par likha hai.</small></p></div>
<?php include '../includes/footer.php'; ?>
