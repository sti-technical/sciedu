<?php require 'inc/auth.php';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="questions_sample.csv"');
echo "\xEF\xBB\xBF";
$o = fopen('php://output', 'w');
fputcsv($o, ['Question', 'Option A', 'Option B', 'Option C', 'Option D', 'Correct (A/B/C/D)', 'Marks']);
fputcsv($o, ['CPU ka full form kya hai?', 'Central Processing Unit', 'Central Program Unit', 'Control Processing Unit', 'Computer Personal Unit', 'A', 1]);
fputcsv($o, ['Ctrl+C ka kaam kya hai?', 'Paste', 'Copy', 'Cut', 'Undo', 'B', 1]);
fclose($o);
