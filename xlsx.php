<?php
// Excel (.xlsx) aur CSV se rows padhne ke helper (koi bahari library nahi chahiye)
function read_xlsx($path) {
    if (!class_exists('ZipArchive')) return null;
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) return [];
    $shared = [];
    $x = $zip->getFromName('xl/sharedStrings.xml');
    if ($x !== false) {
        $sx = @simplexml_load_string(preg_replace('/ xmlns="[^"]+"/', '', $x, 1));
        if ($sx) foreach ($sx->si as $si) {
            $t = isset($si->t) ? (string)$si->t : '';
            foreach ($si->r as $r) $t .= (string)$r->t;
            $shared[] = $t;
        }
    }
    $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
    if ($sheet === false) {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            if (preg_match('#^xl/worksheets/sheet\d+\.xml$#', $zip->getNameIndex($i))) { $sheet = $zip->getFromIndex($i); break; }
        }
    }
    $zip->close();
    if (!$sheet) return [];
    $sx = @simplexml_load_string(preg_replace('/ xmlns="[^"]+"/', '', $sheet, 1));
    if (!$sx || !isset($sx->sheetData)) return [];
    $rows = [];
    foreach ($sx->sheetData->row as $row) {
        $r = [];
        foreach ($row->c as $c) {
            $col = count($r);
            if (preg_match('/^([A-Z]+)/', (string)$c['r'], $m)) {
                $col = 0; foreach (str_split($m[1]) as $ch) $col = $col * 26 + (ord($ch) - 64);
                $col--;
            }
            $t = (string)$c['t'];
            if ($t === 's') $v = $shared[(int)$c->v] ?? '';
            elseif ($t === 'inlineStr') { $v = isset($c->is->t) ? (string)$c->is->t : ''; foreach ($c->is->r as $rr) $v .= (string)$rr->t; }
            else $v = (string)$c->v;
            $r[$col] = trim($v);
        }
        if ($r) { $line = []; for ($i = 0, $max = max(array_keys($r)); $i <= $max; $i++) $line[] = $r[$i] ?? ''; $rows[] = $line; }
    }
    return $rows;
}
function read_csv_rows($path) {
    $txt = file_get_contents($path);
    if ($txt === false) return [];
    if (substr($txt, 0, 3) === "\xEF\xBB\xBF") $txt = substr($txt, 3);
    if (!mb_check_encoding($txt, 'UTF-8')) $txt = mb_convert_encoding($txt, 'UTF-8', 'Windows-1252');
    $first = strtok($txt, "\n");
    $d = substr_count($first, ';') > substr_count($first, ',') ? ';' : ',';
    $h = fopen('php://memory', 'r+'); fwrite($h, $txt); rewind($h);
    $rows = [];
    while (($r = fgetcsv($h, 0, $d)) !== false) { if ($r === [null]) continue; $rows[] = array_map(fn($v) => trim((string)$v), $r); }
    fclose($h);
    return $rows;
}
// Rows ko questions table me daalta hai. [kitne_gaye, [skip hui lines ke karan]] return karta hai
function import_questions($pdo, $eid, $rows, $replace) {
    $ok = 0; $skip = [];
    $pdo->beginTransaction();
    try {
        if ($replace) $pdo->prepare('DELETE FROM questions WHERE exam_id=?')->execute([$eid]);
        $ins = $pdo->prepare('INSERT INTO questions (exam_id,question,option_a,option_b,option_c,option_d,correct_option,marks) VALUES (?,?,?,?,?,?,?,?)');
        foreach (array_slice($rows, 0, 2001) as $i => $r) {
            $r = array_pad($r, 7, '');
            if (implode('', $r) === '') continue;
            $c = strtoupper(trim($r[5]));
            $c = ['1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D'][$c] ?? $c;
            if (!in_array($c, ['A', 'B', 'C', 'D'], true)) {
                if ($i === 0) continue;   // pehli line heading hoti hai
                $skip[] = 'Line ' . ($i + 1) . ': sahi jawab A/B/C/D nahi hai'; continue;
            }
            if ($r[0] === '' || $r[1] === '' || $r[2] === '' || $r[3] === '' || $r[4] === '') { $skip[] = 'Line ' . ($i + 1) . ': question ya option khali hai'; continue; }
            $m = is_numeric($r[6]) && $r[6] > 0 ? (int)round($r[6]) : 1;
            $ins->execute([$eid, $r[0], mb_substr($r[1], 0, 255), mb_substr($r[2], 0, 255), mb_substr($r[3], 0, 255), mb_substr($r[4], 0, 255), $c, $m]);
            $ok++;
        }
        $pdo->commit();
    } catch (Throwable $ex) { $pdo->rollBack(); return [0, ['Database error: questions save nahi hue']]; }
    return [$ok, $skip];
}
