<?php require_once __DIR__ . '/config.php';
$pageTitle = $pageTitle ?? 'Home'; ?>
<!DOCTYPE html>
<html lang="hi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | <?= e(setting('institute_name', 'Computer Institute')) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;800&family=Mukta:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE ?>/assets/style.css">
</head>
<body>
<div class="topbar">
  <span><?= e(setting('phone', 'Phone: add in settings')) ?></span>
  <span><?= e(setting('email', 'Email: add in settings')) ?></span>
  <a href="<?= BASE ?>/student-zone/login.php">Student login</a>
</div>
<header class="site-header">
  <a class="brand" href="<?= BASE ?>/index.php"><?= e(setting('institute_name', 'Your Institute Name')) ?></a>
  <input type="checkbox" id="navtoggle" class="navtoggle" aria-label="Menu">
  <label for="navtoggle" class="burger">Menu</label>
  <nav class="nav">
    <a href="<?= BASE ?>/index.php">Home</a>
    <a href="<?= BASE ?>/about.php">About us</a>
    <a href="<?= BASE ?>/director.php">Director message</a>
    <div class="dd"><a href="<?= BASE ?>/courses.php">Courses</a>
      <div class="dd-menu">
        <a href="<?= BASE ?>/courses.php#computer">Computer programs</a>
        <a href="<?= BASE ?>/courses.php#vocational-program">Vocational programs</a>
        <a href="<?= BASE ?>/courses.php#vocational-training">Vocational training</a>
      </div></div>
    <div class="dd"><a href="#" tabindex="0">Student zone</a>
      <div class="dd-menu">
        <a href="<?= BASE ?>/student-zone/register.php">Online registration</a>
        <a href="<?= BASE ?>/student-zone/verify-registration.php">Verify registration</a>
        <a href="<?= BASE ?>/student-zone/verify-certificate.php">Verify certificate</a>
        <a href="<?= BASE ?>/student-zone/admit-card.php">Download admit card</a>
        <a href="<?= BASE ?>/student-zone/exam-enroll.php">Exam enroll form</a>
        <a href="<?= BASE ?>/student-zone/online-exam.php">Online exam</a>
      </div></div>
    <a href="<?= BASE ?>/gallery.php">Gallery</a>
    <a href="<?= BASE ?>/contact.php">Contact</a>
  </nav>
</header>
<main>
