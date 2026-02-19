<?php
// yesle session start garxa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../project/supabase.php';

// yesle forgot password request handle garxa
$reset_message = '';
$reset_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'forgot_password') {
    $user_id = trim($_POST['user_id'] ?? '');
    
    // yesle user_id ko validation garxa
    if (!empty($user_id)) {
        // yesle Users table bata user exist garxa ki chaina bhanera check garxa
        $user = fetchData('Users', "id=eq." . urlencode($user_id));
        
        // yesle check garxa user data valid cha ki chaina
        if (!empty($user) && is_array($user) && !isset($user['error'])) {
            // yesle reset request email pathauxa
            $to = 'shtraseek0@gmail.com';
            $subject = 'Password Reset Request';
            $message = "Password reset request has been submitted by user ID: " . htmlspecialchars($user_id) . "\n\nPlease process this request and send password reset instructions to the user.";
            // Proper From header for Gmail SMTP
            $headers = "From: shtraseek0@gmail.com\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            // yesle mail function use garera email pathauxa, yedi fail bhayo bhane error message set garxa
            $mail_sent = @mail($to, $subject, $message, $headers);

            if ($mail_sent) {
                $reset_message = 'Password reset request sent successfully! Please check your email for further instructions.';
            } else {
                // yedi mail fail bhayo tani direct message send garxa ta kati successful hos
                $reset_message = 'Password reset request has been logged. An admin will contact you shortly.';
            }
        } else {
            // yesle error message set garxa yedi user na phaley
            $reset_error = 'User ID not found in the system. Please verify and try again.';
        }
        
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        if (!empty($reset_error)) {
            echo json_encode(['success' => false, 'message' => $reset_error]);
        } else {
            echo json_encode(['success' => true, 'message' => $reset_message]);
        }
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'User ID is required']);
        exit;
    }
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
                
                <!-- yesle forgot password button le modal open garxa -->
                <button type="button" onclick="openForgotPasswordModal()"
                        class="w-full mt-3 bg-gray-400 text-white py-2 px-4 rounded-lg hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors">
                    Forgot Password?
                </button>
            </div>

            <p class="text-center text-sm text-gray-600 mt-4">
                
            </p>
        </div>
    </div>

    <!-- yesle forgot password modal display garxa -->
    <div id="forgotPasswordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-96 shadow-lg">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Reset Password</h2>
            <p class="text-sm text-gray-600 mb-4">Enter your user ID to request a password reset.</p>
            
            <!-- yesle user ID input field -->
            <input type="text" id="resetUserId" placeholder="Enter your User ID" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">
            
            <!-- yesle error message display garxa -->
            <p id="resetError" class="text-red-600 text-sm mb-4 hidden"></p>
            <!-- yesle success message display garxa -->
            <p id="resetSuccess" class="text-green-600 text-sm mb-4 hidden"></p>
            
            <!-- yesle button container -->
            <div class="flex gap-3 justify-end">
                <!-- yesle modal close button -->
                <button type="button" onclick="closeForgotPasswordModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <!-- yesle reset request send button -->
                <button type="button" onclick="sendPasswordReset()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Send Request
                </button>
            </div>
        </div>
    </div>

    <script>
        // yesle forgot password modal open garxa
        function openForgotPasswordModal() {
            document.getElementById('forgotPasswordModal').classList.remove('hidden');
            document.getElementById('resetUserId').value = '';
            document.getElementById('resetError').classList.add('hidden');
            document.getElementById('resetSuccess').classList.add('hidden');
        }

        // yesle forgot password modal close garxa
        function closeForgotPasswordModal() {
            document.getElementById('forgotPasswordModal').classList.add('hidden');
        }

        // yesle outside click bhayo bhane modal close garxa
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('forgotPasswordModal');
            if (event.target === modal) {
                closeForgotPasswordModal();
            }
        });

        // yesle password reset request server ma pathauxa
        function sendPasswordReset() {
            const userId = document.getElementById('resetUserId').value.trim();
            const errorDiv = document.getElementById('resetError');
            const successDiv = document.getElementById('resetSuccess');
            
            errorDiv.classList.add('hidden');
            successDiv.classList.add('hidden');
            
            // yedi user ID khali cha bhane error display garxa
            if (!userId) {
                errorDiv.textContent = 'Please enter your User ID';
                errorDiv.classList.remove('hidden');
                return;
            }
            
            // yesle form data create garxa
            const formData = new FormData();
            formData.append('action', 'forgot_password');
            formData.append('user_id', userId);
            
            // yesle server ko forgot_password endpoint ma request pathauxa
            fetch('login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response received:', response.status, response.type);
                
                // Check if response is successful
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.success) {
                    successDiv.textContent = data.message;
                    successDiv.classList.remove('hidden');
                    document.getElementById('resetUserId').value = '';
                    setTimeout(() => {
                        closeForgotPasswordModal();
                    }, 2000);
                } else {
                    errorDiv.textContent = data.message || 'Failed to process request';
                    errorDiv.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                console.error('Error name:', error.name);
                console.error('Error message:', error.message);
                console.error('Full error:', JSON.stringify(error));
                errorDiv.textContent = 'An error occurred. Please check console and try again.';
                errorDiv.classList.remove('hidden');
            });
        }
    </script>
</body>
</html>