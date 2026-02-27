<?php
include '../common/config.php';
if (!isAdminLoggedIn())
    redirect('login.php');

if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    if ($action == 'add') {
        $cat_id = $_POST['cat_id'];
        $product_id = $_POST['product_id'];
        $code = $_POST['code'];
        $stmt = $pdo->prepare("INSERT INTO redeem_codes (cat_id, product_id, code, status) VALUES (?, ?, ?, 'active')");
        $stmt->execute([$cat_id, $product_id, $code]);
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($action == 'delete') {
        $id = $_POST['id'];
        $pdo->prepare("DELETE FROM redeem_codes WHERE id = ?")->execute([$id]);
        echo json_encode(['status' => 'success']);
        exit;
    }
}

$categories = $pdo->query("SELECT id, name FROM categories WHERE type='voucher'")->fetchAll();
$products = $pdo->query("SELECT p.*, c.id as cat_id FROM products p JOIN categories c ON p.cat_id = c.id WHERE c.type='voucher'")->fetchAll();

$active_codes = $pdo->query("SELECT rc.*, p.name as product_name, c.name as cat_name FROM redeem_codes rc JOIN products p ON rc.product_id = p.id JOIN categories c ON rc.cat_id = c.id WHERE rc.status='active' ORDER BY rc.id DESC")->fetchAll();

$expired_codes = $pdo->query("SELECT rc.*, p.name as product_name, c.name as cat_name FROM redeem_codes rc JOIN products p ON rc.product_id = p.id JOIN categories c ON rc.cat_id = c.id WHERE rc.status='expired' ORDER BY rc.used_at DESC")->fetchAll();

include 'common/header.php';
include 'common/sidebar.php';
?>

