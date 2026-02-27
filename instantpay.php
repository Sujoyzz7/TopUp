<?php
include 'common/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$pid = $_GET['pid'] ?? 0;
$qty = $_GET['qty'] ?? 1;
$uid = $_GET['uid'] ?? '';

$stmt = $pdo->prepare("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.cat_id = c.id WHERE p.id = ?");
$stmt->execute([$pid]);
$product = $stmt->fetch();

if (!$product) {
    redirect('index.php');
}

$total_payable = $product['price'] * $qty;

// Fetch Payment Methods
$stmt = $pdo->query("SELECT * FROM payment_methods ORDER BY id ASC");
$methods = $stmt->fetchAll();

include 'common/header.php';
?>

<main class="max-w-xl mx-auto px-4 py-8 space-y-8">
    <div class="bg-white rounded-[2.5rem] p-8 shadow-2xl border border-gray-50 text-center space-y-4">
        <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Total Payable Amount</p>
        <h1 class="text-5xl font-black text-blue-600">
            <?php echo $settings['currency_symbol'] . number_format($total_payable, 2); ?>
        </h1>
        <div class="pt-4 border-t border-gray-50">
            <p class="text-sm font-bold text-gray-800 uppercase tracking-tight">
                <?php echo $product['cat_name']; ?> -
                <?php echo $product['name']; ?> (x
                <?php echo $qty; ?>)
            </p>
            <?php if ($uid): ?>
                <p class="text-xs text-gray-400 font-bold uppercase mt-1">Player ID: <span class="text-blue-600">
                        <?php echo $uid; ?>
                    </span></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="space-y-4">
        <h2 class="text-xl font-black text-gray-800 uppercase tracking-tight flex items-center gap-3">
            <span class="w-2 h-6 bg-blue-600 rounded-full"></span>
            Select Payment Method
        </h2>

        <div class="grid grid-cols-1 gap-4">
            <?php if (empty($methods)): ?>
                <div
                    class="p-8 bg-white rounded-3xl text-center text-gray-400 font-bold border-2 border-dashed border-gray-100 italic">
                    No payment methods configured by admin.
                </div>
            <?php else: ?>
                <?php foreach ($methods as $method): ?>
                    <button onclick="selectMethod(<?php echo htmlspecialchars(json_encode($method)); ?>)"
                        class="bg-white border-2 border-gray-100 rounded-3xl p-5 flex items-center justify-between group hover:border-blue-600 hover:shadow-xl transition-all text-left">
                        <div class="flex items-center gap-6">
                            <div
                                class="w-16 h-12 rounded-xl overflow-hidden bg-gray-50 flex items-center justify-center p-2 border border-gray-100 group-hover:border-blue-100">
                                <?php if ($method['logo']): ?>
                                    <img src="<?php echo $method['logo']; ?>" class="w-full h-full object-contain">
                                <?php else: ?>
                                    <i class="fa-solid fa-money-bill-transfer text-blue-600 text-xl"></i>
                                <?php endif; ?>
                            </div>
                            <span class="font-black text-gray-800 uppercase tracking-tight">
                                <?php echo $method['name']; ?>
                            </span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-300 group-hover:text-blue-600 transition-colors"></i>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Hidden Payment Step -->
    <div id="payment-step-2" class="hidden animate-in fade-in slide-in-from-bottom-5 duration-500 space-y-6">
        <div class="bg-[#1E293B] text-white rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden">
            <div id="qr-container" class="mb-8 hidden">
                <p class="text-center text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Scan QR Code
                    to Pay</p>
                <div class="w-48 h-48 mx-auto bg-white p-4 rounded-3xl shadow-2xl">
                    <img id="qr-image" src="" class="w-full h-full object-contain">
                </div>
            </div>

            <div class="text-center space-y-4">
                <p id="method-desc" class="text-gray-400 text-xs font-medium px-4"></p>
                <div class="bg-white/5 rounded-2xl p-6 border border-white/5">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Send money to this
                        number</p>
                    <div class="flex items-center justify-center gap-4">
                        <h3 id="admin-number" class="text-3xl font-black text-blue-400 tracking-wider"></h3>
                        <button onclick="copyNumber()"
                            class="w-12 h-12 rounded-2xl bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all">
                            <i class="fa-solid fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <form id="payment-form" class="space-y-6">
            <input type="hidden" name="method_id" id="selected-method-id">
            <input type="hidden" name="product_id" value="<?php echo $pid; ?>">
            <input type="hidden" name="quantity" value="<?php echo $qty; ?>">
            <input type="hidden" name="player_id" value="<?php echo $uid; ?>">

            <div class="bg-white rounded-[2.5rem] p-8 shadow-2xl border border-gray-50 space-y-6">
                <div class="space-y-2">
                    <label id="number-label"
                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Your Number</label>
                    <input type="text" name="sender_number" placeholder="Enter your mobile number" required
                        class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl py-4 px-6 focus:bg-white focus:border-blue-600 transition-all outline-none font-bold">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Transaction
                        ID</label>
                    <input type="text" name="trx_id" placeholder="Enter TRX ID from SMS" required
                        class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl py-4 px-6 focus:bg-white focus:border-blue-600 transition-all outline-none font-bold">
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-5 rounded-2xl font-black text-xl shadow-2xl shadow-blue-200 active:scale-95 transition-all">
                    SUBMIT PAYMENT
                </button>
            </div>
        </form>
    </div>
</main>

<script>
    function selectMethod(method) {
        document.getElementById('payment-step-2').classList.remove('hidden');
        document.getElementById('selected-method-id').value = method.id;
        document.getElementById('admin-number').innerText = method.number;
        document.getElementById('method-desc').innerText = method.description || 'Please complete the payment and provide your details below.';
        document.getElementById('number-label').innerText = 'Enter your ' + method.name + ' number';

        const qrContainer = document.getElementById('qr-container');
        const qrImage = document.getElementById('qr-image');
        if (method.qr_image) {
            qrImage.src = method.qr_image;
            qrContainer.classList.remove('hidden');
        } else {
            qrContainer.classList.add('hidden');
        }

        window.scrollTo({ top: document.getElementById('payment-step-2').offsetTop - 100, behavior: 'smooth' });
    }

    function copyNumber() {
        const num = document.getElementById('admin-number').innerText;
        navigator.clipboard.writeText(num);
        showPopup('Copied!', 'Number copied to clipboard.', 'success');
    }

    document.getElementById('payment-form').onsubmit = function (e) {
        e.preventDefault();
        showLoading();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        fetch('api/process_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ...data, paymentType: 'instant' })
        })
            .then(res => res.json())
            .then(data => {
                hideLoading();
                if (data.status === 'success') {
                    showPopup('Order Placed!', 'Your order is pending verification. Redirecting...', 'success');
                    setTimeout(() => window.location.href = 'order.php', 2000);
                } else {
                    showPopup('Error!', data.message, 'error');
                }
            });
    };
</script>

<?php include 'common/sidebar.php';
include 'common/bottom.php';
include 'common/footer.php'; ?>