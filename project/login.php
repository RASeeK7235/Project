<?php
//require_once './config.php';
include '../project/supabase.php';



if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*if (isset($_SESSION['id']) && isset($_SESSION['role'])) {

    if ($_SESSION['role'] === 'student') {
        header('Location: ../student_side/home.php');
        exit();
    } else {
        header('Location: ../teacher_side/students.php');
        exit();
    }
}REMOVED THIS FOR ONCE YESLE TEST GARNA GAARO VO*/





$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $user_data=fetchData("Users","username=eq." . urlencode($username));//yesma sabb data basi sakyo

    

    // Check in database users
    if (isset($user_data[0]) && $user_data[0]['password'] === $password) {  //isset checks if username exist in database or not
       
        
        // Set session variables
        $_SESSION['id'] = (string)$user_data[0]['id'];
        $_SESSION['username'] = $user_data[0]['username'];
        $_SESSION['role'] = $user_data[0]['role'];
       
        
        // Redirect based on role
        if ($user_data[0]['role'] === 'student') {
            header('Location:../student_side/home.php');
        } else {
            header('Location: ../teacher_side/students.php');
        }
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Student Management System</h1>
                <p class="text-gray-600 mt-2">Sign in to your account</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                <?php if ($error): ?>
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" id="username" name="username" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Sign In
                    </button>
                </form>
            </div>

            <p class="text-center text-sm text-gray-600 mt-4">
                Demo credentials: student/password or teacher/password
            </p>
        </div>
    </div>
</body>
</html>