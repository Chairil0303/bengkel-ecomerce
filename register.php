<?php
require_once __DIR__ . '/config/database.php';
$error = "";

if (isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";

    if ($name === "" || $email === "" || $password === "") {
        $error = "Semua field wajib diisi.";
    } else {
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $exists = $checkStmt->get_result()->fetch_assoc();

        if ($exists) {
            $error = "Email sudah terdaftar.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name,email,password,role) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $name, $email, $hash);
            $stmt->execute();
            header("Location: login.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar - Bengkel Motor</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">
<div class="w-full max-w-md bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Buat Akun</h1>
    <p class="text-sm text-gray-500 mb-6">Daftar untuk mulai transaksi servis dan sparepart.</p>

    <?php if ($error): ?>
        <div class="mb-4 p-3 rounded bg-red-100 text-red-700 text-sm"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-sm text-gray-700 mb-1">Nama</label>
            <input name="name" required class="w-full border rounded px-3 py-2" placeholder="Nama lengkap">
        </div>
        <div>
            <label class="block text-sm text-gray-700 mb-1">Email</label>
            <input type="email" name="email" required class="w-full border rounded px-3 py-2" placeholder="nama@email.com">
        </div>
        <div>
            <label class="block text-sm text-gray-700 mb-1">Password</label>
            <input type="password" name="password" required class="w-full border rounded px-3 py-2" placeholder="Minimal 6 karakter">
        </div>
        <button name="register" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Daftar</button>
    </form>

    <p class="text-sm text-gray-600 mt-4 text-center">
        Sudah punya akun?
        <a href="login.php" class="text-blue-600 hover:underline">Login</a>
    </p>
</div>
</body>
</html>