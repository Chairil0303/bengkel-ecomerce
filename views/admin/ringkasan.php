<?php
/**
 * views/admin/ringkasan.php
 * Tab Ringkasan — statistik utama + grafik Chart.js
 * Variabel yang dibutuhkan (dari admin.php):
 *   $statProducts, $statStock, $statOrders, $countCustomers,
 *   $countVehicles, $lowStockCount, $todayStats,
 *   $revDailyLabels, $revDailyValues, $revByMethod, $topProductsArr
 */
?>
<!-- Stat cards baris 1 -->
<section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Total Produk</p>
        <p class="text-2xl font-bold"><?= (int) ($statProducts['total_products'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Total Stok</p>
        <p class="text-2xl font-bold"><?= (int) ($statStock['total_stock'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Total Order</p>
        <p class="text-2xl font-bold"><?= (int) ($statOrders['total_orders'] ?? 0) ?></p>
        <p class="text-sm text-gray-600 mt-1">
            Pendapatan: RP.<?= number_format((int) ($statOrders['total_revenue'] ?? 0), 0, ',', '.') ?>
        </p>
    </div>
</section>

<!-- Stat cards baris 2 -->
<section class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Order Hari Ini</p>
        <p class="text-2xl font-bold"><?= (int) ($todayStats['orders_today'] ?? 0) ?></p>
        <p class="text-sm text-gray-600 mt-1">
            Pendapatan: RP.<?= number_format((int) ($todayStats['revenue_today'] ?? 0), 0, ',', '.') ?>
        </p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Pelanggan</p>
        <p class="text-2xl font-bold"><?= (int) ($countCustomers['c'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Kendaraan</p>
        <p class="text-2xl font-bold"><?= (int) ($countVehicles['c'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Stok Rendah (&lt;=5)</p>
        <p class="text-2xl font-bold text-red-600"><?= (int) ($lowStockCount['c'] ?? 0) ?></p>
    </div>
</section>

<!-- Tabel ringkas + grafik -->
<section class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <!-- Pendapatan per Metode -->
    <div class="bg-white rounded shadow p-5">
        <h3 class="text-lg font-semibold mb-3">Pendapatan per Metode</h3>
        <div class="space-y-2 text-sm">
            <?php if (!empty($revByMethod)): ?>
                <?php foreach ($revByMethod as $m): ?>
                    <div class="flex items-center justify-between border rounded px-3 py-2">
                        <span><?= e($m['payment_method'] ?: 'unknown') ?></span>
                        <span class="font-semibold">RP.<?= number_format((int) $m['revenue'], 0, ',', '.') ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-500">Belum ada data.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Top 5 Produk Terlaris -->
    <div class="bg-white rounded shadow p-5">
        <h3 class="text-lg font-semibold mb-3">Top 5 Produk Terlaris</h3>
        <div class="space-y-2 text-sm">
            <?php if (!empty($topProductsArr)): ?>
                <?php foreach ($topProductsArr as $tp): ?>
                    <div class="flex items-center justify-between border rounded px-3 py-2">
                        <span class="truncate"><?= e($tp['name']) ?></span>
                        <span class="font-semibold shrink-0 ml-2">
                            <?= (int) $tp['qty_sold'] ?>x |
                            RP.<?= number_format((int) $tp['revenue'], 0, ',', '.') ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-500">Belum ada data.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Chart.js Grafik -->
<section class="bg-white rounded shadow p-5 mb-6">
    <h3 class="text-lg font-semibold mb-4">Grafik Statistik</h3>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div>
            <p class="text-sm text-gray-600 mb-2">Pendapatan 14 Hari Terakhir</p>
            <canvas id="chartRevenueDaily" height="120"></canvas>
        </div>
        <div>
            <p class="text-sm text-gray-600 mb-2">Pendapatan per Metode</p>
            <canvas id="chartRevenueMethod" height="120"></canvas>
        </div>
        <div>
            <p class="text-sm text-gray-600 mb-2">Top Produk (Qty)</p>
            <canvas id="chartTopProducts" height="120"></canvas>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const dailyLabels  = <?= json_encode($revDailyLabels) ?>;
    const dailyValues  = <?= json_encode($revDailyValues) ?>;
    const methodData   = <?= json_encode($revByMethod) ?>;
    const topProducts  = <?= json_encode($topProductsArr) ?>;

    // Daily revenue — Line
    const ctxDaily = document.getElementById('chartRevenueDaily');
    if (ctxDaily && window.Chart) {
        new Chart(ctxDaily, {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Pendapatan',
                    data: dailyValues,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,0.15)',
                    tension: 0.2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { ticks: { callback: v => 'RP.' + v.toLocaleString('id-ID') } } }
            }
        });
    }

    // Revenue by method — Doughnut
    const ctxMethod = document.getElementById('chartRevenueMethod');
    if (ctxMethod && window.Chart) {
        new Chart(ctxMethod, {
            type: 'doughnut',
            data: {
                labels: methodData.map(m => m.payment_method || 'unknown'),
                datasets: [{
                    data: methodData.map(m => parseInt(m.revenue || 0, 10)),
                    backgroundColor: ['#10b981','#f59e0b','#3b82f6','#ef4444','#8b5cf6']
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    }

    // Top products — Bar
    const ctxTop = document.getElementById('chartTopProducts');
    if (ctxTop && window.Chart) {
        new Chart(ctxTop, {
            type: 'bar',
            data: {
                labels: topProducts.map(p => p.name),
                datasets: [{
                    label: 'Qty Terjual',
                    data: topProducts.map(p => parseInt(p.qty_sold || 0, 10)),
                    backgroundColor: '#14b8a6'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }
})();
</script>
