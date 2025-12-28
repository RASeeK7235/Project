<?php
/*
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only redirect if current page is **not login.php**
if (!isset($_SESSION['id']) && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header('Location: ../project/login.php'); // path to your login.php
    exit();
}*/


// Demo data - hardcoded for development
$demo_users = [
    'student' => [
        'id' => 1,
        'username' => 'student',
        'password' => 'password',
        'name' => 'John Doe',
        'role' => 'student'
    ],
    'teacher' => [
        'id' => 2,
        'username' => 'teacher',
        'password' => 'password',
        'name' => 'Jane Smith',
        'role' => 'teacher'
    ],
    'admin' => [
        'id' => 3,
        'username' => 'admin',
        'password' => 'password',
        'name' => 'Admin User',
        'role' => 'admin'
    ]
];

// Demo student data
$demo_student = [
    'id' => 1,
    'user_id' => 1,
    'roll_no' => 'CS2024001',//id in my database
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'phone' => '1234567890',
    'address' => '123 Main Street, City, State - 12345',
    'dob' => '2003-05-15',
    'batch' => '2024',
    'program' => 'Computer Science',
    'enrollment_date' => '2024-01-15',
    'guardian_name' => 'Robert Doe',
    'guardian_phone' => '9876543210'
];

// Demo subjects
$demo_subjects = [
    1 => ['id' => 1, 'name' => 'Data Structures', 'code' => 'CS301'],
    2 => ['id' => 2, 'name' => 'Algorithms', 'code' => 'CS302'],
    3 => ['id' => 3, 'name' => 'Database Systems', 'code' => 'CS303'],
    4 => ['id' => 4, 'name' => 'Web Development', 'code' => 'CS304'],
    5 => ['id' => 5, 'name' => 'Computer Networks', 'code' => 'CS305'],
    6 => ['id' => 6, 'name' => 'Operating Systems', 'code' => 'CS306']
];

// Demo attendance data (subject_id => [attended, total])
$demo_attendance = [
    1 => ['attended' => 42, 'total' => 45],
    2 => ['attended' => 38, 'total' => 45],
    3 => ['attended' => 35, 'total' => 44],
    4 => ['attended' => 40, 'total' => 46],
    5 => ['attended' => 45, 'total' => 48],
    6 => ['attended' => 45, 'total' => 52]
];

// Demo results data
$demo_results = [
    ['subject_id' => 1, 'exam_type' => 'Mid-term', 'marks_obtained' => 92, 'full_marks' => 100, 'percentage' => 92, 'grade' => 'A+', 'status' => 'pass'],
    ['subject_id' => 2, 'exam_type' => 'Final', 'marks_obtained' => 85, 'full_marks' => 100, 'percentage' => 85, 'grade' => 'A', 'status' => 'pass'],
    ['subject_id' => 3, 'exam_type' => 'Mid-term', 'marks_obtained' => 78, 'full_marks' => 100, 'percentage' => 78, 'grade' => 'B+', 'status' => 'pass'],
    ['subject_id' => 4, 'exam_type' => 'Final', 'marks_obtained' => 88, 'full_marks' => 100, 'percentage' => 88, 'grade' => 'A', 'status' => 'pass'],
    ['subject_id' => 5, 'exam_type' => 'Mid-term', 'marks_obtained' => 75, 'full_marks' => 100, 'percentage' => 75, 'grade' => 'B', 'status' => 'pass'],
    ['subject_id' => 6, 'exam_type' => 'Final', 'marks_obtained' => 82, 'full_marks' => 100, 'percentage' => 82, 'grade' => 'A', 'status' => 'pass']
];

// Demo notices
$demo_notices = [
    [
        'id' => 1,
        'topic' => 'Welcome to New Semester',
        'body' => 'We are excited to welcome all students to the new semester. Classes will begin on Monday, January 15th, 2024. Please ensure you have completed your registration.',
        'created_at' => '2024-01-10 10:00:00'
    ],
    [
        'id' => 2,
        'topic' => 'Mid-term Examination Schedule',
        'body' => 'Mid-term examinations will be held from March 1st to March 15th, 2024. The detailed timetable will be posted on the notice board next week.',
        'created_at' => '2024-02-15 14:30:00'
    ],
    [
        'id' => 3,
        'topic' => 'Library Hours Extended',
        'body' => 'The library will now be open from 8:00 AM to 10:00 PM on weekdays to help students prepare for their exams. Weekend hours remain unchanged (9:00 AM - 6:00 PM).',
        'created_at' => '2024-02-20 09:00:00'
    ],
    [
        'id' => 4,
        'topic' => 'Sports Day Announcement',
        'body' => 'Annual Sports Day will be organized on April 20th, 2024. All students are encouraged to participate. Registration forms are available at the sports office.',
        'created_at' => '2024-03-01 11:00:00'
    ],
    [
        'id' => 5,
        'topic' => 'Holiday Notice',
        'body' => 'The college will remain closed on March 25th for the national holiday. Regular classes will resume on March 26th.',
        'created_at' => '2024-03-10 16:00:00'
    ]
];
?>