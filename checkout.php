<?php
require_once __DIR__ . '/config/database.php';
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;
foreach ($cart as $item) {
    $total += (int) ($item['price'] ?? 0);
}

$message = "";
if (isset($_POST['bayar']) && $total > 0 && !empty($cart)) {
    $user_id = (int) $_SESSION['user']['id'];
    $status = "dibayar";

    // Metode & referensi pembayaran
    $allowedMethods = ['cash', 'ewallet', 'bank'];
    $payment_method = $_POST['payment_method'] ?? 'cash';
    if (!in_array($payment_method, $allowedMethods, true)) {
        $payment_method = 'cash';
    }
    $payment_reference = trim($_POST['payment_reference'] ?? "");

    $conn->begin_transaction();
    $ok = true;

    // Kurangi stok sesuai item di keranjang.
    foreach ($cart as $item) {
        $productId = (int) ($item['product_id'] ?? 0);
        if ($productId <= 0) {
            continue; // fallback untuk cart lama yang belum menyimpan product_id
        }

        $checkStmt = $conn->prepare("SELECT stock FROM products WHERE id = ? LIMIT 1");
        $checkStmt->bind_param("i", $productId);
        $checkStmt->execute();
        $product = $checkStmt->get_result()->fetch_assoc();
        $stock = (int) ($product['stock'] ?? 0);

        if ($stock <= 0) {
            $ok = false;
            break;
        }

        $decStmt = $conn->prepare("UPDATE products SET stock = stock - 1 WHERE id = ? AND stock > 0");
        $decStmt->bind_param("i", $productId);
        $decStmt->execute();

        if ($decStmt->affected_rows <= 0) {
            $ok = false;
            break;
        }
    }

    if ($ok) {
        // Simpan order dengan informasi pembayaran
        $stmt = $conn->prepare("INSERT INTO orders (user_id,total,status,payment_method,payment_reference) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $user_id, $total, $status, $payment_method, $payment_reference);
        $stmt->execute();

        $order_id = $conn->insert_id;

        // Simpan detail item ke tabel order_items
        foreach ($cart as $item) {
            $productId    = (int) ($item['product_id'] ?? 0);
            $name         = trim($item['name'] ?? '');
            $qty          = 1; // saat ini setiap entri cart mewakili 1 qty
            $price        = (int) ($item['base_price'] ?? $item['price'] ?? 0);
            $servicePrice = (int) ($item['service_price'] ?? 0);
            $subtotal     = $price + $servicePrice;

            if ($productId > 0 && $name !== '') {
                $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, name, qty, price, service_price, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $itemStmt->bind_param("iisiiii", $order_id, $productId, $name, $qty, $price, $servicePrice, $subtotal);
                $itemStmt->execute();
            }
        }

        // Simpan data untuk struk terakhir di sesi
        $_SESSION['last_receipt'] = [
            'order_id'          => $order_id,
            'user'              => $_SESSION['user'],
            'items'             => $cart,
            'total'             => $total,
            'payment_method'    => $payment_method,
            'payment_reference' => $payment_reference,
            'created_at'        => date('Y-m-d H:i:s'),
        ];

        unset($_SESSION['cart']);
        $cart = [];
        $total = 0;
        $message = "Pembayaran berhasil.";
        $conn->commit();

        // Arahkan ke halaman struk untuk cetak
        header("Location: struk.php?id=" . $order_id);
        exit;
    } else {
        $conn->rollback();
        $message = "Gagal bayar: stok salah satu produk tidak tersedia.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout - Bengkel Motor</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
<main class="max-w-3xl mx-auto p-6">
    <a href="user.php" class="text-blue-600 hover:underline">&larr; Kembali ke Produk</a>
    <section class="bg-white rounded shadow p-5 mt-4">
        <h2 class="text-xl font-bold mb-4">Checkout</h2>
        <?php if ($message): ?>
            <div class="mb-4 p-3 rounded bg-green-100 text-green-700"><?= e($message) ?></div>
        <?php endif; ?>

        <?php if (!empty($cart)): ?>
            <div class="space-y-2 mb-4">
                <?php foreach ($cart as $item): ?>
                    <div class="border rounded p-3 flex justify-between">
                        <div class="flex items-center gap-3">
                            <img src="<?= e($item['image_url'] ?: 'https://via.placeholder.com/48?text=No+Image') ?>" alt="<?= e($item['name']) ?>" class="w-12 h-12 rounded object-cover border">
                            <span><?= e($item['name']) ?></span>
                        </div>
                        <span>RP.<?= number_format((int) $item['price'], 0, ',', '.') ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="font-semibold mb-4">Total: RP.<?= number_format($total, 0, ',', '.') ?></p>
            <form method="POST">
                <div class="mb-4 space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Metode Pembayaran</label>
                    <select name="payment_method" class="border rounded px-3 py-2 w-full" required>
                        <option value="cash">Cash / Tunai</option>
                        <option value="ewallet">E-Wallet (Dana, OVO, Gopay, dll)</option>
                        <option value="bank">Transfer Bank</option>
                    </select>
                    <input
                        type="text"
                        name="payment_reference"
                        class="border rounded px-3 py-2 w-full"
                        placeholder="No. referensi / nama e-wallet / bank (opsional)"
                    >
                </div>
                <button name="bayar" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Bayar & Cetak Struk</button>
            </form>
        <?php else: ?>
            <p class="text-gray-500">Belum ada item di keranjang.</p>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
