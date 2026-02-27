<?php
include '../common/config.php';

if (!isLoggedIn())
    exit;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $methodId = $_POST['method_id'];
    $senderPhone = $_POST['sender_number'];
    $trxId = $_POST['trx_id'];

    $stmt = $pdo->prepare("INSERT INTO wallet_topup (user_id, amount, method_id, sender_number, trx_id, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    if ($stmt->execute([$userId, $amount, $methodId, $senderPhone, $trxId])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to log request.']);
    }
}
?>