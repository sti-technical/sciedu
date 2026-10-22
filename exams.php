<?php require 'inc/auth.php'; require 'inc/layout.php'; require 'inc/crud.php';
$opts = []; foreach ($pdo->query('SELECT id,name FROM courses ORDER BY name') as $c) $opts[$c['id']] = $c['name'];
admin_header('Exams / Paper');
crud(['table' => 'exams', 'sql' => 'SELECT t.*, c.short_name course FROM exams t JOIN courses c ON c.id=t.course_id', 'fields' => [
  'course_id' => ['label' => 'Course', 'type' => 'select', 'options' => $opts], 'title' => ['label' => 'Exam / Paper ka naam', 'type' => 'text', 'req' => 1],
  'duration_minutes' => ['label' => 'Time (minutes)', 'type' => 'number', 'default' => 60], 'total_marks' => ['label' => 'Total marks', 'type' => 'number', 'default' => 100],
  'pass_marks' => ['label' => 'Pass marks', 'type' => 'number', 'default' => 40], 'exam_date' => ['label' => 'Exam date', 'type' => 'date'],
  'is_active' => ['label' => 'Active?', 'type' => 'select', 'options' => [1 => 'Haan', 0 => 'Nahi']]],
  'cols' => ['title' => 'Exam', 'course' => 'Course', 'duration_minutes' => 'Minutes', 'total_marks' => 'Total', 'pass_marks' => 'Pass', 'exam_date' => 'Date'],
  'extra' => fn($r) => '<a href="questions.php?exam_id=' . (int)$r['id'] . '">Questions</a>']);
admin_footer();
