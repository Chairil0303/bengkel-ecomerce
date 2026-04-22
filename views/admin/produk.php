<?php
/**
 * views/admin/produk.php
 * Tab Produk — CRUD produk + kelola kategori + daftar produk
 * Variabel yang dibutuhkan (dari admin.php):
 *   $message, $catMessage, $editProduct, $categories (mysqli_result), $products (mysqli_result)
 */
?>
<!-- Form Tambah / Edit Produk -->
<section class="bg-white rounded shadow p-5 mb-6">
    <h2 class="text-xl font-bold mb-4">
        <?= $editProduct ? 'Edit Produk' : 'Tambah Produk' ?>
    </h2>

    <?php if ($message): ?>
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700 text-sm"><?= e($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <?php if ($editProduct): ?>
            <input type="hidden" name="id" value="<?= (int) $editProduct['id'] ?>">
        <?php endif; ?>

        <input name="name" required class="border rounded px-3 py-2" placeholder="Nama Produk"
               value="<?= $editProduct ? e($editProduct['name']) : '' ?>">

        <input name="barcode" class="border rounded px-3 py-2" placeholder="Barcode / SKU (opsional)"
               value="<?= $editProduct ? e($editProduct['barcode'] ?? '') : '' ?>">

        <input name="image_url" class="border rounded px-3 py-2" placeholder="URL Gambar (opsional)"
               value="<?= $editProduct ? e($editProduct['image_url'] ?? '') : '' ?>">

        <select name="category_id" class="border rounded px-3 py-2">
            <option value="">Pilih Kategori (opsional)</option>
            <?php
            // Reset pointer agar bisa di-loop ulang
            if ($categories && $categories->num_rows > 0):
                $categories->data_seek(0);
                while ($c = $categories->fetch_assoc()):
            ?>
                <option value="<?= (int) $c['id'] ?>"
                    <?= $editProduct && (int) ($editProduct['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>>
                    <?= e($c['name']) ?>
                </option>
            <?php
                endwhile;
            endif;
            ?>
        </select>

        <input name="price" type="number" min="0" required class="border rounded px-3 py-2"
               placeholder="Harga barang"
               value="<?= $editProduct ? (int) $editProduct['price'] : '' ?>">

        <input name="service_price" type="number" min="0" class="border rounded px-3 py-2"
               placeholder="Biaya jasa pasang (opsional)"
               value="<?= $editProduct ? (int) ($editProduct['service_price'] ?? 0) : '' ?>">

        <input name="stock" type="number" min="0" required class="border rounded px-3 py-2"
               placeholder="Stok"
               value="<?= $editProduct ? (int) $editProduct['stock'] : '' ?>">

        <?php if ($editProduct): ?>
            <div class="md:col-span-2 flex gap-2">
                <button name="update"
                        class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
                    Simpan Perubahan
                </button>
                <a href="admin.php?tab=produk" class="py-2 px-4 rounded border">Batal</a>
            </div>
        <?php else: ?>
            <button name="add"
                    class="md:col-span-2 bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Tambah Produk
            </button>
        <?php endif; ?>
    </form>
</section>

<!-- Kelola Kategori -->
<section class="bg-white rounded shadow p-5 mb-6">
    <h3 class="text-lg font-semibold mb-3">Kelola Kategori</h3>

    <?php if ($catMessage): ?>
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700 text-sm"><?= e($catMessage) ?></div>
    <?php endif; ?>

    <form method="POST" class="flex flex-wrap items-center gap-2 mb-4">
        <input type="text" name="category_name"
               placeholder="Nama kategori baru (mis. Oli, Ban, Jasa Servis)"
               class="border rounded px-3 py-2 flex-1 min-w-[200px]" required>
        <button name="add_category"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
            Tambah Kategori
        </button>
    </form>

    <div class="space-y-1 text-sm">
        <?php
        $categoriesList = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
        ?>
        <?php if ($categoriesList && $categoriesList->num_rows > 0): ?>
            <?php while ($c = $categoriesList->fetch_assoc()): ?>
                <div class="flex items-center justify-between border rounded px-3 py-2">
                    <span><?= e($c['name']) ?></span>
                    <form method="POST"
                          onsubmit="return confirm('Hapus kategori ini? Pastikan tidak dipakai produk.');">
                        <input type="hidden" name="category_id" value="<?= (int) $c['id'] ?>">
                        <button name="delete_category"
                                class="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-700 text-xs">
                            Hapus
                        </button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-500">Belum ada kategori.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Daftar Produk -->
<section class="bg-white rounded shadow p-5">
    <h3 class="text-lg font-semibold mb-3">Daftar Produk</h3>
    <div class="space-y-2">
        <?php if ($products && $products->num_rows > 0): ?>
            <?php
            // Buat map kategori id → nama untuk tampilan
            $categoryMap = [];
            if ($categories && $categories->num_rows > 0) {
                $categories->data_seek(0);
                while ($cRow = $categories->fetch_assoc()) {
                    $categoryMap[(int) $cRow['id']] = $cRow['name'];
                }
            }
            ?>
            <?php while ($p = $products->fetch_assoc()): ?>
                <?php
                $catName = '';
                if (!empty($p['category_id'])) {
                    $catName = $categoryMap[(int) $p['category_id']] ?? '';
                }
                ?>
                <div class="border rounded p-3 grid grid-cols-1 md:grid-cols-4 gap-3 md:items-center">
                    <div class="md:col-span-2">
                        <span class="font-medium block"><?= e($p['name']) ?></span>
                        <?php if ($catName !== ''): ?>
                            <span class="text-xs text-gray-500">Kategori: <?= e($catName) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($p['barcode'])): ?>
                            <span class="text-xs text-gray-500 truncate block">Barcode: <?= e($p['barcode']) ?></span>
                        <?php endif; ?>
                    </div>
                    <span>
                        RP.<?= number_format((int) $p['price'], 0, ',', '.') ?> | Stok: <?= (int) $p['stock'] ?>
                    </span>
                    <div class="flex gap-2">
                        <a href="admin.php?edit=<?= (int) $p['id'] ?>"
                           class="px-3 py-1 rounded border">Edit</a>
                        <form method="POST" onsubmit="return confirm('Hapus produk ini?');">
                            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                            <button name="delete"
                                    class="px-3 py-1 rounded bg-red-600 text-white">Hapus</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-500">Belum ada produk.</p>
        <?php endif; ?>
    </div>
</section>
