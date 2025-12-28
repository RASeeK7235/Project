<nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between md:justify-between h-16">
            <div class="hidden md:flex items-center">
                <div class="flex items-center gap-2">
                    <div class="h-10 w-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white text-xl"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Teacher Portal</span>
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex items-center gap-1 md:gap-2 md:justify-end justify-between flex-1 overflow-x-auto">
                <?php
                $current_page = basename($_SERVER['PHP_SELF']);
                $nav_items = [
                    ['page' => 'profile.php', 'label' => 'Profile', 'icon' => 'fa-user'],
                    ['page' => 'attendance.php', 'label' => 'Attendance', 'icon' => 'fa-calendar'],
                    ['page' => 'results.php', 'label' => 'Results', 'icon' => 'fa-file-alt'],
                    ['page' => 'notices.php', 'label' => 'Notices', 'icon' => 'fa-bell'],
                    ['page' => 'students.php', 'label' => 'Students', 'icon' => 'fa-users']
                ];

                foreach ($nav_items as $item):
                    $active = ($current_page === $item['page']) ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100';
                ?>
                    <a href="<?php echo $item['page']; ?>" class="flex items-center gap-1 md:gap-2 px-2 md:px-4 py-2 rounded-lg transition-colors <?php echo $active; ?>">
                        <i class="fas <?php echo $item['icon']; ?>"></i>
                        <span class="hidden sm:inline"><?php echo $item['label']; ?></span>
                    </a>
                <?php endforeach; ?>

                <a href="logout.php" class="flex items-center gap-1 md:gap-2 px-2 md:px-4 py-2 rounded-lg text-red-600 hover:bg-red-50 transition-colors ml-1 md:ml-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7"></path>
                    </svg>
                    <span class="hidden sm:inline">Logout</span>
                </a>
            </div>
        </div>
    </div>


</nav>
