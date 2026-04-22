<?php
/**
 * admin.php — Controller tipis (slim controller)
 * Tugas: bootstrap → handle POST → prepare data → render layout + view
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_role('admin');

$user       = $_SESSION['user'];
$message    = '';
$catMessage = '';

// ── Handle POST actions ────────────────────────────────────────────────────────
if (isset($_POST['add']))              { $message    = create_product($conn, $_POST); }
if (isset($_POST['update']))           { $message    = update_product($conn, (int) ($_POST['id'] ?? 0), $_POST); }
if (isset($_POST['delete']))           { $message    = delete_product($conn, (int) ($_POST['id'] ?? 0)); }
if (isset($_POST['add_category']))     { $catMessage = create_category($conn, $_POST['category_name'] ?? ''); }
if (isset($_POST['delete_category'])) { $catMessage = delete_category($conn, (int) ($_POST['category_id'] ?? 0)); }
if (isset($_POST['add_customer']))    { $message = create_customer($conn, $_POST); }
if (isset($_POST['delete_customer'])) { $message = delete_customer($conn, (int) ($_POST['customer_id'] ?? 0)); }
if (isset($_POST['add_vehicle']))     { $message = create_vehicle($conn, $_POST); }
if (isset($_POST['delete_vehicle']))  { $message = delete_vehicle($conn, (int) ($_POST['vehicle_id'] ?? 0)); }
if (isset($_POST['submit_mutasi']))   { $message = submit_stock_mutation($conn, $_POST, (int) ($user['id'] ?? 0)); }
if (isset($_POST['admin_update_status'])) {
    $message = admin_update_order_status($conn, (int) ($_POST['id'] ?? 0), trim($_POST['status'] ?? ''));
}

// Export CSV — lakukan sebelum output HTML
if (isset($_GET['export_customers']) && $_GET['export_customers'] === '1') {
    require_role('admin'); // double-check
    export_customers_csv($conn);
    exit;
}

// ── Tentukan tab aktif ─────────────────────────────────────────────────────────
$editId      = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editProduct = $editId > 0 ? get_product_by_id($conn, $editId) : null;

$allowedTabs = ['ringkasan', 'pendapatan', 'produk', 'pesanan', 'stok', 'mutasi', 'pelanggan'];
$tab = trim($_GET['tab'] ?? '');
if (!in_array($tab, $allowedTabs, true)) {
    $tab = $editProduct ? 'produk' : 'ringkasan';
}

// ── Prepare data (sesuai tab) ─────────────────────────────────────────────────

// Data yang selalu dibutuhkan (untuk beberapa tab)
$categories = get_all_categories($conn);
$products   = get_all_products($conn);

// Data statistik global (dipakai di ringkasan & pesanan)
$dashStats  = get_dashboard_stats($conn);
extract($dashStats); // $statProducts, $statStock, $statOrders, $countCustomers, ...

// Statistik pesanan (juga untuk sidebar summary)
$statOrders = $conn->query("
    SELECT
        COUNT(*) AS total_orders,
        COALESCE(SUM(total),0) AS total_revenue,
        SUM(CASE WHEN status='dibayar'  THEN 1 ELSE 0 END) AS dibayar,
        SUM(CASE WHEN status='diproses' THEN 1 ELSE 0 END) AS diproses,
        SUM(CASE WHEN status='selesai'  THEN 1 ELSE 0 END) AS selesai
    FROM orders
")->fetch_assoc();

// Data per-tab
if ($tab === 'pesanan') {
    $ordersList = get_recent_orders($conn);
}

if ($tab === 'pendapatan') {
    $pendapatanDate  = trim($_GET['rev_date']  ?? '') ?: date('Y-m-d');
    $pendapatanMonth = trim($_GET['rev_month'] ?? '') ?: date('Y-m');
    $pendapatanYear  = trim($_GET['rev_year']  ?? '') ?: date('Y');
    $revStats = get_revenue_stats($conn, $pendapatanDate, $pendapatanMonth, $pendapatanYear);
    extract($revStats); // $revDay, $revMonth, $revYear
}

if ($tab === 'pelanggan') {
    $searchCustomer = trim($_GET['c_q'] ?? '');
    $customersList  = get_customers($conn, $searchCustomer);
    $vehiclesList   = get_vehicles($conn);
}

$lowStockThreshold = 5;

// ── Render layout ─────────────────────────────────────────────────────────────
$pageTitle = 'Admin Panel — Bengkel Motor';

include __DIR__ . '/layouts/header.php';
?>
<body class="bg-gray-100 min-h-screen">
<div class="min-h-screen flex">

    <?php include __DIR__ . '/layouts/sidebar_admin.php'; ?>

    <main class="flex-1 p-6">
        <?php include __DIR__ . "/views/admin/{$tab}.php"; ?>
    </main>

</div>
<?php include __DIR__ . '/layouts/footer.php'; ?>