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

$subjects = fetchData('Subjects');
$students = fetchData('StudentProfile');
$attendance = fetchData('Attendance');

// yesle nayaa subject database ma add garna ko lagi form submit handle garxa
$add_subject_success = '';
$add_subject_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_subject') {
    // yesle input ko whitespace hatayera subject code ra name lina sakxa
    $sub_code = trim($_POST['sub_code'] ?? '');
    $sub_name = trim($_POST['sub_name'] ?? '');
    
    // yedi dono field ma data cha bhane database ma add garxa
    if (!empty($sub_code) && !empty($sub_name)) {
        // yesle Subjects table ma nayaa subject insert garxa
        $result = addData('Subjects', [
            'sub_code' => $sub_code,
            'sub_name' => $sub_name
        ]);
        
        // yesle JSON format ma response pathayo ta frontend le process garna sakincha
        header('Content-Type: application/json');
        if (isset($result['error'])) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $result['error']]);
        } else {
            // yedi subject successfully add bhayo bhane subject details return garxa
            echo json_encode(['success' => true, 'message' => 'Subject added successfully!', 'sub_code' => $sub_code, 'sub_name' => $sub_name]);
        }
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Subject code and name are required']);
        exit;
    }
}

// Map id to roll_no and calculate attendance
foreach ($students as &$student) {
    $student['roll_no'] = $student['id'];
    
    // Calculate overall attendance
    $total_attended = 0;
    $total_classes = 0;
    foreach ($attendance as $att) {
        if ($att['id'] == $student['id']) {
            $total_attended += $att['attended'];
            $total_classes += $att['total'];
        }
    }
    $student['attendance'] = $total_classes > 0 ? round(($total_attended / $total_classes) * 100) : 0;
} 

