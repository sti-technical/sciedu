<?php require 'inc/auth.php'; require 'inc/layout.php'; require 'inc/crud.php';
admin_header('News');
crud(['table' => 'news', 'fields' => [
  'title' => ['label' => 'Title', 'type' => 'text', 'req' => 1], 'body' => ['label' => 'Detail', 'type' => 'textarea'],
  'posted_on' => ['label' => 'Date', 'type' => 'date', 'default' => date('Y-m-d')], 'is_active' => ['label' => 'Website par dikhayein?', 'type' => 'select', 'options' => [1 => 'Haan', 0 => 'Nahi']]],
  'cols' => ['title' => 'Title', 'posted_on' => 'Date', 'is_active' => 'Active (1=haan)']]);
admin_footer();
