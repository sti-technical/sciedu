<?php
// ====== SETTINGS (XAMPP default) ======
const DB_HOST = 'localhost';
const DB_NAME = 'institute_db';
const DB_USER = 'root';
const DB_PASS = '';          // XAMPP me password khali hota hai
const BASE    = '';  // Site root par hai (localhost/). Agar kisi subfolder me rakhein to yahan '/foldername' likhein

session_start();
try {
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $ex) {
    die('Database se connect nahi hua. XAMPP me MySQL start hai? schema.sql import kiya?');
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
