<?php require '../../config/config.php'; ?>


<?php
if (!isset($_SESSION['adminname'])) {
    header('location: ' . ADMINURL . '/admin/login-admin.php');
    exit;
}

$pageTitle = "Admins";
$breadcrumb = "System";

require './layouts/header.php';

$stmt = $conn->query("SELECT id, adminname, email, role FROM admins ORDER BY id DESC");
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

function h($v)
{
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

$flash = null;

if (isset($_GET['created']) && $_GET['created'] === '1') {
    $flash = ['class' => 'alert-success', 'msg' => 'Admin created successfully.'];
} elseif (isset($_GET['updated']) && $_GET['updated'] === '1') {
    $flash = ['class' => 'alert-info', 'msg' => 'Admin updated successfully.'];
} elseif (isset($_GET['deleted']) && $_GET['deleted'] === '1') {
    $flash = ['class' => 'alert-danger', 'msg' => 'Admin deleted successfully.'];
} elseif (!empty($_GET['error'])) {
    // Optional: pass a brief error reason as ?error=...
    $flash = ['class' => 'alert-warning', 'msg' => h($_GET['error'])];
}

?>
<div class="max-w-7xl mx-auto">
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-200">
        <!-- Card Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200 bg-gray-50">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Admins</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Manage all administrator accounts.
                </p>
            </div>
            <a href="<?php echo ADMINURL; ?>/admins/create-admins.php"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                <i class="fas fa-plus"></i>
                Create Admin
            </a>
        </div>
        <!-- Flash Message -->
        <?php if ($flash): ?>
            <div class="px-6 pt-5">
                <div class="<?= $flash['class'] ?> flex items-center justify-between rounded-lg border px-4 py-3 shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        <span><?= $flash['msg'] ?></span>
                    </div>
                    <button onclick="this.parentElement.remove()"
                        class="text-gray-500 hover:text-gray-700 text-xl leading-none">
                        &times;
                    </button>
                </div>
            </div>
        <?php endif; ?>
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                            #
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                            Admin Name
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                            Email
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <?php if (!$admins): ?>
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-500">
                                No admins found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($admins as $admin): ?>
                            <tr class="hover:bg-blue-50 transition duration-150">
                                <td class="px-6 py-4 font-medium text-gray-700">
                                    <?= (int) $admin->id; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                        <span class="font-semibold text-gray-800">
                                            <?= h($admin->adminname); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <?= h($admin->email); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>