<?php require_once '../includes/student.php';
unset($_SESSION['student_id']);
session_regenerate_id(true);
header('Location: ' . BASE . '/student-zone/login.php');
