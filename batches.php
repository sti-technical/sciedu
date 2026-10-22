<?php require 'inc/auth.php'; require 'inc/layout.php'; require 'inc/crud.php';
admin_header('Batches');
crud(['table' => 'batches', 'fields' => ['timing' => ['label' => 'Batch timing (9am to 10am)', 'type' => 'text', 'req' => 1],
  'is_active' => ['label' => 'Active?', 'type' => 'select', 'options' => [1 => 'Haan', 0 => 'Nahi']]], 'cols' => ['timing' => 'Timing', 'is_active' => 'Active (1=haan)']]);
admin_footer();
