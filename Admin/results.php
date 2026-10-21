<?php require 'inc/auth.php'; require 'inc/layout.php'; require 'inc/crud.php';
admin_header('Exam results');
crud(['table' => 'exam_results', 'sql' => 'SELECT t.id, s.reg_no, s.name, e.title exam, t.score, t.total, t.result, t.taken_on FROM exam_results t JOIN students s ON s.id=t.student_id JOIN exams e ON e.id=t.exam_id',
      'cols' => ['reg_no' => 'Reg no.', 'name' => 'Naam', 'exam' => 'Exam', 'score' => 'Score', 'total' => 'Total', 'result' => 'Result', 'taken_on' => 'Date']]);
admin_footer();
