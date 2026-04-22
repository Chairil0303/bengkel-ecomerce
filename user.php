<?php
require_once __DIR__ . '/config/database.php';
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$userId = (int) $_SESSION['user']['id'];

// Pesan saat tambah ke keranjang / aksi lain
$cartMessage = "";

// Tambah ke cart
if (isset($_POST['add'])) {
    $_SESSION['cart'] = $_SESSION['cart'] ?? [];

    $productId = (int) ($_POST['product_id'] ?? 0);
    $withService = isset($_POST['with_service']) && $_POST['with_service'] === '1';
    if ($productId > 0) {
        $stmt = $conn->prepare("SELECT id, name, image_url, price, service_price, stock FROM products WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        if ($product && (int) $product['stock'] > 0) {
            $basePrice = (int) $product['price'];
            $servicePrice = $withService ? (int) ($product['service_price'] ?? 0) : 0;
            $totalPrice = $basePrice + $servicePrice;
            $_SESSION['cart'][] = [
                'product_id'   => (int) $product['id'],
                'name'         => $product['name'],
                'image_url'    => $product['image_url'] ?? '',
                'price'        => $totalPrice,
                'base_price'   => $basePrice,
                'service_on'   => $withService,
                'service_price'=> $servicePrice,
                'stock'        => (int) $product['stock'],
            ];
        } else {
            $cartMessage = "Stok barang habis.";
        }
    }
}

