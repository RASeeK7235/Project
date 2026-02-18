<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../project/config.php'; //remove this after database connection
include '../project/supabase.php';

if(!isset($_SESSION['id'])){
    header('Location:../project/login.php');
}

$notices=fetchData("Notices","order=created_at.desc&limit=5");//fetching notices from database 
$attendanceData=fetchData("Attendance","id=eq.".urlencode($_SESSION['id']));

$resultsData=fetchData("Results","id=eq.".urlencode($_SESSION['id']));

// Calculate overall attendance percentage from demo data
$total_attended = 0;
$total_classes = 0;
foreach ($attendanceData as $attendance) {
    $total_attended += $attendance['attended'];
    $total_classes += $attendance['total'];
}
$attendance_percentage = $total_classes > 0 ? ($total_attended / $total_classes) * 100 : 0;

// Calculate average grade percentage from demo data
$total_percentage = 0;
$total = 0;
$marks = 0;
$count = count($resultsData);
foreach ($resultsData as $result) {
    $total += $result['total'];
    $marks += $result['marks'];
}
$grade_percentage = $count > 0 ? $marks / $total*100 : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notices - Student Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
</head>
<body class="bg-gray-50">
    <?php include 'student_nav.php'; ?>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p class="text-gray-600 mt-1">Here's your academic overview</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Attendance Stats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-gray-700">Overall Attendance</h3>
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2"><?php echo number_format($attendance_percentage, 1); ?>%</div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo $attendance_percentage; ?>%"></div>
                </div>
                <p class="text-sm text-gray-500"><?php echo $total_attended; ?> out of <?php echo $total_classes; ?> classes attended</p>
            </div>

            <!-- Grade Stats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-gray-700">Overall Grade</h3>
                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2"><?php echo number_format($grade_percentage, 1); ?>%</div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: <?php echo $grade_percentage; ?>%"></div>
                </div>
                <p class="text-sm text-gray-500">Average across all subjects</p>
            </div>
        </div>

        <!-- Notices Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Recent Notices</h2>
                <p class="text-sm text-gray-600 mt-1">Important announcements and updates</p>
            </div>
            <div class="divide-y divide-gray-200">
                <?php foreach ($notices as $notice): ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($notice['topic']); ?></h3> 
                        
                                <p class="text-gray-600 text-sm mb-3"><?php echo nl2br(htmlspecialchars($notice['body'])); ?></p>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <?php echo date('M d, Y', strtotime($notice['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

// yesle real-time subscription set up garxa Notices, Attendance ra Results table ko lagi anon key use garera
<script>
// yesle anon key use garera Supabase client initialize garxa
const supabase = window.supabase.createClient('https://lvsogpbcuauofmjsqrde.supabase.co', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imx2c29ncGJjdWF1b2ZtanNxcmRlIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjUyOTIwNzQsImV4cCI6MjA4MDg2ODA3NH0.lwUFlN-Ba8uheoF3kB1rwRDEYBSYt0Ay11TEpZJm_0g');

// yesle logged-in user ko ID pass garxa subscription ma
const userId = '<?php echo $_SESSION['id']; ?>';

// yesle Notices table ko kahi changes bhayo bhane page reload garxa
const noticesChannel = supabase
  .channel('notices_changes')
  .on('postgres_changes', { event: '*', schema: 'public', table: 'Notices' }, (payload) => {
    location.reload();
  })
  .subscribe();

// yesle current user ko Attendance data change bhayo bhane page reload garxa
const attendanceChannel = supabase
  .channel('attendance_changes_home')
  .on('postgres_changes', { event: '*', schema: 'public', table: 'Attendance', filter: `id=eq.${userId}` }, (payload) => {
    location.reload();
  })
  .subscribe();

// yesle current user ko Results data change bhayo bhane page reload garxa
const resultsChannel = supabase
  .channel('results_changes_home')
  .on('postgres_changes', { event: '*', schema: 'public', table: 'Results', filter: `id=eq.${userId}` }, (payload) => {
    location.reload();
  })
  .subscribe();
</script>

</body>
</html>