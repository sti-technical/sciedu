<?php require_once __DIR__ . '/config.php';
require_once __DIR__ . '/site.php';
$pageTitle = $pageTitle ?? 'Home';
$logoIds = media_ids('logo');
$stripIds = media_ids('strip');
$phoneShow = setting('phone', '');
$tel = first_phone($phoneShow);
$wa = first_phone(setting('whatsapp', '')) ?: $tel;
$social = ['facebook' => 'f', 'instagram' => 'ig', 'twitter' => 'X', 'linkedin' => 'in', 'youtube' => 'yt'];
?><!DOCTYPE html>
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
  <span id="clock"></span>
  <span>&#9993; <?= e(setting('email', 'Email: add in settings')) ?></span>
  <span>&#9742; <?= e($phoneShow !== '' ? $phoneShow : 'Phone: add in settings') ?></span>
  <span class="sp"></span>
  <a class="tb-btn blue" href="<?= BASE ?>/student-zone/login.php">Student Login</a>
  <a class="tb-btn cyan" href="<?= BASE ?>/student-zone/online-exam.php">Online Exam</a>
  <span class="social"><?php foreach ($social as $k => $lbl): $u = trim(setting($k, ''));
    if (preg_match('#^https?://#i', $u)): ?><a href="<?= e($u) ?>" target="_blank" rel="noopener" aria-label="<?= e($k) ?>"><?= e($lbl) ?></a><?php endif; endforeach; ?></span>
</div>
<div class="brandrow">
  <a href="<?= BASE ?>/index.php"><?php if ($logoIds): ?><img class="logo" src="<?= media_url($logoIds[0]) ?>" alt="<?= e(setting('institute_name', 'Logo')) ?>"><?php else: ?><span class="brand"><?= e(setting('institute_name', 'Your Institute Name')) ?></span><?php endif; ?></a>
  <?php if ($stripIds): ?><div class="strip"><?php foreach ($stripIds as $sid): ?><img src="<?= media_url($sid) ?>" alt=""><?php endforeach; ?></div><?php endif; ?>
</div>
<header class="navbar">
  <input type="checkbox" id="navtoggle" class="navtoggle" aria-label="Menu">
  <label for="navtoggle" class="burger">&#9776; Menu</label>
  <nav class="nav">
    <a href="<?= BASE ?>/index.php">Home</a>
    <a href="<?= BASE ?>/about.php">About Us</a>
    <a href="<?= BASE ?>/director.php">Director Message</a>
    <div class="dd"><a href="<?= BASE ?>/courses.php">Courses We Provide</a>
      <div class="dd-menu">
        <a href="<?= BASE ?>/courses.php#computer">Computer programs</a>
        <a href="<?= BASE ?>/courses.php#vocational-program">Vocational programs</a>
        <a href="<?= BASE ?>/courses.php#vocational-training">Vocational training</a>
      </div></div>
    <div class="dd"><a href="#" tabindex="0">Student Zone</a>
      <div class="dd-menu">
        <a href="<?= BASE ?>/student-zone/register.php">Online registration</a>
        <a href="<?= BASE ?>/student-zone/verify-registration.php">Verify registration</a>
        <a href="<?= BASE ?>/student-zone/verify-certificate.php">Verify certificate</a>
        <a href="<?= BASE ?>/student-zone/login.php">Student login</a>
        <a href="<?= BASE ?>/student-zone/admit-card.php">Download admit card</a>
        <a href="<?= BASE ?>/student-zone/exam-enroll.php">Exam enroll form</a>
        <a href="<?= BASE ?>/student-zone/online-exam.php">Online exam</a>
      </div></div>
    <a href="<?= BASE ?>/gallery.php?type=photo">Photo Gallery</a>
    <a href="<?= BASE ?>/gallery.php?type=video">Video Gallery</a>
    <a href="<?= BASE ?>/contact.php">Contact</a>
  </nav>
</header>
<main>
