<?php
include '../common/config.php';
if (!isAdminLoggedIn())
    redirect('login.php');

// Handle AJAX Operations
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    if ($action == 'add' || $action == 'edit') {
        $name = $_POST['name'];
        $type = $_POST['type'];
        $desc = $_POST['description'];
        $id = $_POST['id'] ?? null;

        $image_path = $_POST['current_image'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/games/";
            $file_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $file_name = time() . "_" . mt_rand(100, 999) . "." . $file_ext;
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = "uploads/games/" . $file_name;
            }
        }

        if ($action == 'add') {
            $stmt = $pdo->prepare("INSERT INTO categories (name, type, description, image) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $type, $desc, $image_path]);
        } else {
            $stmt = $pdo->prepare("UPDATE categories SET name=?, type=?, description=?, image=? WHERE id=?");
            $stmt->execute([$name, $type, $desc, $image_path, $id]);
        }
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($action == 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success']);
        exit;
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
include 'common/header.php';
include 'common/sidebar.php';
?>

<main class="flex-1 lg:ml-64 p-6 sm:p-10 space-y-10 min-h-screen">
    <?php
    $title = "Game Categories";
    $subtitle = "Manage your game products";
    include 'common/topbar.php';
    ?>

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-black text-white uppercase tracking-tight">Active Catalog</h2>
        <button onclick="openGameModal()"
            class="bg-blue-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-2xl font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-2xl flex items-center gap-3">
            <i class="fa-solid fa-plus text-xs"></i> <span class="hidden sm:inline">Add New Game</span><span
                class="sm:hidden">Add Game</span>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($categories as $cat): ?>
            <div class="bg-[#1E293B] rounded-[2.5rem] border border-white/5 overflow-hidden group">
                <div class="h-48 relative overflow-hidden">
                    <img src="../<?php echo $cat['image']; ?>"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#1E293B] to-transparent"></div>
                    <div class="absolute top-4 right-4 flex gap-2">
                        <button onclick='openGameModal(<?php echo json_encode($cat); ?>)'
                            class="w-10 h-10 bg-white/10 hover:bg-blue-600 text-white rounded-xl backdrop-blur-md flex items-center justify-center transition-all shadow-xl">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </button>
                        <button onclick="deleteGame(<?php echo $cat['id']; ?>)"
                            class="w-10 h-10 bg-white/10 hover:bg-red-600 text-white rounded-xl backdrop-blur-md flex items-center justify-center transition-all shadow-xl">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6 text-center">
                    <span
                        class="text-[8px] font-black text-blue-500 uppercase tracking-widest bg-blue-500/10 px-4 py-1.5 rounded-full mb-3 inline-block">
                        <?php echo $cat['type'] == 'uid' ? 'UID Top Up' : 'Unipin Voucher'; ?>
                    </span>
                    <h3 class="text-lg font-black text-white uppercase tracking-tight line-clamp-1">
                        <?php echo $cat['name']; ?>
                    </h3>
                    <p class="text-xs text-gray-500 mt-2 font-medium line-clamp-2">
                        <?php echo $cat['description'] ?: 'No description provided.'; ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Modal -->
    <div id="game-modal"
        class="fixed inset-0 bg-black/80 z-[100] hidden flex items-center justify-center p-6 backdrop-blur-md">
        <div class="bg-[#1E293B] w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/10">
            <div class="p-8 border-b border-white/5 flex items-center justify-between">
                <h3 id="modal-title" class="text-xl font-black text-white uppercase">Add New Game</h3>
                <button onclick="closeGameModal()" class="text-gray-500 hover:text-white"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="game-form" class="p-8 space-y-6">
                <input type="hidden" name="id" id="game-id">
                <input type="hidden" name="action" id="game-action" value="add">
                <input type="hidden" name="current_image" id="game-current-image">

                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Game Name</label>
                    <input type="text" name="name" id="game-name" required
                        class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Selection Type</label>
                    <select name="type" id="game-type"
                        class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                        <option value="uid" class="bg-[#1E293B]">UID Top Up</option>
                        <option value="voucher" class="bg-[#1E293B]">Unipin Voucher</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Description /
                        Rules</label>
                    <textarea name="description" id="game-desc" rows="3"
                        class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Image
                        Thumbnail</label>
                    <input type="file" name="image"
                        class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none">
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 text-white py-5 rounded-2xl font-black text-lg shadow-2xl shadow-blue-500/20 active:scale-95 transition-all">SAVE
                    CHANGES</button>
            </form>
        </div>
    </div>
</main>

<script>
    function openGameModal(data = null) {
        const modal = document.getElementById('game-modal');
        const form = document.getElementById('game-form');
        form.reset();

        if (data) {
            document.getElementById('modal-title').innerText = "Edit Game";
            document.getElementById('game-action').value = "edit";
            document.getElementById('game-id').value = data.id;
            document.getElementById('game-name').value = data.name;
            document.getElementById('game-type').value = data.type;
            document.getElementById('game-desc').value = data.description;
            document.getElementById('game-current-image').value = data.image;
        } else {
            document.getElementById('modal-title').innerText = "Add New Game";
            document.getElementById('game-action').value = "add";
            document.getElementById('game-id').value = "";
        }

        modal.classList.remove('hidden');
    }

    function closeGameModal() {
        document.getElementById('game-modal').classList.add('hidden');
    }

    document.getElementById('game-form').onsubmit = function (e) {
        e.preventDefault();
        showAdminLoading();
        const formData = new FormData(this);
        fetch('game.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(res => {
                hideAdminLoading();
                if (res.status === 'success') {
                    showAdminToast('Category saved successfully!');
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
    };

    function deleteGame(id) {
        if (confirm('Are you sure you want to delete this game and its products?')) {
            showAdminLoading();
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('id', id);
            fetch('game.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(res => {
                    hideAdminLoading();
                    if (res.status === 'success') {
                        showAdminToast('Game deleted.');
                        setTimeout(() => window.location.reload(), 1000);
                    }
                });
        }
    }
</script>

<?php include 'common/bottom.php'; ?>