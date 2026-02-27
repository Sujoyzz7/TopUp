<div class="h-20 sm:hidden"></div> <!-- Spacer for mobile bottom nav -->
<nav
    class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 flex items-center justify-around py-3 px-2 z-40 sm:hidden glass">
    <a href="index.php"
        class="flex flex-col items-center gap-1 <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'text-blue-600' : 'text-gray-400'; ?>">
        <i class="fa-solid fa-house text-lg"></i>
        <span class="text-[10px] font-bold">Home</span>
    </a>
    <a href="addmoney.php"
        class="flex flex-col items-center gap-1 <?php echo (basename($_SERVER['PHP_SELF']) == 'addmoney.php') ? 'text-blue-600' : 'text-gray-400'; ?>">
        <i class="fa-solid fa-wallet text-lg"></i>
        <span class="text-[10px] font-bold">Add Money</span>
    </a>
    <a href="order.php"
        class="flex flex-col items-center gap-1 <?php echo (basename($_SERVER['PHP_SELF']) == 'order.php') ? 'text-blue-600' : 'text-gray-400'; ?>">
        <i class="fa-solid fa-receipt text-lg"></i>
        <span class="text-[10px] font-bold">My Orders</span>
    </a>
    <a href="mycode.php"
        class="flex flex-col items-center gap-1 <?php echo (basename($_SERVER['PHP_SELF']) == 'mycode.php') ? 'text-blue-600' : 'text-gray-400'; ?>">
        <i class="fa-solid fa-key text-lg"></i>
        <span class="text-[10px] font-bold">My Codes</span>
    </a>
    <a href="profile.php"
        class="flex flex-col items-center gap-1 <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'text-blue-600' : 'text-gray-400'; ?>">
        <i class="fa-solid fa-user text-lg"></i>
        <span class="text-[10px] font-bold">Profile</span>
    </a>
</nav>

<!-- FAB Support -->
<a href="<?php echo $settings['fab_link'] ?? '#'; ?>"
    class="fixed bottom-24 right-5 sm:bottom-10 sm:right-10 w-14 h-14 bg-red-600 text-white rounded-full flex items-center justify-center shadow-2xl z-40 pulse-animation hover:scale-110 transition-transform">
    <i class="fa-solid fa-headset text-2xl"></i>
    <span
        class="absolute -top-10 right-0 bg-red-600 text-white text-[10px] font-bold px-3 py-1.5 rounded-full whitespace-nowrap shadow-lg">সাহায্য
        লাগবে ?</span>
</a>

<!-- Loading Modal -->
<div id="loading-modal"
    class="fixed inset-0 bg-black/60 z-[100] hidden flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white p-8 rounded-2xl shadow-2xl flex flex-col items-center gap-4">
        <div class="flex gap-2 text-blue-600">
            <div class="w-3 h-3 bg-current rounded-full animate-bounce [animation-delay:-0.3s]"></div>
            <div class="w-3 h-3 bg-current rounded-full animate-bounce [animation-delay:-0.15s]"></div>
            <div class="w-3 h-3 bg-current rounded-full animate-bounce"></div>
        </div>
        <p class="font-bold text-gray-700">Processing...</p>
    </div>
</div>

<!-- Custom Popup -->
<div id="popup-modal"
    class="fixed inset-0 bg-black/60 z-[100] hidden flex items-center justify-center backdrop-blur-sm p-6">
    <div class="bg-white w-full max-w-sm rounded-[2rem] shadow-2xl overflow-hidden transform transition-all scale-90 opacity-0"
        id="popup-content">
        <div class="p-8 text-center flex flex-col items-center">
            <div id="popup-icon" class="w-20 h-20 rounded-full flex items-center justify-center mb-6 text-4xl"></div>
            <h3 id="popup-title" class="text-2xl font-bold mb-2">Success!</h3>
            <p id="popup-message" class="text-gray-500 font-medium mb-8"></p>
            <button onclick="closePopup()"
                class="w-full bg-[#1E293B] text-white py-4 rounded-2xl font-bold shadow-xl shadow-slate-200 active:scale-95 transition-all">Continue</button>
        </div>
    </div>
</div>

<script>
    // Security & UI settings
    document.addEventListener('contextmenu', event => event.preventDefault());
    document.addEventListener('keydown', function (event) {
        if (event.ctrlKey && (event.key === 'u' || event.key === 's' || event.key === 'i' || event.key === 'j' || event.key === 'c')) {
            event.preventDefault();
        }
    });

    function showLoading() {
        document.getElementById('loading-modal').classList.remove('hidden');
    }

    function hideLoading() {
        document.getElementById('loading-modal').classList.add('hidden');
    }

    function showPopup(title, message, type = 'success') {
        const modal = document.getElementById('popup-modal');
        const content = document.getElementById('popup-content');
        const iconDiv = document.getElementById('popup-icon');
        const titleH3 = document.getElementById('popup-title');
        const messageP = document.getElementById('popup-message');

        titleH3.innerText = title;
        messageP.innerText = message;

        // Reset styles
        iconDiv.className = "w-20 h-20 rounded-full flex items-center justify-center mb-6 text-4xl ";

        if (type === 'success') {
            iconDiv.innerHTML = '<i class="fa-solid fa-circle-check"></i>';
            iconDiv.classList.add('bg-green-100', 'text-green-500');
        } else if (type === 'error') {
            iconDiv.innerHTML = '<i class="fa-solid fa-circle-xmark"></i>';
            iconDiv.classList.add('bg-red-100', 'text-red-500');
        } else if (type === 'warning') {
            iconDiv.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
            iconDiv.classList.add('bg-yellow-100', 'text-yellow-500');
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-90', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closePopup() {
        const modal = document.getElementById('popup-modal');
        const content = document.getElementById('popup-content');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-90', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>