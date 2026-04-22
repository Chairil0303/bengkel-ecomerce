<?php
require_once __DIR__ . '/config/database.php';
$error = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $isValidPassword = false;
    if ($user) {
        $isValidPassword = password_verify($password, $user['password']) || $password === $user['password'];
    }

    if ($user && $isValidPassword) {
        // Upgrade legacy plain-text passwords after successful login.
        if ($password === $user['password']) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $updateStmt->bind_param("si", $newHash, $user['id']);
            $updateStmt->execute();
        }

        unset($user['password']);
        session_regenerate_id(true);
        $_SESSION['cart'] = $_SESSION['cart'] ?? [];
        $_SESSION['user'] = $user;

        if ($user['role'] == 'admin') {
            header("Location: admin.php");
        } elseif ($user['role'] == 'owner') {
            header("Location: owner.php");
        } else {
            header("Location: user.php");
        }
        exit;
    } else {
        $error = "Email atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Bengkel Motor</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">
<div class="w-full max-w-md bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between mb-2">
        <h1 class="text-2xl font-bold text-gray-800">Masuk</h1>
        <a href="index.php" class="text-sm text-blue-600 hover:underline">Kembali ke Beranda</a>
    </div>
    <p class="text-sm text-gray-500 mb-6">Selamat datang di aplikasi Bengkel Motor.</p>

    <?php if ($error): ?>
        <div class="mb-4 p-3 rounded bg-red-100 text-red-700 text-sm"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-sm text-gray-700 mb-1">Email</label>
            <input name="email" type="email" required class="w-full border rounded px-3 py-2" placeholder="nama@email.com">
        </div>
        <div>
            <label class="block text-sm text-gray-700 mb-1">Password</label>
            <input type="password" name="password" required class="w-full border rounded px-3 py-2" placeholder="********">
        </div>
        <button name="login" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Login</button>
    </form>

    <p class="text-sm text-gray-600 mt-4 text-center">
        Belum punya akun?
        <a href="register.php" class="text-blue-600 hover:underline">Daftar</a>
    </p>
</div>
</body>
</html>
