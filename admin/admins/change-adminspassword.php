<?php require "../../config/config.php"; ?>

<?php
/* ---------- Admin Auth Guard ---------- */
if (!isset($_SESSION['adminname'])) {
    header("location: " . ADMINURL . "/admins/login-admins.php");
    exit;
}

$pageTitle = "Change Password";
$breadcrumb = "System";

/* ---------- Feedback ---------- */
$success = $error = "";
$forceLogout = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($current === '' || $new === '' || $confirm === '') {
        $error = "All fields are required.";
    } elseif ($new !== $confirm) {
        $error = "New password and confirmation do not match.";
    } elseif (strlen($new) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {

        /* Get current admin password */
        $stmt = $conn->prepare("
            SELECT id, password 
            FROM users 
            WHERE username = :username 
            LIMIT 1
        ");
        $stmt->execute([
            ':username' => $_SESSION['adminname']
        ]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($current, $admin['password'])) {
            $error = "Current password is incorrect.";
        } else {

            $newHash = password_hash($new, PASSWORD_DEFAULT);

            $update = $conn->prepare("
                UPDATE users 
                SET password = :pass 
                WHERE id = :id
            ");
            $update->execute([
                ':pass' => $newHash,
                ':id' => $admin['id']
            ]);

            $success = "Password updated successfully.";
            $forceLogout = true;
        }
    }
}

require "../layouts/header.php";
?>
<div class="flex justify-center px-4 py-8">
    <div class="w-full max-w-lg">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-2xl font-bold text-gray-800">
                    Change Admin Password
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Update your password to keep your account secure.
                </p>
            </div>
            <div class="p-6">
                <!-- Error Alert -->
                <?php if ($error): ?>
                    <div
                        class="mb-5 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                        <i class="fas fa-exclamation-circle mt-1"></i>
                        <span><?= htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>
                <!-- Success Alert -->
                <?php if ($success): ?>
                    <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                        <div class="flex items-center gap-2 font-medium">
                            <i class="fas fa-check-circle"></i>
                            <span><?= htmlspecialchars($success); ?></span>
                        </div>
                        <p class="mt-2 text-sm text-green-600">
                            For security reasons, please sign in again to continue.
                        </p>
                    </div>
                <?php endif; ?>
                <!-- Form -->
                <form method="POST" autocomplete="off" class="space-y-5" <?= $forceLogout ? 'style="display:none"' : '' ?>>
                    <!-- Current Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Current Password
                        </label>
                        <input type="password" name="current_password" required
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition"
                            placeholder="Enter current password">
                    </div>
                    <!-- New Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            New Password
                        </label>
                        <input type="password" name="new_password" required
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition"
                            placeholder="Enter new password">
                    </div>
                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Confirm New Password
                        </label>
                        <input type="password" name="confirm_password" required
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition"
                            placeholder="Confirm new password">
                    </div>
                    <!-- Button -->
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white shadow-md transition hover:bg-blue-700 hover:shadow-lg">
                        <i class="fas fa-lock"></i>
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($forceLogout): ?>
    <script>
        setTimeout(function () {
            window.location.href = "<?= ADMINURL; ?>/admins/logout-admins.php";
        }, 1000);
    </script>
<?php endif; ?>

<?php require "../layouts/footer.php"; ?>