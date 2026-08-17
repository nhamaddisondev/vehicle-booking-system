<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$title = isset($pageTitle) ? $pageTitle : 'Admin Dashboard - Vehicle Booking System';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title); ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Heroicons / FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Alpine.js for lightweight interactivity (dropdowns/sidebar) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="h-full flex flex-col font-sans text-gray-800" x-data="{ sidebarOpen: false }">
    <header class="bg-gray-900 text-white sticky top-0 z-30 shadow-md">
        <div class="flex items-center justify-between px-4 py-3">
            <!-- Brand & Mobile Toggle -->
            <div class="flex items-center space-x-3">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="md:hidden text-gray-300 hover:text-white focus:outline-none">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <a href="/admin/index.php"
                    class="text-xl font-bold tracking-wide flex items-center space-x-2 text-indigo-400">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>Admin Panel</span>
                </a>
            </div>

            <!-- Top Right Nav -->
            <div class="flex items-center space-x-4">
                <a href="../index.php" target="_blank"
                    class="hidden sm:flex items-center text-sm text-gray-300 hover:text-white transition">
                    <i class="fa-solid fa-arrow-up-right-from-square mr-2"></i> View Website
                </a>

                <!-- User Dropdown Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center space-x-2 text-sm text-gray-300 hover:text-white focus:outline-none">
                        <i class="fa-solid fa-circle-user text-2xl"></i>
                        <span class="hidden md:inline font-medium">Administrator</span>
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </button>

                    <!-- Dropdown Items -->
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-md shadow-lg py-1 z-50 border border-gray-100"
                        style="display: none;">
                        <a href="/users/profile.php" class="block px-4 py-2 text-sm hover:bg-gray-100">
                            <i class="fa-solid fa-user-gear mr-2 text-gray-500"></i> Profile Settings
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="/auth/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-20 w-64 bg-gray-900 text-gray-300 transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0 flex flex-col justify-between pt-16 md:pt-0">
            <div class="py-4 px-3 space-y-1">
                <a href="/admin/index.php"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium bg-gray-800 text-white">
                    <i class="fa-solid fa-chart-line w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/bookings/index.php"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-800 hover:text-white transition">
                    <i class="fa-solid fa-calendar-check w-5"></i>
                    <span>Bookings</span>
                </a>
                <a href="/vehicles/index.php"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-800 hover:text-white transition">
                    <i class="fa-solid fa-car w-5"></i>
                    <span>Vehicles</span>
                </a>
                <a href="/categories/index.php"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-800 hover:text-white transition">
                    <i class="fa-solid fa-layer-group w-5"></i>
                    <span>Categories</span>
                </a>
                <a href="/users/index.php"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-800 hover:text-white transition">
                    <i class="fa-solid fa-users w-5"></i>
                    <span>Users & Drivers</span>
                </a>
                <a href="/admin/settings.php"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-800 hover:text-white transition">
                    <i class="fa-solid fa-gear w-5"></i>
                    <span>Settings</span>
                </a>
            </div>
        </aside>
</body>

</html>