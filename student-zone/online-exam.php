<?php require_once '../includes/student.php';
$stu = student_required();
$eid = (int)($_GET['exam'] ?? 0);
$self = 'online-exam.php?exam=' . $eid;

$exam = null; $result = null; $attempt = null; $notice = '';
if ($eid) {
    $st = $pdo->prepare('SELECT e.*, (e.exam_date IS NOT NULL AND e.exam_date > CURDATE()) AS not_yet, (SELECT COUNT(*) FROM questions q WHERE q.exam_id=e.id) AS qcount FROM exams e WHERE e.id=? AND e.is_active=1 AND e.course_id=?');
    $st->execute([$eid, (int)$stu['course_id']]); $exam = $st->fetch() ?: null;
}
if ($exam) {
    $st = $pdo->prepare('SELECT * FROM exam_results WHERE student_id=? AND exam_id=? ORDER BY id DESC LIMIT 1');
    $st->execute([$stu['id'], $eid]); $result = $st->fetch() ?: null;
    if (!$result) {
        $st = $pdo->prepare('SELECT a.*, TIMESTAMPDIFF(SECOND, NOW(), DATE_ADD(a.started_at, INTERVAL ' . (int)$exam['duration_minutes'] . ' MINUTE)) AS left_s FROM exam_attempts a WHERE a.student_id=? AND a.exam_id=?');
        $st->execute([$stu['id'], $eid]); $attempt = $st->fetch() ?: null;
        if ($attempt && $attempt['finished_at'] !== null) {   // result delete hua to dobara mauka
            $pdo->prepare('DELETE FROM exam_attempts WHERE id=?')->execute([$attempt['id']]); $attempt = null;
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_scsrf($_POST['k'] ?? '');
        $do = $_POST['do'] ?? '';
        if ($do === 'start' && !$result && !$attempt && !$exam['not_yet'] && $exam['qcount'] > 0) {
            try {
                $pdo->prepare('INSERT INTO exam_attempts (student_id, exam_id) VALUES (?,?)')->execute([$stu['id'], $eid]);
                $pdo->prepare('INSERT IGNORE INTO exam_enrolls (student_id, exam_id, admit_card_no) VALUES (?,?,?)')->execute([$stu['id'], $eid, admit_no($stu, $eid)]);
            } catch (Throwable $ex) {}
            header('Location: ' . $self); exit;
        }
        if (($do === 'save' || $do === 'submit') && $attempt && !$result) {
            $ans = clean_answers($_POST['q'] ?? []);
            if ($do === 'save') {
                if ((int)$attempt['left_s'] > 0) $pdo->prepare('UPDATE exam_attempts SET answers=? WHERE id=?')->execute([json_encode($ans), $attempt['id']]);
                exit('ok');
            }
            if ((int)$attempt['left_s'] < -60) $ans = clean_answers(json_decode((string)$attempt['answers'], true));   // der se aaya: sirf save hue jawab
            finish_exam($pdo, $stu, $exam, $attempt['id'], $ans);
            header('Location: ' . $self); exit;
        }
    }
    if ($attempt && (int)$attempt['left_s'] <= 0) {   // time khatam: save hue jawab se result
        finish_exam($pdo, $stu, $exam, $attempt['id'], clean_answers(json_decode((string)$attempt['answers'], true)));
        header('Location: ' . $self); exit;
    }
} elseif ($eid) { $notice = 'Ye exam aapke course ka nahi hai ya abhi band hai.'; }

$pageTitle = 'Online exam'; include '../includes/header.php'; ?>
<div class="wrap">
<?php if ($exam && $attempt): // ---------- EXAM CHAL RAHA HAI
    $qs = $pdo->prepare('SELECT id, question, option_a, option_b, option_c, option_d, marks FROM questions WHERE exam_id=? ORDER BY id');
    $qs->execute([$eid]); $qs = $qs->fetchAll();
    mt_srand((int)$attempt['id']); shuffle($qs); mt_srand();
    $saved = clean_answers(json_decode((string)$attempt['answers'], true)); ?>
<div class="timer"><span><?= e($exam['title']) ?></span><span>Bacha hua time: <b id="timer">--:--</b></span></div>
<form method="post" id="examform"><input type="hidden" name="k" value="<?= scsrf() ?>"><input type="hidden" name="do" value="submit">
<?php foreach ($qs as $n => $q): ?><div class="q"><p><?= $n + 1 ?>. <?= e($q['question']) ?> <small>(<?= (int)$q['marks'] ?> mark)</small></p>
<?php foreach (['A' => 'option_a', 'B' => 'option_b', 'C' => 'option_c', 'D' => 'option_d'] as $L => $col): ?>
<label><input type="radio" name="q[<?= (int)$q['id'] ?>]" value="<?= $L ?>" <?= ($saved[$q['id']] ?? '') === $L ? 'checked' : '' ?>> <span><?= $L ?>. <?= e($q[$col]) ?></span></label>
<?php endforeach; ?></div><?php endforeach; ?>
<p><button type="submit" onclick="return confirm('Exam submit karein? Submit ke baad jawab badal nahi sakte.')">Exam submit karein</button></p></form>
<script>
(function () {
  var end = Date.now() + <?= max(0, (int)$attempt['left_s']) ?> * 1000, form = document.getElementById('examform'), box = document.getElementById('timer'), sent = false, t;
  function fmt(s) { var m = Math.floor(s / 60), x = s % 60; return (m < 10 ? '0' : '') + m + ':' + (x < 10 ? '0' : '') + x; }
  function tick() {
    var left = Math.round((end - Date.now()) / 1000);
    if (left <= 0) { box.textContent = '00:00'; if (!sent) { sent = true; form.submit(); } return; }
    box.textContent = fmt(left); setTimeout(tick, 500);
  }
  tick();
  function save() { var fd = new FormData(form); fd.set('do', 'save'); fetch(location.href, {method: 'POST', body: fd, credentials: 'same-origin'}).catch(function () {}); }
  form.addEventListener('change', function () { clearTimeout(t); t = setTimeout(save, 500); });
  form.addEventListener('submit', function () { sent = true; });
})();
</script>

<?php elseif ($exam && $result): // ---------- RESULT
    $pct = $result['total'] > 0 ? round($result['score'] * 100 / $result['total'], 2) : 0; ?>
<h1>Exam result</h1>
<div class="who"><span><b><?= e($stu['name']) ?></b> (<?= e($stu['reg_no']) ?>)</span><a href="logout.php">Logout</a></div>
<div class="result"><dl><dt>Exam</dt><dd><?= e($exam['title']) ?></dd>
<dt>Aapke number</dt><dd class="score"><?= (int)$result['score'] ?> / <?= (int)$result['total'] ?> <small>(<?= e($pct) ?>%)</small></dd>
<dt>Result</dt><dd class="score <?= $result['result'] === 'Pass' ? 'pass' : 'fail' ?>"><?= e($result['result'] === 'Pass' ? 'PASS' : 'FAIL') ?></dd></dl></div>
<p><a class="btn" href="online-exam.php">Saare exams</a></p>

<?php elseif ($exam): // ---------- SHURU KARNE SE PEHLE ?>
<h1><?= e($exam['title']) ?></h1>
<div class="who"><span><b><?= e($stu['name']) ?></b> (<?= e($stu['reg_no']) ?>)</span><a href="logout.php">Logout</a></div>
<ul><li>Time: <b><?= (int)$exam['duration_minutes'] ?> minute</b>. Start dabate hi timer chalu ho jayega.</li>
<li>Total questions: <b><?= (int)$exam['qcount'] ?></b></li><li>Har question ka sirf ek sahi jawab hai.</li>
<li>Time khatam hote hi exam apne aap submit ho jayega. Exam sirf <b>ek baar</b> de sakte hain.</li>
<li>Exam ke beech page band na karein. Net chala jaye to wapas isi page par aayein, bacha hua time chalta rahega.</li></ul>
<?php if ($exam['not_yet']): ?><div class="msg err">Ye exam <?= e($exam['exam_date']) ?> se shuru hoga.</div>
<?php elseif (!$exam['qcount']): ?><div class="msg err">Is exam me abhi questions nahi daale gaye hain.</div>
<?php else: ?><form method="post"><input type="hidden" name="k" value="<?= scsrf() ?>"><input type="hidden" name="do" value="start"><button type="submit">Exam shuru karein</button></form><?php endif; ?>
<p><a href="online-exam.php">Wapas</a></p>

<?php else: // ---------- EXAM LIST
    $st = $pdo->prepare('SELECT e.*, (SELECT r.result FROM exam_results r WHERE r.student_id=? AND r.exam_id=e.id ORDER BY r.id DESC LIMIT 1) AS res, (SELECT COUNT(*) FROM exam_attempts a WHERE a.student_id=? AND a.exam_id=e.id AND a.finished_at IS NULL) AS running FROM exams e WHERE e.is_active=1 AND e.course_id=? ORDER BY e.id DESC');
    $st->execute([$stu['id'], $stu['id'], (int)$stu['course_id']]); $exams = $st->fetchAll(); ?>
<h1>Online exam</h1>
<div class="who"><span>Namaste, <b><?= e($stu['name']) ?></b> (<?= e($stu['reg_no']) ?>) &mdash; <?= e($stu['course_name']) ?></span><a href="logout.php">Logout</a></div>
<?php if ($notice): ?><div class="msg err"><?= e($notice) ?></div><?php endif; ?>
<?php if (!$exams): ?><p>Aapke course ka abhi koi exam open nahi hai.</p><?php else: ?>
<table class="t"><tr><th>Exam</th><th>Date</th><th>Time</th><th>Status</th><th></th></tr>
<?php foreach ($exams as $x): ?><tr><td><?= e($x['title']) ?></td><td><?= e($x['exam_date'] ?: '-') ?></td><td><?= (int)$x['duration_minutes'] ?> min</td>
<td><?= $x['res'] ? e($x['res']) : ($x['running'] ? 'Chal raha hai' : 'Baaki') ?></td>
<td><a class="btn" href="online-exam.php?exam=<?= (int)$x['id'] ?>"><?= $x['res'] ? 'Result dekhein' : ($x['running'] ? 'Wapas jayein' : 'Exam dein') ?></a></td></tr><?php endforeach; ?></table><?php endif; ?>
<?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
