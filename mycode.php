<?php
include 'common/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT rc.*, p.name as product_name, p.image as product_image 
                      FROM redeem_codes rc
                      JOIN products p ON rc.product_id = p.id
                      JOIN orders o ON rc.order_id = o.id
                      WHERE o.user_id = ? AND o.status = 'completed'
                      ORDER BY rc.used_at DESC");
$stmt->execute([$userId]);
$codes = $stmt->fetchAll();

include 'common/header.php';
?>

<main class="max-w-2xl mx-auto px-4 py-8 space-y-8 min-h-screen">
    <div class="flex items-center justify-between gap-4">
        <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tight flex items-center gap-4">
            <span class="w-2 h-10 bg-blue-600 rounded-full"></span>
            MY CODES
        </h1>
        <a href="https://shop.garena.my/app" target="_blank"
            class="bg-blue-600 text-white px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest shadow-lg active:scale-95 transition-all">Redeem
            Code</a>
    </div>

    <?php if (empty($codes)): ?>
        <div class="py-20 text-center space-y-6">
            <div
                class="w-24 h-24 bg-gray-100 rounded-[2rem] flex items-center justify-center mx-auto text-gray-300 text-4xl">
                <i class="fa-solid fa-key"></i>
            </div>
            <div class="space-y-2">
                <h3 class="font-black text-gray-800 uppercase">No codes yet!</h3>
                <p class="text-sm font-medium text-gray-400">Your purchased voucher codes will appear here.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($codes as $code): ?>
                <div
                    class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-50 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl overflow-hidden bg-gray-50 shrink-0">
                            <img src="<?php echo $code['product_image']; ?>" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h3 class="font-black text-gray-800 uppercase text-xs tracking-tight">
                                <?php echo $code['product_name']; ?>
                            </h3>
                            <p class="text-xs font-bold text-blue-600 mt-0.5 tracking-widest">
                                <?php echo $code['code']; ?>
                            </p>
                        </div>
                    </div>
                    <button onclick="copyCode('<?php echo $code['code']; ?>')"
                        class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                        <i class="fa-solid fa-copy"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<script>
    function copyCode(code) {
        navigator.clipboard.writeText(code);
        showPopup('Copied!', 'Voucher code copied to clipboard.', 'success');
    }
</script>

<?php include 'common/sidebar.php';
include 'common/bottom.php';
include 'common/footer.php'; ?>