<?php
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];

$project_folder = explode('/', trim($_SERVER['REQUEST_URI'], '/'))[0];
$base_url .= '/' . $project_folder;
?>
<header class="bg-white shadow-md sticky top-0 z-50">
    <!-- Top Bar: Contact & Quick Links -->
    <div class="bg-gray-900 text-gray-300 text-xs sm:text-sm py-2 px-4 sm:px-8 border-b border-gray-800">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-6">
                <a href="tel:+18005550199" class="hover:text-amber-400 transition flex items-center gap-2">
                    <i class="fa-solid fa-phone text-amber-400"></i>
                    <span>+1 (800) 555-0199</span>
                </a>
                <span class="hidden md:inline text-gray-600">|</span>
                <span class="hidden md:flex items-center gap-2 text-gray-400">
                    <i class="fa-solid fa-clock text-amber-400"></i>
                    24/7 Roadside Assistance
                </span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="/my-bookings.php" class="hover:text-amber-400 transition flex items-center gap-1">
                    <i class="fa-solid fa-calendar-check text-amber-400"></i>
                    <span>My Bookings</span>
                </a>
                <a href="/login.php"
                    class="bg-amber-500 hover:bg-amber-600 text-gray-900 font-semibold px-3 py-1 rounded transition text-xs">
                    Sign In
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="max-w-7xl mx-auto px-4 sm:px-8 py-4 flex justify-between items-center">
        <!-- Logo -->
        <a href="/index.php" class="flex items-center space-x-2 group">
            <div class="bg-amber-500 text-gray-900 p-2.5 rounded-lg group-hover:bg-amber-600 transition">
                <i class="fa-solid fa-car-side text-2xl"></i>
            </div>
            <span class="text-2xl font-black text-gray-900 tracking-tight">
                Drive<span class="text-amber-500">Ease</span>
            </span>
        </a>

        <!-- Navigation Links -->
        <ul class="hidden md:flex items-center space-x-8 font-medium text-gray-700">
            <li><a href="/index.php"
                    class="hover:text-amber-500 transition py-2 border-b-2 border-transparent hover:border-amber-500">Home</a>
            </li>
            <li><a href="/vehicles.php"
                    class="hover:text-amber-500 transition py-2 border-b-2 border-transparent hover:border-amber-500">Fleet</a>
            </li>
            <li><a href="/services.php"
                    class="hover:text-amber-500 transition py-2 border-b-2 border-transparent hover:border-amber-500">Services</a>
            </li>
            <li><a href="/about.php"
                    class="hover:text-amber-500 transition py-2 border-b-2 border-transparent hover:border-amber-500">About</a>
            </li>
            <li><a href="/contact.php"
                    class="hover:text-amber-500 transition py-2 border-b-2 border-transparent hover:border-amber-500">Contact</a>
            </li>
        </ul>

        <!-- CTA Button -->
        <div class="hidden md:block">
            <a href="/vehicles.php"
                class="bg-gray-900 hover:bg-amber-500 hover:text-gray-900 text-white font-semibold px-5 py-2.5 rounded-lg shadow transition">
                Book Now
            </a>
        </div>

        <!-- Mobile Menu Button -->
        <button id="mobile-menu-btn"
            class="md:hidden text-gray-700 focus:outline-none p-2 rounded-lg hover:bg-gray-100">
            <i class="fa-solid fa-bars text-2xl"></i>
        </button>
    </nav>

    <!-- Mobile Dropdown Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200 px-4 pt-2 pb-6 space-y-3">
        <a href="/index.php" class="block py-2 text-gray-700 hover:text-amber-500 font-medium">Home</a>
        <a href="/vehicles.php" class="block py-2 text-gray-700 hover:text-amber-500 font-medium">Fleet</a>
        <a href="/services.php" class="block py-2 text-gray-700 hover:text-amber-500 font-medium">Services</a>
        <a href="/about.php" class="block py-2 text-gray-700 hover:text-amber-500 font-medium">About</a>
        <a href="/contact.php" class="block py-2 text-gray-700 hover:text-amber-500 font-medium">Contact</a>
        <div class="pt-2">
            <a href="/vehicles.php"
                class="block text-center bg-amber-500 hover:bg-amber-600 text-gray-900 font-semibold py-2.5 rounded-lg shadow">
                Book a Vehicle
            </a>
        </div>
    </div>
</header>