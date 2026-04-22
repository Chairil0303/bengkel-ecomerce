<?php
/**
 * views/admin/pelanggan.php
 * Tab Pelanggan — CRUD pelanggan + kendaraan + export CSV
 * Variabel yang dibutuhkan (dari admin.php):
 *   $message, $searchCustomer, $customersList (mysqli_result),
 *   $vehiclesList (mysqli_result), $conn
 */
?>
<section class="bg-white rounded shadow p-5">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <h3 class="text-lg font-semibold">Pelanggan &amp; Kendaraan</h3>
        <div class="flex items-center gap-2">
            <form method="GET" class="flex items-center gap-2">
                <input type="hidden" name="tab" value="pelanggan">
                <input name="c_q" value="<?= e($searchCustomer) ?>"
                       class="border rounded px-3 py-2"
                       placeholder="Cari nama/telp/alamat">
                <button class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700">Cari</button>
            </form>
            <a href="admin.php?tab=pelanggan&export_customers=1"
               class="bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">
                Export CSV
            </a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700 text-sm"><?= e($message) ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- ── Kolom Pelanggan ─────────────────────────────────────────────── -->
        <div>
            <h4 class="font-semibold mb-3">Tambah Pelanggan</h4>
            <form method="POST" class="space-y-2">
                <input name="c_name" required class="border rounded px-3 py-2 w-full" placeholder="Nama">
                <input name="c_phone" class="border rounded px-3 py-2 w-full" placeholder="Telepon">
                <input name="c_address" class="border rounded px-3 py-2 w-full" placeholder="Alamat">
                <button name="add_customer"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Tambah
                </button>
            </form>

            <h4 class="font-semibold mt-6 mb-3">Daftar Pelanggan</h4>
            <div class="space-y-2 max-h-96 overflow-auto">
                <?php if ($customersList && $customersList->num_rows > 0): ?>
                    <?php while ($c = $customersList->fetch_assoc()): ?>
                        <div class="border rounded p-3 flex items-center justify-between gap-3">
                            <div>
                                <p class="font-medium"><?= e($c['name']) ?></p>
                                <p class="text-xs text-gray-600">
                                    <?= e($c['phone'] ?: '-') ?> | <?= e($c['address'] ?: '-') ?>
                                </p>
                            </div>
                            <form method="POST" onsubmit="return confirm('Hapus pelanggan ini?');">
                                <input type="hidden" name="customer_id" value="<?= (int) $c['id'] ?>">
                                <button name="delete_customer"
                                        class="px-3 py-1 rounded bg-red-600 text-white text-sm">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-gray-500">Belum ada pelanggan.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── Kolom Kendaraan ────────────────────────────────────────────── -->
        <div>
            <h4 class="font-semibold mb-3">Tambah Kendaraan</h4>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <select name="v_customer_id" class="border rounded px-3 py-2 md:col-span-2" required>
                    <option value="">Pilih Pelanggan</option>
                    <?php
                    $custForVehicle = $conn->query("SELECT id, name FROM customers ORDER BY name ASC");
                    if ($custForVehicle && $custForVehicle->num_rows > 0):
                        while ($cc = $custForVehicle->fetch_assoc()):
                    ?>
                        <option value="<?= (int) $cc['id'] ?>"><?= e($cc['name']) ?></option>
                    <?php
                        endwhile;
                    endif;
                    ?>
                </select>
                <input name="v_plate" required class="border rounded px-3 py-2"
                       placeholder="Plat (contoh: B 1234 CD)">
                <input name="v_brand" class="border rounded px-3 py-2"
                       placeholder="Merk (contoh: Honda)">
                <input name="v_model" class="border rounded px-3 py-2"
                       placeholder="Model (contoh: Beat)">
                <input name="v_km" type="number" min="0" class="border rounded px-3 py-2"
                       placeholder="KM (opsional)">
                <button name="add_vehicle"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 md:col-span-2">
                    Tambah Kendaraan
                </button>
            </form>

            <h4 class="font-semibold mt-6 mb-3">Daftar Kendaraan</h4>
            <div class="space-y-2 max-h-96 overflow-auto">
                <?php if ($vehiclesList && $vehiclesList->num_rows > 0): ?>
                    <?php while ($v = $vehiclesList->fetch_assoc()): ?>
                        <div class="border rounded p-3 grid grid-cols-1 md:grid-cols-3 gap-2 md:items-center">
                            <div>
                                <p class="font-medium"><?= e($v['plate']) ?></p>
                                <p class="text-xs text-gray-600">
                                    <?= e($v['brand'] ?: '-') ?> <?= e($v['model'] ?: '') ?>
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Pelanggan</p>
                                <p class="font-medium"><?= e($v['customer_name']) ?></p>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-sm">
                                    KM: <?= $v['km'] !== null ? (int) $v['km'] : '-' ?>
                                </span>
                                <form method="POST" onsubmit="return confirm('Hapus kendaraan ini?');">
                                    <input type="hidden" name="vehicle_id" value="<?= (int) $v['id'] ?>">
                                    <button name="delete_vehicle"
                                            class="px-3 py-1 rounded bg-red-600 text-white text-sm">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-gray-500">Belum ada kendaraan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
