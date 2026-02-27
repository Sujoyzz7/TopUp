<?php
include 'common/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT w.*, m.name as method_name 
                      FROM wallet_topup w 
                      JOIN payment_methods m ON w.method_id = m.id 
                      WHERE w.user_id = ? 
                      ORDER BY w.created_at DESC");
$stmt->execute([$userId]);
$history = $stmt->fetchAll();

include 'common/header.php';
?>

<main class="max-w-3xl mx-auto px-4 py-8 space-y-8 min-h-screen">
    <div class="space-y-2">
        <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tight flex items-center gap-4">
            <span class="w-2 h-10 bg-blue-600 rounded-full"></span>
            PAYMENT HISTORY
        </h1>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest pl-6">Your wallet top-up records</p>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-50">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Method</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Amount</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($history)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400 font-bold italic">No records found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($history as $item): ?>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-5">
                                    <p class="text-[10px] font-black text-gray-800">
                                        <?php echo date('d M, Y', strtotime($item['created_at'])); ?>
                                    </p>
                                    <p class="text-[8px] font-bold text-gray-400 uppercase">
                                        <?php echo date('h:i A', strtotime($item['created_at'])); ?>
                                    </p>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-[10px] font-black text-gray-800 uppercase">
                                        <?php echo $item['method_name']; ?>
                                    </p>
                                    <p class="text-[8px] font-bold text-gray-400 truncate max-w-[100px]">
                                        <?php echo $item['trx_id']; ?>
                                    </p>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-sm font-black text-blue-600">
                                        <?php echo $settings['currency_symbol'] . number_format($item['amount'], 2); ?>
                                    </p>
                                </td>
                                <td class="px-6 py-5">
                                    <?php
                                    $status_class = 'bg-yellow-100 text-yellow-600';
                                    if ($item['status'] == 'completed')
                                        $status_class = 'bg-green-100 text-green-600';
                                    if ($item['status'] == 'cancelled')
                                        $status_class = 'bg-red-100 text-red-600';
                                    ?>
                                    <span
                                        class="px-3 py-1.5 rounded-full text-[8px] font-black uppercase tracking-widest <?php echo $status_class; ?>">
                                        <?php echo $item['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include 'common/sidebar.php';
include 'common/bottom.php';
include 'common/footer.php'; ?>