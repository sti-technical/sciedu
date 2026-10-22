<?php
// Website ke common helper functions (logo, banner, WhatsApp/Call, gallery)
require_once __DIR__ . '/config.php';

// Kisi type (logo/banner/strip) ki saari media ids. Table na ho to khali list (site crash nahi hogi)
function media_ids($kind) {
    global $pdo;
    try {
        $st = $pdo->prepare('SELECT id FROM media WHERE kind=? ORDER BY sort_order, id');
        $st->execute([$kind]);
        return array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
    } catch (Throwable $ex) { return []; }
}
function media_url($id) { return BASE . '/media.php?id=' . (int)$id; }

// Image ko database me save karta hai. Id return karta hai, file na ho to null, galat ho to false
function save_media($field, $kind, $max = 2097152) {
    global $pdo;
    if (empty($_FILES[$field]['name'])) return null;
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > $max) return false;
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true)) return false;
    try {
        $pdo->prepare('INSERT INTO media (kind, filename, mime, data_b64) VALUES (?,?,?,?)')
            ->execute([$kind, mb_substr(basename($f['name']), 0, 150), $mime, base64_encode(file_get_contents($f['tmp_name']))]);
        return (int)$pdo->lastInsertId();
    } catch (Throwable $ex) { return false; }
}
function delete_media($id) {
    global $pdo;
    try { $pdo->prepare('DELETE FROM media WHERE id=?')->execute([(int)$id]); } catch (Throwable $ex) {}
}

// "9695601648" ya "+91 96956-01648, 0522..." se pehla 10 digit ka number nikalta hai
function first_phone($s) {
    if (preg_match('/\+?\d[\d\s\-]{8,}\d/', (string)$s, $m)) {
        $d = preg_replace('/\D/', '', $m[0]);
        if (strlen($d) >= 10) return substr($d, -10);
    }
    return '';
}

// Gallery ki photo ka address (nayi photo database me, purani uploads folder me)
function gallery_src($v) {
    if (strpos($v, 'media:') === 0) return media_url((int)substr($v, 6));
    return BASE . '/uploads/' . rawurlencode(basename($v));
}
function youtube_id($u) {
    return preg_match('~(?:youtu\.be/|v=|embed/|shorts/)([A-Za-z0-9_-]{11})~', (string)$u, $m) ? $m[1] : '';
}
