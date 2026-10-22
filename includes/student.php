<?php
// Student login aur online exam ke common functions
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/site.php';

function student() {
    global $pdo; static $s = false;
    if ($s !== false) return $s;
    $s = null;
    if (!empty($_SESSION['student_id'])) {
        $st = $pdo->prepare("SELECT s.*, c.name AS course_name FROM students s LEFT JOIN courses c ON c.id=s.course_id WHERE s.id=? AND s.status<>'Inactive'");
        $st->execute([(int)$_SESSION['student_id']]);
        $s = $st->fetch() ?: null;
    }
    return $s;
}
function student_required() {
    $s = student();
    if (!$s) { header('Location: ' . BASE . '/student-zone/login.php'); exit; }
    return $s;
}
function scsrf() { if (empty($_SESSION['scsrf'])) $_SESSION['scsrf'] = bin2hex(random_bytes(16)); return $_SESSION['scsrf']; }
function check_scsrf($t) {
    if (!hash_equals($_SESSION['scsrf'] ?? '', (string)$t)) { http_response_code(403); die('Invalid request. Page refresh karke dobara try karein.'); }
}
function admit_no($stu, $examId) { return 'ADM' . str_pad((string)(int)$examId, 3, '0', STR_PAD_LEFT) . '-' . $stu['reg_no']; }

// Date ko YYYY-MM-DD me badalta hai (dd/mm/yyyy bhi chalega)
function parse_dob($s) {
    $s = trim((string)$s);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) return $s;
    if (preg_match('#^(\d{1,2})[/\-.](\d{1,2})[/\-.](\d{4})$#', $s, $m)) return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
    return '';
}
// Aaye hue jawab saaf karta hai: sirf {question_id => 'A'..'D'}
function clean_answers($a) {
    $o = [];
    if (is_array($a)) foreach ($a as $qid => $v) {
        $v = strtoupper(trim((string)$v));
        if ((int)$qid > 0 && in_array($v, ['A', 'B', 'C', 'D'], true)) $o[(int)$qid] = $v;
    }
    return $o;
}
// Exam check karke result save karta hai. Result array return karta hai
function finish_exam($pdo, $stu, $exam, $attemptId, $answers) {
    $qs = $pdo->prepare('SELECT id, correct_option, marks FROM questions WHERE exam_id=?');
    $qs->execute([$exam['id']]);
    $score = 0; $total = 0;
    foreach ($qs->fetchAll() as $q) {
        $total += (int)$q['marks'];
        if (($answers[$q['id']] ?? '') === $q['correct_option']) $score += (int)$q['marks'];
    }
    $need = ((int)$exam['total_marks'] > 0) ? ((float)$exam['pass_marks'] / (float)$exam['total_marks']) : 0.4;
    $res = ($total > 0 && $score + 1e-9 >= $need * $total) ? 'Pass' : 'Fail';
    $pdo->beginTransaction();
    try {
        $chk = $pdo->prepare('SELECT COUNT(*) FROM exam_results WHERE student_id=? AND exam_id=?');
        $chk->execute([$stu['id'], $exam['id']]);
        if (!(int)$chk->fetchColumn()) {
            $pdo->prepare('INSERT INTO exam_results (student_id, exam_id, score, total, result) VALUES (?,?,?,?,?)')->execute([$stu['id'], $exam['id'], $score, $total, $res]);
        }
        $pdo->prepare('UPDATE exam_attempts SET finished_at=NOW(), answers=? WHERE id=?')->execute([json_encode($answers), $attemptId]);
        $pdo->commit();
    } catch (Throwable $ex) { $pdo->rollBack(); throw $ex; }
    return ['score' => $score, 'total' => $total, 'result' => $res];
}
