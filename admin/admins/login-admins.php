<?php require "../../config/config.php"; ?>
<?php
  // Hide the breadcrumb/page-header on this page
  $suppressPageHead = true;
?>
<?php require "../layouts/header.php"; ?> 
<?php 

    if(isset($_SESSION['adminname'])) {

      header("location: ".ADMINURL."");

    }

    $error = null;


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Email and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT username, email, password, role FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $select = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($select && password_verify($password, $select['password'])) {
            $_SESSION['adminname'] = $select['username'];
            $_SESSION['email']     = $select['email'];
            header("Location: " . ADMINURL . "");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}

?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-100 flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-md">
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="px-8 pt-8 text-center">
                <a href="<?php echo $base_url; ?>" class="inline-block mb-5">
                    <img
                        src="<?php echo ADMINURL; ?>/images/logo.png"
                        alt="Logo"
                        class="w-20 h-20 rounded-2xl shadow-md mx-auto">
                </a>
                <h1 class="text-3xl font-bold text-gray-800">
                    Admin Login
                </h1>
                <p class="mt-2 text-gray-500">
                    Sign in to manage your dashboard
                </p>
            </div>
            <div class="px-8 py-8">
                <!-- Error -->
                <?php if ($error): ?>
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 flex gap-3">
                    <i class="fas fa-circle-exclamation mt-1"></i>
                    <div>
                        <div class="font-semibold">Login Failed</div>
                        <div class="text-sm">
                            <?= htmlspecialchars($error); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <form method="POST" action="login-admins.php" class="space-y-6">
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                required
                                autocomplete="username"
                                placeholder="Enter your email"
                                class="w-full rounded-xl border border-gray-300 pl-12 pr-4 py-3 outline-none transition focus:ring-4 focus:ring-blue-100 focus:border-blue-500">
                        </div>
                    </div>
                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                required
                                minlength="6"
                                autocomplete="current-password"
                                placeholder="Enter your password"
                                class="w-full rounded-xl border border-gray-300 pl-12 pr-12 py-3 outline-none transition focus:ring-4 focus:ring-blue-100 focus:border-blue-500">
                            <button
                                type="button"
                                id="togglePwd"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-blue-600">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                        <p
                            id="capsHint"
                            class="hidden mt-2 text-sm text-yellow-600">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Caps Lock is on
                        </p>
                    </div>
                    <!-- Remember -->
                    <div class="flex justify-between items-center text-sm">
                        <label class="flex items-center gap-2 text-gray-600">
                            <input
                                type="checkbox"
                                disabled
                                class="rounded border-gray-300">
                            Remember me
                        </label>
                        <a
                            href="#"
                            class="text-blue-600 hover:text-blue-700 font-medium">
                            Forgot password?
                        </a>
                    </div>
                    <!-- Button -->
                    <button
                        id="submitBtn"
                        name="submit"
                        class="w-full rounded-xl bg-blue-600 py-3 text-white font-semibold shadow-lg transition duration-200 hover:bg-blue-700 hover:shadow-xl">
                        Sign In
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(() => {

    const pwd = document.getElementById("password");
    const toggle = document.getElementById("togglePwd");
    const hint = document.getElementById("capsHint");
    const btn = document.getElementById("submitBtn");
    const form = document.querySelector('form[action="login-admins.php"]');

    toggle.addEventListener("click", () => {

        const icon = toggle.querySelector("i");

        if (pwd.type === "password") {

            pwd.type = "text";

            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");

        } else {

            pwd.type = "password";

            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    });
    pwd.addEventListener("keyup", (e) => {
        if (!e.getModifierState) return;
        hint.classList.toggle(
            "hidden",
            !e.getModifierState("CapsLock")
        );
    });
    form.addEventListener("submit", () => {
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin h-5 w-5 inline mr-2" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25"
                    cx="12" cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4">
                </circle>
                <path class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                </path>
            </svg>
            Signing in...
        `;
    });
})();
</script>

<?php require "../layouts/footer.php"; ?> 