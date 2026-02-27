<?php
include '../common/config.php';
if (!isAdminLoggedIn())
    redirect('login.php');

// Handle Operations
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    $id = $_POST['id'];
    $action = $_POST['action'];

    // Fetch the request details
    $req = $pdo->prepare("SELECT * FROM wallet_topup WHERE id = ?");
    $req->execute([$id]);
    $request = $req->fetch();

    if (!$request || $request['status'] != 'pending') {
        echo json_encode(['status' => 'error', 'message' => 'Request already processed or not found.']);
        exit;
    }

    if ($action == 'approve') {
        $pdo->beginTransaction();
        try {
            // Update request status
            $stmt = $pdo->prepare("UPDATE wallet_topup SET status = 'completed' WHERE id = ?");
            $stmt->execute([$id]);

            // Update user balance
            $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$request['amount'], $request['user_id']]);

            // Log transaction
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, note) VALUES (?, 'credit', ?, 'Wallet top-up approved')");
            $stmt->execute([$request['user_id'], $request['amount']]);

            $pdo->commit();
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        }
        exit;
    }

    if ($action == 'reject') {
        $stmt = $pdo->prepare("UPDATE wallet_topup SET status = 'cancelled' WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success']);
        exit;
    }
}

// Fetch Pending Requests
$pending_requests = $pdo->query("SELECT w.*, u.name as user_name, u.phone as user_phone, m.name as method_name 
                                FROM wallet_topup w 
                                JOIN users u ON w.user_id = u.id 
                                JOIN payment_methods m ON w.method_id = m.id 
                                WHERE w.status = 'pending' 
                                ORDER BY w.id DESC")->fetchAll();

// Fetch Recent History
$history = $pdo->query("SELECT w.*, u.name as user_name, u.phone as user_phone, m.name as method_name 
                        FROM wallet_topup w 
                        JOIN users u ON w.user_id = u.id 
                        JOIN payment_methods m ON w.method_id = m.id 
                        WHERE w.status != 'pending' 
                        ORDER BY w.id DESC LIMIT 20")->fetchAll();

include 'common/header.php';
include 'common/sidebar.php';
?>

<main class="flex-1 lg:ml-64 p-6 sm:p-10 space-y-10 min-h-screen">
    <?php
    $title = "Wallet Top-ups";
    $subtitle = "Verify and approve balance recharge requests";
    include 'common/topbar.php';
    ?>

    <!-- Pending Requests -->
    <div class="bg-[#1E293B] rounded-[2.5rem] border border-white/5 overflow-hidden">
        <div class="p-8 border-b border-white/5 flex items-center justify-between bg-yellow-500/5">
            <h2 class="text-lg font-black text-white uppercase tracking-tight"><i
                    class="fa-solid fa-clock-rotate-left mr-3 text-yellow-500"></i>Pending Verification</h2>
            <span class="bg-yellow-500 text-black px-4 py-1.5 rounded-full text-[10px] font-black uppercase">
                <?php echo count($pending_requests); ?> Waiting
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-black/20 text-gray-500 text-[10px] font-black uppercase tracking-widest">
                        <th class="px-8 py-6">User</th>
                        <th class="px-8 py-6">Payment Method</th>
                        <th class="px-8 py-6">Sender Number</th>
                        <th class="px-8 py-6">Amount</th>
                        <th class="px-8 py-6">Trx ID</th>
                        <th class="px-8 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($pending_requests)): ?>
                        <tr>
                            <td colspan="6" class="px-8 py-10 text-center text-gray-500 font-bold">Great! No pending
                                requests.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pending_requests as $r): ?>
                            <tr class="hover:bg-white/5 transition-all">
                                <td class="px-8 py-6">
                                    <p class="text-white font-black text-sm uppercase">
                                        <?php echo $r['user_name']; ?>
                                    </p>
                                    <p class="text-[10px] text-gray-500 font-bold">
                                        <?php echo $r['user_phone']; ?>
                                    </p>
                                </td>
                                <td class="px-8 py-6 uppercase font-bold text-xs text-gray-400">
                                    <?php echo $r['method_name']; ?>
                                </td>
                                <td class="px-8 py-6 text-blue-400 font-black tracking-widest text-xs">
                                    <?php echo $r['sender_number']; ?>
                                </td>
                                <td class="px-8 py-6 font-black text-white">
                                    <?php echo $settings['currency_symbol'] . $r['amount']; ?>
                                </td>
                                <td class="px-8 py-6"><span
                                        class="bg-white/5 px-4 py-2 rounded-xl text-[10px] font-black tracking-widest text-gray-400 border border-white/5">
                                        <?php echo $r['trx_id']; ?>
                                    </span></td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button onclick="processRequest(<?php echo $r['id']; ?>, 'approve')"
                                            class="w-10 h-10 bg-green-500/10 text-green-500 rounded-xl hover:bg-green-500 hover:text-white transition-all shadow-lg"><i
                                                class="fa-solid fa-check"></i></button>
                                        <button onclick="processRequest(<?php echo $r['id']; ?>, 'reject')"
                                            class="w-10 h-10 bg-red-500/10 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-lg"><i
                                                class="fa-solid fa-xmark"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Request History -->
    <div class="bg-[#1E293B] rounded-[2.5rem] border border-white/5 overflow-hidden">
        <div class="p-8 border-b border-white/5">
            <h2 class="text-lg font-black text-white uppercase tracking-tight">Recent History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-black/20 text-gray-500 text-[10px] font-black uppercase tracking-widest">
                        <th class="px-8 py-6">User</th>
                        <th class="px-8 py-6">Method</th>
                        <th class="px-8 py-6">Amount</th>
                        <th class="px-8 py-6">Status</th>
                        <th class="px-8 py-6">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php foreach ($history as $h): ?>
                        <tr class="opacity-60 grayscale hover:grayscale-0 hover:opacity-100 transition-all">
                            <td class="px-8 py-4">
                                <p class="text-white font-bold text-xs uppercase">
                                    <?php echo $h['user_name']; ?>
                                </p>
                            </td>
                            <td class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                <?php echo $h['method_name']; ?>
                            </td>
                            <td class="px-8 py-4 font-black text-gray-300 text-xs">
                                <?php echo $settings['currency_symbol'] . $h['amount']; ?>
                            </td>
                            <td class="px-8 py-4">
                                <span
                                    class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest <?php echo $h['status'] == 'completed' ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500'; ?>">
                                    <?php echo $h['status']; ?>
                                </span>
                            </td>
                            <td class="px-8 py-4 text-[10px] text-gray-500 font-medium">
                                <?php echo date('d M, H:i', strtotime($h['created_at'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    function processRequest(id, action) {
        if (confirm('Are you sure you want to ' + action + ' this request?')) {
            showAdminLoading();
            const fd = new FormData();
            fd.append('id', id);
            fd.append('action', action);

            fetch('wallet_requests.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(res => {
                    hideAdminLoading();
                    if (res.status === 'success') {
                        showAdminToast('Request ' + (action === 'approve' ? 'Approved' : 'Rejected') + ' confirmed!');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showAdminToast(res.message, 'error');
                    }
                });
        }
    }
</script>

<?php include 'common/bottom.php'; ?>