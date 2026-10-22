<?php require 'inc/auth.php'; require 'inc/layout.php'; require 'inc/crud.php';
admin_header('Courses');
crud(['table' => 'courses', 'fields' => [
  'category' => ['label' => 'Category', 'type' => 'select', 'options' => ['Computer Program' => 'Computer Program', 'Vocational Program' => 'Vocational Program', 'Vocational Training' => 'Vocational Training']],
  'name' => ['label' => 'Course ka naam', 'type' => 'text', 'req' => 1], 'short_name' => ['label' => 'Short naam (ADCA)', 'type' => 'text'],
  'duration' => ['label' => 'Duration', 'type' => 'text'], 'fee' => ['label' => 'Fee (Rs.)', 'type' => 'number'],
  'description' => ['label' => 'Description', 'type' => 'textarea'], 'is_active' => ['label' => 'Active?', 'type' => 'select', 'options' => [1 => 'Haan', 0 => 'Nahi']]],
  'cols' => ['category' => 'Category', 'name' => 'Course', 'short_name' => 'Short', 'duration' => 'Duration', 'fee' => 'Fee', 'is_active' => 'Active (1=haan)']]);
admin_footer();
