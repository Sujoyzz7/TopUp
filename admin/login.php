<?php
include '../common/config.php';

if (isAdminLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        redirect('index.php');
    } else {
        $error = "Invalid admin credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Prime TopUp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-[#0F172A] min-h-screen flex items-center justify-center p-6">
    <div
        class="w-full max-w-md bg-[#1E293B] rounded-[2.5rem] shadow-2xl p-10 border border-white/5 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>

        <div class="text-center mb-10 space-y-2">
            <div
                class="w-20 h-20 bg-blue-600 text-white rounded-[2rem] flex items-center justify-center text-3xl mx-auto shadow-2xl shadow-blue-500/20 mb-6 border-4 border-white/10">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <h1 class="text-2xl font-black text-white uppercase tracking-tight">Admin Central</h1>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Authorized Access Only</p>
        </div>

        <?php if (isset($error)): ?>
            <div
                class="mb-6 bg-red-500/10 border border-red-500/20 text-red-400 px-6 py-4 rounded-2xl text-xs font-bold flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest px-1">Username</label>
                <div class="relative group">
                    <i
                        class="fa-solid fa-user absolute left-6 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-blue-500 transition-colors"></i>
                    <input type="text" name="username" placeholder="Username" required
                        class="w-full bg-white/5 border-2 border-transparent rounded-2xl py-4 pl-14 pr-6 text-white focus:bg-white/10 focus:border-blue-500 transition-all outline-none font-bold">
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest px-1">Password</label>
                <div class="relative group">
                    <i
                        class="fa-solid fa-lock absolute left-6 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-blue-500 transition-colors"></i>
                    <input type="password" name="password" placeholder="Password" required
                        class="w-full bg-white/5 border-2 border-transparent rounded-2xl py-4 pl-14 pr-6 text-white focus:bg-white/10 focus:border-blue-500 transition-all outline-none font-bold">
                </div>
            </div>
            <button type="submit"
                class="w-full bg-blue-600 text-white py-5 rounded-2xl font-black text-lg shadow-2xl shadow-blue-500/20 active:scale-95 transition-all mt-6">
                SECURE LOGIN
            </button>
        </form>
    </div>
</body>

</html>