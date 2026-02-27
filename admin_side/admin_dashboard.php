<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once '../project/supabase.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['id'])) {
    header('Location: ../project/login.php');
    exit;
}

// Validation functions
function validatePhone($phone)
{
    return preg_match('/^(98|97)\d{8}$/', $phone);
}
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_start();
    $action = $_POST['action'] ?? '';

    // ----------------- ADD STUDENT -----------------
    if ($action === 'add_student') {
        $id = trim($_POST['id'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $batch = trim($_POST['batch'] ?? '');
        $course = trim($_POST['course'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($id) || empty($name) || empty($password) || empty($batch) || empty($course)) {
            echo '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">All required fields must be filled.</div>';
        } elseif (!empty($phone) && !validatePhone($phone)) {
            echo '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">Phone must be 10 digits starting with 98 or 97.</div>';
        } elseif (!empty($email) && !validateEmail($email)) {
            echo '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">Invalid email format.</div>';
        } else {
            $existingUserById = fetchData('Users', "id=eq." . urlencode($id));
            $existingUserByName = fetchData('Users', "username=eq." . urlencode($name));

            if ((!empty($existingUserById) && is_array($existingUserById) && !isset($existingUserById['error'])) ||
                (!empty($existingUserByName) && is_array($existingUserByName) && !isset($existingUserByName['error']))
            ) {
                echo '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">Student with this ID or Name already exists.</div>';
            } else {
                $userResult = addData('Users', ['id' => $id, 'username' => $name, 'password' => $password, 'role' => 'student']);
                if (isset($userResult['error'])) {
                    echo '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">Failed to add user: ' . htmlspecialchars($userResult['error']) . '</div>';
                } else {
                    $studentResult = addData('StudentProfile', [
                        'id' => $id,
                        'name' => $name,
                        'email' => !empty($email) ? $email : null,
                        'phone' => !empty($phone) ? (int)$phone : null,
                        'batch' => is_numeric($batch) ? (int)$batch : null,
                        'program' => $course
                    ]);

                    if (isset($studentResult['error'])) {
                        echo '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">Failed to add student profile: ' . htmlspecialchars($studentResult['error']) . '</div>';
                    } else {
                        echo '<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">Student added successfully!</div>';
                    }
                }
            }
        }
    }

  // ----------------- ADD TEACHER -----------------
if ($action === 'add_teacher') {
    $id = trim($_POST['id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $subjects_input = trim($_POST['subjects_input'] ?? '');

    // ----------------- VALIDATION -----------------
    if (empty($id) || empty($name) || empty($password)) {
        $message = '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">ID, Name, and Password are required.</div>';
    } elseif (!empty($phone) && !validatePhone($phone)) {
        $message = '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">Phone must be 10 digits starting with 98 or 97.</div>';
    } elseif (!empty($email) && !validateEmail($email)) {
        $message = '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">Invalid email format.</div>';
    } elseif (empty($subjects_input)) {
        $message = '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">At least one subject is required.</div>';
    } else {
        $existingUser = fetchData('Users', "id=eq." . urlencode($id));
        if (!empty($existingUser) && is_array($existingUser) && !isset($existingUser['error'])) {
            $message = '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">Teacher with this ID already exists.</div>';
        } else {
            // Add to Users table
            $userResult = addData('Users', [
                'id' => $id,
                'username' => $name,
                'password' => $password,
                'role' => 'teacher'
            ]);

            if (isset($userResult['error'])) {
                $message = '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">Failed to add teacher: ' . htmlspecialchars($userResult['error']) . '</div>';
            } else {
                // Convert subjects to array
                $subjectsArray = array_filter(array_map('trim', explode(',', $subjects_input)));

                // Add to TeacherProfile
                $teacherResult = addData('TeacherProfile', [
                    'id' => $id,
                    'name' => $name,
                    'email' => !empty($email) ? $email : null,
                    'department' => $department,
                    'phone' => !empty($phone) ? (int)$phone : null,
                    'qualification' => $qualification,
                    'experience' => $experience,
                    'sub_code' => $subjectsArray
                ]);

                  if (isset($teacherResult['error'])) {
                        echo '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">Failed to add student profile: ' . htmlspecialchars($teacherResult['error']) . '</div>';
                    } else {
                        echo '<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">Teacher added successfully!</div>';
                    }
            }
        }
    }
}

    // ----------------- ADD SUBJECT -----------------
    if ($action === 'add_subject') {
        $sub_code = trim($_POST['sub_code'] ?? '');
        $sub_name = trim($_POST['sub_name'] ?? '');

        if (empty($sub_code) || empty($sub_name)) {
            echo '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">Subject code and name are required.</div>';
        } else {
            $existingSubject = fetchData('Subjects', "sub_code=eq." . urlencode($sub_code));
            if (!empty($existingSubject) && is_array($existingSubject) && !isset($existingSubject['error'])) {
                echo '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">Subject with this code already exists.</div>';
            } else {
                $subjectResult = addData('Subjects', ['sub_code' => $sub_code, 'sub_name' => $sub_name]);
                if (isset($subjectResult['error'])) {
                    echo '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">Failed to add subject: ' . htmlspecialchars($subjectResult['error']) . '</div>';
                } else {
                    echo '<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">Subject added successfully!</div>';
                }
            }
        }
    }

    // ----------------- REMOVE user -----------------
    if ($action === 'remove_user') {
        $id = trim($_POST['id'] ?? '');
        if (empty($id)) {
            echo '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">ID is required.</div>';
        } else {
            $existingStudent = fetchData('Users', "id=eq." . urlencode($id));
            if (!is_array($existingStudent) || isset($existingStudent['error']) || count($existingStudent) === 0) {
                echo '<div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">No user found with this ID.</div>';
            } else {
                $tables = ['StudentProfile', 'Attendance', 'Notices', 'Results', 'Users','TeacherProfile'];
                $errors = [];
                foreach ($tables as $table) {
                    $result = deleteData($table, "id=eq." . urlencode($id));
                    if (isset($result['error'])) $errors[] = "Failed to remove from $table: " . htmlspecialchars($result['error']);
                }
                if (!empty($errors)) echo '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">' . implode('<br>', $errors) . '</div>';
                else echo '<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">user removed successfully!</div>';
            }
        }
    }

    // ----------------- REMOVE TEACHER -----------------
   

    // ----------------- REMOVE SUBJECT -----------------
    if ($action === 'remove_subject') {
        $sub_code = trim($_POST['sub_code'] ?? '');
        if (empty($sub_code)) {
            echo '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">Subject code is required.</div>';
        } else {
            $existingSubject = fetchData('Subjects', "sub_code=eq." . urlencode($sub_code));
            if (!is_array($existingSubject) || isset($existingSubject['error']) || count($existingSubject) === 0) {
                echo '<div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">No subject found with this code.</div>';
            } else {
                $result = deleteData('Subjects', "sub_code=eq." . urlencode($sub_code));
                if (isset($result['error'])) echo '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">Failed to remove subject: ' . htmlspecialchars($result['error']) . '</div>';
                else echo '<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">Subject removed successfully!</div>';
            }
        }
    }

    $message = ob_get_clean();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 p-6">
    <div class="container mx-auto">
        <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>

        <!-- Display messages -->
        <?php if (!empty($message)): ?>
            <div class="mb-4"><?= $message ?></div>
        <?php endif; ?>

        <!-- ADD / REMOVE user -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <h2 class="text-xl font-semibold mb-2">Add Student</h2>
            <form method="POST" class="space-y-2">
                <input type="hidden" name="action" value="add_student">
                <input type="text" name="id" placeholder="Student ID" class="border p-2 rounded w-full">
                <input type="text" name="name" placeholder="Name" class="border p-2 rounded w-full">
                <input type="password" name="password" placeholder="Password" class="border p-2 rounded w-full">
                <input type="text" name="batch" placeholder="Batch" class="border p-2 rounded w-full">
                <input type="text" name="course" placeholder="Program/Course" class="border p-2 rounded w-full">
                <input type="text" name="email" placeholder="Email (optional)" class="border p-2 rounded w-full">
                <input type="text" name="phone" placeholder="Phone (optional)" class="border p-2 rounded w-full">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add Student</button>
            </form>

            <h2 class="text-xl font-semibold mt-4 mb-2">Remove User</h2>
            <form method="POST" class="space-y-2">
                <input type="hidden" name="action" value="remove_user">
                <input type="text" name="id" placeholder="Student ID" class="border p-2 rounded w-full">
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Remove user</button>
            </form>
        </div>

        <!-- ADD / REMOVE TEACHER -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <h2 class="text-xl font-semibold mb-2">Add Teacher</h2>
            <form method="POST" class="space-y-2">
                <input type="hidden" name="action" value="add_teacher">
                <input type="text" name="id" placeholder="Teacher ID" class="border p-2 rounded w-full">
                <input type="text" name="name" placeholder="Name" class="border p-2 rounded w-full">
                <input type="password" name="password" placeholder="Password" class="border p-2 rounded w-full">
                <input type="text" name="email" placeholder="Email (optional)" class="border p-2 rounded w-full">
                <input type="text" name="department" placeholder="Department" class="border p-2 rounded w-full">
                <input type="text" name="phone" placeholder="Phone (optional)" class="border p-2 rounded w-full">
                <input type="text" name="qualification" placeholder="Qualification" class="border p-2 rounded w-full">
                <input type="text" name="experience" placeholder="Experience" class="border p-2 rounded w-full">
                <input type="text" name="subjects_input" placeholder="Subject(s), comma separated" class="border p-2 rounded w-full">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add Teacher</button>
            </form>

            
            
        </div>

        <!-- ADD / REMOVE SUBJECT -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <h2 class="text-xl font-semibold mb-2">Add Subject</h2>
            <form method="POST" class="space-y-2">
                <input type="hidden" name="action" value="add_subject">
                <input type="text" name="sub_code" placeholder="Subject Code" class="border p-2 rounded w-full">
                <input type="text" name="sub_name" placeholder="Subject Name" class="border p-2 rounded w-full">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add Subject</button>
            </form>

            <h2 class="text-xl font-semibold mt-4 mb-2">Remove Subject</h2>
            <form method="POST" class="space-y-2">
                <input type="hidden" name="action" value="remove_subject">
                <input type="text" name="sub_code" placeholder="Subject Code" class="border p-2 rounded w-full">
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Remove Subject</button>
            </form>
        </div>

    </div> <!-- container -->
</body>
</html>