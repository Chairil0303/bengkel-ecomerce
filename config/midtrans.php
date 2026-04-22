<?php
/**
 * config/midtrans.php
 * Konfigurasi Midtrans Snap — Bengkel Motor
 *
 * Cara pakai:
 *   require_once __DIR__ . '/../config/midtrans.php';
 *
 * PENTING: Ganti nilai MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY
 *          dengan credentials dari Midtrans Dashboard → Settings → Access Keys.
 *          Jangan pernah commit file ini ke public repository jika sudah berisi key asli.
 */

// ── Kredensial (ISI SETELAH DAPAT KEY DARI MIDTRANS DASHBOARD) ────────────────
define('MIDTRANS_SERVER_KEY', 'SB-Mid-server-GANTI_DENGAN_SERVER_KEY_KAMU');
define('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-GANTI_DENGAN_CLIENT_KEY_KAMU');

// ── Mode ──────────────────────────────────────────────────────────────────────
// true  = Sandbox (untuk testing/development)
// false = Production (untuk live, butuh akun terverifikasi)
define('MIDTRANS_IS_SANDBOX', true);

// ── URL Endpoint Midtrans ─────────────────────────────────────────────────────
// Ditentukan otomatis berdasarkan mode Sandbox/Production
define('MIDTRANS_SNAP_URL',
    MIDTRANS_IS_SANDBOX
        ? 'https://app.sandbox.midtrans.com/snap/snap.js'
        : 'https://app.midtrans.com/snap/snap.js'
);

define('MIDTRANS_API_URL',
    MIDTRANS_IS_SANDBOX
        ? 'https://app.sandbox.midtrans.com/snap/v1/transactions'
        : 'https://app.midtrans.com/snap/v1/transactions'
);

// ── Pengaturan Order ──────────────────────────────────────────────────────────
// Berapa menit order pending akan expired jika tidak dibayar
define('MIDTRANS_ORDER_EXPIRY_MINUTES', 60);
