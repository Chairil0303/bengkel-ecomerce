<?php
/**
 * owner.php — Controller tipis (slim controller)
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

require_role('owner');

// ── Handle POST: update status pesanan ───────────────────────────────────────
if (isset($_POST['update_status'])) {
    $id     = (int) ($_POST['id'] ?? 0);
    $status = trim($_POST['status'] ?? 'dibayar');
    if ($id > 0 && in_array($status, ['dibayar', 'diproses', 'selesai'], true)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
    }
}

// ── Filter pesanan ─────────────────────────────────────────────────────────────
$filter      = trim($_GET['status'] ?? '');
$dateFilter  = trim($_GET['date']   ?? '');
$monthFilter = trim($_GET['month']  ?? '');
$yearFilter  = trim($_GET['year']   ?? '');

$allowedFilter = ['', 'dibayar', 'diproses', 'selesai'];
if (!in_array($filter, $allowedFilter, true)) $filter = '';

// Query pesanan
$baseQuery = "SELECT * FROM orders WHERE 1=1";
$types = "";
$params = [];

if ($filter !== '') { $baseQuery .= " AND status = ?"; $types .= "s"; $params[] = $filter; }
if ($dateFilter !== '') { $baseQuery .= " AND DATE(created_at) = ?"; $types .= "s"; $params[] = $dateFilter; }
$baseQuery .= " ORDER BY id DESC";

if ($types === '') {
    $orders = $conn->query($baseQuery);
} else {
    $stmtOrders = $conn->prepare($baseQuery);
    $stmtOrders->bind_param($types, ...$params);
    $stmtOrders->execute();
    $orders = $stmtOrders->get_result();
}

// ── Statistik keseluruhan ─────────────────────────────────────────────────────
$stats = ['total_orders' => 0, 'total_revenue' => 0, 'dibayar' => 0, 'diproses' => 0, 'selesai' => 0];
$statRow = $conn->query("
    SELECT COUNT(*) AS total_orders, COALESCE(SUM(total),0) AS total_revenue,
           SUM(CASE WHEN status='dibayar'  THEN 1 ELSE 0 END) AS dibayar,
           SUM(CASE WHEN status='diproses' THEN 1 ELSE 0 END) AS diproses,
           SUM(CASE WHEN status='selesai'  THEN 1 ELSE 0 END) AS selesai
    FROM orders
")->fetch_assoc();
if ($statRow) $stats = $statRow;

// ── Pendapatan per periode ────────────────────────────────────────────────────
$selectedDate  = $dateFilter  !== '' ? $dateFilter  : date('Y-m-d');
$selectedMonth = $monthFilter !== '' ? $monthFilter : date('Y-m');
$selectedYear  = $yearFilter  !== '' ? $yearFilter  : date('Y');

$makeStats = function (string $whereClause, string $type, string $value) use ($conn): array {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_orders, COALESCE(SUM(total),0) AS total_revenue FROM orders WHERE $whereClause");
    $stmt->bind_param($type, $value);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: ['total_orders' => 0, 'total_revenue' => 0];
};

$dayStats   = $makeStats("DATE(created_at) = ?",              "s", $selectedDate);
$monthStats = $makeStats("DATE_FORMAT(created_at,'%Y-%m') = ?","s", $selectedMonth);
$yearStats  = $makeStats("YEAR(created_at) = ?",               "s", $selectedYear);

// ── Render ────────────────────────────────────────────────────────────────────
$pageTitle = 'Dashboard Owner — Bengkel Motor';

include __DIR__ . '/layouts/header.php';
?>
<body class="bg-gray-100 min-h-screen">
<div class="min-h-screen flex">

    <?php include __DIR__ . '/layouts/sidebar_owner.php'; ?>

    <main class="flex-1 p-6">
        <?php include __DIR__ . '/views/owner/ringkasan.php'; ?>
        <?php include __DIR__ . '/views/owner/pendapatan.php'; ?>
        <?php include __DIR__ . '/views/owner/laporan.php'; ?>
    </main>

</div>
<?php include __DIR__ . '/layouts/footer.php'; ?>
