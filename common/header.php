<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>
        <?php echo $settings['site_title'] ?? 'Prime Top Up'; ?>
    </title>
    <meta name="description" content="<?php echo $settings['site_description'] ?? ''; ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

        :root {
            --primary:
                <?php echo $settings['primary_color'] ?? '#2563eb'; ?>
            ;
            --primary-rgb:
                <?php echo hexToRgb($settings['primary_color'] ?? '#2563eb'); ?>
            ;
            --secondary:
                <?php echo $settings['secondary_color'] ?? '#4f46e5'; ?>
            ;
            --bg: #F8FAFC;
            --text: #1E293B;
            --card: #FFFFFF;
            --border: #F1F5F9;
        }

        [data-theme="dark"] {
            --bg: #0F172A;
            --text: #F8FAFC;
            --card: #1E293B;
            --border: #334155;
        }

        body {
            font-family: 'Outfit', sans-serif;
            -webkit-tap-highlight-color: transparent;
            background-color: var(--bg);
            color: var(--text);
            transition: background-color 0.3s, color 0.3s;
        }

        /* Override Tailwind with CSS Variables */
        .bg-white {
            background-color: var(--card) !important;
        }

        .text-gray-800,
        .text-[#1E293B] {
            color: var(--text) !important;
        }

        .border-gray-50,
        .border-gray-100 {
            border-color: var(--border) !important;
        }

        .bg-blue-600 {
            background-color: var(--primary) !important;
        }

        .text-blue-600 {
            color: var(--primary) !important;
        }

        .shadow-blue-200 {
            --tw-shadow-color: var(--primary);
        }

        .no-select {
            user-select: none;
            -webkit-user-select: none;
        }

        .glass {
            background: rgba(var(--card-rgb), 0.7);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
        }

        header.glass {
            background-color: var(--card);
            opacity: 0.95;
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .marquee-container {
            overflow: hidden;
            white-space: nowrap;
        }

        .marquee-text {
            display: inline-block;
            animation: marquee 15s linear infinite;
        }

        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }
    </style>
    <script>
        // Theme logic
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);

        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            updateThemeIcons();
        }

        function updateThemeIcons() {
            const current = document.documentElement.getAttribute('data-theme');
            const icon = document.getElementById('theme-icon');
            if (icon) {
                icon.className = current === 'light' ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
            }
        }

        document.addEventListener('DOMContentLoaded', updateThemeIcons);
    </script>
</head>

<body class="bg-[#F8FAFC] text-[#1E293B] no-select overflow-x-hidden">

    <?php if ($settings['marquee_status'] == 'on'): ?>
        <div class="bg-[#1E293B] text-white py-1 marquee-container">
            <div class="marquee-text text-sm font-medium">
                <?php echo $settings['marquee_text']; ?>
            </div>
        </div>
    <?php endif; ?>

    <header class="glass sticky top-0 z-50 border-b border-gray-100 px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-xl p-1"><i class="fa-solid fa-bars-staggered"></i></button>
            <a href="index.php" class="flex items-center gap-2">
                <div class="bg-blue-600 text-white p-2 rounded-lg"><i class="fa-solid fa-bolt"></i></div>
                <span class="font-bold text-xl tracking-tight">
                    <?php echo $settings['site_name'] ?? 'Prime'; ?> <span class="text-blue-600">TopUp</span>
                </span>
            </a>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="toggleTheme()"
                class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center transition-all hover:scale-110 border border-gray-200"
                style="background-color: var(--card); border-color: var(--border);">
                <i id="theme-icon" class="fa-solid fa-moon"></i>
            </button>

            <?php if (isLoggedIn()):
                // Refresh balance from DB
                $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $current_balance = $stmt->fetchColumn();
                $_SESSION['balance'] = $current_balance;
                ?>
                <div
                    class="bg-blue-50 text-blue-700 px-3 py-1.5 rounded-full flex items-center gap-2 border border-blue-100">
                    <i class="fa-solid fa-wallet text-sm"></i>
                    <span class="font-bold text-sm">
                        <?php echo ($settings['currency_symbol'] ?? '৳') . number_format($current_balance, 2); ?>
                    </span>
                </div>
                <a href="profile.php"
                    class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                    <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
                </a>
            <?php else: ?>
                <a href="login.php"
                    class="bg-blue-600 text-white px-5 py-2 rounded-full font-semibold text-sm hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                    Log In
                </a>
            <?php endif; ?>
        </div>
    </header>

    <?php if ($settings['support_popup_status'] == 'on'): ?>
        <!-- Support Alert Popup -->
        <div id="support-modal"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-all duration-500 opacity-0 pointer-events-none">
            <div
                class="bg-white dark:bg-[#1E293B] w-full max-w-sm rounded-[2.5rem] p-8 shadow-2xl scale-90 transition-all duration-500 border border-gray-100 dark:border-white/5 relative overflow-hidden">
                <!-- Decorative background -->
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-600/10 rounded-full blur-3xl"></div>

                <div class="relative space-y-6 text-center">
                    <div
                        class="w-16 h-16 bg-blue-600/10 text-blue-600 rounded-2xl flex items-center justify-center mx-auto text-2xl animate-bounce">
                        <i class="fa-brands fa-telegram"></i>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tight">Support Notice
                        </h3>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 leading-relaxed">
                            <?php echo $settings['support_popup_text']; ?>
                        </p>
                    </div>

                    <div class="flex flex-col gap-3">
                        <a href="<?php echo $settings['support_popup_link']; ?>" target="_blank"
                            class="bg-blue-600 text-white py-4 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-blue-600/20 active:scale-95 transition-all text-sm">
                            ক্লিক করুন
                        </a>
                        <button onclick="closeSupportModal()"
                            class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 dark:hover:text-white transition-all">
                            Close Message
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('support-modal');
                const inner = modal.querySelector('div');

                // Check if shown in dynamic interval
                const lastShown = localStorage.getItem('lastSupportShown');
                const now = new Date().getTime();
                const intervalHours = <?php echo (int) ($settings['support_popup_interval'] ?? 24); ?>;
                const intervalMs = intervalHours * 60 * 60 * 1000;

                if (intervalHours === 0 || !lastShown || (now - lastShown) > intervalMs) {
                    // Show modal after a small delay
                    setTimeout(() => {
                        modal.classList.remove('opacity-0', 'pointer-events-none');
                        inner.classList.remove('scale-90');
                        // Save timestamp
                        localStorage.setItem('lastSupportShown', now);
                    }, 2000);
                }
            });

            function closeSupportModal() {
                const modal = document.getElementById('support-modal');
                const inner = modal.querySelector('div');
                modal.classList.add('opacity-0', 'pointer-events-none');
                inner.classList.add('scale-90');
            }
        </script>
    <?php endif; ?>