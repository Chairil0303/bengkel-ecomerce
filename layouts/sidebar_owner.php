<aside class="w-64 bg-indigo-800 text-white flex flex-col min-h-screen">
    <div class="px-6 py-4 border-b border-indigo-700">
        <h1 class="font-semibold text-lg">Dashboard Owner</h1>
        <p class="text-xs text-indigo-200">Bengkel Motor</p>
    </div>
    <nav class="flex-1 px-3 py-4 space-y-1">
        <a href="#ringkasan"
           class="block px-3 py-2 rounded hover:bg-indigo-700">
            📊 Ringkasan
        </a>
        <a href="#pendapatan"
           class="block px-3 py-2 rounded hover:bg-indigo-700">
            💰 Pendapatan
        </a>
        <a href="#laporan"
           class="block px-3 py-2 rounded hover:bg-indigo-700">
            📋 Laporan Pesanan
        </a>
    </nav>
    <div class="px-3 py-4 border-t border-indigo-700">
        <p class="text-xs text-indigo-300 px-3 mb-2">
            <?= e($_SESSION['user']['name'] ?? $_SESSION['user']['email'] ?? 'Owner') ?>
        </p>
        <a href="logout.php"
           class="block px-3 py-2 rounded bg-indigo-700 text-center hover:bg-indigo-600">
            Logout
        </a>
    </div>
</aside>
