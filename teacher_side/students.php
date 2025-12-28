<?php
include '../project/supabase.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id'])) {
    header('Location:../project/login.php');
}

$students = fetchData("StudentProfile"); //fetching students from database
$attendance = fetchData("Attendance");
$results = fetchData("Results");


function getGrade($percentage)
{
    if ($percentage >= 90) return 'A+';
    if ($percentage >= 80) return 'A';
    if ($percentage >= 70) return 'B+';
    if ($percentage >= 60) return 'B';
    if ($percentage >= 50) return 'C+';
    if ($percentage >= 40) return 'C';
    return 'F';
}

// Precompute attendance and average grade for each student so the client JS can use them
// yesle harek student ko attendance ra avg grade pahile batai rakxa, JS le seedha use garna sakincha
foreach ($students as &$s) {
    // Ensure roll number exists
    // yesle roll_no nagareko bhaye id use garera roll_no set garxa
    if (!isset($s['roll_no']) || empty($s['roll_no'])) {
        $s['roll_no'] = $s['id'];
    }

    // Attendance
    // yesle attendance calculate garera percentage ma store garxa
    $s['attendance'] = 0;
    foreach ($attendance as $att) {
        if ($att['id'] == $s['id']) {
            if (!empty($att['total'])) {
                $s['attendance'] = round(($att['marks'] / $att['total']) * 100);
            } else {
                $s['attendance'] = 0;
            }
            break;
        }
    }

    // Average grade (percentage -> grade letter)
    // yesle results haru bata avg percentage calculate garera grade letter assign garxa
    $total_percentage = 0;
    $count = 0;
    foreach ($results as $res) {
        if ($res['id'] == $s['id'] && !empty($res['total'])) {
            $percentage = ($res['marks'] / $res['total']) * 100;
            $total_percentage += $percentage;
            $count++;
        }
    }
    $average_percentage = $count > 0 ? round($total_percentage / $count) : 0;
    $s['avg_grade'] = getGrade($average_percentage);

    // Optional fields defaults
    // yesle missing optional fields haru lai blank default value set garera modal ma N/A dekhauna milos bhanera
    $defaults = ['phone'=>'','address'=>'','date_of_birth'=>'','program'=>'','enrollment_date'=>'','semester'=>'','guardian_name'=>'','guardian_phone'=>'','email'=>''];
    foreach ($defaults as $k=>$v) {
        if (!isset($s[$k])) $s[$k] = $v;
    }
}
unset($s);

// Handle search
$search_term = $_GET['search'] ?? '';
$filtered_students = $students;

if (!empty($search_term)) {
    $filtered_students = array_filter($students, function ($student) use ($search_term) { //yesle sabai students ma gayera search term match hunxa ki nai bhanera herxa
        return stripos($student['name'], $search_term) !== false ||
            stripos($student['id'], $search_term) !== false ||
            stripos($student['email'], $search_term) !== false;
    }); //yesle filtered_students ma matra tyo student haru rakxa jun search term sanga match hunxa
}

