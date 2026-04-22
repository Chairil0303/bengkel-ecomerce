<?php
require_once __DIR__ . '/config/database.php';

$user = $_SESSION['user'] ?? null;
if (!$user || ($user['role'] ?? '') !== 'admin') {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ok' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

$barcode = trim($_GET['barcode'] ?? '');
header('Content-Type: application/json; charset=utf-8');

if ($barcode === '') {
    echo json_encode([
        'ok' => false,
        'message' => 'Barcode wajib diisi'
    ]);
    exit;
}

$stmt = $conn->prepare("SELECT id, name, stock FROM products WHERE barcode = ? LIMIT 1");
$stmt->bind_param("s", $barcode);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo json_encode([
        'ok' => false,
        'message' => 'Produk tidak ditemukan untuk barcode: ' . $barcode
    ]);
    exit;
}

echo json_encode([
    'ok' => true,
    'product_id' => (int) $product['id'],
    'name' => $product['name'],
    'stock' => (int) ($product['stock'] ?? 0),
]);
exit;

