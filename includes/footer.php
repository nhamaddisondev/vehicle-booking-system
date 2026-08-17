<?php
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];

$project_folder = explode('/', trim($_SERVER['REQUEST_URI'], '/'))[0];
$base_url .= '/' . $project_folder;
?>
<footer class="bg-gray-900 text-gray-300 mt-auto">
    <!-- Content Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

        <!-- Brand / About -->
        <div>
            <a href="/index.php" class="flex items-center space-x-2 mb-4">
                <div class="bg-amber-500 text-gray-900 p-2 rounded-lg">
                    <i class="fa-solid fa-car-side text-xl"></i>
                </div>
                <span class="text-xl font-black text-white tracking-tight">
                    Drive<span class="text-amber-500">Ease</span>
                </span>
            </a>
            <p class="text-gray-400 text-sm leading-relaxed mb-4">
                Your reliable partner for vehicle rentals. Pick from our verified fleet of sedans, SUVs, and luxury
                rides with flexible booking options.
            </p>
            <div class="flex space-x-3 text-gray-400">
                <a href="#"
                    class="w-9 h-9 rounded-full bg-gray-800 flex items-center justify-center hover:bg-amber-500 hover:text-gray-900 transition"><i
                        class="fa-brands fa-facebook-f"></i></a>
                <a href="#"
                    class="w-9 h-9 rounded-full bg-gray-800 flex items-center justify-center hover:bg-amber-500 hover:text-gray-900 transition"><i
                        class="fa-brands fa-x-twitter"></i></a>
                <a href="#"
                    class="w-9 h-9 rounded-full bg-gray-800 flex items-center justify-center hover:bg-amber-500 hover:text-gray-900 transition"><i
                        class="fa-brands fa-instagram"></i></a>
            </div>
        </div>

        <!-- Quick Links -->
        <div>
            <h3 class="text-white font-semibold mb-4 text-base uppercase border-l-4 border-amber-500 pl-2">Navigation
            </h3>
            <ul class="space-y-2 text-sm">
                <li><a href="/vehicles.php" class="hover:text-amber-400 transition">Browse Vehicles</a></li>
                <li><a href="/how-it-works.php" class="hover:text-amber-400 transition">How It Works</a></li>
                <li><a href="/pricing.php" class="hover:text-amber-400 transition">Pricing Plans</a></li>
                <li><a href="/faqs.php" class="hover:text-amber-400 transition">FAQs</a></li>
            </ul>
        </div>

        <!-- Fleet Types -->
        <div>
            <h3 class="text-white font-semibold mb-4 text-base uppercase border-l-4 border-amber-500 pl-2">Vehicle Types
            </h3>
            <ul class="space-y-2 text-sm">
                <li><a href="/vehicles.php?type=sedan" class="hover:text-amber-400 transition">Sedans</a></li>
                <li><a href="/vehicles.php?type=suv" class="hover:text-amber-400 transition">SUVs & Crossovers</a></li>
                <li><a href="/vehicles.php?type=luxury" class="hover:text-amber-400 transition">Luxury Cars</a></li>
                <li><a href="/vehicles.php?type=ev" class="hover:text-amber-400 transition">Electric / Hybrids</a></li>
            </ul>
        </div>

        <!-- Newsletter -->
        <div>
            <h3 class="text-white font-semibold mb-4 text-base uppercase border-l-4 border-amber-500 pl-2">Newsletter
            </h3>
            <p class="text-gray-400 text-sm mb-3">Get the latest discounts and rental deals.</p>
            <form action="/subscribe.php" method="POST" class="space-y-2">
                <input type="email" name="email" placeholder="Your email address" required
                    class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded text-sm text-gray-200 focus:outline-none focus:border-amber-500">
                <button type="submit"
                    class="w-full bg-amber-500 hover:bg-amber-600 text-gray-900 font-semibold py-2 rounded text-sm transition">
                    Subscribe
                </button>
            </form>
        </div>

    </div>

    <!-- Bottom Copyright Bar -->
    <div class="border-t border-gray-800 bg-gray-950 py-6">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-8 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500 space-y-3 md:space-y-0">
            <div>
                &copy; <?php echo date('Y'); ?> DriveEase. All rights reserved.
            </div>
            <div class="flex space-x-6">
                <a href="/privacy.php" class="hover:text-gray-400 transition">Privacy Policy</a>
                <a href="/terms.php" class="hover:text-gray-400 transition">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>