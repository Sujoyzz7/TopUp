<?php
include '../common/config.php';
if (!isAdminLoggedIn())
    redirect('login.php');

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT o.*, u.name as user_name, u.phone as user_phone, u.email as user_email, oi.quantity, oi.price as unit_price, oi.player_id, p.name as product_name
                      FROM orders o 
                      JOIN users u ON o.user_id = u.id 
                      JOIN order_items oi ON o.id = oi.order_id 
                      JOIN products p ON oi.product_id = p.id
                      WHERE o.id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order)
    die("Order not found.");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #
        <?php echo $order['id']; ?>
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Outfit', sans-serif;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }

            .print-shadow-none {
                shadow: none;
                border: 1px solid #eee;
            }
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen py-10 px-4">

    <div
        class="max-w-3xl mx-auto bg-white rounded-3xl shadow-2xl p-8 sm:p-12 print-shadow-none relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute top-0 right-0 w-40 h-40 bg-blue-600/5 rounded-full -mr-20 -mt-20"></div>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start mb-12 gap-6 relative">
            <div>
                <h1 class="text-4xl font-black text-gray-900 uppercase tracking-tighter mb-2">
                    <?php echo $settings['site_name']; ?>
                </h1>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                    <?php echo $settings['site_title']; ?>
                </p>
            </div>
            <div class="text-left sm:text-right space-y-1">
                <p class="text-3xl font-black text-blue-600 uppercase">INVOICE</p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Order ID: #
                    <?php echo $order['id']; ?>
                </p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Date:
                    <?php echo date('d M, Y', strtotime($order['created_at'])); ?>
                </p>
            </div>
        </div>

        <hr class="border-gray-100 mb-12">

        <!-- Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-12 mb-12">
            <div>
                <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-4">Customer Details</p>
                <div class="space-y-1">
                    <p class="font-black text-gray-800 uppercase">
                        <?php echo $order['user_name']; ?>
                    </p>
                    <p class="text-sm font-bold text-gray-500">
                        <?php echo $order['user_phone']; ?>
                    </p>
                    <p class="text-sm font-bold text-gray-500">
                        <?php echo $order['user_email']; ?>
                    </p>
                </div>
            </div>
            <div class="text-left sm:text-right">
                <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-4">Payment Info</p>
                <div class="space-y-1">
                    <p class="text-sm font-bold text-gray-500 uppercase">Method: <span class="text-gray-800">
                            <?php echo $order['payment_method']; ?>
                        </span></p>
                    <p class="text-sm font-bold text-gray-500 uppercase">Status:
                        <span
                            class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest 
                        <?php echo $order['status'] == 'completed' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600'; ?>">
                            <?php echo $order['status']; ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto mb-12">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 rounded-2xl overflow-hidden">
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            Product Description</th>
                        <th
                            class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            Qty</th>
                        <th
                            class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            Price</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr>
                        <td class="px-6 py-6">
                            <p class="font-black text-gray-800 uppercase text-xs">
                                <?php echo $order['product_name']; ?>
                            </p>
                            <p class="text-[10px] text-gray-400 font-bold mt-1">Player ID:
                                <?php echo $order['player_id'] ?: 'N/A'; ?>
                            </p>
                        </td>
                        <td class="px-6 py-6 text-center text-xs font-black text-gray-800">
                            <?php echo $order['quantity']; ?>
                        </td>
                        <td class="px-6 py-6 text-center text-xs font-black text-gray-800">
                            <?php echo $settings['currency_symbol'] . number_format($order['unit_price'], 2); ?>
                        </td>
                        <td class="px-6 py-6 text-right text-xs font-black text-blue-600">
                            <?php echo $settings['currency_symbol'] . number_format($order['total_amount'], 2); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="flex flex-col items-end space-y-4">
            <div class="w-full sm:w-64 space-y-4 p-6 bg-gray-50 rounded-2xl">
                <div class="flex justify-between items-center">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Subtotal</p>
                    <p class="text-xs font-black text-gray-800">
                        <?php echo $settings['currency_symbol'] . number_format($order['total_amount'], 2); ?>
                    </p>
                </div>
                <div class="flex justify-between items-center">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tax (0%)</p>
                    <p class="text-xs font-black text-gray-800">0.00</p>
                </div>
                <hr class="border-gray-200">
                <div class="flex justify-between items-center">
                    <p class="text-xs font-black text-blue-600 uppercase tracking-widest">Grand Total</p>
                    <p class="text-lg font-black text-blue-600">
                        <?php echo $settings['currency_symbol'] . number_format($order['total_amount'], 2); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="mt-12 text-center">
            <p
                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6 border-b border-gray-50 pb-6 italic">
                Thank you for your business!</p>
            <div class="flex justify-center gap-8 no-print">
                <button onclick="window.print()"
                    class="bg-blue-600 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-blue-500/20 active:scale-95 transition-all">
                    <i class="fa-solid fa-print mr-2"></i> Print Invoice
                </button>
                <a href="order_detail.php?id=<?php echo $id; ?>"
                    class="bg-gray-100 text-gray-500 px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all">
                    Back to Details
                </a>
            </div>
        </div>
    </div>

</body>

</html>