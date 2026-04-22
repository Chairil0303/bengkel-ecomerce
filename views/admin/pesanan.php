<?php
/**
 * views/admin/pesanan.php
 * Tab Pesanan — daftar 50 pesanan terakhir + update status
 * Variabel yang dibutuhkan (dari admin.php):
 *   $ordersList (mysqli_result), $statOrders, $message
 */
?>
<section class="bg-white rounded shadow p-5">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <h3 class="text-lg font-semibold">Pesanan (Last 50)</h3>
        <div class="text-sm text-gray-600">
            Dibayar: <span class="font-semibold"><?= (int) ($statOrders['dibayar']  ?? 0) ?></span> |
            Diproses: <span class="font-semibold"><?= (int) ($statOrders['diproses'] ?? 0) ?></span> |
            Selesai: <span class="font-semibold"><?= (int) ($statOrders['selesai']  ?? 0) ?></span>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700 text-sm"><?= e($message) ?></div>
    <?php endif; ?>

    <div class="space-y-2">
        <?php if ($ordersList && $ordersList->num_rows > 0): ?>
            <?php while ($o = $ordersList->fetch_assoc()): ?>
                <div class="border rounded p-3 grid grid-cols-1 md:grid-cols-6 gap-3 md:items-center">
                    <div class="md:col-span-2">
                        <p class="font-medium">#<?= (int) $o['id'] ?></p>
                        <p class="text-sm text-gray-600"><?= e($o['user_name'] ?: $o['user_email']) ?></p>
                    </div>
                    <div class="md:col-span-1">
                        <p class="text-sm text-gray-600">Total</p>
                        <p class="font-semibold">RP.<?= number_format((int) $o['total'], 0, ',', '.') ?></p>
                    </div>
                    <div class="md:col-span-1">
                        <p class="text-sm text-gray-600">Tanggal</p>
                        <p class="font-medium text-sm"><?= e($o['created_at']) ?></p>
                    </div>
                    <div class="md:col-span-2 flex items-center gap-2">
                        <form method="POST" class="flex items-center gap-2 w-full">
                            <input type="hidden" name="id" value="<?= (int) $o['id'] ?>">
                            <select name="status" class="border rounded px-2 py-1 w-full">
                                <option value="dibayar"  <?= $o['status'] === 'dibayar'  ? 'selected' : '' ?>>dibayar</option>
                                <option value="diproses" <?= $o['status'] === 'diproses' ? 'selected' : '' ?>>diproses</option>
                                <option value="selesai"  <?= $o['status'] === 'selesai'  ? 'selected' : '' ?>>selesai</option>
                            </select>
                            <button name="admin_update_status"
                                    class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 shrink-0">
                                Update
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-500">Belum ada pesanan.</p>
        <?php endif; ?>
    </div>
</section>
