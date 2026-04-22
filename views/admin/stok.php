<?php
/**
 * views/admin/stok.php
 * Tab Stok — daftar stok semua produk beserta indikator stok rendah
 * Variabel yang dibutuhkan (dari admin.php):
 *   $stockProducts (mysqli_result — sudah paginated),
 *   $stockPage, $stockTotalPages, $stockOffset, $STOCK_PER_PAGE,
 *   $totalProducts, $lowStockThreshold (int)
 */
$lowStockThreshold = $lowStockThreshold ?? 5;
?>
<section class="bg-white rounded shadow p-5">
    <!-- Header + info pagination -->
    <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
        <h3 class="text-lg font-semibold">Stok Barang (Per Item)</h3>
        <?php
        $sStart = $totalProducts > 0 ? $stockOffset + 1 : 0;
        $sEnd   = min($stockOffset + $STOCK_PER_PAGE, $totalProducts);
        ?>
        <span class="text-sm text-gray-500">
            Menampilkan <strong><?= $sStart ?>–<?= $sEnd ?></strong> dari <strong><?= $totalProducts ?></strong> item
        </span>
    </div>
    <p class="text-sm text-gray-600 mb-4">
        Tampilan stok per nama barang. Item dengan stok ≤ <?= (int) $lowStockThreshold ?> ditandai LOW.
    </p>

    <div class="space-y-2">
        <?php if ($stockProducts && $stockProducts->num_rows > 0): ?>
            <?php while ($p = $stockProducts->fetch_assoc()): ?>
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

    <!-- Pagination Controls -->
    <?php if ($stockTotalPages > 1): ?>
    <div class="mt-5 flex flex-wrap items-center justify-center gap-1">
        <?php
        $baseQuery = $_GET;
        unset($baseQuery['s_page']);
        $baseQuery['tab'] = 'stok';
        $baseUrl = 'admin.php?' . http_build_query($baseQuery);
        ?>

        <!-- Tombol Pertama & Prev -->
        <?php if ($stockPage > 1): ?>
            <a href="<?= $baseUrl ?>&s_page=1"
               class="px-3 py-1.5 rounded border border-gray-300 text-sm text-gray-600 hover:bg-gray-50 transition">«</a>
            <a href="<?= $baseUrl ?>&s_page=<?= $stockPage - 1 ?>"
               class="px-3 py-1.5 rounded border border-gray-300 text-sm text-gray-600 hover:bg-gray-50 transition">‹ Prev</a>
        <?php else: ?>
            <span class="px-3 py-1.5 rounded border border-gray-200 text-sm text-gray-300 cursor-not-allowed">«</span>
            <span class="px-3 py-1.5 rounded border border-gray-200 text-sm text-gray-300 cursor-not-allowed">‹ Prev</span>
        <?php endif; ?>

        <!-- Nomor Halaman (window ±2) -->
        <?php
        $sWinStart = max(1, $stockPage - 2);
        $sWinEnd   = min($stockTotalPages, $stockPage + 2);
        if ($sWinStart > 1): ?>
            <a href="<?= $baseUrl ?>&s_page=1"
               class="px-3 py-1.5 rounded border border-gray-300 text-sm text-gray-600 hover:bg-gray-50 transition">1</a>
            <?php if ($sWinStart > 2): ?>
                <span class="px-2 py-1.5 text-sm text-gray-400">…</span>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($sn = $sWinStart; $sn <= $sWinEnd; $sn++): ?>
            <?php if ($sn === $stockPage): ?>
                <span class="px-3 py-1.5 rounded border border-blue-600 bg-blue-600 text-white text-sm font-semibold">
                    <?= $sn ?>
                </span>
            <?php else: ?>
                <a href="<?= $baseUrl ?>&s_page=<?= $sn ?>"
                   class="px-3 py-1.5 rounded border border-gray-300 text-sm text-gray-600 hover:bg-gray-50 transition">
                    <?= $sn ?>
                </a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($sWinEnd < $stockTotalPages): ?>
            <?php if ($sWinEnd < $stockTotalPages - 1): ?>
                <span class="px-2 py-1.5 text-sm text-gray-400">…</span>
            <?php endif; ?>
            <a href="<?= $baseUrl ?>&s_page=<?= $stockTotalPages ?>"
               class="px-3 py-1.5 rounded border border-gray-300 text-sm text-gray-600 hover:bg-gray-50 transition">
                <?= $stockTotalPages ?>
            </a>
        <?php endif; ?>

        <!-- Tombol Next & Terakhir -->
        <?php if ($stockPage < $stockTotalPages): ?>
            <a href="<?= $baseUrl ?>&s_page=<?= $stockPage + 1 ?>"
               class="px-3 py-1.5 rounded border border-gray-300 text-sm text-gray-600 hover:bg-gray-50 transition">Next ›</a>
            <a href="<?= $baseUrl ?>&s_page=<?= $stockTotalPages ?>"
               class="px-3 py-1.5 rounded border border-gray-300 text-sm text-gray-600 hover:bg-gray-50 transition">»</a>
        <?php else: ?>
            <span class="px-3 py-1.5 rounded border border-gray-200 text-sm text-gray-300 cursor-not-allowed">Next ›</span>
            <span class="px-3 py-1.5 rounded border border-gray-200 text-sm text-gray-300 cursor-not-allowed">»</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</section>

