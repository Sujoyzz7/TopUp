<!-- Sidebar Wrap -->
<div id="sidebar-overlay" onclick="toggleSidebar()"
    class="fixed inset-0 bg-black/50 z-[60] opacity-0 pointer-events-none transition-opacity duration-300"></div>
<aside id="sidebar"
    class="fixed top-0 left-0 bottom-0 w-72 bg-white z-[70] translate-x-[-100%] transition-transform duration-300 ease-out shadow-2xl flex flex-col">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="bg-blue-600 text-white p-2 rounded-lg text-sm"><i class="fa-solid fa-bolt"></i></div>
            <span class="font-bold text-lg">Prime TopUp</span>
        </div>
        <button onclick="toggleSidebar()" class="text-gray-400 hover:text-gray-600"><i
                class="fa-solid fa-xmark text-xl"></i></button>
    </div>

    <div class="flex-1 overflow-y-auto p-4 space-y-2">
        <a href="index.php"
            class="flex items-center gap-4 p-3 rounded-xl hover:bg-blue-50 text-gray-700 font-medium transition-all group">
            <i class="fa-solid fa-house text-blue-600 group-hover:scale-110"></i>
            <span>Home</span>
        </a>
        <a href="addmoney.php"
            class="flex items-center gap-4 p-3 rounded-xl hover:bg-blue-50 text-gray-700 font-medium transition-all group">
            <i class="fa-solid fa-circle-plus text-blue-600 group-hover:scale-110"></i>
            <span>Add Money</span>
        </a>
        <a href="order.php"
            class="flex items-center gap-4 p-3 rounded-xl hover:bg-blue-50 text-gray-700 font-medium transition-all group">
            <i class="fa-solid fa-receipt text-blue-600 group-hover:scale-110"></i>
            <span>My Orders</span>
        </a>
        <a href="mycode.php"
            class="flex items-center gap-4 p-3 rounded-xl hover:bg-blue-50 text-gray-700 font-medium transition-all group">
            <i class="fa-solid fa-key text-blue-600 group-hover:scale-110"></i>
            <span>My Codes</span>
        </a>
        <a href="profile.php"
            class="flex items-center gap-4 p-3 rounded-xl hover:bg-blue-50 text-gray-700 font-medium transition-all group">
            <i class="fa-solid fa-user text-blue-600 group-hover:scale-110"></i>
            <span>Profile</span>
        </a>
        <div class="pt-4 border-t border-gray-50 mt-4">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold px-3 mb-2">Support</p>
            <a href="<?php echo $settings['fab_link'] ?? '#'; ?>"
                class="flex items-center gap-4 p-3 rounded-xl hover:bg-green-50 text-gray-700 font-medium transition-all group">
                <i class="fa-brands fa-whatsapp text-green-500 group-hover:scale-110"></i>
                <span>Customer Support</span>
            </a>
        </div>
    </div>

    <?php if (isLoggedIn()): ?>
        <div class="p-4 border-t border-gray-100">
            <a href="logout.php"
                class="flex items-center gap-4 p-3 rounded-xl bg-red-50 text-red-600 font-bold transition-all hover:bg-red-100">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    <?php endif; ?>
</aside>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (sidebar.classList.contains('translate-x-[-100%]')) {
            sidebar.classList.remove('translate-x-[-100%]');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100');
        } else {
            sidebar.classList.add('translate-x-[-100%]');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            overlay.classList.remove('opacity-100');
        }
    }
</script>