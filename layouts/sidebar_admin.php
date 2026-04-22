<aside class="w-64 bg-blue-800 text-white flex flex-col min-h-screen">
    <div class="px-6 py-4 border-b border-blue-700">
        <h1 class="font-semibold text-lg">Panel Admin</h1>
        <p class="text-xs text-blue-200">Bengkel Motor</p>
    </div>
    <nav class="flex-1 px-3 py-4 space-y-1">
        <a href="admin.php?tab=ringkasan"
           class="block px-3 py-2 rounded <?= ($tab ?? '') === 'ringkasan'  ? 'bg-blue-700' : 'hover:bg-blue-700' ?>">
            📊 Ringkasan
        </a>
        <a href="admin.php?tab=pendapatan"
           class="block px-3 py-2 rounded <?= ($tab ?? '') === 'pendapatan' ? 'bg-blue-700' : 'hover:bg-blue-700' ?>">
            💰 Pendapatan
        </a>
        <a href="admin.php?tab=pesanan"
           class="block px-3 py-2 rounded <?= ($tab ?? '') === 'pesanan'   ? 'bg-blue-700' : 'hover:bg-blue-700' ?>">
            📋 Pesanan
        </a>
        <a href="admin.php?tab=produk"
           class="block px-3 py-2 rounded <?= ($tab ?? '') === 'produk'    ? 'bg-blue-700' : 'hover:bg-blue-700' ?>">
            🛒 Tambah/Edit Produk
        </a>
        <a href="admin.php?tab=stok"
           class="block px-3 py-2 rounded <?= ($tab ?? '') === 'stok'      ? 'bg-blue-700' : 'hover:bg-blue-700' ?>">
            📦 Stok Barang
        </a>
        <a href="admin.php?tab=mutasi"
           class="block px-3 py-2 rounded <?= ($tab ?? '') === 'mutasi'    ? 'bg-blue-700' : 'hover:bg-blue-700' ?>">
            🔄 Mutasi Stok
        </a>
        <a href="admin.php?tab=pelanggan"
           class="block px-3 py-2 rounded <?= ($tab ?? '') === 'pelanggan' ? 'bg-blue-700' : 'hover:bg-blue-700' ?>">
            👥 Pelanggan
        </a>
    </nav>
    <div class="px-3 py-4 border-t border-blue-700">
        <p class="text-xs text-blue-300 px-3 mb-2">
            <?= e($_SESSION['user']['name'] ?? $_SESSION['user']['email'] ?? 'Admin') ?>
        </p>
        <a href="logout.php"
           class="block px-3 py-2 rounded bg-blue-700 text-center hover:bg-blue-600">
            Logout
        </a>
    </div>
</aside>
