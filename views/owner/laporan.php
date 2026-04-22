<?php
/**
 * views/owner/laporan.php
 * Laporan pesanan untuk Owner — filter status & tanggal + update status
 * Variabel: $orders (mysqli_result), $filter, $selectedDate, $dayStats
 */
?>
<section class="bg-white rounded shadow p-5">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <h2 id="laporan" class="text-xl font-bold">Laporan Pesanan</h2>
        <form method="GET" class="flex items-center gap-2">
            <input type="date" name="date" value="<?= e($selectedDate) ?>"
                   class="border rounded px-2 py-1 text-sm">
            <select name="status" class="border rounded px-2 py-1">
                <option value=""        <?= $filter === ''        ? 'selected' : '' ?>>Semua Status</option>
                <option value="dibayar"  <?= $filter === 'dibayar'  ? 'selected' : '' ?>>dibayar</option>
                <option value="diproses" <?= $filter === 'diproses' ? 'selected' : '' ?>>diproses</option>
                <option value="selesai"  <?= $filter === 'selesai'  ? 'selected' : '' ?>>selesai</option>
            </select>
            <button class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700">Filter</button>
        </form>
    </div>

    <div class="mb-4 text-sm text-gray-700">
        <p class="font-semibold">
            Rekapan tanggal <?= e($selectedDate) ?>:
            <?= (int) $dayStats['total_orders'] ?> order,
            Pendapatan RP.<?= number_format((int) $dayStats['total_revenue'], 0, ',', '.') ?>
        </p>
    </div>

    <div class="space-y-2">
        <?php if ($orders && $orders->num_rows > 0): ?>
            <?php while ($o = $orders->fetch_assoc()): ?>
                <div class="border rounded p-3 grid grid-cols-1 md:grid-cols-3 gap-3 md:items-center">
                    <span>Total: RP.<?= number_format((int) $o['total'], 0, ',', '.') ?></span>
                    <span class="font-medium">
                        Status: <?= e($o['status']) ?>
                        <?php if (!empty($o['payment_method'])): ?>
                            <span class="block text-xs text-gray-500">
                                Metode: <?= e($o['payment_method']) ?>
                            </span>
                        <?php endif; ?>
                    </span>
                    <form method="POST" class="flex gap-2">
                        <input type="hidden" name="id" value="<?= (int) $o['id'] ?>">
                        <select name="status" class="border rounded px-2 py-1">
                            <option value="dibayar"  <?= $o['status'] === 'dibayar'  ? 'selected' : '' ?>>dibayar</option>
                            <option value="diproses" <?= $o['status'] === 'diproses' ? 'selected' : '' ?>>diproses</option>
                            <option value="selesai"  <?= $o['status'] === 'selesai'  ? 'selected' : '' ?>>selesai</option>
                        </select>
                        <button name="update_status"
                                class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700">
                            Update
                        </button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-500">Belum ada pesanan.</p>
        <?php endif; ?>
    </div>
</section>
