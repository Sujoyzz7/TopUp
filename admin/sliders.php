<?php
include '../common/config.php';
if (!isAdminLoggedIn())
    redirect('login.php');

if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    if ($action == 'add') {
        $link = $_POST['link'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/sliders/";
            $file_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $file_name = time() . "_" . mt_rand(100, 999) . "." . $file_ext;
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = "uploads/sliders/" . $file_name;
                $stmt = $pdo->prepare("INSERT INTO sliders (image, link) VALUES (?, ?)");
                $stmt->execute([$image_path, $link]);
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Upload failed.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Please select a valid image file.']);
        }
        exit;
    }

    if ($action == 'delete') {
        $id = (int) $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM sliders WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Slider not found or already deleted.']);
        }
        exit;
    }
}

$sliders = $pdo->query("SELECT * FROM sliders ORDER BY id DESC")->fetchAll();

include 'common/header.php';
include 'common/sidebar.php';
?>

<main class="flex-1 lg:ml-64 p-6 sm:p-10 space-y-10 min-h-screen">
    <?php
    $title = "Banner Sliders";
    $subtitle = "Manage promotional banners and links";
    include 'common/topbar.php';
    ?>

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-black text-white uppercase tracking-tight">Active Banners</h2>
        <button onclick="document.getElementById('slider-modal').classList.remove('hidden')"
            class="bg-blue-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-2xl font-black text-[10px] sm:text-xs uppercase tracking-widest shadow-2xl flex items-center gap-3">
            <i class="fa-solid fa-plus text-xs"></i> <span class="hidden sm:inline">Add New Slider</span><span
                class="sm:hidden">Add Slider</span>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($sliders as $s): ?>
            <div class="bg-[#1E293B] rounded-[2.5rem] border border-white/5 overflow-hidden group relative">
                <img src="../<?php echo $s['image']; ?>"
                    class="w-full h-48 object-cover opacity-80 group-hover:opacity-100 transition-all">
                <div
                    class="p-6 flex items-center justify-between absolute bottom-0 left-0 right-0 bg-gradient-to-t from-[#1E293B] to-transparent pt-20">
                    <span
                        class="text-[10px] font-black text-white/50 bg-white/5 px-4 py-2 rounded-full truncate max-w-[150px]">
                        <?php echo $s['link'] ?: 'No link'; ?>
                    </span>
                    <button onclick="deleteSlider(<?php echo $s['id']; ?>)"
                        class="w-10 h-10 bg-red-500/10 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Add Modal -->
    <div id="slider-modal"
        class="fixed inset-0 bg-black/80 z-[100] hidden flex items-center justify-center p-6 backdrop-blur-md">
        <div class="bg-[#1E293B] w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/10">
            <div class="p-8 border-b border-white/5 flex items-center justify-between">
                <h3 class="text-xl font-black text-white uppercase">Add Admin Slider</h3>
                <button onclick="document.getElementById('slider-modal').classList.add('hidden')"
                    class="text-gray-500 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="slider-form" class="p-8 space-y-6">
                <input type="hidden" name="action" value="add">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Slider Image</label>
                    <input type="file" name="image" required
                        class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Click Link
                        (Optional)</label>
                    <input type="text" name="link" placeholder="https://"
                        class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-5 rounded-2xl font-black text-lg shadow-2xl shadow-blue-500/20 active:scale-95 transition-all">UPLOAD
                    BANNER</button>
            </form>
        </div>
    </div>
</main>

<script>
    document.getElementById('slider-form').onsubmit = function (e) {
        e.preventDefault();
        showAdminLoading();
        const formData = new FormData(this);
        fetch('sliders.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(res => {
                hideAdminLoading();
                if (res.status === 'success') {
                    showAdminToast('Slider added!');
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
    };

    function deleteSlider(id) {
        if (confirm('Delete this slider?')) {
            showAdminLoading();
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('id', id);
            fetch('sliders.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(res => {
                    hideAdminLoading();
                    if (res.status === 'success') {
                        showAdminToast('Slider removed.');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showAdminToast(res.message || 'Failed to remove slider.', 'error');
                    }
                });
        }
    }
</script>

<?php include 'common/bottom.php'; ?>