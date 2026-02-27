<?php
$host = '127.0.0.1';
$user = 'root';
$pass = ''; // User specified password

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS prime_top_up");
    $pdo->exec("USE prime_top_up");

    // Create Tables
    $queries = [
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            phone VARCHAR(20),
            email VARCHAR(100) UNIQUE,
            password VARCHAR(255),
            balance DECIMAL(10, 2) DEFAULT 0.00,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS admin (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE,
            password VARCHAR(255),
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            type ENUM('uid', 'voucher') DEFAULT 'uid',
            description TEXT,
            image VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cat_id INT,
            name VARCHAR(100),
            description TEXT,
            price DECIMAL(10, 2),
            stock INT DEFAULT 0,
            image VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (cat_id) REFERENCES categories(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS sliders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            image VARCHAR(255),
            link VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            site_name VARCHAR(100),
            site_title VARCHAR(255),
            site_description TEXT,
            currency_symbol VARCHAR(10) DEFAULT '৳',
            fab_link VARCHAR(255),
            youtube_link VARCHAR(255),
            marquee_status ENUM('on', 'off') DEFAULT 'on',
            marquee_text TEXT,
            primary_color VARCHAR(20) DEFAULT '#2563eb',
            secondary_color VARCHAR(20) DEFAULT '#4f46e5',
            admin_primary_color VARCHAR(20) DEFAULT '#2563eb',
            support_popup_status ENUM('on', 'off') DEFAULT 'on',
            support_popup_text TEXT,
            support_popup_link VARCHAR(255) DEFAULT 'https://t.me/yourusername',
            support_popup_interval INT DEFAULT 24
        )",
        "CREATE TABLE IF NOT EXISTS payment_methods (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            logo VARCHAR(255),
            qr_image VARCHAR(255),
            number VARCHAR(20),
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS wallet_topup (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            amount DECIMAL(10, 2),
            method_id INT,
            sender_number VARCHAR(20),
            trx_id VARCHAR(100),
            status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS transactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            type ENUM('credit', 'debit'),
            amount DECIMAL(10, 2),
            note TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            total_amount DECIMAL(10, 2),
            payment_method VARCHAR(50),
            status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT,
            product_id INT,
            quantity INT,
            price DECIMAL(10, 2),
            player_id VARCHAR(100),
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS redeem_codes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cat_id INT,
            product_id INT,
            order_id INT DEFAULT NULL,
            code VARCHAR(100),
            status ENUM('active', 'expired') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            used_at TIMESTAMP NULL
        )",
        "CREATE TABLE IF NOT EXISTS expired_codes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(100),
            expired_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS user_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            action VARCHAR(255),
            ip_address VARCHAR(45),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    ];

    foreach ($queries as $sql) {
        $pdo->exec($sql);
    }

    // Insert Default Admin
    $admin_user = 'admin';
    $admin_pass = password_hash('sujoy', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO admin (username, password) VALUES (?, ?)");
    $stmt->execute([$admin_user, $admin_pass]);

    // Insert Default Settings
    $pdo->exec("INSERT IGNORE INTO settings (id, site_name, site_title, site_description, currency_symbol, marquee_status, marquee_text, primary_color, secondary_color, admin_primary_color) 
                VALUES (1, 'Prime Top Up', 'Best Gaming Top Up Site', 'Get your gaming currency instantly', '৳', 'on', 'Welcome to Prime Top Up! Best prices for Free Fire, PUBG and more.', '#2563eb', '#4f46e5', '#3b82f6')");

    // Create Folders
    $folders = ['uploads', 'uploads/games', 'uploads/sliders', 'uploads/payments'];
    foreach ($folders as $folder) {
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
    }

    echo "Installation Successful! <a href='login.php'>Go to Login</a>";

} catch (PDOException $e) {
    die("Installation Failed: " . $e->getMessage());
}
?>