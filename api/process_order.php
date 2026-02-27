<?php
include '../common/config.php';

if (!isLoggedIn()) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    // If not JSON, it might be FormData (for instantpay.php usually handles via fetch but let's be safe)
    $input = $_POST;
}

$userId = $_SESSION['user_id'];
$productId = $input['productId'] ?? $input['product_id'] ?? 0;
$qty = $input['qty'] ?? $input['quantity'] ?? 1;
$playerId = $input['playerId'] ?? $input['player_id'] ?? '';
$paymentType = $input['paymentType'] ?? '';

// Fetch Product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
    exit;
}

$totalPrice = $product['price'] * $qty;

if ($paymentType == 'wallet') {
    // Check balance
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $userBalance = $stmt->fetchColumn();

    if ($userBalance < $totalPrice) {
        echo json_encode(['status' => 'error', 'message' => 'Insufficient balance.']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Deduct balance
        $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$totalPrice, $userId]);
        $_SESSION['balance'] -= $totalPrice;

        // Create Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, payment_method, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $totalPrice, 'Wallet', 'pending']);
        $orderId = $pdo->lastInsertId();

        // Create Order Item
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, player_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$orderId, $productId, $qty, $product['price'], $playerId]);

        // Log Transaction
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, note) VALUES (?, 'debit', ?, ?)");
        $stmt->execute([$userId, $totalPrice, "Order #$orderId - " . $product['name']]);

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Order placed successfully.']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Order failed: ' . $e->getMessage()]);
    }

} else if ($paymentType == 'instant') {
    $methodId = $input['method_id'] ?? 0;
    $senderNumber = $input['sender_number'] ?? '';
    $trxId = $input['trx_id'] ?? '';

    if (!$senderNumber || !$trxId) {
        echo json_encode(['status' => 'error', 'message' => 'Please provide sender number and TRX ID.']);
        exit;
    }

    // Fetch Method Name
    $stmt = $pdo->prepare("SELECT name FROM payment_methods WHERE id = ?");
    $stmt->execute([$methodId]);
    $methodName = $stmt->fetchColumn() ?: 'Instant Pay';

    try {
        $pdo->beginTransaction();

        // Create Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, payment_method, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $totalPrice, "$methodName ($trxId)", 'pending']);
        $orderId = $pdo->lastInsertId();

        // Create Order Item
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, player_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$orderId, $productId, $qty, $product['price'], $playerId]);

        // Actually the prompt says "Instant payment method (like: BKash, Nagad) ... create order"
        // But some systems use wallet topup first. Here the request says "On submit: Create order" in instantpay.php

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Order placed. Pending verification.']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Order failed: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid payment type.']);
}
?>