



<?php
//this page will be removed after testing
// Session configuration


// Check if user is logged in
function checkLogin() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'teacher') {
        header('Location: login.php');
        exit();
    }
}

// Mock teacher data
function getTeacherData() {
    return [
        'name' => 'Dr. Sarah Johnson',
        'employee_id' => 'EMP2023001',
        'department' => 'Computer Science',
        'email' => 'sarah.johnson@university.edu',
        'phone' => '+1 (555) 123-4567',
        'qualification' => 'Ph.D. in Computer Science',
        'experience' => '12 years',
        'subjects' => ['Data Structures', 'Algorithms', 'Database Systems', 'Web Development']
    ];
}
?>