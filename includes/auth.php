<?php
/**
 * includes/auth.php
 * Helper autentikasi — gunakan require_role() di setiap halaman terproteksi.
 */

/**
 * Pastikan user sudah login dan memiliki role yang diminta.
 * Jika tidak, redirect ke halaman login dan hentikan eksekusi.
 *
 * @param string $role  'admin' | 'owner' | 'user'
 */
function require_role(string $role): void
{
    $user = $_SESSION['user'] ?? null;
    if (!$user || ($user['role'] ?? '') !== $role) {
        header('Location: ../login.php');
        exit;
    }
}

/**
 * Pastikan user sudah login (role apapun).
 */
function require_auth(): void
{
    if (!isset($_SESSION['user'])) {
        header('Location: ../login.php');
        exit;
    }
}

/**
 * Ambil data user yang sedang login.
 */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}
