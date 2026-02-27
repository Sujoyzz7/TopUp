<div class="flex items-center justify-between mb-8">
    <div class="lg:hidden flex items-center gap-4">
        <button onclick="toggleAdminSidebar()"
            class="w-12 h-12 bg-[#1E293B] rounded-2xl flex items-center justify-center text-white shadow-xl shadow-black/20 border border-white/5">
            <i class="fa-solid fa-bars-staggered"></i>
        </button>
        <div class="flex flex-col">
            <span class="font-black text-white uppercase tracking-tight text-xs leading-none">Admin Panel</span>
            <span class="text-[8px] font-black text-blue-500 uppercase tracking-widest mt-1">
                <?php echo $title ?? 'Dashboard'; ?>
            </span>
        </div>
    </div>
    <div class="hidden lg:block space-y-1">
        <h1 class="text-3xl font-black text-white uppercase tracking-tight">
            <?php echo $title ?? 'Dashboard'; ?>
        </h1>
        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">
            <?php echo $subtitle ?? 'System Monitor'; ?>
        </p>
    </div>

    <div class="flex items-center gap-4">
        <div class="hidden sm:flex flex-col items-end">
            <p class="text-white font-bold text-sm">
                <?php echo $_SESSION['admin_username'] ?? 'Admin'; ?>
            </p>
            <p class="text-[10px] text-green-500 font-black uppercase tracking-widest">System Master</p>
        </div>
        <div
            class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-2xl shadow-blue-500/20">
            <i class="fa-solid fa-user-gear"></i>
        </div>
    </div>
</div>