<?php
/**
 * views/admin/mutasi.php
 * Tab Mutasi Stok — form mutasi, barcode scan, riwayat mutasi
 * Variabel yang dibutuhkan (dari admin.php): $message, $conn
 */
?>
<section class="bg-white rounded shadow p-5">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <h3 class="text-lg font-semibold">Mutasi Stok (Barang Masuk/Keluar)</h3>
    </div>

    <?php if ($message): ?>
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700 text-sm"><?= e($message) ?></div>
    <?php endif; ?>

    <!-- Barcode scan helper -->
    <div class="mb-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[260px]">
            <label for="barcode_scan" class="block text-sm font-semibold text-gray-700 mb-1">
                Scan Barcode Produk
            </label>
            <input id="barcode_scan" type="text"
                   class="border rounded px-3 py-2 w-full"
                   placeholder="Tempel/scan lalu Enter"
                   autocomplete="off">
            <div id="barcode_scan_msg" class="text-xs mt-1 text-gray-600"></div>
        </div>
        <div class="text-xs text-gray-500">
            Barcode akan mencari produk lalu otomatis memilih di dropdown.
        </div>
    </div>

    <!-- Form mutasi stok -->
    <form method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-3 mb-6">
        <select name="mutasi_product_id" id="mutasi_product_id"
                class="border rounded px-3 py-2 md:col-span-2" required>
            <option value="">Pilih Produk</option>
            <?php
            $allProductsForMutasi = $conn->query("SELECT id, name, stock FROM products ORDER BY name ASC");
            if ($allProductsForMutasi && $allProductsForMutasi->num_rows > 0):
                while ($pp = $allProductsForMutasi->fetch_assoc()):
            ?>
                <option value="<?= (int) $pp['id'] ?>">
                    <?= e($pp['name']) ?> (Stok: <?= (int) $pp['stock'] ?>)
                </option>
            <?php
                endwhile;
            endif;
            ?>
        </select>

        <select name="mutasi_type" class="border rounded px-3 py-2" required>
            <option value="in">Barang Masuk</option>
            <option value="out">Barang Keluar</option>
        </select>

        <input type="number" id="mutasi_qty" name="mutasi_qty"
               min="1" class="border rounded px-3 py-2"
               placeholder="Jumlah" required>

        <input type="text" name="mutasi_note"
               class="border rounded px-3 py-2 md:col-span-2"
               placeholder="Catatan (opsional)">

        <button name="submit_mutasi"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Simpan
        </button>
    </form>

    <!-- Riwayat mutasi -->
    <h4 class="text-md font-semibold mb-2">Riwayat Mutasi Terakhir</h4>
    <div class="space-y-2">
        <?php
        $mutasiList = $conn->query("
            SELECT sm.id, sm.type, sm.qty, sm.note, sm.created_at, p.name AS product_name
            FROM stock_movements sm
            INNER JOIN products p ON p.id = sm.product_id
            ORDER BY sm.id DESC
            LIMIT 50
        ");
        ?>
        <?php if ($mutasiList && $mutasiList->num_rows > 0): ?>
            <?php while ($m = $mutasiList->fetch_assoc()): ?>
                <div class="border rounded p-3 grid grid-cols-1 md:grid-cols-5 gap-3 md:items-center">
                    <div class="md:col-span-2">
                        <p class="font-medium"><?= e($m['product_name']) ?></p>
                        <p class="text-xs text-gray-500"><?= e($m['created_at']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tipe</p>
                        <p class="font-semibold <?= $m['type'] === 'in' ? 'text-green-600' : 'text-red-600' ?>">
                            <?= $m['type'] === 'in' ? '⬆ Masuk' : '⬇ Keluar' ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Jumlah</p>
                        <p class="font-semibold"><?= (int) $m['qty'] ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Catatan</p>
                        <p class="font-medium"><?= e($m['note'] ?? '') ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-500">Belum ada mutasi.</p>
        <?php endif; ?>
    </div>
</section>

<script>
(function () {
    const input  = document.getElementById('barcode_scan');
    const select = document.getElementById('mutasi_product_id');
    const msg    = document.getElementById('barcode_scan_msg');
    const qty    = document.getElementById('mutasi_qty');
    if (!input || !select || !msg) return;

    function setMsg(text, isError) {
        msg.textContent = text || '';
        msg.className   = isError
            ? 'text-xs mt-1 text-red-600'
            : 'text-xs mt-1 text-gray-600';
    }

    input.addEventListener('keydown', async (e) => {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        const barcode = input.value.trim();
        if (!barcode) return;
        setMsg('Mencari produk...', false);
        try {
            const res  = await fetch('product_by_barcode.php?barcode=' + encodeURIComponent(barcode), { credentials: 'same-origin' });
            const data = await res.json();
            if (data && data.ok) {
                select.value = String(data.product_id);
                setMsg('Ditemukan: ' + data.name + ' (stok: ' + data.stock + ')', false);
                input.value = '';
                if (qty) qty.focus();
            } else {
                select.value = '';
                setMsg((data && data.message) ? data.message : 'Produk tidak ditemukan', true);
            }
        } catch {
            select.value = '';
            setMsg('Gagal memuat data barcode', true);
        }
    });
})();
</script>
