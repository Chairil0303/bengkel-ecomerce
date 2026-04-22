<?php
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$receipt = $_SESSION['last_receipt'] ?? null;

// Jika struk diakses untuk order terakhir di sesi dan id cocok, gunakan data sesi (lengkap dengan item).
if ($receipt && (int) ($receipt['order_id'] ?? 0) === $orderId) {
    $orderData = $receipt;
} else {
    // Fallback: ambil data dari database (order + item) jika sesi tidak ada / beda.
    $orderData = null;
    if ($orderId > 0) {
        $stmt = $conn->prepare("SELECT id, user_id, total, status, payment_method, payment_reference, created_at FROM orders WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $dbOrder = $stmt->get_result()->fetch_assoc();
        if ($dbOrder) {
            // Ambil detail item dari order_items
            $items = [];
            $stmtItems = $conn->prepare("SELECT name, qty, price, service_price, subtotal FROM order_items WHERE order_id = ?");
            $stmtItems->bind_param("i", $dbOrder['id']);
            $stmtItems->execute();
            $resItems = $stmtItems->get_result();
            while ($row = $resItems->fetch_assoc()) {
                $items[] = [
                    'name'          => $row['name'],
                    'qty'           => (int) $row['qty'],
                    'price'         => (int) $row['subtotal'], // subtotal per baris (harga + jasa)
                    'base_price'    => (int) $row['price'],
                    'service_price' => (int) $row['service_price'],
                ];
            }

            $orderData = [
                'order_id'          => (int) $dbOrder['id'],
                'user'              => $_SESSION['user'],
                'items'             => $items,
                'total'             => (int) $dbOrder['total'],
                'payment_method'    => $dbOrder['payment_method'] ?? 'cash',
                'payment_reference' => $dbOrder['payment_reference'] ?? '',
                'created_at'        => $dbOrder['created_at'] ?? '',
            ];
        }
    }
}

if (!$orderData) {
    echo "Data struk tidak ditemukan.";
    exit;
}

$paymentLabelMap = [
    'cash'    => 'Cash / Tunai',
    'ewallet' => 'E-Wallet',
    'bank'    => 'Transfer Bank',
];
$paymentLabel = $paymentLabelMap[$orderData['payment_method']] ?? ucfirst((string) $orderData['payment_method']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Struk Pembayaran #<?= (int) $orderData['order_id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="bg-white w-full max-w-md shadow rounded p-6">
    <div class="text-center mb-4">
        <h1 class="text-lg font-bold">Bengkel Motor Pamulang</h1>
        <p class="text-xs text-gray-600">Struk Pembayaran</p>
    </div>
    <div class="text-sm text-gray-700 mb-4">
        <div class="flex justify-between">
            <span>No. Order</span>
            <span>#<?= (int) $orderData['order_id'] ?></span>
        </div>
        <div class="flex justify-between">
            <span>Tanggal</span>
            <span><?= e($orderData['created_at']) ?></span>
        </div>
        <div class="flex justify-between">
            <span>Pelanggan</span>
            <span><?= e($orderData['user']['name'] ?? $orderData['user']['email'] ?? 'User') ?></span>
        </div>
        <div class="flex justify-between">
            <span>Metode Pembayaran</span>
            <span><?= e($paymentLabel) ?></span>
        </div>
        <?php if (!empty($orderData['payment_reference'])): ?>
            <div class="flex justify-between">
                <span>Referensi</span>
                <span><?= e($orderData['payment_reference']) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($orderData['items'])): ?>
        <div class="border-t border-b py-3 mb-4 text-sm">
            <?php foreach ($orderData['items'] as $item): ?>
                <div class="flex justify-between gap-3 py-1">
                    <span class="truncate"><?= e($item['name']) ?></span>
                    <span class="shrink-0">RP.<?= number_format((int) $item['price'], 0, ',', '.') ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="border-t border-b py-3 mb-4 text-sm text-gray-500">
            Detail item tidak tersedia, hanya total yang tercatat.
        </div>
    <?php endif; ?>

    <div class="flex justify-between text-base font-semibold mb-6">
        <span>Total Bayar</span>
        <span>RP.<?= number_format((int) $orderData['total'], 0, ',', '.') ?></span>
    </div>

    <div class="text-center text-xs text-gray-500 mb-4">
        Terima kasih telah melakukan servis di Bengkel Motor Pamulang.
    </div>

    <div class="no-print flex justify-between gap-3">
        <button onclick="window.print()" class="flex-1 bg-green-600 text-white py-2 rounded hover:bg-green-700">
            Cetak Struk
        </button>
        <a href="user.php" class="flex-1 text-center bg-gray-600 text-white py-2 rounded hover:bg-gray-700">
            Kembali
        </a>
    </div>
</div>
</body>
</html>

