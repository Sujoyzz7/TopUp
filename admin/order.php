<?php
include '../common/config.php';
if (!isAdminLoggedIn())
    redirect('login.php');

$orders = $pdo->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC")->fetchAll();

include 'common/header.php';
include 'common/sidebar.php';
?>

<main class="flex-1 lg:ml-64 p-6 sm:p-10 space-y-10 min-h-screen">
    <?php
    $title = "System Orders";
    $subtitle = "Process and monitor incoming requests";
    include 'common/topbar.php';
    ?>

    <div class="bg-[#1E293B] rounded-[2.5rem] border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-black/20 text-gray-500 text-[10px] font-black uppercase tracking-widest">
                        <th class="px-8 py-6">Order ID</th>
                        <th class="px-8 py-6">Customer</th>
                        <th class="px-8 py-6">Total Amount</th>
                        <th class="px-8 py-6">Payment Method</th>
                        <th class="px-8 py-6">Status</th>
                        <th class="px-8 py-6">Date</th>
                        <th class="px-8 py-6">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php foreach ($orders as $o): ?>
                        <tr class="hover:bg-white/5 transition-all group">
                            <td class="px-8 py-6 font-black text-gray-400">#
                                <?php echo $o['id']; ?>
                            </td>
                            <td class="px-8 py-6">
                                <p class="font-bold text-white uppercase text-[10px]">
                                    <?php echo $o['user_name']; ?>
                                </p>
                            </td>
                            <td class="px-8 py-6 font-black text-blue-500">
                                <?php echo $settings['currency_symbol'] . number_format($o['total_amount'], 2); ?>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-[10px] font-bold text-gray-500 uppercase">
                                    <?php echo $o['payment_method']; ?>
                                </p>
                            </td>
                            <td class="px-8 py-6">
                                <?php
                                $status_class = 'bg-yellow-500/10 text-yellow-500';
                                if ($o['status'] == 'completed')
                                    $status_class = 'bg-green-500/10 text-green-500';
                                if ($o['status'] == 'cancelled')
                                    $status_class = 'bg-red-500/10 text-red-500';
                                ?>
                                <span
                                    class="px-4 py-2 rounded-full text-[8px] font-black uppercase tracking-widest <?php echo $status_class; ?>">
                                    <?php echo $o['status']; ?>
                                </span>
                            </td>
                            <td class="px-8 py-6 text-[10px] text-gray-500 font-bold">
                                <?php echo date('d M, Y H:i', strtotime($o['created_at'])); ?>
                            </td>
                            <td class="px-8 py-6">
                                <a href="order_detail.php?id=<?php echo $o['id']; ?>"
                                    class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-blue-500/10 inline-block hover:scale-105 transition-transform">Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include 'common/bottom.php'; ?>