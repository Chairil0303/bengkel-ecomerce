<?php
/**
 * includes/functions.php
 * Semua fungsi bisnis (CRUD produk, kategori, pelanggan, kendaraan, mutasi stok,
 * dan query statistik). Tidak ada output HTML di sini.
 *
 * Setiap fungsi menerima $conn (mysqli) sebagai dependensi eksplisit.
 */

// ═══════════════════════════════════════════════════════════════════════════════
// PRODUK
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Ambil semua produk, diurutkan terbaru.
 */
function get_all_products(mysqli $conn): mysqli_result|false
{
    return $conn->query("SELECT * FROM products ORDER BY id DESC");
}

/**
 * Hitung total produk (untuk pagination).
 */
function count_products(mysqli $conn): int
{
    $res = $conn->query("SELECT COUNT(*) AS total FROM products");
    return $res ? (int) $res->fetch_assoc()['total'] : 0;
}

/**
 * Ambil produk dengan LIMIT/OFFSET — untuk pagination.
 *
 * @param int $limit  Jumlah item per halaman
 * @param int $offset Baris awal
 */
function get_products_paginated(mysqli $conn, int $limit, int $offset): mysqli_result|false
{
    $stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    return $stmt->get_result();
}

/**
 * Ambil satu produk berdasarkan ID.
 */
function get_product_by_id(mysqli $conn, int $id): ?array
{
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

/**
 * Tambah produk baru. Return pesan hasil operasi.
 */
function create_product(mysqli $conn, array $post): string
{
    $name        = trim($post['name'] ?? '');
    $barcode     = trim($post['barcode'] ?? '');
    $barcode     = $barcode !== '' ? $barcode : null;
    $image_url   = trim($post['image_url'] ?? '');
    $category_id = isset($post['category_id']) && $post['category_id'] !== ''
                   ? (int) $post['category_id'] : null;
    $price         = (int) ($post['price'] ?? 0);
    $service_price = (int) ($post['service_price'] ?? 0);
    $stock         = (int) ($post['stock'] ?? 0);

    if ($name === '' || $price < 0 || $stock < 0) {
        return 'Data produk tidak valid.';
    }

    if ($category_id !== null) {
        $stmt = $conn->prepare("INSERT INTO products (name, category_id, barcode, image_url, price, service_price, stock) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissiii", $name, $category_id, $barcode, $image_url, $price, $service_price, $stock);
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, barcode, image_url, price, service_price, stock) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiii", $name, $barcode, $image_url, $price, $service_price, $stock);
    }

    $stmt->execute();
    return 'Produk berhasil ditambahkan.';
}

/**
 * Update produk. Return pesan hasil operasi.
 */
function update_product(mysqli $conn, int $id, array $post): string
{
    $name        = trim($post['name'] ?? '');
    $barcode     = trim($post['barcode'] ?? '');
    $barcode     = $barcode !== '' ? $barcode : null;
    $image_url   = trim($post['image_url'] ?? '');
    $category_id = isset($post['category_id']) && $post['category_id'] !== ''
                   ? (int) $post['category_id'] : null;
    $price         = (int) ($post['price'] ?? 0);
    $service_price = (int) ($post['service_price'] ?? 0);
    $stock         = (int) ($post['stock'] ?? 0);

    if ($id <= 0 || $name === '' || $price < 0 || $stock < 0) {
        return 'Data produk tidak valid.';
    }

    if ($category_id !== null) {
        $stmt = $conn->prepare("UPDATE products SET name = ?, category_id = ?, barcode = ?, image_url = ?, price = ?, service_price = ?, stock = ? WHERE id = ?");
        $stmt->bind_param("sissiiii", $name, $category_id, $barcode, $image_url, $price, $service_price, $stock, $id);
    } else {
        $stmt = $conn->prepare("UPDATE products SET name = ?, category_id = NULL, barcode = ?, image_url = ?, price = ?, service_price = ?, stock = ? WHERE id = ?");
        $stmt->bind_param("sssiiii", $name, $barcode, $image_url, $price, $service_price, $stock, $id);
    }

    $stmt->execute();
    return 'Produk berhasil diperbarui.';
}

