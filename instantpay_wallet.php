<?php
include 'common/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$amount = $_GET['amount'] ?? 0;
if ($amount <= 0)
    redirect('addmoney.php');

// Fetch Payment Methods
$stmt = $pdo->query("SELECT * FROM payment_methods ORDER BY id ASC");
$methods = $stmt->fetchAll();

include 'common/header.php';
?>

<main class="max-w-xl mx-auto px-4 py-8 space-y-8 min-h-screen">
    <div class="bg-white rounded-[2.5rem] p-8 shadow-2xl border border-gray-50 text-center space-y-4">
        <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Adding Balance</p>
        <h1 class="text-5xl font-black text-blue-600">
            <?php echo $settings['currency_symbol'] . number_format($amount, 2); ?>
        </h1>
    </div>

    <div class="space-y-4">
        <h2 class="text-xl font-black text-gray-800 uppercase tracking-tight flex items-center gap-3">
            <span class="w-2 h-6 bg-blue-600 rounded-full"></span>
            Select Payment Method
        </h2>

        <div class="grid grid-cols-1 gap-4">
            <?php foreach ($methods as $method): ?>
                <button onclick="selectMethod(<?php echo htmlspecialchars(json_encode($method)); ?>)"
                    class="bg-white border-2 border-gray-100 rounded-3xl p-5 flex items-center justify-between group hover:border-blue-600 hover:shadow-xl transition-all">
                    <div class="flex items-center gap-6">
                        <div
                            class="w-16 h-12 rounded-xl overflow-hidden bg-gray-50 flex items-center justify-center p-2 border border-gray-100 group-hover:border-blue-100">
                            <img src="<?php echo $method['logo']; ?>" class="w-full h-full object-contain">
                        </div>
                        <span class="font-black text-gray-800 uppercase tracking-tight">
                            <?php echo $method['name']; ?>
                        </span>
                    </div>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="payment-step-2" class="hidden space-y-6">
        <!-- Same UI as instantpay.php but for wallet topup -->
        <div class="bg-[#1E293B] text-white rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden">
            <div id="qr-container" class="mb-8 hidden">
                <div class="w-48 h-48 mx-auto bg-white p-4 rounded-3xl shadow-2xl">
                    <img id="qr-image" src="" class="w-full h-full object-contain">
                </div>
            </div>
            <div class="text-center space-y-4">
                <div class="bg-white/5 rounded-2xl p-6 border border-white/5">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Send money to this
                        number</p>
                    <div class="flex items-center justify-center gap-4">
                        <h3 id="admin-number" class="text-3xl font-black text-blue-400 tracking-wider"></h3>
                        <button onclick="copyNumber()"
                            class="w-12 h-12 rounded-2xl bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all"><i
                                class="fa-solid fa-copy"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <form id="wallet-form" class="space-y-6">
            <input type="hidden" name="method_id" id="selected-method-id">
            <input type="hidden" name="amount" value="<?php echo $amount; ?>">
            <div class="bg-white rounded-[2.5rem] p-8 shadow-2xl border border-gray-50 space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Your
                        Number</label>
                    <input type="text" name="sender_number" required
                        class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl py-4 px-6 outline-none font-bold">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Transaction
                        ID</label>
                    <input type="text" name="trx_id" required
                        class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl py-4 px-6 outline-none font-bold">
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-5 rounded-2xl font-black text-xl shadow-2xl shadow-blue-200">DONE</button>
            </div>
        </form>
    </div>
</main>

<script>
    function selectMethod(method) {
        document.getElementById('payment-step-2').classList.remove('hidden');
        document.getElementById('selected-method-id').value = method.id;
        document.getElementById('admin-number').innerText = method.number;
        if (method.qr_image) {
            document.getElementById('qr-image').src = method.qr_image;
            document.getElementById('qr-container').classList.remove('hidden');
        }
    }
    function copyNumber() {
        navigator.clipboard.writeText(document.getElementById('admin-number').innerText);
        showPopup('Copied!', 'Number copied.', 'success');
    }
    document.getElementById('wallet-form').onsubmit = function (e) {
        e.preventDefault();
        showLoading();
        const formData = new FormData(this);
        fetch('api/process_wallet.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                hideLoading();
                if (data.status === 'success') {
                    showPopup('Success!', 'Wallet top-up request submitted.', 'success');
                    setTimeout(() => window.location.href = 'payment_history.php', 1500);
                } else {
                    showPopup('Error!', data.message, 'error');
                }
            });
    };
</script>

<?php include 'common/sidebar.php';
include 'common/bottom.php';
include 'common/footer.php'; ?>