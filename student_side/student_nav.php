<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Load Font Awesome (used for nav icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Logo (hidden on mobile) -->
            <div class="hidden md:flex items-center">
                <div class="flex items-center gap-2">
                    <div class="h-10 w-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white text-xl" aria-hidden="true"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Student Portal</span>
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex items-center gap-1 md:gap-2 justify-between md:justify-end flex-1 overflow-x-auto">

                <?php
                $nav_items = [
                    ['page' => 'home.php', 'label' => 'Home'],
                    ['page' => 'attendance.php', 'label' => 'Attendance'],
                    ['page' => 'results.php', 'label' => 'Results'],
                    ['page' => 'profile.php', 'label' => 'Profile'],
                ];

                foreach ($nav_items as $item):
                    $active = $current_page === $item['page']
                        ? 'bg-blue-50 text-blue-600'
                        : 'text-gray-600 hover:bg-gray-100';
                ?>
                    <a href="<?= $item['page']; ?>"
                       class="flex items-center gap-1 md:gap-2 px-2 md:px-4 py-2 rounded-lg transition-colors <?= $active; ?>">
                        <!-- Icon -->
                        <?php if ($item['page'] === 'attendance.php'): ?>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <?php elseif ($item['page'] === 'results.php'): ?>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <?php elseif ($item['page'] === 'home.php'): ?>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.75L12 3l9 6.75V20a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4a1 1 0 00-1-1h-2a1 1 0 00-1 1v4a1 1 0 01-1 1H4a1 1 0 01-1-1V9.75z"/></svg>
                        <?php else: ?>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <?php endif; ?>

                        <!-- Text (hidden on mobile) -->
                        <span class="hidden sm:inline"><?= $item['label']; ?></span>
                    </a>
                <?php endforeach; ?>

                <!-- Logout -->
                <a href="logout.php"
                   class="flex items-center gap-1 md:gap-2 px-2 md:px-4 py-2 rounded-lg
                          text-red-600 hover:bg-red-50 transition-colors ml-1 md:ml-2">
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
