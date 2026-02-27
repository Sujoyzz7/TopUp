<?php
include '../common/config.php';
if (!isAdminLoggedIn())
    redirect('login.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $site_name = $_POST['site_name'];
    $site_title = $_POST['site_title'];
    $site_desc = $_POST['site_description'];
    $currency = $_POST['currency_symbol'];
    $fab = $_POST['fab_link'];
    $youtube = $_POST['youtube_link'];
    $m_status = $_POST['marquee_status'] ?? 'off';
    $m_text = $_POST['marquee_text'];
    $p_color = $_POST['primary_color'];
    $s_color = $_POST['secondary_color'];
    $a_p_color = $_POST['admin_primary_color'];
    $s_p_status = $_POST['support_popup_status'] ?? 'off';
    $s_p_text = $_POST['support_popup_text'];
    $s_p_link = $_POST['support_popup_link'];
    $s_p_interval = $_POST['support_popup_interval'];

    $stmt = $pdo->prepare("UPDATE settings SET site_name=?, site_title=?, site_description=?, currency_symbol=?, fab_link=?, youtube_link=?, marquee_status=?, marquee_text=?, primary_color=?, secondary_color=?, admin_primary_color=?, support_popup_status=?, support_popup_text=?, support_popup_link=?, support_popup_interval=? WHERE id=1");
    $stmt->execute([$site_name, $site_title, $site_desc, $currency, $fab, $youtube, $m_status, $m_text, $p_color, $s_color, $a_p_color, $s_p_status, $s_p_text, $s_p_link, $s_p_interval]);

    // Check if password change requested
    if (!empty($_POST['new_password'])) {
        $adm_user = $_POST['admin_username'];
        $pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE admin SET username=?, password=? WHERE id=1")->execute([$adm_user, $pass]);
    }

    $success = "Settings updated successfully!";
}

$settings = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch();
$admin = $pdo->query("SELECT username FROM admin WHERE id = 1")->fetch();

include 'common/header.php';
include 'common/sidebar.php';
?>

