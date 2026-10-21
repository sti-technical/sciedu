<?php require 'inc/auth.php'; require 'inc/layout.php'; require 'inc/crud.php';
$eid = (int)($_GET['exam_id'] ?? 0);
$st = $pdo->prepare('SELECT title FROM exams WHERE id=?'); $st->execute([$eid]); $title = $st->fetchColumn();
if (!$title) go('exams.php');
admin_header('Questions: ' . $title); ?>
<p><a class="btn grey" href="exams.php">Wapas exams</a></p>
<?php crud(['table' => 'questions', 'fixed' => ['exam_id' => $eid], 'fields' => [
  'question' => ['label' => 'Question', 'type' => 'textarea', 'req' => 1],
  'option_a' => ['label' => 'Option A', 'type' => 'text', 'req' => 1], 'option_b' => ['label' => 'Option B', 'type' => 'text', 'req' => 1],
  'option_c' => ['label' => 'Option C', 'type' => 'text', 'req' => 1], 'option_d' => ['label' => 'Option D', 'type' => 'text', 'req' => 1],
  'correct_option' => ['label' => 'Sahi jawab', 'type' => 'select', 'options' => ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D']],
  'marks' => ['label' => 'Marks', 'type' => 'number', 'default' => 1]],
  'cols' => ['question' => 'Question', 'correct_option' => 'Sahi', 'marks' => 'Marks']]);
admin_footer();
