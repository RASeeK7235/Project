<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['id'])){
    header('Location:../project/login.php');
}

// Mock data
$subjects = ['Data Structures', 'Algorithms', 'Database Systems', 'Web Development'];
$batches = ['2021-2025', '2022-2026', '2023-2027'];
$students = [
    ['id' => 1, 'roll_no' => 'CS2023001', 'name' => 'John Doe'],
    ['id' => 2, 'roll_no' => 'CS2023002', 'name' => 'Jane Smith'],
    ['id' => 3, 'roll_no' => 'CS2023003', 'name' => 'Michael Johnson'],
    ['id' => 4, 'roll_no' => 'CS2023004', 'name' => 'Emily Williams'],
    ['id' => 5, 'roll_no' => 'CS2023005', 'name' => 'David Brown'],
];

// Handle form submission
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'] ?? '';
    $batch = $_POST['batch'] ?? '';
    $exam_type = $_POST['exam_type'] ?? '';
    $marks = $_POST['marks'] ?? [];
    
    if (!empty($subject) && !empty($batch) && !empty($exam_type)) {
        $success_message = "Marks saved successfully for " . htmlspecialchars($exam_type) . "!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Results - Teacher Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <?php include 'includes/nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Manage Results</h1>
            <p class="text-gray-600">Enter and manage student grades</p>
        </div>

        <?php if ($success_message): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span><?php echo $success_message; ?></span>
        </div>
        <?php endif; ?>

        <form method="POST" action="results.php">
            <div class="space-y-6">
                <!-- Selection Card -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Exam Details</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Subject <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="subject" 
                                name="subject" 
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
                            >
                                <option value="">Choose a subject</option>
                                <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo htmlspecialchars($subject); ?>"><?php echo htmlspecialchars($subject); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="batch" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Batch <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="batch" 
                                name="batch" 
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
                            >
                                <option value="">Choose a batch</option>
                                <?php foreach ($batches as $batch): ?>
                                <option value="<?php echo htmlspecialchars($batch); ?>"><?php echo htmlspecialchars($batch); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="exam_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Exam Type <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="exam_type" 
                                name="exam_type" 
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
                            >
                                <option value="">Choose exam type</option>
                                <option value="Mid-Term">Mid-Term</option>
                                <option value="Final">Final</option>
                                <option value="Quiz">Quiz</option>
                                <option value="Assignment">Assignment</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex gap-3">
                    <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                    <div>
                        <p class="text-blue-900 font-medium">Instructions:</p>
                        <p class="text-blue-700 text-sm mt-1">Enter marks for each student. Leave blank if the student was absent or not applicable.</p>
                    </div>
                </div>

                <!-- Marks Entry Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Enter Marks</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Roll No</th>
                                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Student Name</th>
                                    <th class="px-6 py-3 text-center text-sm font-medium text-gray-700">Marks Obtained</th>
                                    <th class="px-6 py-3 text-center text-sm font-medium text-gray-700">Total Marks</th>
                                    <th class="px-6 py-3 text-center text-sm font-medium text-gray-700">Percentage</th>
                                    <th class="px-6 py-3 text-center text-sm font-medium text-gray-700">Grade</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($students as $student): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <?php echo htmlspecialchars($student['roll_no']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                                <span class="text-indigo-600 text-sm font-medium">
                                                    <?php echo strtoupper(substr($student['name'], 0, 1)); ?>
                                                </span>
                                            </div>
                                            <span class="text-gray-900"><?php echo htmlspecialchars($student['name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input 
                                            type="number" 
                                            name="marks[<?php echo $student['id']; ?>][obtained]" 
                                            min="0" 
                                            max="100"
                                            placeholder="0"
                                            class="w-24 px-3 py-2 border border-gray-300 rounded-lg text-center focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
                                            onchange="calculatePercentage(<?php echo $student['id']; ?>)"
                                        />
                                    </td>
                                    <td class="px-6 py-4">
                                        <input 
                                            type="number" 
                                            name="marks[<?php echo $student['id']; ?>][total]" 
                                            min="0" 
                                            max="100"
                                            value="100"
                                            class="w-24 px-3 py-2 border border-gray-300 rounded-lg text-center focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
                                            onchange="calculatePercentage(<?php echo $student['id']; ?>)"
                                        />
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span id="percentage-<?php echo $student['id']; ?>" class="font-medium text-gray-900">-</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span id="grade-<?php echo $student['id']; ?>" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            -
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-4">
                    <button 
                        type="reset" 
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Clear All
                    </button>
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2"
                    >
                        <i class="fas fa-save"></i>
                        Save Marks
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function calculatePercentage(studentId) {
            const obtained = parseFloat(document.querySelector(`input[name="marks[${studentId}][obtained]"]`).value) || 0;
            const total = parseFloat(document.querySelector(`input[name="marks[${studentId}][total]"]`).value) || 100;
            
            if (total > 0) {
                const percentage = ((obtained / total) * 100).toFixed(2);
                document.getElementById(`percentage-${studentId}`).textContent = percentage + '%';
                
                // Calculate grade
                let grade = '';
                let gradeColor = '';
                if (percentage >= 90) {
                    grade = 'A+';
                    gradeColor = 'bg-green-100 text-green-800';
                } else if (percentage >= 80) {
                    grade = 'A';
                    gradeColor = 'bg-green-100 text-green-800';
                } else if (percentage >= 70) {
                    grade = 'B';
                    gradeColor = 'bg-blue-100 text-blue-800';
                } else if (percentage >= 60) {
                    grade = 'C';
                    gradeColor = 'bg-yellow-100 text-yellow-800';
                } else if (percentage >= 50) {
                    grade = 'D';
                    gradeColor = 'bg-orange-100 text-orange-800';
                } else {
                    grade = 'F';
                    gradeColor = 'bg-red-100 text-red-800';
                }
                
                const gradeElement = document.getElementById(`grade-${studentId}`);
                gradeElement.textContent = grade;
                gradeElement.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${gradeColor}`;
            }
        }
    </script>
</body>
</html>



