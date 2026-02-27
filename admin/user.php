<?php
include '../common/config.php';
if (!isAdminLoggedIn())
    redirect('login.php');

if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = $_POST['id'];
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    echo json_encode(['status' => 'success']);
    exit;
}

$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();

include 'common/header.php';
include 'common/sidebar.php';
?>

<main class="flex-1 lg:ml-64 p-6 sm:p-10 space-y-10 min-h-screen">
    <?php
    $title = "User Database";
    $subtitle = count($users) . " Total Members registered";
    include 'common/topbar.php';
    ?>

    <div class="bg-[#1E293B] rounded-[2.5rem] border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-black/20 text-gray-500 text-[10px] font-black uppercase tracking-widest">
                        <th class="px-8 py-6">User Details</th>
                        <th class="px-8 py-6">Contact info</th>
                        <th class="px-8 py-6">Balance</th>
                        <th class="px-8 py-6">Joined on</th>
                        <th class="px-8 py-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="px-8 py-10 text-center text-gray-500">No users found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-white/5 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-black uppercase shadow-lg">
                                            <?php echo substr($user['name'], 0, 1); ?>
                                        </div>
                                        <p class="font-black text-white uppercase text-xs">
                                            <?php echo $user['name']; ?>
                                        </p>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-xs text-gray-400 font-bold">
                                        <?php echo $user['email']; ?>
                                    </p>
                                    <p class="text-[8px] text-gray-600 font-black uppercase mt-1">
                                        <?php echo $user['phone']; ?>
                                    </p>
                                </td>
                                <td class="px-8 py-6 font-black text-blue-500">
                                    <?php echo $settings['currency_symbol'] . number_format($user['balance'], 2); ?>
                                </td>
                                <td class="px-8 py-6 text-[10px] text-gray-500 font-bold">
                                    <?php echo date('d M, Y', strtotime($user['created_at'])); ?>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <button onclick="deleteUser(<?php echo $user['id']; ?>)"
                                        class="bg-red-500/10 text-red-500 p-3 rounded-xl hover:bg-red-500 transition-all hover:text-white">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    function deleteUser(id) {
        if (confirm('Permanently delete this user? All their orders and data will be lost.')) {
            showAdminLoading();
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('id', id);
            fetch('user.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(res => {
                    hideAdminLoading();
                    if (res.status === 'success') {
                        showAdminToast('User deleted.');
                        setTimeout(() => window.location.reload(), 1000);
                    }
                });
        }
    }
</script>

<?php include 'common/bottom.php'; ?>