/**
 * Hapus produk. Return pesan hasil operasi.
 */
function delete_product(mysqli $conn, int $id): string
{
    if ($id <= 0) return 'ID produk tidak valid.';

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return 'Produk berhasil dihapus.';
}

// ═══════════════════════════════════════════════════════════════════════════════
// KATEGORI
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Ambil semua kategori diurutkan A-Z.
 */
function get_all_categories(mysqli $conn): mysqli_result|false
{
    return $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
}

/**
 * Tambah kategori baru.
 */
function create_category(mysqli $conn, string $name): string
{
    $name = trim($name);
    if ($name === '') return 'Nama kategori tidak boleh kosong.';

    $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    return 'Kategori berhasil ditambahkan.';
}

/**
 * Hapus kategori.
 */
function delete_category(mysqli $conn, int $id): string
{
    if ($id <= 0) return 'ID kategori tidak valid.';

    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return 'Kategori berhasil dihapus.';
}

// ═══════════════════════════════════════════════════════════════════════════════
// STATISTIK DASHBOARD
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Ambil semua data statistik yang dibutuhkan tab Ringkasan.
 */
function get_dashboard_stats(mysqli $conn): array
{
    $statProducts = $conn->query("SELECT COUNT(*) AS total_products FROM products")->fetch_assoc();
    $statStock    = $conn->query("SELECT COALESCE(SUM(stock),0) AS total_stock FROM products")->fetch_assoc();
    $statOrders   = $conn->query("
        SELECT
            COUNT(*) AS total_orders,
            COALESCE(SUM(total),0) AS total_revenue,
            SUM(CASE WHEN status='dibayar'  THEN 1 ELSE 0 END) AS dibayar,
            SUM(CASE WHEN status='diproses' THEN 1 ELSE 0 END) AS diproses,
            SUM(CASE WHEN status='selesai'  THEN 1 ELSE 0 END) AS selesai
        FROM orders
    ")->fetch_assoc();

    $countCustomers = $conn->query("SELECT COUNT(*) AS c FROM customers")->fetch_assoc();
    $countVehicles  = $conn->query("SELECT COUNT(*) AS c FROM vehicles")->fetch_assoc();
    $lowStockCount  = $conn->query("SELECT COUNT(*) AS c FROM products WHERE stock <= 5")->fetch_assoc();

    // Statistik hari ini
    $today    = date('Y-m-d');
    $stmtToday = $conn->prepare("SELECT COUNT(*) AS orders_today, COALESCE(SUM(total),0) AS revenue_today FROM orders WHERE DATE(created_at) = ?");
    $stmtToday->bind_param("s", $today);
    $stmtToday->execute();
    $todayStats = $stmtToday->get_result()->fetch_assoc();

    // Pendapatan 14 hari terakhir
    $revDailyRaw = [];
    $resDaily = $conn->query("
        SELECT DATE(created_at) AS d, COALESCE(SUM(total),0) AS total
        FROM orders
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 13 DAY)
        GROUP BY DATE(created_at)
        ORDER BY d ASC
    ");
    if ($resDaily && $resDaily->num_rows > 0) {
        while ($row = $resDaily->fetch_assoc()) {
            $revDailyRaw[$row['d']] = (int) $row['total'];
        }
    }

    $revDailyLabels = [];
    $revDailyValues = [];
    for ($i = 13; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-{$i} day"));
        $revDailyLabels[] = $d;
        $revDailyValues[] = (int) ($revDailyRaw[$d] ?? 0);
    }

    // Pendapatan per metode pembayaran
    $revByMethod = [];
    $resMethod = $conn->query("SELECT payment_method, COALESCE(SUM(total),0) AS revenue FROM orders GROUP BY payment_method");
    if ($resMethod && $resMethod->num_rows > 0) {
        while ($row = $resMethod->fetch_assoc()) {
            $revByMethod[] = $row;
        }
    }

    // Top 5 produk terlaris
    $topProductsArr = [];
    $topProductsRes = $conn->query("
        SELECT name, COALESCE(SUM(qty),0) AS qty_sold, COALESCE(SUM(subtotal),0) AS revenue
        FROM order_items
        GROUP BY product_id, name
        ORDER BY qty_sold DESC
        LIMIT 5
    ");
    if ($topProductsRes && $topProductsRes->num_rows > 0) {
        while ($row = $topProductsRes->fetch_assoc()) {
            $topProductsArr[] = $row;
        }
    }

    return compact(
        'statProducts', 'statStock', 'statOrders',
        'countCustomers', 'countVehicles', 'lowStockCount',
        'todayStats', 'revDailyLabels', 'revDailyValues',
        'revByMethod', 'topProductsArr'
    );
}

/**
 * Ambil data pendapatan berdasarkan filter tanggal/bulan/tahun.
 */
function get_revenue_stats(mysqli $conn, string $date, string $month, string $year): array
{
    $statOrders = $conn->query("
        SELECT COUNT(*) AS total_orders, COALESCE(SUM(total),0) AS total_revenue
        FROM orders
    ")->fetch_assoc();

    $stmtDay = $conn->prepare("SELECT COALESCE(SUM(total),0) AS total FROM orders WHERE DATE(created_at) = ?");
    $stmtDay->bind_param("s", $date);
    $stmtDay->execute();
    $revDay = (int) ($stmtDay->get_result()->fetch_assoc()['total'] ?? 0);

    $stmtMonth = $conn->prepare("SELECT COALESCE(SUM(total),0) AS total FROM orders WHERE DATE_FORMAT(created_at, '%Y-%m') = ?");
    $stmtMonth->bind_param("s", $month);
    $stmtMonth->execute();
    $revMonth = (int) ($stmtMonth->get_result()->fetch_assoc()['total'] ?? 0);

    $stmtYear = $conn->prepare("SELECT COALESCE(SUM(total),0) AS total FROM orders WHERE YEAR(created_at) = ?");
    $stmtYear->bind_param("s", $year);
    $stmtYear->execute();
    $revYear = (int) ($stmtYear->get_result()->fetch_assoc()['total'] ?? 0);

    return compact('statOrders', 'revDay', 'revMonth', 'revYear');
}

// ═══════════════════════════════════════════════════════════════════════════════
// PESANAN (ORDER)
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Ambil 50 pesanan terakhir beserta info user.
 */
function get_recent_orders(mysqli $conn): mysqli_result|false
{
    return $conn->query("
        SELECT o.id, o.total, o.status, o.payment_method, o.created_at,
               u.name AS user_name, u.email AS user_email
        FROM orders o
        INNER JOIN users u ON u.id = o.user_id
        ORDER BY o.id DESC
        LIMIT 50
    ");
}

/**
 * Update status pesanan oleh admin.
 */
function admin_update_order_status(mysqli $conn, int $id, string $status): string
{
    $allowed = ['dibayar', 'diproses', 'selesai'];
    if ($id <= 0 || !in_array($status, $allowed, true)) {
        return 'Data tidak valid.';
    }
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ? LIMIT 1");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    return 'Status pesanan berhasil diperbarui.';
}

// ═══════════════════════════════════════════════════════════════════════════════
// MUTASI STOK
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Catat mutasi stok (masuk/keluar).
 */
function submit_stock_mutation(mysqli $conn, array $post, int $adminUserId): string
{
    $type      = ($post['mutasi_type'] ?? '') === 'out' ? 'out' : 'in';
    $qty       = max(0, (int) ($post['mutasi_qty'] ?? 0));
    $productId = (int) ($post['mutasi_product_id'] ?? 0);
    $note      = trim($post['mutasi_note'] ?? '');

    if ($productId <= 0 || $qty <= 0) {
        return 'Mutasi gagal: produk dan jumlah wajib diisi.';
    }

    if ($type === 'in') {
        $stmt = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        $stmt->bind_param("ii", $qty, $productId);
    } else {
        $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
        $stmt->bind_param("iii", $qty, $productId, $qty);
    }
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $log = $conn->prepare("INSERT INTO stock_movements (product_id, user_id, type, qty, note) VALUES (?, ?, ?, ?, ?)");
        $log->bind_param("iisis", $productId, $adminUserId, $type, $qty, $note);
        $log->execute();
        return 'Mutasi stok berhasil disimpan.';
    }

    return 'Mutasi gagal: stok tidak mencukupi atau produk tidak ditemukan.';
}

/**
 * Ambil 50 riwayat mutasi stok terakhir.
 */
function get_stock_movements(mysqli $conn): mysqli_result|false
{
    return $conn->query("
        SELECT sm.id, sm.type, sm.qty, sm.note, sm.created_at, p.name AS product_name
        FROM stock_movements sm
        INNER JOIN products p ON p.id = sm.product_id
        ORDER BY sm.id DESC
        LIMIT 50
    ");
}

// ═══════════════════════════════════════════════════════════════════════════════
// PELANGGAN
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Cari dan ambil daftar pelanggan (dengan opsional keyword pencarian).
 */
function get_customers(mysqli $conn, string $search = ''): mysqli_result|false
{
    if ($search !== '') {
        $like = '%' . $conn->real_escape_string($search) . '%';
        return $conn->query("
            SELECT id, name, phone, address, created_at FROM customers
            WHERE name LIKE '{$like}' OR phone LIKE '{$like}' OR address LIKE '{$like}'
            ORDER BY id DESC LIMIT 200
        ");
    }
    return $conn->query("SELECT id, name, phone, address, created_at FROM customers ORDER BY id DESC LIMIT 200");
}

/**
 * Tambah pelanggan baru.
 */
function create_customer(mysqli $conn, array $post): string
{
    $name    = trim($post['c_name'] ?? '');
    $phone   = trim($post['c_phone'] ?? '');
    $address = trim($post['c_address'] ?? '');

    if ($name === '') return 'Nama pelanggan tidak boleh kosong.';

    $stmt = $conn->prepare("INSERT INTO customers (name, phone, address) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $phone, $address);
    $stmt->execute();
    return 'Pelanggan berhasil ditambahkan.';
}

/**
 * Hapus pelanggan.
 */
function delete_customer(mysqli $conn, int $id): string
{
    if ($id <= 0) return 'ID pelanggan tidak valid.';

    $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return 'Pelanggan berhasil dihapus.';
}

// ═══════════════════════════════════════════════════════════════════════════════
// KENDARAAN
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Ambil semua kendaraan beserta nama pelanggan.
 */
function get_vehicles(mysqli $conn): mysqli_result|false
{
    return $conn->query("
        SELECT v.id, v.plate, v.brand, v.model, v.km, v.created_at, c.name AS customer_name
        FROM vehicles v
        INNER JOIN customers c ON c.id = v.customer_id
        ORDER BY v.id DESC
        LIMIT 200
    ");
}

/**
 * Tambah kendaraan baru.
 */
function create_vehicle(mysqli $conn, array $post): string
{
    $customerId = (int) ($post['v_customer_id'] ?? 0);
    $plate      = trim($post['v_plate'] ?? '');
    $brand      = trim($post['v_brand'] ?? '');
    $model      = trim($post['v_model'] ?? '');
    $km         = isset($post['v_km']) && $post['v_km'] !== '' ? (int) $post['v_km'] : null;

    if ($customerId <= 0 || $plate === '') return 'Pelanggan dan plat nomor wajib diisi.';

    $stmt = $conn->prepare("INSERT INTO vehicles (customer_id, plate, brand, model, km) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssi", $customerId, $plate, $brand, $model, $km);
    $stmt->execute();
    return 'Kendaraan berhasil ditambahkan.';
}

/**
 * Hapus kendaraan.
 */
function delete_vehicle(mysqli $conn, int $id): string
{
    if ($id <= 0) return 'ID kendaraan tidak valid.';

    $stmt = $conn->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return 'Kendaraan berhasil dihapus.';
}

// ═══════════════════════════════════════════════════════════════════════════════
// EXPORT
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Export daftar pelanggan ke CSV dan langsung output ke browser.
 */
function export_customers_csv(mysqli $conn): void
{
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=customers.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Nama', 'Telepon', 'Alamat', 'Dibuat']);
    $res = $conn->query("SELECT id, name, phone, address, created_at FROM customers ORDER BY id DESC");
    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            fputcsv($output, [$row['id'], $row['name'], $row['phone'], $row['address'], $row['created_at']]);
        }
    }
    fclose($output);
}