// Handle form submission
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'] ?? '';
    $date = $_POST['date'] ?? '';
    $absent_students = $_POST['absent'] ?? [];
    
    if (!empty($subject) && !empty($date)) {
        foreach ($students as $student) {
            $is_present = !in_array($student['id'], $absent_students);
            
            // Check if attendance record exists for this student and subject
            $existing = fetchData('Attendance', "id=eq.{$student['id']}&sub_code=eq.{$subject}");
            
            if (!empty($existing) && !isset($existing['error'])) {
                // Update existing record
                $current_attended = $existing[0]['attended'];
                $current_total = $existing[0]['total'];
                $new_attended = $current_attended + ($is_present ? 1 : 0);
                $new_total = $current_total + 1;
                
                updateData('Attendance', "id=eq.{$student['id']}&sub_code=eq.{$subject}", [
                    'attended' => $new_attended,
                    'total' => $new_total
                ]);
            } else {
                // Insert new record
                addData('Attendance', [
                    'sub_code' => $subject,
                    'id' => $student['id'],
                    'attended' => $is_present ? 1 : 0,
                    'total' => 1
                ]);
            }
        }
        
        $present_count = count($students) - count($absent_students);
        $success_message = "Attendance recorded successfully! Present: $present_count, Absent: " . count($absent_students);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Attendance - Teacher Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <?php include 'includes/nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Take Attendance</h1>
            <p class="text-gray-600">Mark student attendance for your classes</p>
        </div>

        <?php if ($success_message): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span><?php echo htmlspecialchars($success_message); ?></span>
        </div>
        <?php endif; ?>

        <form method="POST" action="attendance.php">
            <div class="space-y-6">
                <!-- Selection Card -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Class Details</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Subject <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2 items-center">
                                <select 
                                    id="subject" 
                                    name="subject" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
                                >
                                    <option value="">Choose a subject</option>
                                    <?php foreach ($subjects as $subject): ?>
                                    <option value="<?php echo htmlspecialchars($subject['sub_code']); ?>"><?php echo htmlspecialchars($subject['sub_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <button type="button" id="add-subject-toggle" onclick="toggleAddSubject()" class="ml-2 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                                    Add
                                </button>
                            </div>

                            <!-- Add Subject Form (hidden by default) -->
                            <div id="add-subject-form" class="mt-3 p-4 border border-gray-200 rounded-lg bg-gray-50 hidden">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label for="new-subject-code" class="block text-xs font-medium text-gray-600 mb-1">Subject Code</label>
                                        <input id="new-subject-code" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" placeholder="e.g., CS401">
                                    </div>
                                    <div>
                                        <label for="new-subject-name" class="block text-xs font-medium text-gray-600 mb-1">Subject Name</label>
                                        <input id="new-subject-name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" placeholder="e.g., Machine Learning">
                                    </div>
                                </div>
                                <p id="add-subject-error" class="text-sm text-red-600 mt-2 hidden">Both fields are required.</p>
                                <div class="mt-3 flex gap-2 justify-end">
                                    <button type="button" onclick="clearAddSubjectForm()" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm hover:bg-gray-100">Clear</button>
                                    <button type="button" onclick="addSubject()" class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">Add</button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                                Date <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                id="date" 
                                name="date" 
                                required
                                value="<?php echo date('Y-m-d'); ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
                            />
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex gap-3">
                    <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                    <div>
                        <p class="text-blue-900 font-medium">How to mark attendance:</p>
                        <p class="text-blue-700 text-sm mt-1">All students are marked PRESENT by default. Toggle the switch for students who are ABSENT.</p>
                    </div>
                </div>

                <!-- Students List -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-900">Student List</h2>
                            <span class="text-sm text-gray-600">Total: <?php echo count($students); ?> students</span>
                        </div>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        <?php foreach ($students as $student): ?>
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <span class="text-indigo-600 font-medium">
                                            <?php echo strtoupper(substr($student['name'], 0, 1)); ?>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($student['name']); ?></p>
                                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($student['roll_no']); ?></p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-4">
                                    <span class="text-sm text-gray-600">
                                        Attendance: <span class="font-medium"><?php echo $student['attendance']; ?>%</span>
                                    </span>
                                    
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            name="absent[]" 
                                            value="<?php echo $student['id']; ?>" 
                                            class="sr-only peer"
                                            onchange="updateLabel(this)"
                                        />
                                        <div class="w-14 h-7 bg-green-500 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-red-500"></div>
                                        <span class="ml-3 text-sm font-medium status-label text-green-700">PRESENT</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-4">
                    <button 
                        type="reset" 
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Reset
                    </button>
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2"
                    >
                        <i class="fas fa-save"></i>
                        Save Attendance
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function updateLabel(checkbox) {
            const label = checkbox.parentElement.querySelector('.status-label');
            if (checkbox.checked) {
                label.textContent = 'ABSENT';
                label.classList.remove('text-green-700');
                label.classList.add('text-red-700');
            } else {
                label.textContent = 'PRESENT';
                label.classList.remove('text-red-700');
                label.classList.add('text-green-700');
            }
        }

        // Add Subject form handlers
        function toggleAddSubject() {
            const form = document.getElementById('add-subject-form');
            form.classList.toggle('hidden');
            // focus first field when showing
            if (!form.classList.contains('hidden')) {
                document.getElementById('new-subject-code').focus();
            }
        }

        function clearAddSubjectForm() {
            document.getElementById('new-subject-code').value = '';
            document.getElementById('new-subject-name').value = '';
            document.getElementById('add-subject-error').classList.add('hidden');
        }

        function addSubject() {
            const code = document.getElementById('new-subject-code').value.trim();
            const name = document.getElementById('new-subject-name').value.trim();
            const err = document.getElementById('add-subject-error');
            
            if (!code || !name) {
                err.textContent = 'Both subject code and name are required';
                err.classList.remove('hidden');
                return;
            }

            // Send to database via AJAX
            const formData = new FormData();
            formData.append('action', 'add_subject');
            formData.append('sub_code', code);
            formData.append('sub_name', name);

            fetch('attendance.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response:', data);
                if (data.success) {
                    // Add to select dropdown
                    const select = document.getElementById('subject');
                    const option = document.createElement('option');
                    option.value = data.sub_code;
                    option.textContent = `${data.sub_name} `;
                    select.appendChild(option);
                    select.value = data.sub_code;

                    clearAddSubjectForm();
                    document.getElementById('add-subject-form').classList.add('hidden');
                    
                    // Show success message
                    alert('Subject added successfully!');
                } else {
                    err.textContent = data.message || 'Error adding subject';
                    err.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                err.textContent = 'Error: ' + error.message;
                err.classList.remove('hidden');
            });
        }
    </script>
</body>
</html>