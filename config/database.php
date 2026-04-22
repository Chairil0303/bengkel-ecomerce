<?php
/**
 * config/database.php
 * Koneksi database, auto-migration tabel, dan helper e().
 */

$conn = new mysqli("localhost", "root", "", "bengkel");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
session_start();

// ── Auto-migration: kolom image_url ───────────────────────────────────────────
$checkImageColumn = $conn->query("SHOW COLUMNS FROM products LIKE 'image_url'");
if ($checkImageColumn && $checkImageColumn->num_rows === 0) {
    $conn->query("ALTER TABLE products ADD COLUMN image_url VARCHAR(255) NULL AFTER name");
}

$checkServicePrice = $conn->query("SHOW COLUMNS FROM products LIKE 'service_price'");
if ($checkServicePrice && $checkServicePrice->num_rows === 0) {
    $conn->query("ALTER TABLE products ADD COLUMN service_price INT NOT NULL DEFAULT 0 AFTER price");
}

// Backward-compat: rename kolom Indonesia → English
$checkStock = $conn->query("SHOW COLUMNS FROM products LIKE 'stock'");
if ($checkStock && $checkStock->num_rows === 0) {
    $checkStok = $conn->query("SHOW COLUMNS FROM products LIKE 'stok'");
    if ($checkStok && $checkStok->num_rows > 0) {
        $conn->query("ALTER TABLE products CHANGE COLUMN stok stock INT NOT NULL DEFAULT 0");
    }
}

$checkPrice = $conn->query("SHOW COLUMNS FROM products LIKE 'price'");
if ($checkPrice && $checkPrice->num_rows === 0) {
    $checkHarga = $conn->query("SHOW COLUMNS FROM products LIKE 'harga'");
    if ($checkHarga && $checkHarga->num_rows > 0) {
        $conn->query("ALTER TABLE products CHANGE COLUMN harga price INT NOT NULL DEFAULT 0");
    }
}

// Migrasi data lama: pindah nilai teks di kolom stock ke name jika name kosong
if (!isset($_SESSION['product_data_migrated'])) {
    $_SESSION['product_data_migrated'] = true;
    $conn->query("
        UPDATE products
        SET name = stock
        WHERE (name IS NULL OR name = '')
          AND stock IS NOT NULL
          AND stock NOT REGEXP '^[0-9]+$'
    ");
    $conn->query("
        UPDATE products
        SET stock = 0
        WHERE stock IS NOT NULL
          AND stock NOT REGEXP '^[0-9]+$'
    ");
}

// ── Auto-migration: kolom orders ─────────────────────────────────────────────
$checkPaymentMethod = $conn->query("SHOW COLUMNS FROM orders LIKE 'payment_method'");
if ($checkPaymentMethod && $checkPaymentMethod->num_rows === 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50) NOT NULL DEFAULT 'cash' AFTER status");
}

$checkPaymentRef = $conn->query("SHOW COLUMNS FROM orders LIKE 'payment_reference'");
if ($checkPaymentRef && $checkPaymentRef->num_rows === 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN payment_reference VARCHAR(100) NULL AFTER payment_method");
}

$checkBarcode = $conn->query("SHOW COLUMNS FROM products LIKE 'barcode'");
if ($checkBarcode && $checkBarcode->num_rows === 0) {
    $conn->query("ALTER TABLE products ADD COLUMN barcode VARCHAR(100) NULL AFTER image_url");
}

// ── Auto-create: tabel order_items ───────────────────────────────────────────
$checkOrderItems = $conn->query("SHOW TABLES LIKE 'order_items'");
if ($checkOrderItems && $checkOrderItems->num_rows === 0) {
    $conn->query("
        CREATE TABLE order_items (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_id INT UNSIGNED NOT NULL,
            product_id INT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            qty INT NOT NULL DEFAULT 1,
            price INT NOT NULL DEFAULT 0,
            service_price INT NOT NULL DEFAULT 0,
            subtotal INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_order_id (order_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
}

// ── Auto-create: tabel categories ────────────────────────────────────────────
$checkCategories = $conn->query("SHOW TABLES LIKE 'categories'");
if ($checkCategories && $checkCategories->num_rows === 0) {
    $conn->query("
        CREATE TABLE categories (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
}

$checkCategoryId = $conn->query("SHOW COLUMNS FROM products LIKE 'category_id'");
if ($checkCategoryId && $checkCategoryId->num_rows === 0) {
    $conn->query("ALTER TABLE products ADD COLUMN category_id INT UNSIGNED NULL AFTER name");
}

// ── Auto-create: tabel stock_movements ───────────────────────────────────────
$checkStockMovements = $conn->query("SHOW TABLES LIKE 'stock_movements'");
if ($checkStockMovements && $checkStockMovements->num_rows === 0) {
    $conn->query("
        CREATE TABLE stock_movements (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            product_id INT UNSIGNED NOT NULL,
            user_id INT UNSIGNED NULL,
            type ENUM('in','out') NOT NULL,
            qty INT NOT NULL DEFAULT 0,
            note VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_product_id (product_id),
            INDEX idx_type (type),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
}

// ── Auto-create: tabel customers ─────────────────────────────────────────────
$checkCustomers = $conn->query("SHOW TABLES LIKE 'customers'");
if ($checkCustomers && $checkCustomers->num_rows === 0) {
    $conn->query("
        CREATE TABLE customers (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            phone VARCHAR(30) NULL,
            address VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
}

// ── Auto-create: tabel vehicles ──────────────────────────────────────────────
$checkVehicles = $conn->query("SHOW TABLES LIKE 'vehicles'");
if ($checkVehicles && $checkVehicles->num_rows === 0) {
    $conn->query("
        CREATE TABLE vehicles (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            customer_id INT UNSIGNED NOT NULL,
            plate VARCHAR(20) NOT NULL,
            brand VARCHAR(100) NULL,
            model VARCHAR(100) NULL,
            km INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_customer_id (customer_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
}

// ── Helper ────────────────────────────────────────────────────────────────────

/**
 * Escape output untuk mencegah XSS.
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
