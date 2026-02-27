<?php
include '../common/config.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

// Stats Counting
$stats = [
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'pending_orders' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn(),
    'completed_orders' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status='completed'")->fetchColumn(),
    'categories' => $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
    'products' => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'sliders' => $pdo->query("SELECT COUNT(*) FROM sliders")->fetchColumn(),
    'revenue' => $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status='completed'")->fetchColumn() ?: 0,
    'methods' => $pdo->query("SELECT COUNT(*) FROM payment_methods")->fetchColumn(),
    'pending_wallets' => $pdo->query("SELECT COUNT(*) FROM wallet_topup WHERE status='pending'")->fetchColumn(),
];

// Recent Orders
$recent_orders = $pdo->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();

include 'common/header.php';
include 'common/sidebar.php';
?>

<main class="flex-1 lg:ml-64 p-6 sm:p-10 space-y-10 min-h-screen">

    <?php
    $title = "System Overview";
    $subtitle = "Real-time stats monitor";
    include 'common/topbar.php';
    ?>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6">
        <div class="bg-[#1E293B] rounded-[2rem] p-6 border border-white/5 relative overflow-hidden group">
            <div
                class="absolute -right-4 -bottom-4 text-6xl text-blue-500 opacity-5 -rotate-12 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-users"></i>
            </div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Total Users</p>
            <h3 class="text-3xl font-black text-white">
                <?php echo $stats['users']; ?>
            </h3>
            <div class="mt-4 flex items-center gap-2 text-green-500 text-[10px] font-bold">
                <i class="fa-solid fa-arrow-trend-up"></i> 12% increase
            </div>
        </div>
        <div class="bg-[#1E293B] rounded-[2rem] p-6 border border-white/5 relative overflow-hidden group">
            <div
                class="absolute -right-4 -bottom-4 text-6xl text-yellow-500 opacity-5 -rotate-12 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Pending Orders</p>
            <h3 class="text-3xl font-black text-white">
                <?php echo $stats['pending_orders']; ?>
            </h3>
            <div class="mt-4 flex items-center gap-2 text-yellow-500 text-[10px] font-bold">Needs attention</div>
        </div>
        <div class="bg-[#1E293B] rounded-[2rem] p-6 border border-white/5 relative overflow-hidden group">
            <div
                class="absolute -right-4 -bottom-4 text-6xl text-green-500 opacity-5 -rotate-12 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Completed</p>
            <h3 class="text-3xl font-black text-white">
                <?php echo $stats['completed_orders']; ?>
            </h3>
            <div class="mt-4 flex items-center gap-2 text-green-500 text-[10px] font-bold">Great performance</div>
        </div>
        <div class="bg-[#1E293B] rounded-[2rem] p-6 border border-white/5 relative overflow-hidden group">
            <div
                class="absolute -right-4 -bottom-4 text-6xl text-purple-500 opacity-5 -rotate-12 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-money-bill-trend-up"></i>
            </div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Revenue</p>
            <h3 class="text-3xl font-black text-white">
                <?php echo $settings['currency_symbol'] . number_format($stats['revenue'], 0); ?>
            </h3>
            <div class="mt-4 flex items-center gap-2 text-blue-500 text-[10px] font-bold">Lifetime earnings</div>
        </div>
        <div class="bg-[#1E293B] rounded-[2rem] p-6 border border-white/5 relative overflow-hidden group">
            <div
                class="absolute -right-4 -bottom-4 text-6xl text-pink-500 opacity-5 -rotate-12 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-gamepad"></i>
            </div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Active Games</p>
            <h3 class="text-3xl font-black text-white">
                <?php echo $stats['categories']; ?>
            </h3>
            <div class="mt-4 flex items-center gap-2 text-pink-500 text-[10px] font-bold">Catalog health</div>
        </div>
        <div class="bg-[#1E293B] rounded-[2rem] p-6 border border-white/5 relative overflow-hidden group">
            <div
                class="absolute -right-4 -bottom-4 text-6xl text-blue-400 opacity-5 -rotate-12 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Wallet Req.</p>
            <h3 class="text-3xl font-black text-white">
                <?php echo $stats['pending_wallets']; ?>
            </h3>
            <div
                class="mt-4 flex items-center gap-2 <?php echo $stats['pending_wallets'] > 0 ? 'text-red-500' : 'text-green-500'; ?> text-[10px] font-bold">
                <?php echo $stats['pending_wallets'] > 0 ? 'Awaiting verification' : 'All clear'; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4">
        <a href="product.php"
            class="bg-blue-600/10 border border-blue-500/20 p-6 rounded-[2rem] text-center hover:bg-blue-600/20 transition-all group">
            <i
                class="fa-solid fa-plus-circle text-blue-500 text-2xl mb-3 group-hover:scale-125 transition-transform"></i>
            <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest">Add Recharge</p>
        </a>
        <a href="user.php"
            class="bg-purple-600/10 border border-purple-500/20 p-6 rounded-[2rem] text-center hover:bg-purple-600/20 transition-all group">
            <i
                class="fa-solid fa-user-plus text-purple-500 text-2xl mb-3 group-hover:scale-125 transition-transform"></i>
            <p class="text-[10px] font-black text-purple-500 uppercase tracking-widest">Manage Users</p>
        </a>
        <a href="sliders.php"
            class="bg-pink-600/10 border border-pink-500/20 p-6 rounded-[2rem] text-center hover:bg-pink-600/20 transition-all group">
            <i
                class="fa-solid fa-layer-group text-pink-500 text-2xl mb-3 group-hover:scale-125 transition-transform"></i>
            <p class="text-[10px] font-black text-pink-500 uppercase tracking-widest">Add Sliders</p>
        </a>
        <a href="game.php"
            class="bg-green-600/10 border border-green-500/20 p-6 rounded-[2rem] text-center hover:bg-green-600/20 transition-all group">
            <i class="fa-solid fa-gamepad text-green-500 text-2xl mb-3 group-hover:scale-125 transition-transform"></i>
            <p class="text-[10px] font-black text-green-500 uppercase tracking-widest">Add Game</p>
        </a>
    </div>

    <!-- Recent Orders & Details -->
    <div class="bg-[#1E293B] rounded-[2.5rem] border border-white/5 overflow-hidden">
        <div class="p-8 border-b border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-yellow-500/10 text-yellow-500 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <h2 class="text-xl font-black text-white uppercase tracking-tight">Recent Orders Activity</h2>
            </div>
            <a href="order.php" class="text-xs font-black text-blue-500 uppercase tracking-widest hover:underline">View
                All Orders</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-black/20 text-gray-500 text-[10px] font-black uppercase tracking-widest">
                        <th class="px-8 py-6">Order ID</th>
                        <th class="px-8 py-6">Customer</th>
                        <th class="px-8 py-6">Amount</th>
                        <th class="px-8 py-6">Status</th>
                        <th class="px-8 py-6">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($recent_orders)): ?>
                        <tr>
                            <td colspan="5" class="px-8 py-10 text-center text-gray-500 font-bold italic">No recent orders
                                yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_orders as $o): ?>
                            <tr class="hover:bg-white/5 transition-all group">
                                <td class="px-8 py-6 font-black text-gray-400">#
                                    <?php echo $o['id']; ?>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="font-bold text-white">
                                        <?php echo $o['user_name']; ?>
                                    </p>
                                    <p class="text-[8px] text-gray-500 uppercase font-black">
                                        <?php echo $o['payment_method']; ?>
                                    </p>
                                </td>
                                <td class="px-8 py-6 text-blue-500 font-black tracking-tight">
                                    <?php echo $settings['currency_symbol'] . number_format($o['total_amount'], 2); ?>
                                </td>
                                <td class="px-8 py-6">
                                    <span
                                        class="px-4 py-2 rounded-full text-[8px] font-black uppercase tracking-widest <?php echo $o['status'] == 'completed' ? 'bg-green-500/10 text-green-500' : 'bg-yellow-500/10 text-yellow-500'; ?>">
                                        <?php echo $o['status']; ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-xs text-gray-500 font-medium">
                                    <?php echo date('M d, H:i', strtotime($o['created_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include 'common/bottom.php'; ?>