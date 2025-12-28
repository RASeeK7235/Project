<?php
include 'config.php';
include '../project/supabase.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['id'])){
    header('Location:../project/login.php');
}


$teacher =  fetchData('TeacherProfile','id=eq.'.$_SESSION['id']);





?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Profile - Teacher Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <?php include 'includes/nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Profile</h1>
            <p class="text-gray-600">View your profile information</p>
        </div>

        <div class="space-y-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Personal Information</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <p class="text-gray-900 px-4 py-2 bg-gray-50 rounded-lg"><?php echo htmlspecialchars($teacher[0]['name']);
                         ?></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee ID</label>
                        <p class="text-gray-900 px-4 py-2 bg-gray-100 rounded-lg cursor-not-allowed"><?php echo htmlspecialchars($teacher[0]['id']); ?></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <p class="text-gray-900 px-4 py-2 bg-gray-50 rounded-lg"><?php echo htmlspecialchars($teacher[0]['email']); ?></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <p class="text-gray-900 px-4 py-2 bg-gray-50 rounded-lg"><?php echo htmlspecialchars($teacher[0]['phone']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-green-600"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Professional Information</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <p class="text-gray-900 px-4 py-2 bg-gray-100 rounded-lg cursor-not-allowed"><?php echo htmlspecialchars($teacher[0]['department']); ?></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Qualification</label>
                        <p class="text-gray-900 px-4 py-2 bg-gray-50 rounded-lg"><?php echo htmlspecialchars($teacher[0]['qualification']); ?></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Experience</label>
                        <p class="text-gray-900 px-4 py-2 bg-gray-50 rounded-lg"><?php echo htmlspecialchars($teacher[0]['experience']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Subjects Teaching -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-purple-600"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Subjects Teaching</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($teacher[0]['subjects'] as $subject): ?>
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                        <span class="text-gray-900"><?php echo htmlspecialchars($subject); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>