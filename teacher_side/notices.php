<?php
include '../project/supabase.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['id'])){
    header('Location:../project/login.php');
}

// yesle session ko username anusar teacher ko notices fetch garxa
$notices = fetchData('Notices','created_by=eq.'.urlencode($_SESSION['username']));

// Handle form submissions
// yesle create / edit / delete ka request haru lai process garxa
$success_message = '';
$error_message = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            // yesle naya notice DB ma save garxa
            $topic = trim($_POST['topic'] ?? '');
            $body = trim($_POST['body'] ?? '');

            if (!empty($topic) && !empty($body)) {
                // Save to database
                $payload = [
                    'topic' => $topic,
                    'body' => $body,
                    'created_by' => $_SESSION['username']
                ];
                $result = addData('Notices', $payload);
                if (isset($result['error'])) {
                    $error_message = 'Failed to create notice.';
                } else {
                    $success_message = 'Notice created successfully!';
                }
            } else {
                $error_message = 'Please fill in all required fields.';
            }
        } elseif ($_POST['action'] === 'edit') {
            // yesle diyo bhayeko id anusar notice update garxa
            $id = intval($_POST['id'] ?? 0);
            $topic = trim($_POST['topic'] ?? '');
            $body = trim($_POST['body'] ?? '');
            if ($id && !empty($topic) && !empty($body)) {
                $data = ['topic' => $topic, 'body' => $body];
                $result = updateData('Notices', 'id=eq.' . $id, $data);
                if (isset($result['error'])) {
                    $error_message = 'Failed to update notice.';
                } else {
                    $success_message = 'Notice updated successfully!';
                }
            } else {
                $error_message = 'Please fill in all required fields for editing.';
            }
        } elseif ($_POST['action'] === 'delete') {
            // yesle id anusar notice delete garxa
            $id = intval($_POST['id'] ?? 0);
            if ($id) {
                $result = deleteData('Notices', 'id=eq.' . $id);
                if (isset($result['error'])) {
                    $error_message = 'Failed to delete notice.';
                } else {
                    $success_message = 'Notice deleted successfully!';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notices - Teacher Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <?php include 'includes/nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Manage Notices</h1>
            <p class="text-gray-600">Create, edit, and delete notices for students</p>
        </div>

        <?php if ($success_message): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span><?php echo htmlspecialchars($success_message); ?></span>
        </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo htmlspecialchars($error_message); ?></span>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Create Notice Form -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Create New Notice</h2>
                
                <form method="POST" action="notices.php">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="space-y-4">
                        <div>
                            <label for="topic" class="block text-sm font-medium text-gray-700 mb-2">
                                Notice Topic <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="topic" 
                                name="topic" 
                                required
                                placeholder="Enter notice topic"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
                            />
                        </div>

                        <!-- Priority removed -->

                        <div>
                            <label for="body" class="block text-sm font-medium text-gray-700 mb-2">
                                Notice Body <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="body" 
                                name="body" 
                                required
                                rows="8"
                                placeholder="Enter notice description..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none resize-none"
                            ></textarea>
                            <p class="text-sm text-gray-500 mt-2">
                                <i class="fas fa-info-circle"></i> This notice will be visible to all students
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <button 
                                type="submit" 
                                class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2"
                            >
                                <i class="fas fa-plus"></i>
                                Create Notice
                            </button>
                            <button 
                                type="reset" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Existing Notices -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">My Notices</h2>
                </div>
                
                <div class="divide-y divide-gray-200 max-h-[600px] overflow-y-auto">
                    <?php if (empty($notices)): ?>
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>No notices yet. Create your first notice!</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($notices as $notice): ?>
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($notice['topic']); ?></h3>
                                          
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2"><?php echo htmlspecialchars($notice['body']); ?></p>
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($notice['created_at'])); ?>
                                    </p>
                                </div>
                                
                                <div class="flex gap-2">
                                    <button 
                                        onclick="editNotice(<?php echo $notice['id']; ?>)"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="Edit"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="notices.php" class="inline" onsubmit="return confirm('Are you sure you want to delete this notice?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $notice['id']; ?>">
                                        <button 
                                            type="submit"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Delete"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Notice Modal -->
    <!-- yesle modal kholyo bhane form pre-fill ra edit submit garna milxa -->
    <div id="edit-notice-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg w-full max-w-xl p-6 relative">
            <button id="edit-modal-close" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
            <h3 class="text-lg font-semibold mb-4">Edit Notice</h3>
            <form id="edit-notice-form" method="POST" action="notices.php">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit-id" name="id" value="">
                <div class="mb-4">
                    <label for="edit-topic" class="block text-sm font-medium text-gray-700 mb-2">Topic</label>
                    <input id="edit-topic" name="topic" type="text" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="edit-body" class="block text-sm font-medium text-gray-700 mb-2">Body</label>
                    <textarea id="edit-body" name="body" rows="6" class="w-full px-4 py-2 border rounded-lg" required></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" id="edit-cancel" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Save Changes</button> 
                </div>
            </form>
        </div>
    </div>

    <script>
        // yesle server bata aayeko notices array lai JS ma rakcha
        const notices = <?php echo json_encode($notices); ?> || []; 

        // yesle modal kholxa
        function openEditModal() {
            document.getElementById('edit-notice-modal').classList.remove('hidden');
        }
        // yesle modal band garxa
        function closeEditModal() {
            document.getElementById('edit-notice-modal').classList.add('hidden');
        }

        // yesle diyeko id ko notice modal ma set garera dekhauncha
        function editNotice(id) {
            if (typeof notices === 'undefined' || !Array.isArray(notices)) {
                // yesle notices data chaina bhane user lai bhannu parcha
                alert('Notices data missing. Please refresh the page.');
                return;
            }
            const notice = notices.find(n => n.id == id);//yesle id sanga milni notice khojxa
            if (!notice) {
                alert('Notice not found.');
                return;
            }
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-topic').value = notice.topic || '';
            document.getElementById('edit-body').value = notice.body || '';
            openEditModal();
        }

        document.getElementById('edit-modal-close').addEventListener('click', closeEditModal);
        document.getElementById('edit-cancel').addEventListener('click', closeEditModal);

        // Close on outside click
        document.getElementById('edit-notice-modal').addEventListener('click', function (e) {
            if (e.target === this) closeEditModal();
        });


        // Update is handled by the form submit back to server (no client-side update function)

  
    </script>
</body>
</html>