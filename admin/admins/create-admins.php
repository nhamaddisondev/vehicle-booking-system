<?php require "../../config/config.php"; ?>

<?php
// Auth guard
if (!isset($_SESSION['adminname'])) {
    header("location: " . ADMINURL . "/admins/login-admins.php");
    exit;
}

// Page context for header (title/breadcrumb)
$pageTitle = "Create Admin";
$breadcrumb = "System";

require "../layouts/header.php";

// Helpers
function h($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

// Defaults for sticky form
$errors = [];
$success = false;
$adminnameVal = '';
$emailVal = '';

if (isset($_POST['submit'])) {
    $adminname = trim($_POST['adminname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Sticky
    $adminnameVal = $adminname;
    $emailVal = $email;

    // Basic validation
    if ($adminname === '' || $email === '' || $password === '') {
        $errors[] = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    } else {
        try {
            // Optional: check for duplicate email/adminname
            $dup = $conn->prepare("SELECT 1 FROM users WHERE email = :email OR username = :username LIMIT 1");
            $dup->execute([':email' => $email, ':username' => $adminname]);

            if ($dup->fetch()) {
                $errors[] = "An admin with that email or username already exists.";
            } else {
                $insert = $conn->prepare("
          INSERT INTO users (username, email, password)
          VALUES (:username, :email, :password)
        ");
                $insert->execute([
                    ':username' => $adminname,
                    ':email' => $email,
                    ':password' => password_hash($password, PASSWORD_DEFAULT),
                ]);

                // Success → redirect to list
                $success = true;
                header("Location: " . ADMINURL . "/admins/admins.php?created=1");
                exit;
            }
        } catch (Exception $e) {
            // Log if you have a logger; show generic error
            $errors[] = "Something went wrong while creating the admin. Please try again.";
        }
    }
}
?>

<div class="flex justify-center px-4 py-8">
    <div class="w-full max-w-3xl">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200 bg-gray-50">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        Create Admin
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Add a new administrator to the system.
                    </p>
                </div>
                <a href="<?= ADMINURL ?>/admins/admins.php"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>
            </div>
            <div class="p-6">
                <!-- Error Messages -->
                <?php if ($errors): ?>
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">
                        <div class="flex items-center gap-2 text-red-700 font-semibold mb-3">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>Please fix the following errors:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-red-600">
                            <?php foreach ($errors as $err): ?>
                                <li><?= h($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form method="POST" action="create-admins.php" novalidate class="space-y-6">
                    <!-- Email -->
                    <div>
                        <label for="adminEmail" class="block text-sm font-semibold text-gray-700 mb-2">
                            Email Address
                        </label>
                        <input type="email" name="email" id="adminEmail" value="<?= h($emailVal) ?>"
                            placeholder="name@example.com" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition">
                    </div>
                    <!-- Username -->
                    <div>
                        <label for="adminName" class="block text-sm font-semibold text-gray-700 mb-2">
                            Username
                        </label>
                        <input type="text" name="adminname" id="adminName" value="<?= h($adminnameVal) ?>"
                            placeholder="Enter username" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition">
                    </div>
                    <!-- Password -->
                    <div>
                        <label for="adminPassword" class="block text-sm font-semibold text-gray-700 mb-2">
                            Password
                        </label>
                        <input type="password" name="password" id="adminPassword" placeholder="Enter password"
                            minlength="6" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition">
                        <p class="mt-2 text-sm text-gray-500">
                            Minimum <strong>6 characters</strong> is recommended for security.
                        </p>
                    </div>
                    <!-- Submit -->
                    <div class="flex justify-end pt-2">
                        <button type="submit" name="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white shadow-md hover:bg-blue-700 hover:shadow-lg transition">
                            <i class="fas fa-user-plus"></i>
                            Create Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require "../layouts/footer.php"; ?>