<?php require 'inc/auth.php'; require 'inc/layout.php'; require 'inc/crud.php';
admin_header('Enquiry record');
crud(['table' => 'enquiries', 'cols' => ['name' => 'Naam', 'mobile' => 'Mobile', 'email' => 'Email', 'course_interest' => 'Course', 'message' => 'Message', 'created_at' => 'Date']]);
admin_footer();