// Hapus item cart berdasarkan index
if (isset($_POST['remove']) && isset($_POST['index'])) {
    $index = (int) $_POST['index'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

// Kosongkan cart
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
}

// Hapus riwayat pesanan milik user (opsional)
if (isset($_POST['delete_order'])) {
    $orderId = (int) ($_POST['order_id'] ?? 0);
    if ($orderId > 0) {
        $stmtDel = $conn->prepare("DELETE FROM orders WHERE id = ? AND user_id = ?");
        $stmtDel->bind_param("ii", $orderId, $userId);
        $stmtDel->execute();
    }
}

$result = $conn->query("SELECT * FROM products");

// Riwayat pesanan user (dengan opsi delete)
$ordersStmt = $conn->prepare("SELECT id, total, status, payment_method FROM orders WHERE user_id = ? ORDER BY id DESC");
$ordersStmt->bind_param("i", $userId);
$ordersStmt->execute();
$orders = $ordersStmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>User - Bengkel Motor</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
<header class="bg-white border-b sticky top-0 z-10">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 rounded bg-blue-600"></div>
            <span class="font-semibold text-gray-800">Bengkel Motor</span>
        </div>
        <a href="logout.php" class="text-sm text-white bg-blue-600 px-4 py-2 rounded hover:bg-blue-700">Logout</a>
    </div>
</header>

<main class="max-w-6xl mx-auto p-6">
    <div class="grid lg:grid-cols-3 gap-6">
    <section id="produk" class="bg-white rounded shadow p-5 lg:col-span-2">
        <h2 class="text-xl font-bold mb-4">Produk</h2>
        <?php if ($cartMessage): ?>
            <div class="mb-4 p-3 rounded bg-yellow-100 text-yellow-800 text-sm">
                <?= e($cartMessage) ?>
            </div>
        <?php endif; ?>
        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
            <?php while($row = $result->fetch_assoc()): ?>
                <?php $stockInt = (int) ($row['stock'] ?? 0); ?>
                <div class="bg-white border rounded-xl overflow-hidden hover:shadow-sm transition">
                    <img
                        src="<?= e($row['image_url'] ?: 'https://via.placeholder.com/256?text=No+Image') ?>"
                        alt="<?= e($row['name']) ?>"
                        class="w-full h-28 object-cover bg-gray-100"
                    >
                    <div class="p-4">
                        <p class="font-medium text-gray-900 line-clamp-2"><?= e($row['name']) ?></p>
                        <p class="mt-2 font-semibold text-blue-700">RP.<?= number_format((int) $row['price'], 0, ',', '.') ?></p>
                        <?php if (!empty($row['service_price'])): ?>
                            <p class="text-xs text-gray-500 mt-1">Jasa pasang: RP.<?= number_format((int) $row['service_price'], 0, ',', '.') ?></p>
                        <?php endif; ?>
                        <p class="text-sm text-gray-600 mt-1">Stok: <?= $stockInt ?></p>

                        <form method="POST" class="mt-3 space-y-2">
                            <input type="hidden" name="product_id" value="<?= (int) $row['id'] ?>">
                            <?php if (!empty($row['service_price'])): ?>
                                <label class="flex items-center gap-2 text-xs text-gray-700">
                                    <input type="checkbox" name="with_service" value="1" class="border rounded">
                                    <span>Tambahkan jasa pasang</span>
                                </label>
                            <?php endif; ?>
                            <?php if ($stockInt > 0): ?>
                                <button name="add" class="w-full bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700">Tambah</button>
                            <?php else: ?>
                                <button disabled class="w-full bg-gray-300 text-gray-600 px-3 py-2 rounded cursor-not-allowed">Stok Habis</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <aside id="keranjang" class="bg-white rounded shadow p-5 lg:sticky top-24 self-start">
        <h3 class="text-xl font-bold mb-4">Keranjang</h3>
<?php
$total = 0;
        $cart = $_SESSION['cart'] ?? [];
        if (!empty($cart)):
            foreach ($cart as $i => $item):
                $total += (int) $item['price'];
        ?>
            <div class="border rounded p-3 mb-2 flex justify-between items-center gap-3">
                <div class="flex items-center gap-3">
                    <img src="<?= e($item['image_url'] ?: 'https://via.placeholder.com/48?text=No+Image') ?>" alt="<?= e($item['name']) ?>" class="w-12 h-12 rounded object-cover border">
                    <div>
                        <span class="block"><?= e($item['name']) ?></span>
                        <span class="text-sm text-gray-600">
                            RP.<?= number_format((int) $item['price'], 0, ',', '.') ?>
                            <?php if (!empty($item['service_on'])): ?>
                                <span class="text-xs text-blue-600">(termasuk jasa pasang)</span>
                            <?php endif; ?>
                        </span>
                        <?php if (isset($item['stock'])): ?>
                            <span class="text-xs text-gray-500">Stok tersisa: <?= (int) $item['stock'] ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <form method="POST">
                    <input type="hidden" name="index" value="<?= (int) $i ?>">
                    <button name="remove" class="px-3 py-1 rounded bg-red-600 text-white">Hapus</button>
                </form>
            </div>
        <?php
            endforeach;
        else:
        ?>
            <p class="text-gray-500">Keranjang masih kosong.</p>
        <?php endif; ?>
        <p class="mt-4 font-semibold">Total: RP.<?= number_format($total, 0, ',', '.') ?></p>
        <div class="mt-3 flex gap-2">
            <?php if (!empty($cart)): ?>
                <a href="checkout.php" class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Checkout</a>
            <?php else: ?>
                <span class="inline-block bg-green-300 text-white px-4 py-2 rounded cursor-not-allowed">Checkout</span>
            <?php endif; ?>
            <form method="POST">
                <button name="clear_cart" class="inline-block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700" <?= empty($cart) ? "disabled style='opacity:0.6;cursor:not-allowed;'" : "" ?>>Kosongkan</button>
            </form>
        </div>
    </aside>
    </div>
    <section id="riwayat" class="bg-white rounded shadow p-5 mt-8">
        <h3 class="text-xl font-bold mb-4">Riwayat Pesanan Saya</h3>
        <div class="space-y-2">
            <?php if ($orders->num_rows > 0): ?>
                <?php while($o = $orders->fetch_assoc()): ?>
                    <div class="border rounded p-3 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <span class="block font-medium">Order #<?= (int) $o['id'] ?></span>
                            <span class="block text-sm text-gray-600">
                                Total: RP.<?= number_format((int) $o['total'], 0, ',', '.') ?>
                                | Status: <?= e($o['status']) ?>
                                <?php if (!empty($o['payment_method'])): ?>
                                    <span class="text-xs text-gray-500"> (<?= e($o['payment_method']) ?>)</span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <form method="POST" onsubmit="return confirm('Hapus riwayat pesanan ini?');">
                            <input type="hidden" name="order_id" value="<?= (int) $o['id'] ?>">
                            <button
                                name="delete_order"
                                class="px-3 py-1 rounded bg-red-600 text-white text-sm hover:bg-red-700"
                            >
                                Hapus Riwayat
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-gray-500">Belum ada riwayat pesanan.</p>
            <?php endif; ?>
        </div>
    </section>
</main>
</body>
</html>