// Get student details for modal
$view_student_id = $_GET['view'] ?? null;
$selected_student = null;
if ($view_student_id) {
    foreach ($students as $student) {
        if ($student['id'] == $view_student_id) {
            $selected_student = $student;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students - Teacher Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Modal overlay */ /* yesle modal overlay ra animation manage garxa */
        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            transition: opacity 0.18s ease-in-out;
            opacity: 0;
        }

        .modal.active {
            display: flex;
            opacity: 1;
        }

        /* Modal content (centered card) */
        .modal-content {
            background-color: white;
            margin: 16px;
            padding: 0;
            border-radius: 12px;
            width: min(96%, 900px);
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 12px 40px rgba(15, 23, 42, 0.25);
            animation: slideDown 0.28s cubic-bezier(.2,.9,.3,1) both;
        }

        .modal-header {
            padding: 18px 20px;
            border-bottom: 1px solid #edf2f7;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 640px) {
            .modal-grid {
                grid-template-columns: 1fr;
            }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Mobile view: hide Email, Attendance, Avg. Grade columns (keep Actions visible) */
        @media (max-width: 550px) {

            .student-table th:nth-child(3),
            .student-table th:nth-child(4),
            .student-table th:nth-child(5),
            .student-table td:nth-child(3),
            .student-table td:nth-child(4),
            .student-table td:nth-child(5) {
                display: none;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <?php include 'includes/nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">View Students</h1>
            <p class="text-gray-600">Browse and view student information</p>
        </div>

        <!-- Search Bar -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 mb-6">
            <form method="GET" action="students.php" class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input
                    type="text"
                    name="search"
                    value="<?php echo htmlspecialchars($search_term); ?>"
                    placeholder="Search by name, roll number, or email..."
                    class="w-full pl-11 pr-20 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none" />

                <button type="submit" aria-label="Search" class="absolute right-3 top-1/2 transform -translate-y-1/2 z-10 inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <i class="fas fa-search"></i>
                    <span>Search</span>
                </button>
            </form>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mb-6">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <p class="text-sm text-gray-600">Total Students</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?php echo count($students); ?></p>
            </div>
        </div>

        <!-- Students Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Student List</h2>
                <p class="text-sm text-gray-600 mt-1">Click on a student to view their complete profile (read-only)</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full student-table">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Roll No</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Name</th>
                          
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Email</th>
                            <th class="px-6 py-3 text-center text-sm font-medium text-gray-700">Attendance</th>
                            <th class="px-6 py-3 text-center text-sm font-medium text-gray-700">Avg. Grade</th>
                            <th class="px-6 py-3 text-right text-sm font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($filtered_students)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No students found matching your search.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filtered_students as $student): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-300">
                                            <?php echo htmlspecialchars($student['id']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">

                                        <span class="text-gray-900"><?php echo htmlspecialchars($student['name']); ?></span>
            </div>
            </td>

            <td class="px-6 py-4">
                <span class="text-sm text-gray-600"><?php echo htmlspecialchars($student['email']); ?></span>
            </td>
            <td class="px-6 py-4 text-center">
                <?php
                                // yesle attendance ko color determine garxa percentage ko adhar ma
                                $attendance_color = 'text-red-600';
                                $attendance_val = isset($student['attendance']) ? $student['attendance'] : 0;
                                if ($attendance_val >= 85) {
                                    $attendance_color = 'text-green-600';
                                } elseif ($attendance_val >= 75) {
                                    $attendance_color = 'text-yellow-600';
                                }
                ?>
                <span class="font-medium <?php echo $attendance_color; ?>">
                    <?php echo $attendance_val; ?>%
                </span>
            </td>
            <td class="px-6 py-4 text-center">
                <!-- yesle avg grade letter display garxa -->
                <span class="font-medium text-gray-900"><?php echo htmlspecialchars($student['avg_grade']); ?></span>
            </td>
            <td class="px-6 py-4 text-right">
                <!-- yesle button le student id pass garera modal kholxa -->
                <button
                    onclick="openModal('<?php echo htmlspecialchars($student['id'], ENT_QUOTES); ?>')"
                    aria-label="View student details"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                    <i class="fas fa-eye"></i>
                    <span class="hidden sm:inline">View</span>
                </button>
            </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
    </table>
        </div>
    </div>
    </div>

    <!-- Student Profile Modal -->
    <div id="studentModal" class="modal" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="studentModalTitle"> <!-- yesle modal accessibility manage garxa -->
        <div class="modal-content" role="document">
            <div id="modalBody" class="modal-body">
                <!-- Content will be loaded here -->
                <!-- yesle bottom close button remove gareko cha -->
            </div>
        </div>
    </div>

    <script>
        const students = <?php echo json_encode($students); ?>; // yesle php bata students JSON ma convert garera JS ma pathauncha

        function openModal(studentId) {
            // yesle id match garera student find garxa
            const student = students.find(s => String(s.id) === String(studentId));
            if (!student) return;

            const modal = document.getElementById('studentModal');
            const modalBody = document.getElementById('modalBody');

            // Get initials safely
            const initials = ((student.name || '').trim().split(/\s+/).map(n => n[0] || '').join('') || 'NA').toUpperCase();

            // Determine attendance color
            let attendanceColor = 'text-red-600';
            const attendanceVal = Number(student.attendance || 0);
            if (attendanceVal >= 85) {
                attendanceColor = 'text-green-600';
            } else if (attendanceVal >= 75) {
                attendanceColor = 'text-yellow-600';
            }

            // yesle modal ko HTML dynamic bhayera set garxa
            modalBody.innerHTML = `
                <div class="modal-header">
                    <div>
                        <h2 id="studentModalTitle" class="text-lg font-semibold text-gray-900">Student Profile</h2>
                        <p class="text-sm text-gray-500">Read-only view of selected student</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="closeModalBtn" aria-label="Close student profile" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                            <i class="fas fa-times text-gray-600"></i>
                        </button>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="modal-grid">
                        <div class="bg-gray-50 rounded-lg p-4 flex flex-col items-start gap-4">
                            <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center">
                                <span class="text-indigo-600 text-2xl font-bold">${initials}</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">${student.name || 'N/A'}</h3>
                                <p class="text-sm text-gray-600 mt-1">Roll: <span class="font-medium text-gray-800">${student.roll_no || student.id}</span></p>
                            </div>

                            <div class="w-full grid grid-cols-2 gap-2 mt-2">
                                <div class="bg-white border rounded-lg p-3">
                                    <p class="text-xs text-gray-500">Attendance</p>
                                    <p class="text-xl font-bold ${attendanceColor}">${attendanceVal}%</p>
                                </div>
                                <div class="bg-white border rounded-lg p-3">
                                    <p class="text-xs text-gray-500">Average Grade</p>
                                    <p class="text-xl font-bold text-gray-900">${student.avg_grade}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="grid grid-cols-1 gap-3">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-envelope text-gray-400 mt-1"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Email</p>
                                        <p class="text-sm text-gray-900">${student.email || 'N/A'}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-phone text-gray-400 mt-1"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Phone</p>
                                        <p class="text-sm text-gray-900">${student.phone || 'N/A'}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-map-marker-alt text-gray-400 mt-1"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Address</p>
                                        <p class="text-sm text-gray-900">${student.address || 'N/A'}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-calendar text-gray-400 mt-1"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Date of Birth</p>
                                        <p class="text-sm text-gray-900">${student.dob|| 'N/A'}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-book-open text-gray-400 mt-1"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Program</p>
                                        <p class="text-sm text-gray-900">${student.program || 'N/A'}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-user text-gray-400 mt-1"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Guardian</p>
                                        <p class="text-sm text-gray-900">${student.guardian_name || 'N/A'} • ${student.guardian_phone || ''}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            `;

            // Hook up the close button and accessibility attributes
            const closeBtn = document.getElementById('closeModalBtn');
            closeBtn.addEventListener('click', closeModal);
            // yesle close button le modal band garxa

            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            // prevent background scroll
            // yesle background scroll lock garera focus modal ma rakcha
            document.body.style.overflow = 'hidden';

            // focus management
            // yesle close button lai focus garera accessibility improve garxa
            closeBtn.focus();
        }

        function closeModal() {
            const modal = document.getElementById('studentModal');
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
            // allow background scroll
            // yesle background scroll lock hatayera page normal garxa
            document.body.style.overflow = '';
            // remove modal contents after a short delay so animation can finish
            // yesle modal ko content clear garxa after animation
            setTimeout(() => {
                const modalBody = document.getElementById('modalBody');
                if (modalBody) modalBody.innerHTML = '';
            }, 220);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('studentModal');
            if (event.target === modal && modal.classList.contains('active')) {
                closeModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('studentModal');
                if (modal && modal.classList.contains('active')) closeModal();
            }
        });
    </script>
</body>

</html>