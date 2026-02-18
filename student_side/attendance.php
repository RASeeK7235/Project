<?php
// yesle session active cha ki chaina bhanera check garxa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// yedi login gareko chaina bhane login page ma bhejxa
if(!isset($_SESSION['id'])){
    header('Location:../project/login.php');
}

include '../project/supabase.php';
// yesle current student ko attendance data fetch garxa student ID use garera
$attendanceData=fetchData("Attendance","id=eq.".urlencode($_SESSION['id']));

// yesle sabai Subjects ko list fetch garxa dropdown populate garna ko lagi
$sub_name=fetchData("Subjects");




/*$attendanceData = [
    [
        "subject" => "Data Structures",
        "code" => "CS301",
        "totalClasses" => 45,
        "attendedClasses" => 42,
        "percentage" => 93.3,
        "status" => "excellent",
    ],
    [
        "subject" => "Algorithms",
        "code" => "CS302",
        "totalClasses" => 45,
        "attendedClasses" => 38,
        "percentage" => 84.4,
        "status" => "good",
    ],
    [
        "subject" => "Database Systems",
        "code" => "CS303",
        "totalClasses" => 44,
        "attendedClasses" => 35,
        "percentage" => 79.5,
        "status" => "warning",
    ],
    [
        "subject" => "Web Development",
        "code" => "CS304",
        "totalClasses" => 46,
        "attendedClasses" => 40,
        "percentage" => 87.0,
        "status" => "good",
    ],
    [
        "subject" => "Computer Networks",
        "code" => "CS305",
        "totalClasses" => 48,
        "attendedClasses" => 45,
        "percentage" => 93.8,
        "status" => "excellent",
    ],
    [
        "subject" => "Operating Systems",
        "code" => "CS306",
        "totalClasses" => 52,
        "attendedClasses" => 45,
        "percentage" => 86.5,
        "status" => "good",
    ],
];*/

$totalClasses = array_sum(array_column($attendanceData, 'total'));
$totalAttended = array_sum(array_column($attendanceData, 'attended'));
$overallPercentage = $totalClasses>0?($totalAttended / $totalClasses) * 100: 0;

function getStatusColor($percent) {
    return ($percent >= 80 ? 'bg-green-100 text-green-800 border-green-800' :
          ($percent >= 60 ? 'bg-yellow-100 text-yellow-800 border-yellow-800' :
           ($percent >= 40 ? 'bg-orange-100 text-orange-800 border-orange-800' : 'bg-red-100 text-red-800 border-red-800')));
    }
    


function getStatusLabel($percent) {
    return ($percent >= 80 ? 'Excellent' :
          ($percent >= 60 ? 'Good' :
           ($percent >= 40 ? 'Warning' : 'Critical')));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
</head>
<body class="bg-gray-50 min-h-screen">
<?php include 'student_nav.php'; ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl text-gray-900">Attendance Records</h1>
    <p class="text-gray-600 mt-1">View your attendance for all subjects</p>

    <!-- Overall Summary -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h2 class="text-xl font-semibold">Overall Attendance Summary</h2>
        <p class="text-gray-500 text-sm">Your total attendance across all subjects</p>

        <div class="mt-4">
            <div class="flex justify-between mb-2">
                <span class="text-sm text-gray-600">Overall Attendance</span>
                <span class="text-2xl text-gray-900"><?= number_format($overallPercentage, 1) ?>%</span>
            </div>

            <div class="w-full bg-gray-200 rounded h-3">
                <div class="bg-blue-600 h-3 rounded"
                     style="width: <?= $overallPercentage ?>%"></div>
            </div>

            <p class="text-sm text-gray-500 mt-2">
                <?= $totalAttended ?> out of <?= $totalClasses ?> classes attended
            </p>

            <span class="inline-block mt-3 px-3 py-1 border rounded text-sm
                <?= getStatusColor($overallPercentage) ?>">
                <?= getStatusLabel($overallPercentage) ?>
            </span>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h2 class="text-xl font-semibold">Subject-wise Attendance</h2>
        <p class="text-gray-500 text-sm mb-4">Detailed breakdown by subject</p>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Code</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Attended</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Total</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($attendanceData as $item): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900"><?php 
                            foreach($sub_name as $sub){//choosing subject name from subject array using subject code
                                if($sub['sub_code']==$item['sub_code']){
                                    echo $sub['sub_name'];
                                }
                            }
                            ?></p>
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                <?= $item['sub_code'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center hidden sm:table-cell">
                            <span class="text-gray-900"><?= $item['attended'] ?></span>
                        </td>
                        <td class="px-6 py-4 text-center hidden sm:table-cell">
                            <span class="text-gray-600"><?= $item['total'] ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1 items-center">
                                <!--progress bar-->
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: <?php
                                    $percent=($item['total']>0?(($item['attended'] / $item['total']) * 100):0);
                                    echo $percent;
                                    ?>%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900"><?= number_format($percent, 1) ?>%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center hidden sm:table-cell">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= getStatusColor($percent) ?>">
                                <?= getStatusLabel($percent) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const supabase = window.supabase.createClient('https://lvsogpbcuauofmjsqrde.supabase.co', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imx2c29ncGJjdWF1b2ZtanNxcmRlIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjUyOTIwNzQsImV4cCI6MjA4MDg2ODA3NH0.lwUFlN-Ba8uheoF3kB1rwRDEYBSYt0Ay11TEpZJm_0g');

const userId = '<?php echo $_SESSION['id']; ?>';

console.log('User ID:', userId);

const attendanceChannel = supabase
  .channel('attendance_changes')
  .on('postgres_changes', { event: '*', schema: 'public', table: 'Attendance', filter: `id=eq.${userId}` }, (payload) => {
    console.log('Attendance change detected:', payload);
    location.reload();
  })
  .subscribe((status) => {
    console.log('Subscription status:', status);
  });
</script>

</body>
</html>
