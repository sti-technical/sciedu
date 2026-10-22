<?php
// Database me save hui image dikhata hai: /media.php?id=5
session_cache_limiter('');
require_once __DIR__ . '/includes/config.php';
$id = (int)($_GET['id'] ?? 0);
try {
    $st = $pdo->prepare('SELECT mime, data_b64 FROM media WHERE id=?');
    $st->execute([$id]);
    $m = $st->fetch();
} catch (Throwable $ex) { $m = null; }
if (!$m) { http_response_code(404); exit('Not found'); }
session_write_close();
header('Content-Type: ' . $m['mime']);
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=604800');
echo base64_decode($m['data_b64']);