<main class="flex-1 lg:ml-64 p-6 sm:p-10 space-y-10 min-h-screen">
    <?php
    $title = "Global Settings";
    $subtitle = "Configure system variables and appearance";
    include 'common/topbar.php';
    ?>

    <?php if (isset($success)): ?>
        <div
            class="bg-green-500/10 border border-green-500/20 text-green-500 px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest">
            <i class="fa-solid fa-circle-check mr-2"></i>
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-10">

        <!-- Site Settings -->
        <div class="bg-[#1E293B] rounded-[2.5rem] p-8 border border-white/5 space-y-8">
            <h3 class="text-lg font-black text-white uppercase flex items-center gap-4">
                <span class="w-2 h-6 bg-blue-500 rounded-full"></span>
                General Settings
            </h3>

            <div class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Site Name</label>
                        <input type="text" name="site_name" value="<?php echo $settings['site_name']; ?>"
                            class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Currency
                            Code</label>
                        <input type="text" name="currency_symbol" value="<?php echo $settings['currency_symbol']; ?>"
                            class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Meta / Search
                        Title</label>
                    <input type="text" name="site_title" value="<?php echo $settings['site_title']; ?>"
                        class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Meta
                        Description</label>
                    <textarea name="site_description" rows="3"
                        class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600"><?php echo $settings['site_description']; ?></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">FAB Support
                            Link</label>
                        <input type="text" name="fab_link" value="<?php echo $settings['fab_link']; ?>"
                            class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">YT Guide
                            Link</label>
                        <input type="text" name="youtube_link" value="<?php echo $settings['youtube_link']; ?>"
                            class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                    </div>
                </div>
            </div>

            <!-- Appearance Section -->
            <div class="bg-[#1E293B] rounded-[2.5rem] p-8 border border-white/5 space-y-8 mt-10">
                <h3 class="text-lg font-black text-white uppercase flex items-center gap-4">
                    <span class="w-2 h-6 bg-purple-500 rounded-full"></span>
                    Appearance Customization
                </h3>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">User Primary
                                Color</label>
                            <input type="color" name="primary_color" value="<?php echo $settings['primary_color']; ?>"
                                class="w-full h-14 bg-white/5 border-2 border-white/5 rounded-2xl p-1 cursor-pointer">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">User Secondary
                                Color</label>
                            <input type="color" name="secondary_color"
                                value="<?php echo $settings['secondary_color']; ?>"
                                class="w-full h-14 bg-white/5 border-2 border-white/5 rounded-2xl p-1 cursor-pointer">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Admin Theme
                                Color</label>
                            <input type="color" name="admin_primary_color"
                                value="<?php echo $settings['admin_primary_color']; ?>"
                                class="w-full h-14 bg-white/5 border-2 border-white/5 rounded-2xl p-1 cursor-pointer">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Popup Settings -->
        <div class="bg-[#1E293B] rounded-[2.5rem] p-8 border border-white/5 space-y-8 mt-10">
            <h3 class="text-lg font-black text-white uppercase flex items-center gap-4">
                <span class="w-2 h-6 bg-green-500 rounded-full"></span>
                Support Alert Popup
            </h3>
            <div class="space-y-6">
                <div class="flex items-center justify-between p-6 bg-white/5 rounded-2xl">
                    <span class="text-xs font-black text-gray-300 uppercase tracking-widest">Popup Status</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="support_popup_status" value="on" class="sr-only peer" <?php echo $settings['support_popup_status'] == 'on' ? 'checked' : ''; ?>>
                        <div
                            class="w-14 h-7 bg-white/5 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-gray-500 after:border-gray-300 after:border after:rounded-full after:h-5 after:w-6 after:transition-all peer-checked:bg-green-600 peer-checked:after:bg-white">
                        </div>
                    </label>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Popup Message
                        (Bengali)</label>
                    <textarea name="support_popup_text" rows="2"
                        class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600"><?php echo $settings['support_popup_text']; ?></textarea>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Support Link
                        (Telegram)</label>
                    <input type="text" name="support_popup_link" value="<?php echo $settings['support_popup_link']; ?>"
                        class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Show Frequency (Repeat
                        After)</label>
                    <select name="support_popup_interval"
                        class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                        <option value="0" <?php echo $settings['support_popup_interval'] == 0 ? 'selected' : ''; ?>>Always
                            Show (Every Refresh)</option>
                        <option value="1" <?php echo $settings['support_popup_interval'] == 1 ? 'selected' : ''; ?>>Every
                            1 Hour</option>
                        <option value="6" <?php echo $settings['support_popup_interval'] == 6 ? 'selected' : ''; ?>>Every
                            6 Hours</option>
                        <option value="12" <?php echo $settings['support_popup_interval'] == 12 ? 'selected' : ''; ?>>
                            Every 12 Hours</option>
                        <option value="24" <?php echo $settings['support_popup_interval'] == 24 ? 'selected' : ''; ?>>
                            Every 1 Day (24 Hours)</option>
                        <option value="168" <?php echo $settings['support_popup_interval'] == 168 ? 'selected' : ''; ?>>
                            Every 1 Week</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- System & Marquee -->
        <div class="space-y-10">
            <div class="bg-[#1E293B] rounded-[2.5rem] p-8 border border-white/5 space-y-8">
                <h3 class="text-lg font-black text-white uppercase flex items-center gap-4">
                    <span class="w-2 h-6 bg-yellow-500 rounded-full"></span>
                    Announcement Marquee
                </h3>
                <div class="space-y-6">
                    <div class="flex items-center justify-between p-6 bg-white/5 rounded-2xl">
                        <span class="text-xs font-black text-gray-300 uppercase tracking-widest">Marquee Status</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="marquee_status" value="on" class="sr-only peer" <?php echo $settings['marquee_status'] == 'on' ? 'checked' : ''; ?>>
                            <div
                                class="w-14 h-7 bg-white/5 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-gray-500 after:border-gray-300 after:border after:rounded-full after:h-5 after:w-6 after:transition-all peer-checked:bg-blue-600 peer-checked:after:bg-white">
                            </div>
                        </label>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Running Text
                            Message</label>
                        <textarea name="marquee_text" rows="2"
                            class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600"><?php echo $settings['marquee_text']; ?></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-[#1E293B] rounded-[2.5rem] p-8 border border-white/5 space-y-8">
                <h3 class="text-lg font-black text-white uppercase flex items-center gap-4">
                    <span class="w-2 h-6 bg-red-500 rounded-full"></span>
                    Admin Credentials
                </h3>
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Admin
                            Username</label>
                        <input type="text" name="admin_username" value="<?php echo $admin['username']; ?>"
                            class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Update Password
                            (Leave blank to keep same)</label>
                        <input type="password" name="new_password" placeholder="••••••••"
                            class="w-full bg-white/5 border-2 border-white/5 rounded-2xl py-4 px-6 text-white outline-none focus:border-blue-600">
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-6 rounded-[2rem] font-black text-xl shadow-2xl shadow-blue-500/20 active:scale-95 transition-all">
                SAVE ALL SETTINGS
            </button>
        </div>
    </form>
</main>

<?php include 'common/bottom.php'; ?>