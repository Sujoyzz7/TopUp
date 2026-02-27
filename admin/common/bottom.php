</div> <!-- Close Flex Wrap from header -->

<!-- Toast / Notification container -->
<div id="admin-toast"
    class="fixed bottom-10 right-10 z-[100] transform translate-y-20 opacity-0 transition-all duration-300">
    <div
        class="bg-blue-600 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-4 border border-white/10 font-bold text-sm">
        <i class="fa-solid fa-circle-check"></i>
        <span id="admin-toast-msg">Operation successful!</span>
    </div>
</div>

<!-- Admin Loading Overlay -->
<div id="admin-loading"
    class="fixed inset-0 bg-[#0F172A]/80 z-[100] hidden flex items-center justify-center backdrop-blur-sm">
    <div class="flex flex-col items-center gap-6">
        <div class="relative w-20 h-20">
            <div class="absolute inset-0 border-4 border-blue-500/20 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-blue-500 rounded-full border-t-transparent animate-spin"></div>
        </div>
        <p class="font-black text-white uppercase tracking-widest text-xs">Syncing Data...</p>
    </div>
</div>

<script>
    function showAdminToast(msg, type = 'success') {
        const toast = document.getElementById('admin-toast');
        const msgSpan = document.getElementById('admin-toast-msg');
        const toastInner = toast.querySelector('div');
        const toastIcon = toast.querySelector('i');

        msgSpan.innerText = msg;

        if (type === 'error') {
            toastInner.classList.replace('bg-blue-600', 'bg-red-600');
            toastIcon.className = 'fa-solid fa-circle-exclamation';
        } else {
            toastInner.classList.replace('bg-red-600', 'bg-blue-600');
            toastIcon.className = 'fa-solid fa-circle-check';
        }

        toast.classList.remove('translate-y-20', 'opacity-0');
        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 3000);
    }

    function showAdminLoading() {
        document.getElementById('admin-loading').classList.remove('hidden');
    }

    function hideAdminLoading() {
        document.getElementById('admin-loading').classList.add('hidden');
    }
</script>
</body>

</html>