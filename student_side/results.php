<?php
include '../project/config.php'; //remove this after database connection
include '../project/supabase.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['id'])){//user logged in cha ki chaina bhanera check garxa
    header('Location:../project/login.php');//if not logged in login page ma redirect garxa
}

$sub=fetchData("Subjects");

$results=fetchData("Results","id=eq.".urlencode($_SESSION['id']));

// Calculate average percentage 
$total_percentage = 0;
$count = count($results);
foreach ($results as $result) {
    $percentage = $result['marks'] / $result['total'] * 100;
    $total_percentage += $percentage;//result ko percentage haru add garxa
}
$average_percentage = $count > 0 ? $total_percentage / $count : 0;


function getGrade($percentage) {
    if ($percentage >= 90) return 'A+';
    if ($percentage >= 80) return 'A';
    if ($percentage >= 70) return 'B+';
    if ($percentage >= 60) return 'B';
    if ($percentage >= 50) return 'C+';
    if ($percentage >= 40) return 'C';
    return 'F';
}

function getGradeColor($percentage) {
   if ($percentage >= 90) return 'bg-green-100 text-green-800 border-green-200';
    if ($percentage >= 80) return 'bg-blue-100 text-blue-800 border-blue-200';
    if ($percentage >= 70) return 'bg-yellow-100 text-yellow-800 border-yellow-200';
    if ($percentage >= 60) return 'bg-orange-100 text-orange-800 border-orange-200';
    if ($percentage >= 50) return 'bg-gray-100 text-gray-800 border-gray-200';
    if ($percentage >= 40) return 'bg-purple-100 text-purple-800 border-purple-200';
    return 'bg-red-100 text-red-800 border-red-200';
}

$overall_grade = getGrade($average_percentage);



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results - Student Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <?php include 'student_nav.php'; ?>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Examination Results</h1>
            <p class="text-gray-600 mt-1">View your academic performance</p>
        </div>

        <!-- Average Percentage Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-700">Average Percentage</h3>
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-2"><?php echo number_format($average_percentage, 1); ?>%</div>
            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: <?php echo $average_percentage; ?>%"></div>
            </div>
            <p class="text-xs text-gray-500">Overall Grade: <?php echo $overall_grade; ?></p>
        </div>

        <!-- Detailed Results Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Subject-wise Results</h3>
                <p class="text-sm text-gray-600 mt-1">Detailed breakdown of your examination scores</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Code</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Type</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Marks Obtained</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Full Marks</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Grade</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($results as $row): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900"><?php   foreach($sub as $sub_name){//choosing subject name from subject array using subject code
                                if($sub_name['sub_code']==$row['sub_code']){
                                    echo $sub_name['sub_name'];
                                }//yo part le row ko sub_code ra subject array ko sub_code milxa ki nai bhanera herxa ani milxa vane tyo subject ko name print garxa
                            } ?></p>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        <?php echo htmlspecialchars($row['sub_code']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-gray-900"><?php echo htmlspecialchars($row['exam_type']); ?></span>
                                </td>
                                <td class="px-6 py-4 text-center hidden md:table-cell">
                                    <span class="text-gray-900"><?php echo $row['marks']; ?></span>
                                </td>
                                <td class="px-6 py-4 text-center hidden md:table-cell">
                                    <span class="text-gray-600"><?php echo $row['total']; ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1 items-center">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: <?php 
                                            $percentage=$row['total']>0?(($row['marks'] / $row['total']) * 100):0;
                                            echo $percentage ; ?>%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">
                                            <?php echo $percentage; ?>%
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center hidden md:table-cell">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?php echo getGradeColor($percentage); ?>">
                                        <?php echo getGrade($percentage); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center hidden md:table-cell">
                                    <?php if ($percentage>=40): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium border bg-green-100 text-green-800 border-green-200">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Pass
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium border bg-red-100 text-red-800 border-red-200">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Fail
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>