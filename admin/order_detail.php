<?php
include '../common/config.php';
if (!isAdminLoggedIn())
    redirect('login.php');

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT o.*, u.name as user_name, u.phone as user_phone, u.email as user_email, oi.quantity, oi.price as unit_price, oi.player_id, p.name as product_name, p.image as product_image
                      FROM orders o 
                      JOIN users u ON o.user_id = u.id 
                      JOIN order_items oi ON o.id = oi.order_id 
                      JOIN products p ON oi.product_id = p.id
                      WHERE o.id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order)
    redirect('order.php');

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    // If completed, check if it's a voucher order and assign code
    if ($status == 'completed') {
        // Find the product being ordered
        $stmt = $pdo->prepare("SELECT oi.product_id, c.type FROM order_items oi 
                              JOIN products p ON oi.product_id = p.id 
                              JOIN categories c ON p.cat_id = c.id 
                              WHERE oi.order_id = ? LIMIT 1");
        $stmt->execute([$id]);
        $prod = $stmt->fetch();

        if ($prod && $prod['type'] == 'voucher') {
            // Check if a code is already linked (to avoid double assignment)
            $stmt = $pdo->prepare("SELECT id FROM redeem_codes WHERE order_id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                // Assign a free code
                $stmt = $pdo->prepare("UPDATE redeem_codes SET order_id = ?, status = 'expired', used_at = CURRENT_TIMESTAMP WHERE product_id = ? AND status = 'active' LIMIT 1");
                $stmt->execute([$id, $prod['product_id']]);
            }
        }
    }

    redirect("order_detail.php?id=$id");
}

include 'common/header.php';
include 'common/sidebar.php';
?>

