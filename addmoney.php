<?php
include 'common/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

include 'common/header.php';
?>

<main class="max-w-xl mx-auto px-4 py-8 space-y-10 min-h-screen">

    <div class="space-y-4">
        <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tight flex items-center gap-4">
            <span class="w-2 h-10 bg-blue-600 rounded-full"></span>
            ADD MONEY
        </h1>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest pl-6">Securely add balance to your wallet
        </p>
    </div>

    <!-- Add Money Form -->
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-50 overflow-hidden">
        <div class="p-8 space-y-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Enter the
                    amount</label>
                <div class="relative group">
                    <span
                        class="absolute left-6 top-1/2 -translate-y-1/2 text-2xl font-black text-gray-300 group-focus-within:text-blue-600">
                        <?php echo $settings['currency_symbol']; ?>
                    </span>
                    <input type="number" id="balance_amount" placeholder="Amount"
                        class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl py-5 pl-14 pr-6 focus:bg-white focus:border-blue-600 transition-all outline-none font-black text-2xl"
                        min="10">
                </div>
            </div>
            <button onclick="redirectToAddMoney()"
                class="w-full bg-blue-600 text-white py-5 rounded-2xl font-black text-xl shadow-2xl shadow-blue-200 active:scale-95 transition-all">
                CLICK HERE TO ADD MONEY
            </button>
        </div>
    </div>

    <!-- Video Guide Section -->
    <div class="space-y-6">
        <h2 class="text-xl font-black text-gray-800 uppercase tracking-tight flex items-center gap-3">
            <span class="w-2 h-6 bg-green-500 rounded-full"></span>
            HOW TO ADD MONEY
        </h2>

        <?php if ($settings['youtube_link']): ?>
            <div class="aspect-video bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border-8 border-white">
                <?php
                // Simple logic to extract youtube ID
                $url = $settings['youtube_link'];
                $video_id = '';
                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
                    $video_id = $match[1];
                }
                ?>
                <?php if ($video_id): ?>
                    <iframe class="w-full h-full" src="https://www.youtube.com/embed/<?php echo $video_id; ?>" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
                <?php else: ?>
                    <div
                        class="w-full h-full flex items-center justify-center bg-gray-50 text-gray-400 p-10 text-center italic">
                        <a href="<?php echo $url; ?>" target="_blank" class="font-black text-blue-600 underline">WATCH GUIDE ON
                            YOUTUBE</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div
                class="py-12 bg-white rounded-[2.5rem] text-center text-gray-400 font-bold border-2 border-dashed border-gray-100 italic">
                Video guide coming soon!
            </div>
        <?php endif; ?>
    </div>

</main>

<script>
    function redirectToAddMoney() {
        const amount = document.getElementById('balance_amount').value;
        if (!amount || amount < 10) {
            showPopup('Wait!', 'Please enter at least <?php echo $settings["currency_symbol"]; ?> 10.', 'warning');
            return;
        }
        // For manual topup, we can repurpose the flow or have a dedicated page.
        // The prompt says "Redirect to instantpay.php"
        // So we'll pass a special flag or just handle it there.
        // Let's assume a dummy product for 'Wallet Topup' in index 0 or handle separately.
        window.location.href = `instantpay_wallet.php?amount=${amount}`;
    }
</script>

<?php include 'common/sidebar.php';
include 'common/bottom.php';
include 'common/footer.php'; ?>