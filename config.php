<?php
// ====== SETTINGS ======
// Online (Render) par ye values Environment Variables se aati hain.
// Apne computer (XAMPP) par ye default values use hoti hain.
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'institute_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('BASE', '');   // Site root par hai. Subfolder me rakhein to '/foldername' likhein

session_start();
try {
    $opts = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
    if (getenv('DB_SSL') === '1') $opts[PDO::MYSQL_ATTR_SSL_CA] = '/etc/ssl/certs/ca-certificates.crt'; // cloud database ke liye
    $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, $opts);
} catch (PDOException $ex) {
    error_log('DB connect error: ' . $ex->getMessage()); // asli wajah Render ke Logs me dikhegi (page par nahi)
    die('Database se connect nahi hua. Database ki settings check karein.');
}

// Text ko safe banata hai (hacking se bachne ke liye har output par use karein)
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// settings table se value nikalta hai
function setting($key, $default = '') {
    global $pdo; static $cache = null;
    if ($cache === null) {
        $cache = $pdo->query('SELECT setting_key, setting_value FROM settings')->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    return ($cache[$key] ?? '') !== '' ? $cache[$key] : $default;
}
