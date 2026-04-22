<?php
/**
 * views/admin/stok.php
 * Tab Stok — daftar stok semua produk beserta indikator stok rendah
 * Variabel yang dibutuhkan (dari admin.php):
 *   $products (mysqli_result), $lowStockThreshold (int)
 */
$lowStockThreshold = $lowStockThreshold ?? 5;
?>
<section class="bg-white rounded shadow p-5">
    <h3 class="text-lg font-semibold mb-1">Stok Barang (Per Item)</h3>
    <p class="text-sm text-gray-600 mb-4">
        Tampilan stok per nama barang. Item dengan stok ≤ <?= (int) $lowStockThreshold ?> ditandai LOW.
    </p>

    <div class="space-y-2">
        <?php if ($products && $products->num_rows > 0): ?>
            <?php while ($p = $products->fetch_assoc()): ?>
                <?php
                $stockInt = (int) $p['stock'];
                $isLow    = $stockInt <= (int) $lowStockThreshold;
                ?>
                <div class="border rounded p-3 flex items-center justify-between gap-3">
                    <div>
                        <p class="font-medium"><?= e($p['name']) ?></p>
                        <p class="text-sm text-gray-600">
                            RP.<?= number_format((int) $p['price'], 0, ',', '.') ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">Stok: <?= $stockInt ?></p>
                        <?php if ($isLow): ?>
                            <span class="inline-block mt-1 px-2 py-1 rounded text-xs bg-red-100 text-red-700 font-semibold">
                                LOW
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-500">Belum ada produk.</p>
        <?php endif; ?>
    </div>
</section>
