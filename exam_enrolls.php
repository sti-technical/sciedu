<?php require 'inc/auth.php'; require 'inc/layout.php'; require 'inc/crud.php';
admin_header('Exam enroll record');
crud(['table' => 'exam_enrolls', 'sql' => 'SELECT t.id, s.reg_no, s.name, e.title exam, t.enrolled_on FROM exam_enrolls t JOIN students s ON s.id=t.student_id JOIN exams e ON e.id=t.exam_id',
      'cols' => ['reg_no' => 'Reg no.', 'name' => 'Naam', 'exam' => 'Exam', 'enrolled_on' => 'Enroll date']]);
admin_footer();
