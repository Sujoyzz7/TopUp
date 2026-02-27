<?php
include 'common/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];

// Get fresh user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Stats
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$stmt->execute([$userId]);
$total_orders = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT SUM(total_amount) FROM orders WHERE user_id = ? AND status='completed'");
$stmt->execute([$userId]);
$total_spent = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->prepare("SELECT SUM(total_amount) FROM orders WHERE user_id = ? AND status='completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$stmt->execute([$userId]);
$weekly_spent = $stmt->fetchColumn() ?: 0;

include 'common/header.php';
?>

<main class="max-w-4xl mx-auto px-4 py-8 space-y-10 min-h-screen">

    <!-- Profile Header -->
    <div class="text-center space-y-4">
        <div class="relative inline-block group">
            <div
                class="w-32 h-32 rounded-full border-4 border-white shadow-2xl overflow-hidden bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-4xl text-white font-black">
                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
            </div>
            <div
                class="absolute bottom-1 right-1 w-10 h-10 bg-green-500 border-4 border-white rounded-full flex items-center justify-center text-white shadow-lg">
                <i class="fa-solid fa-check text-xs"></i>
            </div>
        </div>
        <div>
            <h1 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Hi,
                <?php echo $user['name']; ?>
            </h1>
            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mt-1">Available Balance:
                <?php echo $settings['currency_symbol'] . number_format($user['balance'], 2); ?>
            </p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
        <div
            class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-50 text-center space-y-2 group hover:bg-blue-600 transition-all duration-300">
            <p
                class="text-[10px] font-black text-gray-400 uppercase tracking-widest group-hover:text-blue-100 Transition-colors">
                Support Pin</p>
            <h3 class="text-2xl font-black text-gray-800 group-hover:text-white transition-colors">
                <?php echo rand(10000, 99999); ?>
            </h3>
        </div>
        <div
            class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-50 text-center space-y-2 group hover:bg-green-600 transition-all duration-300">
            <p
                class="text-[10px] font-black text-gray-400 uppercase tracking-widest group-hover:text-green-100 Transition-colors">
                Weekly Spent</p>
            <h3 class="text-2xl font-black text-gray-800 group-hover:text-white transition-colors">
                <?php echo $settings['currency_symbol'] . number_format($weekly_spent, 0); ?>
            </h3>
        </div>
        <div
            class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-50 text-center space-y-2 group hover:bg-purple-600 transition-all duration-300">
            <p
                class="text-[10px] font-black text-gray-400 uppercase tracking-widest group-hover:text-purple-100 Transition-colors">
                Total Spent</p>
            <h3 class="text-2xl font-black text-gray-800 group-hover:text-white transition-colors">
                <?php echo $settings['currency_symbol'] . number_format($total_spent, 0); ?>
            </h3>
        </div>
        <div
            class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-50 text-center space-y-2 group hover:bg-orange-600 transition-all duration-300">
            <p
                class="text-[10px] font-black text-gray-400 uppercase tracking-widest group-hover:text-orange-100 Transition-colors">
                Total Order</p>
            <h3 class="text-2xl font-black text-gray-800 group-hover:text-white transition-colors">
                <?php echo $total_orders; ?>
            </h3>
        </div>
    </div>

    <!-- Account Information -->
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-50 overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex items-center gap-4">
            <div
                class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-address-card"></i>
            </div>
            <h2 class="text-xl font-black text-gray-800 uppercase tracking-tight">Account Information</h2>
        </div>
        <div class="p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Full Name</p>
                    <p class="font-black text-gray-800 text-lg">
                        <?php echo $user['name']; ?>
                    </p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Email Address</p>
                    <p class="font-black text-gray-800 text-lg">
                        <?php echo $user['email']; ?>
                    </p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Phone Number</p>
                    <p class="font-black text-gray-800 text-lg">
                        <?php echo $user['phone']; ?>
                    </p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Wallet Balance</p>
                    <p class="font-black text-blue-600 text-lg">
                        <?php echo $settings['currency_symbol'] . number_format($user['balance'], 2); ?>
                    </p>
                </div>
            </div>
            <div class="pt-8 flex flex-col sm:flex-row gap-4">
                <a href="payment_history.php"
                    class="flex-1 bg-gray-900 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl flex items-center justify-center gap-3">
                    Payment History <i class="fa-solid fa-clock-rotate-left"></i>
                </a>
                <a href="logout.php"
                    class="flex-1 bg-red-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl flex items-center justify-center gap-3">
                    Logout <i class="fa-solid fa-power-off"></i>
                </a>
            </div>
        </div>
    </div>

</main>

<?php include 'common/sidebar.php';
include 'common/bottom.php';
include 'common/footer.php'; ?>