<?php
include '../common/config.php';
if (!isAdminLoggedIn())
    redirect('login.php');

if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    if ($action == 'add' || $action == 'edit') {
        $name = $_POST['name'];
        $number = $_POST['number'];
        $desc = $_POST['description'];
        $id = $_POST['id'] ?? null;

        $logo_path = $_POST['current_logo'] ?? '';
        $qr_path = $_POST['current_qr'] ?? '';

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $logo_name = time() . "_logo_" . $_FILES["logo"]["name"];
            move_uploaded_file($_FILES["logo"]["tmp_name"], "../uploads/payments/" . $logo_name);
            $logo_path = "uploads/payments/" . $logo_name;
        }
        if (isset($_FILES['qr_image']) && $_FILES['qr_image']['error'] == 0) {
            $qr_name = time() . "_qr_" . $_FILES["qr_image"]["name"];
            move_uploaded_file($_FILES["qr_image"]["tmp_name"], "../uploads/payments/" . $qr_name);
            $qr_path = "uploads/payments/" . $qr_name;
        }

        if ($action == 'add') {
            $stmt = $pdo->prepare("INSERT INTO payment_methods (name, logo, qr_image, number, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $logo_path, $qr_path, $number, $desc]);
        } else {
            $stmt = $pdo->prepare("UPDATE payment_methods SET name=?, logo=?, qr_image=?, number=?, description=? WHERE id=?");
            $stmt->execute([$name, $logo_path, $qr_path, $number, $desc, $id]);
        }
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($action == 'delete') {
        $id = $_POST['id'];
        $pdo->prepare("DELETE FROM payment_methods WHERE id = ?")->execute([$id]);
        echo json_encode(['status' => 'success']);
        exit;
    }
}

$methods = $pdo->query("SELECT * FROM payment_methods ORDER BY id DESC")->fetchAll();

include 'common/header.php';
include 'common/sidebar.php';
?>

<main class="flex-1 lg:ml-64 p-6 sm:p-10 space-y-10 min-h-screen">
    <?php
    $title = "Payment Gateways";
    $subtitle = "Manage secure payment methods and instructions";
    include 'common/topbar.php';
    ?>

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-black text-white uppercase tracking-tight">Active Gateways</h2>
        <button onclick="openMethodModal()"
            class="bg-blue-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-2xl font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-2xl flex items-center gap-3">
            <i class="fa-solid fa-plus-circle text-xs"></i> <span class="hidden sm:inline">Add New Method</span><span
                class="sm:hidden">Add Method</span>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($methods as $m): ?>
            <div
                class="bg-[#1E293B] rounded-[2.5rem] border border-white/5 p-8 space-y-6 group hover:border-blue-500/50 transition-all">
                <div class="flex items-center justify-between">
                    <div
                        class="w-20 h-16 bg-white rounded-2xl p-3 flex items-center justify-center border-4 border-white/5">
                        <img src="../<?php echo $m['logo']; ?>" class="w-full h-full object-contain">
                    </div>
                    <div class="flex gap-2">
                        <button onclick='openMethodModal(<?php echo json_encode($m); ?>)'
                            class="w-10 h-10 bg-white/5 hover:bg-blue-600 rounded-xl text-white transition-all"><i
                                class="fa-solid fa-pen text-[10px]"></i></button>
                        <button onclick="deleteMethod(<?php echo $m['id']; ?>)"
                            class="w-10 h-10 bg-white/5 hover:bg-red-600 rounded-xl text-white transition-all"><i
                                class="fa-solid fa-trash text-[10px]"></i></button>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-black text-white uppercase tracking-tight">
                        <?php echo $m['name']; ?>
                    </h3>
                    <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest mt-1">
                        <?php echo $m['number']; ?>
                    </p>
                </div>
                <div class="pt-6 border-t border-white/5 flex items-center justify-between">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">QR Code Available</p>
                    <i
                        class="fa-solid <?php echo $m['qr_image'] ? 'fa-circle-check text-green-500' : 'fa-circle-xmark text-gray-700'; ?>"></i>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Modal -->
    <div id="method-modal"
        class="fixed inset-0 bg-black/80 z-[100] hidden flex items-center justify-center p-6 backdrop-blur-md">
        <div class="bg-[#1E293B] w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/10">
            <div class="p-8 border-b border-white/5 flex items-center justify-between">
                <h3 id="modal-title" class="text-xl font-black text-white uppercase tracking-widest">Configuration</h3>
                <button onclick="closeMethodModal()" class="text-gray-500 hover:text-white"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="method-form" class="p-8 space-y-5">
                <input type="hidden" name="id" id="method-id">
                <input type="hidden" name="action" id="method-action" value="add">
                <input type="hidden" name="current_logo" id="method-current-logo">
                <input type="hidden" name="current_qr" id="method-current-qr">

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Method
                            Name</label>
                        <input type="text" name="name" id="method-name" required
                            class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Admin
                            Number</label>
                        <input type="text" name="number" id="method-num" required
                            class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Short
                        Description</label>
                    <textarea name="description" id="method-desc" rows="2"
                        class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Logo Icon</label>
                        <input type="file" name="logo"
                            class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-3 px-4 text-xs text-white outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">QR Image</label>
                        <input type="file" name="qr_image"
                            class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-3 px-4 text-xs text-white outline-none">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 text-white py-5 rounded-2xl font-black text-sm uppercase tracking-widest shadow-2xl shadow-blue-500/20 active:scale-95 transition-all">SAVE
                    METHOD</button>
            </form>
        </div>
    </div>
</main>

<script>
    function openMethodModal(data = null) {
        const modal = document.getElementById('method-modal');
        const form = document.getElementById('method-form');
        form.reset();
        if (data) {
            document.getElementById('method-id').value = data.id;
            document.getElementById('method-action').value = 'edit';
            document.getElementById('method-name').value = data.name;
            document.getElementById('method-num').value = data.number;
            document.getElementById('method-desc').value = data.description;
            document.getElementById('method-current-logo').value = data.logo;
            document.getElementById('method-current-qr').value = data.qr_image;
        } else {
            document.getElementById('method-action').value = 'add';
            document.getElementById('method-id').value = '';
        }
        modal.classList.remove('hidden');
    }
    function closeMethodModal() { document.getElementById('method-modal').classList.add('hidden'); }
    document.getElementById('method-form').onsubmit = function (e) {
        e.preventDefault();
        showAdminLoading();
        fetch('paymentmathod.php', { method: 'POST', body: new FormData(this) })
            .then(r => r.json())
            .then(res => {
                hideAdminLoading();
                if (res.status === 'success') { showAdminToast('Saved!'); setTimeout(() => location.reload(), 1000); }
            });
    };
    function deleteMethod(id) {
        if (confirm('Delete?')) {
            showAdminLoading();
            const fd = new FormData(); fd.append('action', 'delete'); fd.append('id', id);
            fetch('paymentmathod.php', { method: 'POST', body: fd })
                .then(r => r.json()).then(res => { hideAdminLoading(); location.reload(); });
        }
    }
</script>

<?php include 'common/bottom.php'; ?>