<main class="flex-1 lg:ml-64 p-6 sm:p-10 space-y-10 min-h-screen">
    <?php
    $title = "Voucher Management";
    $subtitle = "Manage digital redeem codes and delivery";
    include 'common/topbar.php';
    ?>

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-black text-white uppercase tracking-tight">Voucher Inventory</h2>
        <button onclick="document.getElementById('code-modal').classList.remove('hidden')"
            class="bg-blue-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-2xl font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-2xl flex items-center gap-3">
            <i class="fa-solid fa-plus text-xs"></i> <span class="hidden sm:inline">Add New Code</span><span
                class="sm:hidden">Add Code</span>
        </button>
    </div>

    <!-- Tabs -->
    <div class="flex gap-4 p-1.5 bg-white/5 rounded-2xl w-fit">
        <button onclick="switchCodeTab('active')" id="tab-active"
            class="px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-widest transition-all bg-blue-600 text-white">Active
            Codes</button>
        <button onclick="switchCodeTab('expired')" id="tab-expired"
            class="px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-widest transition-all text-gray-500 hover:bg-white/5">Used
            / Expired</button>
    </div>

    <!-- Active Codes List -->
    <div id="active-sec" class="bg-[#1E293B] rounded-[2.5rem] border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-black/20 text-gray-500 text-[10px] font-black uppercase tracking-widest">
                        <th class="px-8 py-6">Game</th>
                        <th class="px-8 py-6">Package</th>
                        <th class="px-8 py-6">Code</th>
                        <th class="px-8 py-6">Added On</th>
                        <th class="px-8 py-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($active_codes)): ?>
                        <tr>
                            <td colspan="5" class="px-8 py-10 text-center text-gray-500">No active codes available.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($active_codes as $ac): ?>
                            <tr class="hover:bg-white/5 transition-all">
                                <td class="px-8 py-6 text-[10px] font-black text-white uppercase">
                                    <?php echo $ac['cat_name']; ?>
                                </td>
                                <td class="px-8 py-6 font-bold text-gray-400 text-xs">
                                    <?php echo $ac['product_name']; ?>
                                </td>
                                <td class="px-8 py-6"><span
                                        class="bg-blue-600/10 text-blue-500 px-4 py-1.5 rounded-lg font-black text-xs tracking-widest">
                                        <?php echo $ac['code']; ?>
                                    </span></td>
                                <td class="px-8 py-6 text-[10px] text-gray-500">
                                    <?php echo date('d M, Y', strtotime($ac['created_at'])); ?>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <button onclick="deleteCode(<?php echo $ac['id']; ?>)"
                                        class="text-red-500 hover:text-red-400 p-2"><i
                                            class="fa-solid fa-trash-can"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Expired Codes List -->
    <div id="expired-sec" class="hidden bg-[#1E293B] rounded-[2.5rem] border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-black/20 text-gray-500 text-[10px] font-black uppercase tracking-widest">
                        <th class="px-8 py-6">Game</th>
                        <th class="px-8 py-6">Package</th>
                        <th class="px-8 py-6">Code</th>
                        <th class="px-8 py-6">Used On</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($expired_codes)): ?>
                        <tr>
                            <td colspan="4" class="px-8 py-10 text-center text-gray-500">History is empty.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($expired_codes as $ec): ?>
                            <tr>
                                <td class="px-8 py-6 text-[10px] font-black text-white uppercase">
                                    <?php echo $ec['cat_name']; ?>
                                </td>
                                <td class="px-8 py-6 font-bold text-gray-400 text-xs">
                                    <?php echo $ec['product_name']; ?>
                                </td>
                                <td class="px-8 py-6"><span
                                        class="text-gray-600 line-through font-black text-xs tracking-widest">
                                        <?php echo $ec['code']; ?>
                                    </span></td>
                                <td class="px-8 py-6 text-[10px] text-gray-500">
                                    <?php echo date('d M, Y H:i', strtotime($ec['used_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="code-modal"
        class="fixed inset-0 bg-black/80 z-[100] hidden flex items-center justify-center p-6 backdrop-blur-md">
        <div class="bg-[#1E293B] w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/10">
            <div class="p-8 border-b border-white/5 flex items-center justify-between">
                <h3 class="text-xl font-black text-white uppercase">Add Admin Voucher</h3>
                <button onclick="document.getElementById('code-modal').classList.add('hidden')"
                    class="text-gray-500 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="code-form" class="p-8 space-y-6">
                <input type="hidden" name="action" value="add">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Select Product</label>
                    <select name="product_id" id="sel-prod" required
                        class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                        <?php foreach ($products as $pr): ?>
                            <option value="<?php echo $pr['id']; ?>" data-cat="<?php echo $pr['cat_id']; ?>"
                                class="bg-[#1E293B]">
                                <?php echo $pr['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="cat_id" id="hidden-cat-id">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Voucher Code</label>
                    <input type="text" name="code" required
                        class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-5 rounded-2xl font-black text-lg shadow-2xl shadow-blue-500/20 active:scale-95 transition-all">ADD
                    TO INVENTORY</button>
            </form>
        </div>
    </div>
</main>

<script>
    function switchCodeTab(tab) {
        const activeBtn = document.getElementById('tab-active');
        const expiredBtn = document.getElementById('tab-expired');
        const activeSec = document.getElementById('active-sec');
        const expiredSec = document.getElementById('expired-sec');

        if (tab === 'active') {
            activeSec.classList.remove('hidden');
            expiredSec.classList.add('hidden');
            activeBtn.classList.add('bg-blue-600', 'text-white');
            activeBtn.classList.remove('text-gray-500');
            expiredBtn.classList.add('text-gray-500');
            expiredBtn.classList.remove('bg-blue-600', 'text-white');
        } else {
            expiredSec.classList.remove('hidden');
            activeSec.classList.add('hidden');
            expiredBtn.classList.add('bg-blue-600', 'text-white');
            expiredBtn.classList.remove('text-gray-500');
            activeBtn.classList.add('text-gray-500');
            activeBtn.classList.remove('bg-blue-600', 'text-white');
        }
    }

    document.getElementById('code-form').onsubmit = function (e) {
        e.preventDefault();
        const sel = document.getElementById('sel-prod');
        document.getElementById('hidden-cat-id').value = sel.options[sel.selectedIndex].dataset.cat;
        showAdminLoading();
        fetch('redeemcode.php', { method: 'POST', body: new FormData(this) })
            .then(r => r.json()).then(res => { hideAdminLoading(); location.reload(); });
    };

    function deleteCode(id) {
        if (confirm('Delete?')) {
            showAdminLoading();
            const fd = new FormData(); fd.append('action', 'delete'); fd.append('id', id);
            fetch('redeemcode.php', { method: 'POST', body: fd })
                .then(r => r.json()).then(res => { hideAdminLoading(); location.reload(); });
        }
    }
</script>

<?php include 'common/bottom.php'; ?>