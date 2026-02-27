<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
<aside id="admin-sidebar"
    class="fixed top-0 left-0 bottom-0 w-64 bg-[#1E293B] border-r border-white/5 z-50 translate-x-[-100%] lg:translate-x-0 transition-transform duration-300">
    <div class="p-8 border-b border-white/5 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg"><i
                    class="fa-solid fa-cube"></i></div>
            <span class="font-black text-white uppercase tracking-tight">Core <span
                    class="text-blue-500">Panel</span></span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="toggleAdminTheme()"
                class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-gray-400 hover:text-white transition-all"><i
                    class="fa-solid fa-circle-half-stroke"></i></button>
            <button onclick="toggleAdminSidebar()" class="lg:hidden text-gray-500"><i
                    class="fa-solid fa-xmark"></i></button>
        </div>
    </div>

    <div class="p-4 space-y-2 py-8 overflow-y-auto" style="height: calc(100vh - 100px);">
        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest px-4 mb-4">Core Analytics</p>
        <a href="index.php"
            class="flex items-center gap-4 px-4 py-4 rounded-2xl transition-all <?php echo $current_page == 'index.php' ? 'sidebar-active text-white' : 'hover:bg-white/5 text-gray-400'; ?>">
            <i class="fa-solid fa-grid-2 text-sm w-5"></i>
            <span class="text-sm font-bold">Dashboard</span>
        </a>
        <a href="order.php"
            class="flex items-center gap-4 px-4 py-4 rounded-2xl transition-all <?php echo $current_page == 'order.php' ? 'sidebar-active text-white' : 'hover:bg-white/5 text-gray-400'; ?>">
            <i class="fa-solid fa-cart-flatbed-suitcases text-sm w-5"></i>
            <span class="text-sm font-bold">Manage Orders</span>
        </a>
        <a href="wallet_requests.php"
            class="flex items-center gap-4 px-4 py-4 rounded-2xl transition-all <?php echo $current_page == 'wallet_requests.php' ? 'sidebar-active text-white' : 'hover:bg-white/5 text-gray-400'; ?>">
            <i class="fa-solid fa-hand-holding-dollar text-sm w-5"></i>
            <span class="text-sm font-bold">Wallet Requests</span>
            <?php
            $pending_count = $pdo->query("SELECT COUNT(*) FROM wallet_topup WHERE status='pending'")->fetchColumn();
            if ($pending_count > 0):
                ?>
                <span
                    class="ml-auto bg-yellow-500 text-black text-[8px] font-black w-4 h-4 rounded-full flex items-center justify-center"><?php echo $pending_count; ?></span>
            <?php endif; ?>
        </a>

        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest px-4 mt-8 mb-4">Catalog Management</p>
        <a href="game.php"
            class="flex items-center gap-4 px-4 py-4 rounded-2xl transition-all <?php echo $current_page == 'game.php' ? 'sidebar-active text-white' : 'hover:bg-white/5 text-gray-400'; ?>">
            <i class="fa-solid fa-gamepad text-sm w-5"></i>
            <span class="text-sm font-bold">Games / Categories</span>
        </a>
        <a href="product.php"
            class="flex items-center gap-4 px-4 py-4 rounded-2xl transition-all <?php echo $current_page == 'product.php' ? 'sidebar-active text-white' : 'hover:bg-white/5 text-gray-400'; ?>">
            <i class="fa-solid fa-box-archive text-sm w-5"></i>
            <span class="text-sm font-bold">Inventory / Product</span>
        </a>
        <a href="redeemcode.php"
            class="flex items-center gap-4 px-4 py-4 rounded-2xl transition-all <?php echo $current_page == 'redeemcode.php' ? 'sidebar-active text-white' : 'hover:bg-white/5 text-gray-400'; ?>">
            <i class="fa-solid fa-key text-sm w-5"></i>
            <span class="text-sm font-bold">Voucher Codes</span>
        </a>

        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest px-4 mt-8 mb-4">System Assets</p>
        <a href="sliders.php"
            class="flex items-center gap-4 px-4 py-4 rounded-2xl transition-all <?php echo $current_page == 'sliders.php' ? 'sidebar-active text-white' : 'hover:bg-white/5 text-gray-400'; ?>">
            <i class="fa-solid fa-images text-sm w-5"></i>
            <span class="text-sm font-bold">Banner Sliders</span>
        </a>
        <a href="paymentmathod.php"
            class="flex items-center gap-4 px-4 py-4 rounded-2xl transition-all <?php echo $current_page == 'paymentmathod.php' ? 'sidebar-active text-white' : 'hover:bg-white/5 text-gray-400'; ?>">
            <i class="fa-solid fa-wallet text-sm w-5"></i>
            <span class="text-sm font-bold">Payment Methods</span>
        </a>
        <a href="user.php"
            class="flex items-center gap-4 px-4 py-4 rounded-2xl transition-all <?php echo $current_page == 'user.php' ? 'sidebar-active text-white' : 'hover:bg-white/5 text-gray-400'; ?>">
            <i class="fa-solid fa-users text-sm w-5"></i>
            <span class="text-sm font-bold">Registered Users</span>
        </a>

        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest px-4 mt-8 mb-4">Configuration</p>
        <a href="setting.php"
            class="flex items-center gap-4 px-4 py-4 rounded-2xl transition-all <?php echo $current_page == 'setting.php' ? 'sidebar-active text-white' : 'hover:bg-white/5 text-gray-400'; ?>">
            <i class="fa-solid fa-gears text-sm w-5"></i>
            <span class="text-sm font-bold">Global Settings</span>
        </a>

        <div class="pt-8 pb-4">
            <a href="logout.php"
                class="flex items-center gap-4 px-4 py-4 rounded-2xl bg-red-500/10 text-red-500 font-bold hover:bg-red-500/20 transition-all">
                <i class="fa-solid fa-right-from-bracket text-sm w-5"></i>
                <span class="text-sm">Secure Logout</span>
            </a>
        </div>
    </div>
</aside>

<div id="admin-sidebar-overlay" onclick="toggleAdminSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden">
</div>

<script>
    function toggleAdminSidebar() {
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('admin-sidebar-overlay');
        sidebar.classList.toggle('translate-x-[-100%]');
        overlay.classList.toggle('hidden');
    }
</script>