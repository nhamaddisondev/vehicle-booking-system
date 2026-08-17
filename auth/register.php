<?php require '../config/config.php'; ?>


<?php
if (isset($_SESSION['username'])) {
    header("location: " . APPURL . "");
}
if (isset($_POST['submit'])) {
    if (empty($_POST['username']) OR empty($_POST['email']) OR empty($_POST['password'])) {
        echo "<script>alert('Please fill in all fields.')</script>";
    } else {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $repassword = password_hash($_POST['repassword'], PASSWORD_DEFAULT);

        if ($password == $repassword) {

            if (strlen($email) > 22 OR strlen($username) > 15) {
                echo "<script>alert('Username or email too long.')</script>";
            }
            else{
                $insert = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                $insert->execute([
                    ':username' => $username,
                    ':email' => $email,
                    ':password' => $password
                ]);
                header("location: " . APPURL . "/auth/login.php");
            }
        } else {
            echo "<script>alert('Passwords do not match.')</script>";
        }
    }
}
?>

<!-- Hero Section -->
<section
    class="relative bg-cover bg-center py-24"
    style="background-image: url('../images/hero_1.jpg');"
    id="home-section"
>
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/50"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <h1 class="text-4xl md:text-5xl font-bold text-white">
                Register
            </h1>

            <div class="flex items-center gap-3 mt-4 text-sm">
                <a
                    href="<?php echo $base_url; ?>"
                    class="text-white/80 hover:text-white transition"
                >
                    Home
                </a>

                <span class="text-white/60">/</span>

                <span class="text-white font-semibold">
                    Register
                </span>
            </div>
        </div>
    </div>
</section>


<!-- Registration Section -->
<section class="bg-gray-50 py-12 md:py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">

            <!-- Card Header -->
            <div class="px-6 py-6 md:px-8 border-b border-gray-200 bg-gray-50">
                <h2 class="text-2xl font-bold text-gray-800">
                    Create Your Account
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Register an account to get started.
                </p>
            </div>


            <!-- Form -->
            <form
                action="register.php"
                method="POST"
                class="p-6 md:p-8 space-y-6"
            >

                <!-- Username -->
                <div>
                    <label
                        for="username"
                        class="block text-sm font-semibold text-gray-700 mb-2"
                    >
                        Username
                    </label>

                    <div class="relative">
                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Enter your username"
                            class="w-full rounded-xl border border-gray-300 pl-11 pr-4 py-3 text-gray-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >
                    </div>
                </div>


                <!-- Email -->
                <div>
                    <label
                        for="email"
                        class="block text-sm font-semibold text-gray-700 mb-2"
                    >
                        Email Address
                    </label>

                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="name@example.com"
                            class="w-full rounded-xl border border-gray-300 pl-11 pr-4 py-3 text-gray-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >
                    </div>
                </div>


                <!-- User Type -->
                <div>
                    <label
                        for="user-type"
                        class="block text-sm font-semibold text-gray-700 mb-2"
                    >
                        User Type
                    </label>

                    <div class="relative">
                        <i class="fas fa-users absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>

                        <select
                            name="type"
                            id="user-type"
                            class="w-full appearance-none rounded-xl border border-gray-300 bg-white pl-11 pr-10 py-3 text-gray-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            required
                        >
                            <option value="" selected disabled>
                                Select User Type
                            </option>

                            <option value="Worker">
                                Worker
                            </option>

                            <option value="Company">
                                Company
                            </option>
                        </select>

                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>


                <!-- Password -->
                <div>
                    <label
                        for="password"
                        class="block text-sm font-semibold text-gray-700 mb-2"
                    >
                        Password
                    </label>

                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            class="w-full rounded-xl border border-gray-300 pl-11 pr-12 py-3 text-gray-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >

                        <button
                            type="button"
                            id="togglePassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 transition"
                        >
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>


                <!-- Confirm Password -->
                <div>
                    <label
                        for="re-password"
                        class="block text-sm font-semibold text-gray-700 mb-2"
                    >
                        Confirm Password
                    </label>

                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

                        <input
                            type="password"
                            id="re-password"
                            name="re-password"
                            placeholder="Re-enter your password"
                            class="w-full rounded-xl border border-gray-300 pl-11 pr-12 py-3 text-gray-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >

                        <button
                            type="button"
                            id="toggleConfirmPassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 transition"
                        >
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>


                <!-- Submit -->
                <div class="pt-2">
                    <button
                        type="submit"
                        name="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3.5 text-white font-semibold shadow-lg hover:bg-blue-700 hover:shadow-xl transition duration-200"
                    >
                        <i class="fas fa-user-plus"></i>
                        Create Account
                    </button>
                </div>


                <!-- Login Link -->
                <div class="text-center pt-2">
                    <p class="text-sm text-gray-500">
                        Already have an account?
                        <a
                            href="login.php"
                            class="font-semibold text-blue-600 hover:text-blue-700"
                        >
                            Sign in
                        </a>
                    </p>
                </div>

            </form>

        </div>

    </div>
</section>


<!-- Password Toggle -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    function setupPasswordToggle(buttonId, inputId) {

        const button = document.getElementById(buttonId);
        const input = document.getElementById(inputId);

        if (!button || !input) return;

        button.addEventListener("click", function () {

            const icon = button.querySelector("i");

            if (input.type === "password") {

                input.type = "text";

                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");

            } else {

                input.type = "password";

                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");

            }

        });

    }

    setupPasswordToggle(
        "togglePassword",
        "password"
    );

    setupPasswordToggle(
        "toggleConfirmPassword",
        "re-password"
    );

});
</script>
<?php require "../includes/footer.php"; ?>