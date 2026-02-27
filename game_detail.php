<?php
include 'common/config.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    redirect('index.php');
}

// Fetch Products for this category
$stmt = $pdo->prepare("SELECT * FROM products WHERE cat_id = ? ORDER BY price ASC");
$stmt->execute([$id]);
$products = $stmt->fetchAll();

// Fetch Related Products (Random 6 from other categories)
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id != ? ORDER BY RAND() LIMIT 6");
$stmt->execute([$id]);
$related = $stmt->fetchAll();

include 'common/header.php';
?>

<main class="max-w-4xl mx-auto px-4 py-8 space-y-8">

    <!-- Hero Section -->
    <div
        class="bg-white rounded-[2.5rem] p-6 sm:p-8 shadow-xl border border-gray-50 flex flex-col sm:flex-row gap-8 items-center">
        <div class="w-40 h-40 rounded-[2rem] overflow-hidden shadow-2xl shrink-0">
            <img src="<?php echo $category['image']; ?>" class="w-full h-full object-cover">
        </div>
        <div class="text-center sm:text-left space-y-3">
            <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tight">
                <?php echo $category['name']; ?>
            </h1>
            <div class="flex flex-wrap justify-center sm:justify-start gap-2">
                <span
                    class="px-4 py-1.5 bg-blue-50 text-blue-600 text-[10px] font-black uppercase rounded-full border border-blue-100">Official
                    Partner</span>
                <span
                    class="px-4 py-1.5 bg-green-50 text-green-600 text-[10px] font-black uppercase rounded-full border border-green-100">Instant
                    Delivery</span>
                <span
                    class="px-4 py-1.5 bg-purple-50 text-purple-600 text-[10px] font-black uppercase rounded-full border border-purple-100">
                    <?php echo $category['type'] == 'uid' ? 'UID TOP UP' : 'VOUCHER'; ?>
                </span>
            </div>
            <p class="text-gray-400 text-sm font-medium leading-relaxed sm:max-w-xl">
                <?php echo $category['description'] ?: 'Top up ' . $category['name'] . ' easily and quickly on Prime TopUp. The best prices and instant delivery guaranteed.'; ?>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Panel -->
        <div class="lg:col-span-2 space-y-8">
            <!-- 1. Select Recharge -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold shadow-lg shadow-blue-200">
                        1</div>
                    <h2 class="text-xl font-black text-gray-800 uppercase tracking-tight">Select Recharge</h2>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-2 gap-4">
                    <?php if (empty($products)): ?>
                        <div
                            class="col-span-full py-10 bg-gray-50 rounded-3xl text-center text-gray-400 font-bold border-2 border-dashed border-gray-200">
                            No recharge options available for this game.
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <label class="relative block group cursor-pointer">
                                <input type="radio" name="product_id" value="<?php echo $product['id']; ?>"
                                    data-price="<?php echo $product['price']; ?>" class="peer hidden product-radio">
                                <div
                                    class="bg-white border-2 border-gray-100 rounded-3xl p-5 transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50/50 hover:shadow-xl hover:border-blue-300">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="space-y-1">
                                            <p class="font-black text-gray-800 uppercase text-xs tracking-tight line-clamp-1">
                                                <?php echo $product['name']; ?>
                                            </p>
                                            <p class="text-blue-600 font-black text-sm">
                                                <?php echo ($settings['currency_symbol'] ?? '৳') . number_format($product['price'], 2); ?>
                                            </p>
                                        </div>
                                        <div
                                            class="w-8 h-8 rounded-full border-2 border-gray-100 peer-checked:border-blue-600 flex items-center justify-center p-1 group-hover:border-blue-200 transition-all">
                                            <div
                                                class="w-full h-full rounded-full bg-blue-600 opacity-0 transition-opacity product-check-dot">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- 2. Account Info (Only if UID type) -->
            <?php if ($category['type'] == 'uid'): ?>
                <section class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold shadow-lg shadow-blue-200">
                            2</div>
                        <h2 class="text-xl font-black text-gray-800 uppercase tracking-tight">Account Info</h2>
                    </div>
                    <div class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-50 space-y-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Player ID
                                (UID)</label>
                            <input type="text" id="player_id" placeholder="Enter your Player ID"
                                class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl py-4 px-6 focus:bg-white focus:border-blue-600 transition-all outline-none font-bold text-lg">
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- 3. Payment Method -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold shadow-lg shadow-blue-200">
                        <?php echo $category['type'] == 'uid' ? '3' : '2'; ?>
                    </div>
                    <h2 class="text-xl font-black text-gray-800 uppercase tracking-tight">Select Payment</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="relative block group cursor-pointer">
                        <input type="radio" name="payment_type" value="wallet" checked class="peer hidden">
                        <div
                            class="bg-white border-2 border-gray-100 rounded-3xl p-5 h-full transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50/50 hover:shadow-xl">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-xl shrink-0">
                                    <i class="fa-solid fa-wallet"></i>
                                </div>
                                <div class="space-y-1">
                                    <p class="font-black text-gray-800 uppercase text-xs tracking-tight">Wallet Pay</p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">Balance:
                                        <?php echo ($settings['currency_symbol'] ?? '৳') . number_format($_SESSION['balance'] ?? 0, 2); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="relative block group cursor-pointer">
                        <input type="radio" name="payment_type" value="instant" class="peer hidden">
                        <div
                            class="bg-white border-2 border-gray-100 rounded-3xl p-5 h-full transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50/50 hover:shadow-xl">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-xl shrink-0">
                                    <i class="fa-solid fa-bolt-lightning"></i>
                                </div>
                                <div class="space-y-1">
                                    <p class="font-black text-gray-800 uppercase text-xs tracking-tight">Instant Pay</p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">Bikash / Nagad / Rocket</p>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </section>

            <!-- Buy Button -->
            <div class="bg-white rounded-[2rem] p-8 shadow-2xl border border-gray-50 space-y-6">
                <div class="flex items-center justify-between text-gray-800">
                    <div class="items-center gap-4 hidden sm:flex">
                        <div class="flex items-center bg-gray-50 rounded-2xl p-1">
                            <button onclick="updateQty(-1)"
                                class="w-10 h-10 rounded-xl bg-white border border-gray-100 shadow-sm flex items-center justify-center active:scale-90 transition-all"><i
                                    class="fa-solid fa-minus text-xs"></i></button>
                            <span id="qty-text" class="w-12 text-center font-black text-lg">1</span>
                            <button onclick="updateQty(1)"
                                class="w-10 h-10 rounded-xl bg-white border border-gray-100 shadow-sm flex items-center justify-center active:scale-90 transition-all"><i
                                    class="fa-solid fa-plus text-xs"></i></button>
                        </div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Quantity</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Payable</p>
                        <h3 id="total-price" class="text-3xl font-black text-blue-600">
                            <?php echo $settings['currency_symbol']; ?> 0.00
                        </h3>
                    </div>
                </div>
                <button onclick="handleBuyNow()"
                    class="w-full bg-blue-600 text-white py-5 rounded-2xl font-black text-xl shadow-2xl shadow-blue-200 active:scale-95 transition-all overflow-hidden relative group">
                    <span class="relative z-10 flex items-center justify-center gap-4">
                        BUY NOW <i class="fa-solid fa-cart-shopping"></i>
                    </span>
                    <div
                        class="absolute inset-0 bg-white/20 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000">
                    </div>
                </button>
            </div>
        </div>

        <!-- Rules Panel -->
        <div class="space-y-8">
            <div class="bg-[#1E293B] text-white rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden">
                <i class="fa-solid fa-shield-halved absolute -right-4 -bottom-4 text-8xl opacity-10 -rotate-12"></i>
                <h3 class="font-black text-lg mb-6 flex items-center gap-3">
                    <span class="w-2 h-6 bg-blue-500 rounded-full"></span>
                    RULES & CONDITIONS
                </h3>
                <div class="space-y-4 text-xs font-medium text-gray-400 leading-relaxed">
                    <p class="flex gap-3"><span class="text-blue-500 font-black">•</span> Please make sure to provide
                        the correct Player ID. Errors cannot be refunded.</p>
                    <p class="flex gap-3"><span class="text-blue-500 font-black">•</span> Orders are usually processed
                        within 2-15 minutes.</p>
                    <p class="flex gap-3"><span class="text-blue-500 font-black">•</span> For any issues, contact our
                        support team immediately.</p>
                </div>
            </div>

            <!-- Related Products -->
            <div class="space-y-4">
                <h3 class="font-black text-gray-800 uppercase tracking-tight flex items-center gap-3">
                    <span class="w-2 h-6 bg-red-500 rounded-full"></span>
                    MOST POPULAR
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <?php foreach ($related as $rel): ?>
                        <a href="game_detail.php?id=<?php echo $rel['id']; ?>"
                            class="bg-white rounded-[2rem] p-3 shadow-md border border-gray-50 flex flex-col items-center gap-2 group">
                            <div class="w-full aspect-square rounded-[1.5rem] overflow-hidden">
                                <img src="<?php echo $rel['image']; ?>"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                            </div>
                            <span
                                class="text-[10px] font-black text-gray-800 uppercase tracking-tighter text-center line-clamp-1">
                                <?php echo $rel['name']; ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    let selectedPrice = 0;
    let qty = 1;

    document.querySelectorAll('.product-radio').forEach(radio => {
        radio.onchange = () => {
            selectedPrice = parseFloat(radio.dataset.price);
            updateTotal();

            // Visual indicator
            document.querySelectorAll('.product-check-dot').forEach(dot => dot.classList.add('opacity-0'));
            radio.closest('label').querySelector('.product-check-dot').classList.remove('opacity-0');
        };
    });

    function updateQty(n) {
        qty = Math.max(1, qty + n);
        document.getElementById('qty-text').innerText = qty;
        updateTotal();
    }

    function updateTotal() {
        const total = selectedPrice * qty;
        document.getElementById('total-price').innerText = '<?php echo $settings["currency_symbol"]; ?> ' + total.toLocaleString(undefined, { minimumFractionDigits: 2 });
    }

    function handleBuyNow() {
        const productId = document.querySelector('input[name="product_id"]:checked')?.value;
        const paymentType = document.querySelector('input[name="payment_type"]:checked').value;
        const playerId = document.getElementById('player_id')?.value || '';

        if (!productId) {
            showPopup('Wait!', 'Please select a recharge option first.', 'warning');
            return;
        }

        if (<?php echo $category['type'] == 'uid' ? 'true' : 'false'; ?> && !playerId) {
            showPopup('Wait!', 'Please enter your Player ID.', 'warning');
            return;
        }

        showLoading();

        // In a real app, you'd send this to a PHP script via AJAX
        // For now, let's simulate the flow
        setTimeout(() => {
            hideLoading();
            if (paymentType === 'instant') {
                window.location.href = `instantpay.php?pid=${productId}&qty=${qty}&uid=${playerId}`;
            } else {
                // Wallet pay logic
                fetch('api/process_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        productId, qty, playerId, paymentType: 'wallet'
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showPopup('Success!', 'Order placed successfully using wallet balance.', 'success');
                            setTimeout(() => window.location.href = 'order.php', 1500);
                        } else {
                            showPopup('Error!', data.message, 'error');
                        }
                    });
            }
        }, 1000);
    }
</script>

<?php include 'common/sidebar.php';
include 'common/bottom.php';
include 'common/footer.php'; ?>