<?php
ob_start(); // redirect (header) kaam kare, chahe page ka kuch hissa pehle print ho chuka ho
require_once __DIR__ . '/../../includes/config.php';
if (empty($_SESSION['admin'])) { header('Location: ' . BASE . '/Admin/login.php'); exit; }

function csrf() { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function check_csrf($t) {
    if (!hash_equals($_SESSION['csrf'] ?? '', (string)$t)) { http_response_code(403); die('Invalid request. Page refresh karke dobara try karein.'); }
}
function flash($m = null) {
    if ($m !== null) { $_SESSION['flash'] = $m; return ''; }
    $x = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']); return $x;
}
function go($url) { header('Location: ' . $url); exit; }

// Image upload: file ka naam return karta hai, na ho to null, galat ho to false
function upload_image($field, $dir) {
    if (empty($_FILES[$field]['name'])) return null;
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > 2 * 1024 * 1024) return false;
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
    if (!$ext) return false;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    return move_uploaded_file($f['tmp_name'], $dir . '/' . $name) ? $name : false;
}
