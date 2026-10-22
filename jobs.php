<?php require 'inc/auth.php'; require 'inc/layout.php'; require 'inc/crud.php';
admin_header('Applied job record');
crud(['table' => 'job_applications', 'cols' => ['name' => 'Naam', 'mobile' => 'Mobile', 'email' => 'Email', 'position' => 'Position', 'created_at' => 'Date']]);
admin_footer();
