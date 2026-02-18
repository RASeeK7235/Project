<?php
include '../project/supabase.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['id'])){
    header('Location:../project/login.php');
}



$user_data=fetchData("StudentProfile","id=eq." . urlencode($_SESSION['id']));//yesma sabb data basi sakyo

$success_message = '';
$error_message = '';




// yesle form submit bhayo bhane student ko profile update garxa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // yesle POST data bata user input lina sakxa
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $guardian_name = $_POST['guardian_name'];
    $guardian_phone = $_POST['guardian_phone'];
    // yesle update garna ko lagi data array ma organize garxa
    $updated_data = [
        'email' => $email,
        'phone' => $phone,
        'dob' => $dob,
        'address' => $address,
        'guardian_name' => $guardian_name,
        'guardian_phone' => $guardian_phone
    ];
    // yesle StudentProfile table ma current user ko record update garxa
    $result = updateData("StudentProfile", "id=eq.". $_SESSION['id'], $updated_data);
    
    // yedi update successful bhayo bhane success message dekhaxa, natra error message
    if (isset($result['error'])) {
        $error_message = 'Failed to update profile: ' . $result['error'];
    } else {
        $success_message = 'Profile updated successfully!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Student Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <?php include 'student_nav.php'; ?>
    
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
            <p class="text-gray-600 mt-1">View and update your personal information</p>
        </div>

        <?php if ($success_message): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Read-only Information -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Roll Number</label>
                            <input type="text" value="<?php echo htmlspecialchars($user_data[0]['id']); ?>" 
                                   disabled class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Batch</label>
                            <input type="text" value="<?php echo htmlspecialchars($user_data[0]['batch']); ?>" 
                                   disabled class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Program</label>
                            <input type="text" value="<?php echo htmlspecialchars($user_data[0]['program']); ?>" 
                                   disabled class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Enrollment Date</label>
                            <input type="text" value="<?php echo date('M d, Y', strtotime($user_data[0]['enrollment_date'])); ?>" 
                                   disabled class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed">
                        </div>
                    </div>
                </div>

                <!-- Editable Personal Information -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <input type="text" id="name" name="name" required disabled
                                   value="<?php echo htmlspecialchars($user_data[0]['name']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" id="email" name="email" required
                                   value="<?php echo htmlspecialchars($user_data[0]['email']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                            <input type="tel" id="phone" name="phone" required
                                   value="<?php echo htmlspecialchars($user_data[0]['phone']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="dob" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth *</label>
                            <input type="date" id="dob" name="dob" required
                                   value="<?php echo htmlspecialchars($user_data[0]['dob']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                            <textarea id="address" name="address" rows="3" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?php echo htmlspecialchars($user_data[0]['address']); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Guardian Information -->
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Guardian Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="guardian_name" class="block text-sm font-medium text-gray-700 mb-2">Guardian Name *</label>
                            <input type="text" id="guardian_name" name="guardian_name" required
                                   value="<?php echo htmlspecialchars($user_data[0]['guardian_name']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="guardian_phone" class="block text-sm font-medium text-gray-700 mb-2">Guardian Phone *</label>
                            <input type="tel" id="guardian_phone" name="guardian_phone" required
                                   value="<?php echo htmlspecialchars($user_data[0]['guardian_phone']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                    <button type="reset" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        Reset
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>