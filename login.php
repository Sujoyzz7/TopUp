<?php
include 'common/config.php';

if (isLoggedIn()) {
    redirect('index.php');
}

// AJAX Request Handling
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    if ($action == 'login') {
        $email = $_POST['email_phone'];
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
        $stmt->execute([$email, $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['balance'] = $user['balance'];
            echo json_encode(['status' => 'success', 'message' => 'Login Successful!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid credentials.']);
        }
        exit;
    }

    if ($action == 'signup') {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Check if exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
        $stmt->execute([$email, $phone]);
        if ($stmt->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'User already exists.']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO users (name, phone, email, password) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $phone, $email, $password])) {
            echo json_encode(['status' => 'success', 'message' => 'Registration Successful! Please login.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Registration failed.']);
        }
        exit;
    }
}

include 'common/header.php';
?>

<main class="min-h-screen flex items-center justify-center p-6 sm:py-12">
    <div
        class="w-full max-w-md bg-white rounded-[2.5rem] shadow-2xl p-8 sm:p-10 border border-gray-50 overflow-hidden relative">
        <div class="absolute top-0 right-0 p-4 opacity-10 text-9xl -rotate-12 translate-x-12 -translate-y-12">
            <i class="fa-solid fa-bolt text-blue-600"></i>
        </div>

        <div class="flex gap-4 p-1.5 bg-gray-50 rounded-2xl mb-10 relative z-10">
            <button onclick="switchTab('login')" id="login-tab-btn"
                class="flex-1 py-3.5 rounded-xl font-bold transition-all text-sm bg-blue-600 text-white shadow-xl shadow-blue-100">Login</button>
            <button onclick="switchTab('signup')" id="signup-tab-btn"
                class="flex-1 py-3.5 rounded-xl font-bold transition-all text-sm text-gray-500 hover:bg-white">Sign
                Up</button>
        </div>

        <!-- Login Form -->
        <form id="login-form" class="space-y-6 relative z-10 transition-all duration-300">
            <input type="hidden" name="action" value="login">
            <div class="space-y-2">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1">Email / Number</label>
                <div class="relative group">
                    <i
                        class="fa-solid fa-user absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                    <input type="text" name="email_phone" placeholder="Enter your email or phone" required
                        class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl py-4 pl-14 pr-4 focus:bg-white focus:border-blue-600 transition-all outline-none font-medium">
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1">Password</label>
                <div class="relative group">
                    <i
                        class="fa-solid fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                    <input type="password" name="password" placeholder="Enter password" required
                        class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl py-4 pl-14 pr-4 focus:bg-white focus:border-blue-600 transition-all outline-none font-medium">
                </div>
            </div>
            <button type="submit"
                class="w-full bg-blue-600 text-white py-5 rounded-2xl font-bold text-lg shadow-2xl shadow-blue-200 active:scale-95 transition-all flex items-center justify-center gap-3">
                Log In <i class="fa-solid fa-arrow-right-long text-sm"></i>
            </button>
        </form>

        <!-- Signup Form -->
        <form id="signup-form" class="space-y-5 hidden relative z-10 transition-all duration-300">
            <input type="hidden" name="action" value="signup">
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Full Name</label>
                <div class="relative group">
                    <i class="fa-solid fa-signature absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="name" placeholder="Name" required
                        class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl py-3.5 pl-14 pr-4 focus:bg-white focus:border-blue-600 transition-all outline-none font-medium text-sm">
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Phone Number</label>
                <div class="relative group">
                    <i class="fa-solid fa-phone absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="phone" placeholder="Phone" required
                        class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl py-3.5 pl-14 pr-4 focus:bg-white focus:border-blue-600 transition-all outline-none font-medium text-sm">
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Email Address</label>
                <div class="relative group">
                    <i class="fa-solid fa-envelope absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="email" name="email" placeholder="Email" required
                        class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl py-3.5 pl-14 pr-4 focus:bg-white focus:border-blue-600 transition-all outline-none font-medium text-sm">
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Password</label>
                <div class="relative group">
                    <i class="fa-solid fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="password" name="password" placeholder="Password" required
                        class="w-full bg-gray-50 border-2 border-gray-50 rounded-2xl py-3.5 pl-14 pr-4 focus:bg-white focus:border-blue-600 transition-all outline-none font-medium text-sm">
                </div>
            </div>
            <button type="submit"
                class="w-full bg-blue-600 text-white py-4 mt-4 rounded-2xl font-bold shadow-2xl shadow-blue-200 active:scale-95 transition-all">Create
                Account</button>
        </form>
    </div>
</main>

<script>
    function switchTab(tab) {
        const loginForm = document.getElementById('login-form');
        const signupForm = document.getElementById('signup-form');
        const loginBtn = document.getElementById('login-tab-btn');
        const signupBtn = document.getElementById('signup-tab-btn');

        if (tab === 'login') {
            loginForm.classList.remove('hidden');
            signupForm.classList.add('hidden');
            loginBtn.classList.add('bg-blue-600', 'text-white', 'shadow-xl', 'shadow-blue-100');
            loginBtn.classList.remove('text-gray-500', 'hover:bg-white');
            signupBtn.classList.add('text-gray-500', 'hover:bg-white');
            signupBtn.classList.remove('bg-blue-600', 'text-white', 'shadow-xl', 'shadow-blue-100');
        } else {
            loginForm.classList.add('hidden');
            signupForm.classList.remove('hidden');
            signupBtn.classList.add('bg-blue-600', 'text-white', 'shadow-xl', 'shadow-blue-100');
            signupBtn.classList.remove('text-gray-500', 'hover:bg-white');
            loginBtn.classList.add('text-gray-500', 'hover:bg-white');
            loginBtn.classList.remove('bg-blue-600', 'text-white', 'shadow-xl', 'shadow-blue-100');
        }
    }

    document.getElementById('login-form').onsubmit = function (e) {
        e.preventDefault();
        showLoading();
        const formData = new FormData(this);
        fetch('login.php', {
            method: 'POST',
            body: formData
        })
            .then(r => r.json())
            .then(data => {
                hideLoading();
                if (data.status === 'success') {
                    showPopup('Success', data.message, 'success');
                    setTimeout(() => window.location.href = 'index.php', 1500);
                } else {
                    showPopup('Error', data.message, 'error');
                }
            });
    };

    document.getElementById('signup-form').onsubmit = function (e) {
        e.preventDefault();
        showLoading();
        const formData = new FormData(this);
        fetch('login.php', {
            method: 'POST',
            body: formData
        })
            .then(r => r.json())
            .then(data => {
                hideLoading();
                if (data.status === 'success') {
                    showPopup('Success', data.message, 'success');
                    switchTab('login');
                } else {
                    showPopup('Error', data.message, 'error');
                }
            });
    };
</script>

<?php include 'common/sidebar.php';
include 'common/bottom.php';
include 'common/footer.php'; ?>