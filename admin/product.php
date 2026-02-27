<?php
include '../common/config.php';
if (!isAdminLoggedIn())
    redirect('login.php');

if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    if ($action == 'add' || $action == 'edit') {
        $cat_id = $_POST['cat_id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $id = $_POST['id'] ?? null;

        if ($action == 'add') {
            $stmt = $pdo->prepare("INSERT INTO products (cat_id, name, price) VALUES (?, ?, ?)");
            $stmt->execute([$cat_id, $name, $price]);
        } else {
            $stmt = $pdo->prepare("UPDATE products SET cat_id=?, name=?, price=? WHERE id=?");
            $stmt->execute([$cat_id, $name, $price, $id]);
        }
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($action == 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success']);
        exit;
    }
}

$categories = $pdo->query("SELECT id, name FROM categories")->fetchAll();
$products = $pdo->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.cat_id = c.id ORDER BY p.id DESC")->fetchAll();

include 'common/header.php';
include 'common/sidebar.php';
?>

<main class="flex-1 lg:ml-64 p-6 sm:p-10 space-y-10 min-h-screen">
    <?php
    $title = "Inventory Management";
    $subtitle = "Configure recharge packages and prices";
    include 'common/topbar.php';
    ?>

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-black text-white uppercase tracking-tight">Active Packages</h2>
        <button onclick="openProductModal()"
            class="bg-blue-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-2xl font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-2xl flex items-center gap-3">
            <i class="fa-solid fa-plus text-xs"></i> <span class="hidden sm:inline">Add New Package</span><span
                class="sm:hidden">Add Package</span>
        </button>
    </div>

    <!-- User Table UI for Products -->
    <div class="bg-[#1E293B] rounded-[2.5rem] border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-black/20 text-gray-500 text-[10px] font-black uppercase tracking-widest">
                        <th class="px-8 py-6">ID</th>
                        <th class="px-8 py-6">Game / Category</th>
                        <th class="px-8 py-6">Package Name</th>
                        <th class="px-8 py-6">Price</th>
                        <th class="px-8 py-6">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="5" class="px-8 py-10 text-center text-gray-500 font-bold italic">No products yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr class="hover:bg-white/5 transition-all group">
                                <td class="px-8 py-6 font-black text-gray-400">#
                                    <?php echo $p['id']; ?>
                                </td>
                                <td class="px-8 py-6">
                                    <span
                                        class="bg-blue-600/10 text-blue-500 px-3 py-1 rounded-full text-[8px] font-black uppercase">
                                        <?php echo $p['cat_name']; ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6 font-bold text-white uppercase text-xs">
                                    <?php echo $p['name']; ?>
                                </td>
                                <td class="px-8 py-6 font-black text-blue-500">
                                    <?php echo $settings['currency_symbol'] . number_format($p['price'], 2); ?>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex gap-2">
                                        <button onclick='openProductModal(<?php echo json_encode($p); ?>)'
                                            class="w-9 h-9 bg-white/5 hover:bg-blue-600 text-white rounded-lg flex items-center justify-center transition-all">
                                            <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                        </button>
                                        <button onclick="deleteProduct(<?php echo $p['id']; ?>)"
                                            class="w-9 h-9 bg-white/5 hover:bg-red-600 text-white rounded-lg flex items-center justify-center transition-all">
                                            <i class="fa-solid fa-trash text-[10px]"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="product-modal"
        class="fixed inset-0 bg-black/80 z-[100] hidden flex items-center justify-center p-6 backdrop-blur-md">
        <div class="bg-[#1E293B] w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/10">
            <div class="p-8 border-b border-white/5 flex items-center justify-between">
                <h3 id="modal-title" class="text-xl font-black text-white uppercase">Add Package</h3>
                <button onclick="closeProductModal()" class="text-gray-500 hover:text-white"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="product-form" class="p-8 space-y-6">
                <input type="hidden" name="id" id="product-id">
                <input type="hidden" name="action" id="product-action" value="add">

                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Select Game</label>
                    <select name="cat_id" id="product-cat-id" required
                        class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                        <?php foreach ($categories as $c): ?>
                            <option value="<?php echo $c['id']; ?>" class="bg-[#1E293B]">
                                <?php echo $c['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Package Name (e.g. 100
                        Diamonds)</label>
                    <input type="text" name="name" id="product-name" required
                        class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Selling Price (
                        <?php echo $settings['currency_symbol']; ?>)
                    </label>
                    <input type="number" step="0.01" name="price" id="product-price" required
                        class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 text-white py-5 rounded-2xl font-black text-lg shadow-2xl shadow-blue-500/20 active:scale-95 transition-all">SAVE
                    PACKAGE</button>
            </form>
        </div>
    </div>
</main>

<script>
    function openProductModal(data = null) {
        const modal = document.getElementById('product-modal');
        const form = document.getElementById('product-form');
        form.reset();

        if (data) {
            document.getElementById('modal-title').innerText = "Edit Package";
            document.getElementById('product-action').value = "edit";
            document.getElementById('product-id').value = data.id;
            document.getElementById('product-cat-id').value = data.cat_id;
            document.getElementById('product-name').value = data.name;
            document.getElementById('product-price').value = data.price;
        } else {
            document.getElementById('modal-title').innerText = "Add Package";
            document.getElementById('product-action').value = "add";
            document.getElementById('product-id').value = "";
        }

        modal.classList.remove('hidden');
    }

    function closeProductModal() {
        document.getElementById('product-modal').classList.add('hidden');
    }

    document.getElementById('product-form').onsubmit = function (e) {
        e.preventDefault();
        showAdminLoading();
        const formData = new FormData(this);
        fetch('product.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(res => {
                hideAdminLoading();
                if (res.status === 'success') {
                    showAdminToast('Product saved successfully!');
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
    };

    function deleteProduct(id) {
        if (confirm('Delete this product?')) {
            showAdminLoading();
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('id', id);
            fetch('product.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(res => {
                    hideAdminLoading();
                    if (res.status === 'success') {
                        showAdminToast('Product deleted.');
                        setTimeout(() => window.location.reload(), 1000);
                    }
                });
        }
    }
</script>

<?php include 'common/bottom.php'; ?>