<?php
/**
 * views/owner/pendapatan.php
 * Filter dan kartu pendapatan per hari/bulan/tahun untuk Owner
 * Variabel: $stats, $selectedDate, $selectedMonth, $selectedYear,
 *           $dayStats, $monthStats, $yearStats
 */
?>
<section id="pendapatan" class="bg-white rounded shadow p-5 mb-6">
    <h2 class="text-xl font-bold mb-4">Pendapatan</h2>

    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4 text-sm">
        <div>
            <label class="block text-gray-600 mb-1">Per Hari (tanggal)</label>
            <input type="date" name="date" value="<?= e($selectedDate) ?>"
                   class="border rounded px-3 py-2 w-full">
        </div>
        <div>
            <label class="block text-gray-600 mb-1">Per Bulan (YYYY-MM)</label>
            <input type="month" name="month" value="<?= e($selectedMonth) ?>"
                   class="border rounded px-3 py-2 w-full">
        </div>
        <div>
            <label class="block text-gray-600 mb-1">Per Tahun (YYYY)</label>
            <input type="number" name="year" min="2000" max="2100"
                   value="<?= e($selectedYear) ?>"
                   class="border rounded px-3 py-2 w-full">
        </div>
        <div class="flex items-end">
            <button class="w-full bg-indigo-600 text-white px-3 py-2 rounded hover:bg-indigo-700">
                Tampilkan
            </button>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gray-50 border rounded p-4">
            <p class="text-xs text-gray-500 uppercase">Keseluruhan</p>
            <p class="text-2xl font-bold">
                RP.<?= number_format((int) $stats['total_revenue'], 0, ',', '.') ?>
            </p>
        </div>
        <div class="bg-gray-50 border rounded p-4">
            <p class="text-xs text-gray-500 uppercase">Per Hari</p>
            <p class="mt-1 text-sm text-gray-600"><?= e($selectedDate) ?></p>
            <p class="text-2xl font-bold">
                RP.<?= number_format((int) $dayStats['total_revenue'], 0, ',', '.') ?>
            </p>
        </div>
        <div class="bg-gray-50 border rounded p-4">
            <p class="text-xs text-gray-500 uppercase">Per Bulan</p>
            <p class="mt-1 text-sm text-gray-600"><?= e($selectedMonth) ?></p>
            <p class="text-2xl font-bold">
                RP.<?= number_format((int) $monthStats['total_revenue'], 0, ',', '.') ?>
            </p>
        </div>
        <div class="bg-gray-50 border rounded p-4">
            <p class="text-xs text-gray-500 uppercase">Per Tahun</p>
            <p class="mt-1 text-sm text-gray-600"><?= e($selectedYear) ?></p>
            <p class="text-2xl font-bold">
                RP.<?= number_format((int) $yearStats['total_revenue'], 0, ',', '.') ?>
            </p>
        </div>
    </div>
</section>
