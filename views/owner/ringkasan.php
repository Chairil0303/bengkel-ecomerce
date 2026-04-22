<?php
/**
 * views/owner/ringkasan.php
 * Kartu statistik ringkasan untuk Owner
 * Variabel: $stats (array)
 */
?>
<section id="ringkasan" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Total Order</p>
        <p class="text-2xl font-bold"><?= (int) $stats['total_orders'] ?></p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Pendapatan</p>
        <p class="text-2xl font-bold">RP.<?= number_format((int) $stats['total_revenue'], 0, ',', '.') ?></p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Dibayar</p>
        <p class="text-2xl font-bold"><?= (int) $stats['dibayar'] ?></p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Diproses</p>
        <p class="text-2xl font-bold"><?= (int) $stats['diproses'] ?></p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Selesai</p>
        <p class="text-2xl font-bold"><?= (int) $stats['selesai'] ?></p>
    </div>
</section>
