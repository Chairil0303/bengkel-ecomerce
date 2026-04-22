<?php
/**
 * views/admin/pendapatan.php
 * Tab Pendapatan — filter tanggal/bulan/tahun + kartu summary
 * Variabel yang dibutuhkan (dari admin.php):
 *   $statOrders, $pendapatanDate, $pendapatanMonth, $pendapatanYear,
 *   $revDay, $revMonth, $revYear
 */
?>
<section class="bg-white rounded shadow p-5 mb-6">
    <h2 class="text-xl font-bold mb-4">Pendapatan</h2>

    <!-- Form filter -->
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6 text-sm">
        <input type="hidden" name="tab" value="pendapatan">
        <div>
            <label class="block text-gray-600 mb-1">Per Hari (tanggal)</label>
            <input type="date" name="rev_date"
                   value="<?= e($pendapatanDate) ?>"
                   class="border rounded px-3 py-2 w-full">
        </div>
        <div>
            <label class="block text-gray-600 mb-1">Per Bulan (YYYY-MM)</label>
            <input type="month" name="rev_month"
                   value="<?= e($pendapatanMonth) ?>"
                   class="border rounded px-3 py-2 w-full">
        </div>
        <div>
            <label class="block text-gray-600 mb-1">Per Tahun (YYYY)</label>
            <input type="number" name="rev_year" min="2000" max="2100"
                   value="<?= e($pendapatanYear) ?>"
                   class="border rounded px-3 py-2 w-full">
        </div>
        <div class="flex items-end">
            <button class="w-full bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700">
                Tampilkan
            </button>
        </div>
    </form>

    <!-- Kartu summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gray-50 border rounded p-4">
            <p class="text-xs text-gray-500 uppercase">Keseluruhan</p>
            <p class="mt-1 text-sm text-gray-600">
                Total Order: <?= (int) ($statOrders['total_orders'] ?? 0) ?>
            </p>
            <p class="text-2xl font-bold">
                RP.<?= number_format((int) ($statOrders['total_revenue'] ?? 0), 0, ',', '.') ?>
            </p>
        </div>
        <div class="bg-gray-50 border rounded p-4">
            <p class="text-xs text-gray-500 uppercase">Per Hari</p>
            <p class="mt-1 text-sm text-gray-600"><?= e($pendapatanDate) ?></p>
            <p class="text-2xl font-bold">RP.<?= number_format((int) $revDay, 0, ',', '.') ?></p>
        </div>
        <div class="bg-gray-50 border rounded p-4">
            <p class="text-xs text-gray-500 uppercase">Per Bulan</p>
            <p class="mt-1 text-sm text-gray-600"><?= e($pendapatanMonth) ?></p>
            <p class="text-2xl font-bold">RP.<?= number_format((int) $revMonth, 0, ',', '.') ?></p>
        </div>
        <div class="bg-gray-50 border rounded p-4">
            <p class="text-xs text-gray-500 uppercase">Per Tahun</p>
            <p class="mt-1 text-sm text-gray-600"><?= e($pendapatanYear) ?></p>
            <p class="text-2xl font-bold">RP.<?= number_format((int) $revYear, 0, ',', '.') ?></p>
        </div>
    </div>
</section>