<main class="flex-1 lg:ml-64 p-6 sm:p-10 space-y-10 min-h-screen">
    <?php
    $title = "Order Processor";
    $subtitle = "Viewing details for Order #" . $order['id'];
    include 'common/topbar.php';
    ?>
    <div class="flex items-center justify-between">
        <a href="order.php"
            class="text-blue-500 font-bold text-xs sm:text-sm flex items-center gap-2 hover:translate-x-[-4px] transition-transform">
            <i class="fa-solid fa-arrow-left text-xs"></i> Back to Orders
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-[#1E293B] rounded-[2.5rem] border border-white/5 overflow-hidden">
                <div class="p-8 border-b border-white/5 flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden bg-white/5 group relative">
                        <img src="../<?php echo $order['product_image']; ?>" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white uppercase tracking-tight">
                            <?php echo $order['product_name']; ?>
                        </h3>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-1">Quantity:
                            <?php echo $order['quantity']; ?> Unit
                        </p>
                    </div>
                </div>
                <div class="p-8 grid grid-cols-2 sm:grid-cols-4 gap-8">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Player ID</p>
                        <p class="text-white font-black">
                            <?php echo $order['player_id'] ?: 'N/A'; ?>
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Total Price</p>
                        <p class="text-blue-500 font-black">
                            <?php echo $settings['currency_symbol'] . number_format($order['total_amount'], 2); ?>
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Unit Price</p>
                        <p class="text-gray-400 font-bold">
                            <?php echo $settings['currency_symbol'] . number_format($order['unit_price'], 2); ?>
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Method</p>
                        <p class="text-gray-400 font-bold uppercase">
                            <?php echo $order['payment_method']; ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-[#1E293B] rounded-[2.5rem] p-8 border border-white/5 space-y-6">
                <h3 class="text-lg font-black text-white uppercase flex items-center gap-3">
                    <span class="w-2 h-6 bg-purple-500 rounded-full"></span>
                    Delivered Assets
                </h3>
                <?php
                $stmt = $pdo->prepare("SELECT code FROM redeem_codes WHERE order_id = ?");
                $stmt->execute([$id]);
                $code = $stmt->fetchColumn();
                if ($code):
                    ?>
                    <div class="flex items-center justify-between p-6 bg-white/5 rounded-2xl border border-white/5">
                        <div>
                            <p class="text-[8px] text-gray-500 font-black uppercase mb-1">Redeem Code</p>
                            <p class="text-xl font-black text-blue-500 tracking-widest uppercase"><?php echo $code; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-blue-500/10 text-blue-500 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-key"></i>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-xs font-bold text-gray-500 italic">No code assigned to this order yet.</p>
                <?php endif; ?>
            </div>

            <div class="bg-[#1E293B] rounded-[2.5rem] p-8 border border-white/5 space-y-6">
                <h3 class="text-lg font-black text-white uppercase flex items-center gap-3">
                    <span class="w-2 h-6 bg-blue-500 rounded-full"></span>
                    Customer Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center text-blue-500"><i
                                class="fa-solid fa-user"></i></div>
                        <div>
                            <p class="text-xs font-bold text-white">
                                <?php echo $order['user_name']; ?>
                            </p>
                            <p class="text-[8px] text-gray-500 font-black uppercase">Name</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center text-green-500"><i
                                class="fa-solid fa-phone"></i></div>
                        <div>
                            <p class="text-xs font-bold text-white">
                                <?php echo $order['user_phone']; ?>
                            </p>
                            <p class="text-[8px] text-gray-500 font-black uppercase">Phone</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center text-purple-500">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white">
                                <?php echo $order['user_email']; ?>
                            </p>
                            <p class="text-[8px] text-gray-500 font-black uppercase">Email</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Actions -->
        <div class="space-y-8">
            <div class="bg-[#1E293B] rounded-[2.5rem] p-8 border border-white/5 space-y-6">
                <h3 class="text-lg font-black text-white uppercase text-center">Update Status</h3>
                <form method="POST" class="space-y-4">
                    <div class="flex flex-col gap-3">
                        <label
                            class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border-2 border-transparent cursor-pointer has-[:checked]:border-yellow-500 has-[:checked]:bg-yellow-500/10 transition-all">
                            <input type="radio" name="status" value="pending" class="hidden" <?php echo $order['status'] == 'pending' ? 'checked' : ''; ?>>
                            <i class="fa-solid fa-spinner text-yellow-500"></i>
                            <span
                                class="text-sm font-bold text-gray-400 group-checked:text-white uppercase tracking-tight">Pending</span>
                        </label>
                        <label
                            class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border-2 border-transparent cursor-pointer has-[:checked]:border-green-500 has-[:checked]:bg-green-500/10 transition-all">
                            <input type="radio" name="status" value="completed" class="hidden" <?php echo $order['status'] == 'completed' ? 'checked' : ''; ?>>
                            <i class="fa-solid fa-circle-check text-green-500"></i>
                            <span class="text-sm font-bold text-gray-400 uppercase tracking-tight">Complete</span>
                        </label>
                        <label
                            class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border-2 border-transparent cursor-pointer has-[:checked]:border-red-500 has-[:checked]:bg-red-500/10 transition-all">
                            <input type="radio" name="status" value="cancelled" class="hidden" <?php echo $order['status'] == 'cancelled' ? 'checked' : ''; ?>>
                            <i class="fa-solid fa-circle-xmark text-red-500"></i>
                            <span class="text-sm font-bold text-gray-400 uppercase tracking-tight">Cancel</span>
                        </label>
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-2xl shadow-blue-500/20 active:scale-95 transition-all mt-4">
                        UPDATE ORDER STATUS
                    </button>
                </form>
            </div>

            <a href="invoice.php?id=<?php echo $order['id']; ?>" target="_blank"
                class="bg-white/10 hover:bg-white/20 rounded-[2.5rem] p-8 shadow-2xl text-center space-y-4 relative overflow-hidden block transition-all group">
                <div class="relative z-10 text-white">
                    <i
                        class="fa-solid fa-print text-4xl text-white/50 mb-4 block group-hover:scale-110 transition-transform"></i>
                    <h3 class="font-black uppercase">Invoice Generator</h3>
                    <p class="text-[10px] text-white/60 font-medium">Click here to generate and print official invoice.
                    </p>
                </div>
                <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-white/10 rounded-full"></div>
            </a>
        </div>
    </div>
</main>

<?php include 'common/bottom.php'; ?>