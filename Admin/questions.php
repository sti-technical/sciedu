<?php require 'inc/auth.php'; require 'inc/layout.php'; require 'inc/crud.php'; require 'inc/xlsx.php';
$eid = (int)($_GET['exam_id'] ?? 0);
$st = $pdo->prepare('SELECT title FROM exams WHERE id=?'); $st->execute([$eid]); $title = $st->fetchColumn();
if (!$title) go('exams.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'import') {
    check_csrf($_POST['k'] ?? '');
    $f = $_FILES['sheet'] ?? null;
    $ext = $f ? strtolower(pathinfo($f['name'], PATHINFO_EXTENSION)) : '';
    if (!$f || $f['error'] !== UPLOAD_ERR_OK || $f['size'] > 3 * 1024 * 1024) flash('File chunein (Excel .xlsx ya .csv, 3 MB se chhoti).');
    elseif (!in_array($ext, ['xlsx', 'csv'], true)) flash('Sirf .xlsx ya .csv file chalegi.');
    else {
        $rows = $ext === 'xlsx' ? read_xlsx($f['tmp_name']) : read_csv_rows($f['tmp_name']);
        if ($rows === null) flash('Server par Excel padhne ka support nahi hai. Excel me File > Save As > CSV UTF-8 karke .csv upload karein.');
        elseif (!$rows) flash('File khali hai ya padhi nahi gayi.');
        else {
            [$ok, $skip] = import_questions($pdo, $eid, $rows, !empty($_POST['replace']));
            flash($ok . ' question jud gaye.' . ($skip ? ' Chhoot gaye: ' . implode('; ', array_slice($skip, 0, 8)) . (count($skip) > 8 ? ' ...' : '') : ''));
        }
    }
    go('questions.php?exam_id=' . $eid);
}
admin_header('Questions: ' . $title); ?>
<p><a class="btn grey" href="exams.php">Wapas exams</a></p>
<form method="post" enctype="multipart/form-data" class="card"><h3>Excel se questions upload karein</h3>
<p>Columns is order me hone chahiye: <b>Question, Option A, B, C, D, Sahi jawab (A/B/C/D), Marks</b>. Pehli line heading ki ho sakti hai. <a href="questions_sample.php">Sample file download karein</a></p>
<input type="hidden" name="k" value="<?= csrf() ?>"><input type="hidden" name="do" value="import">
<input type="file" name="sheet" accept=".xlsx,.csv" required>
<label style="font-weight:400"><input type="checkbox" name="replace" value="1" style="width:auto"> Purane saare questions hata kar naye daalein</label>
<p><button>Upload karein</button></p></form>
<?php crud(['table' => 'questions', 'fixed' => ['exam_id' => $eid], 'fields' => [
  'question' => ['label' => 'Question', 'type' => 'textarea', 'req' => 1],
  'option_a' => ['label' => 'Option A', 'type' => 'text', 'req' => 1], 'option_b' => ['label' => 'Option B', 'type' => 'text', 'req' => 1],
  'option_c' => ['label' => 'Option C', 'type' => 'text', 'req' => 1], 'option_d' => ['label' => 'Option D', 'type' => 'text', 'req' => 1],
  'correct_option' => ['label' => 'Sahi jawab', 'type' => 'select', 'options' => ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D']],
  'marks' => ['label' => 'Marks', 'type' => 'number', 'default' => 1]],
  'cols' => ['question' => 'Question', 'correct_option' => 'Sahi', 'marks' => 'Marks']]);
admin_footer();
