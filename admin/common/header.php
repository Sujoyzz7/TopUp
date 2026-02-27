<?php include_once '../common/config.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard -
        <?php echo $settings['site_name']; ?>
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

        :root {
            --admin-primary:
                <?php echo $settings['admin_primary_color'] ?? '#3b82f6'; ?>
            ;
            --admin-primary-rgb:
                <?php echo hexToRgb($settings['admin_primary_color'] ?? '#3b82f6'); ?>
            ;
            --bg: #0F172A;
            --card: #1E293B;
            --text: #D1D5DB;
            --border: #334155;
        }

        [data-theme="light"] {
            --bg: #F1F5F9;
            --card: #FFFFFF;
            --text: #1E293B;
            --border: #E2E8F0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            transition: all 0.3s ease;
        }

        .bg-[#0F172A] {
            background-color: var(--bg) !important;
        }

        .bg-[#1E293B] {
            background-color: var(--card) !important;
        }

        .border-white\/5 {
            border-color: var(--border) !important;
        }

        .text-white {
            color: var(--text) !important;
        }

        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
        }

        .sidebar-active {
            background: rgba(var(--admin-primary-rgb), 0.1);
            color: var(--admin-primary);
            border-right: 4px solid var(--admin-primary);
        }

        /* Override blue classes */
        .bg-blue-600 {
            background-color: var(--admin-primary) !important;
        }

        .text-blue-500 {
            color: var(--admin-primary) !important;
        }

        .border-blue-600 {
            border-color: var(--admin-primary) !important;
        }

        .shadow-blue-500\/20 {
            --tw-shadow-color: var(--admin-primary);
        }

        /* Mobile Table Fix */
        @media (max-width: 1024px) {
            .overflow-x-auto {
                -webkit-overflow-scrolling: touch;
            }

            th,
            td {
                white-space: nowrap;
            }
        }
    </style>
    <script>
        const savedTheme = localStorage.getItem('admin_theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);

        function toggleAdminTheme() {
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('admin_theme', next);
        }
    </script>
</head>

<body class="bg-[#0F172A] text-gray-300 overflow-x-hidden">
    <div class="flex">