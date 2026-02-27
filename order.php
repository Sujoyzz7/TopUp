<?php
include 'common/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT o.*, oi.player_id, p.name as product_name, p.image as product_image, c.type as cat_type
                      FROM orders o 
                      JOIN order_items oi ON o.id = oi.order_id 
                      JOIN products p ON oi.product_id = p.id 
                      JOIN categories c ON p.cat_id = c.id
                      WHERE o.user_id = ? ORDER BY o.created_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

include 'common/header.php';
?>

<main class="max-w-3xl mx-auto px-4 py-8 space-y-8 min-h-screen">
    <div class="space-y-2">
        <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tight flex items-center gap-4">
            <span class="w-2 h-10 bg-blue-600 rounded-full"></span>
            MY ORDERS
        </h1>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest pl-6">Track your gaming top-ups</p>
    </div>

    <?php if (empty($orders)): ?>
        <div class="py-20 text-center space-y-6">
            <div
                class="w-24 h-24 bg-gray-100 rounded-[2rem] flex items-center justify-center mx-auto text-gray-300 text-4xl">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <div class="space-y-2">
                <h3 class="font-black text-gray-800 uppercase">Empty Box!</h3>
                <p class="text-sm font-medium text-gray-400">You haven't placed any orders yet.</p>
            </div>
            <a href="index.php"
                class="inline-block bg-blue-600 text-white px-8 py-4 rounded-2xl font-black text-sm shadow-xl shadow-blue-200">START
                SHOPPING</a>
        </div>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($orders as $order): ?>
                <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-50 overflow-hidden group">
                    <div class="p-6 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 rounded-2xl overflow-hidden bg-gray-50 shrink-0 shadow-inner">
                                <img src="<?php echo $order['product_image']; ?>" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h3 class="font-black text-gray-800 uppercase text-sm tracking-tight">
                                    <?php echo $order['product_name']; ?>
                                </h3>
                                <p class="text-xs font-bold text-gray-400 mt-1 uppercase">Order #
                                    <?php echo $order['id']; ?> •
                                    <?php echo date('d M, Y', strtotime($order['created_at'])); ?>
                                </p>
                                <p class="text-blue-600 font-extrabold text-sm mt-1">
                                    <?php echo $settings['currency_symbol'] . number_format($order['total_amount'], 2); ?>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <?php
                            $status_class = 'bg-yellow-100 text-yellow-600';
                            if ($order['status'] == 'completed')
                                $status_class = 'bg-green-100 text-green-600';
                            if ($order['status'] == 'cancelled')
                                $status_class = 'bg-red-100 text-red-600';
                            ?>
                            <span
                                class="px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest <?php echo $status_class; ?>">
                                <?php echo $order['status']; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Tracking / Details -->
                    <div class="bg-gray-50/50 p-6 border-t border-gray-50 space-y-6">
                        <!-- Status Timeline -->
                        <div class="relative flex items-center justify-between max-w-sm mx-auto">
                            <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-0.5 bg-gray-200"></div>
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-0.5 bg-blue-600 transition-all duration-1000"
                                style="width: <?php echo $order['status'] == 'completed' ? '100%' : '50%'; ?>"></div>

                            <div class="relative z-10 flex flex-col items-center gap-2 group">
                                <div
                                    class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-200 animate-pulse">
                                    <i class="fa-solid fa-receipt text-xs"></i>
                                </div>
                                <span class="text-[8px] font-black text-gray-800 uppercase tracking-tighter">Placed</span>
                            </div>

                            <div class="relative z-10 flex flex-col items-center gap-2">
                                <div
                                    class="w-10 h-10 rounded-full <?php echo $order['status'] != 'pending' ? 'bg-blue-600 shadow-blue-200' : 'bg-gray-200'; ?> text-white flex items-center justify-center shadow-lg transition-colors">
                                    <i
                                        class="fa-solid fa-spinner text-xs <?php echo $order['status'] == 'pending' ? 'animate-spin' : ''; ?>"></i>
                                </div>
                                <span class="text-[8px] font-black text-gray-800 uppercase tracking-tighter">Processing</span>
                            </div>

                            <div class="relative z-10 flex flex-col items-center gap-2">
                                <div
                                    class="w-10 h-10 rounded-full <?php echo $order['status'] == 'completed' ? 'bg-green-500 shadow-green-200' : 'bg-gray-200'; ?> text-white flex items-center justify-center shadow-lg transition-colors">
                                    <i class="fa-solid fa-check-double text-xs"></i>
                                </div>
                                <span class="text-[8px] font-black text-gray-800 uppercase tracking-tighter">Completed</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100 text-center">
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Player ID</p>
                                <p class="font-black text-gray-800 uppercase text-xs">
                                    <?php echo $order['player_id'] ?: 'N/A'; ?>
                                </p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Payment</p>
                                <p class="font-black text-gray-800 uppercase text-xs">
                                    <?php echo $order['payment_method']; ?>
                                </p>
                            </div>
                        </div>

                        <?php if ($order['status'] == 'completed' && $order['cat_type'] == 'voucher'): ?>
                            <a href="mycode.php"
                                class="w-full bg-[#1E293B] text-white py-4 rounded-xl font-black text-xs uppercase tracking-widest shadow-xl flex items-center justify-center gap-3">
                                View Voucher Code <i class="fa-solid fa-key"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include 'common/sidebar.php';
include 'common/bottom.php';
include 'common/footer.php'; ?>