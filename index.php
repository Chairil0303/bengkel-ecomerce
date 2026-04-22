<!DOCTYPE html>
<html>
<head>
    <title>Bengkel Motor pamulang</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
<header class="bg-white/80 backdrop-blur sticky top-0 z-10 border-b">
	<div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
		<div class="flex items-center gap-2">
			<div class="w-8 h-8 rounded-full bg-blue-600"></div>
			<span class="font-semibold text-gray-800">Bengkel Motor Pamulang</span>
		</div>
		<nav class="hidden md:flex items-center gap-6 text-sm text-gray-700">
			<a href="#fitur" class="hover:text-blue-600">Fitur</a>
			<a href="#layanan" class="hover:text-blue-600">Layanan</a>
			<a href="#testimoni" class="hover:text-blue-600">Testimoni</a>
		</nav>
		<div class="flex items-center gap-3">
			<a href="login.php" class="text-sm text-gray-700 hover:text-blue-600">Login</a>
			<a href="register.php" class="text-sm bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Daftar</a>
		</div>
	</div>
</header>

<main class="flex-1">
	<!-- Hero -->
	<section class="relative overflow-hidden">
		<div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-blue-100"></div>
		<div class="relative max-w-6xl mx-auto grid md:grid-cols-2 gap-8 px-6 py-16 md:py-24 items-center">
			<div>
				<h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight">
					Servis Motor Lebih Mudah, Cepat, dan Transparan
				</h1>
				<p class="mt-4 text-gray-600">
					Kelola servis, sparepart, dan pesanan dalam satu aplikasi. Pantau stok, transaksi, dan laporan tanpa ribet.
				</p>
				<div class="mt-8 flex flex-wrap gap-3">
					<a href="register.php" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700">Coba Gratis</a>
					<a href="login.php" class="px-6 py-3 rounded border border-gray-300 text-gray-800 hover:bg-gray-100">Masuk</a>
				</div>
				<div class="mt-6 flex items-center gap-6 text-sm text-gray-600">
					<div class="flex items-center gap-2">
						<span class="inline-block w-2.5 h-2.5 rounded-full bg-green-500"></span>
						<span>Real-time laporan</span>
					</div>
					<div class="flex items-center gap-2">
						<span class="inline-block w-2.5 h-2.5 rounded-full bg-green-500"></span>
						<span>Manajemen stok rapi</span>
					</div>
				</div>
			</div>
			<div class="md:pl-8">
				<div class="rounded-xl overflow-hidden shadow-lg ring-1 ring-black/5 bg-white">
					<img src="images/bengkel.jpg" alt="Servis motor" class="w-full h-72 md:h-96 object-cover">
				</div>
			</div>
		</div>
	</section>

	<!-- Fitur -->
	<section id="fitur" class="max-w-6xl mx-auto px-6 py-16">
		<h2 class="text-2xl md:text-3xl font-bold text-gray-900 text-center">Semua yang Dibutuhkan Bengkel</h2>
		<p class="text-gray-600 text-center mt-2">Terintegrasi, sederhana, dan siap dipakai.</p>
		<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-10">
			<div class="bg-white rounded-xl p-5 shadow-sm border">
				<div class="w-10 h-10 rounded bg-blue-100 text-blue-700 flex items-center justify-center font-bold">P</div>
				<h3 class="mt-4 font-semibold text-gray-900">Produk & Stok</h3>
				<p class="text-sm text-gray-600 mt-1">Kelola sparepart dengan mudah, pantau stok secara real-time.</p>
			</div>
			<div class="bg-white rounded-xl p-5 shadow-sm border">
				<div class="w-10 h-10 rounded bg-blue-100 text-blue-700 flex items-center justify-center font-bold">O</div>
				<h3 class="mt-4 font-semibold text-gray-900">Pesanan Cepat</h3>
				<p class="text-sm text-gray-600 mt-1">Proses transaksi tanpa ribet, histori tercatat rapi.</p>
			</div>
			<div class="bg-white rounded-xl p-5 shadow-sm border">
				<div class="w-10 h-10 rounded bg-blue-100 text-blue-700 flex items-center justify-center font-bold">R</div>
				<h3 class="mt-4 font-semibold text-gray-900">Role & Akses</h3>
				<p class="text-sm text-gray-600 mt-1">Akses terpisah untuk Admin, Owner, dan User.</p>
			</div>
			<div class="bg-white rounded-xl p-5 shadow-sm border">
				<div class="w-10 h-10 rounded bg-blue-100 text-blue-700 flex items-center justify-center font-bold">L</div>
				<h3 class="mt-4 font-semibold text-gray-900">Laporan</h3>
				<p class="text-sm text-gray-600 mt-1">Ringkasan order dan pendapatan yang mudah dipahami.</p>
			</div>
		</div>
	</section>

	<!-- Layanan -->
	<section id="layanan" class="bg-white border-t">
		<div class="max-w-6xl mx-auto px-6 py-16">
			<div class="grid md:grid-cols-3 gap-8">
				<div>
					<h2 class="text-2xl font-bold text-gray-900">Layanan Unggulan</h2>
					<p class="text-gray-600 mt-2">Kami membantu bengkel berjalan lebih efisien dengan tools yang sederhana.</p>
				</div>
				<ul class="md:col-span-2 grid sm:grid-cols-2 gap-6">
					<li class="flex items-start gap-3">
						<span class="mt-1 inline-block w-2.5 h-2.5 rounded-full bg-blue-600"></span>
						<div>
							<p class="font-medium text-gray-900">Servis berkala</p>
							<p class="text-sm text-gray-600">Jadwalkan dan catat servis motor pelanggan.</p>
						</div>
					</li>
					<li class="flex items-start gap-3">
						<span class="mt-1 inline-block w-2.5 h-2.5 rounded-full bg-blue-600"></span>
						<div>
							<p class="font-medium text-gray-900">Sparepart terkurasi</p>
							<p class="text-sm text-gray-600">Data produk jelas: nama, harga, stok, dan gambar.</p>
						</div>
					</li>
					<li class="flex items-start gap-3">
						<span class="mt-1 inline-block w-2.5 h-2.5 rounded-full bg-blue-600"></span>
						<div>
							<p class="font-medium text-gray-900">Notifikasi internal</p>
							<p class="text-sm text-gray-600">Pantau status order dari dibayar hingga selesai.</p>
						</div>
					</li>
					<li class="flex items-start gap-3">
						<span class="mt-1 inline-block w-2.5 h-2.5 rounded-full bg-blue-600"></span>
						<div>
							<p class="font-medium text-gray-900">Aman & ringan</p>
							<p class="text-sm text-gray-600">Teknologi ringan, aman, dan mudah dipasang.</p>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</section>

	<!-- Testimoni -->
	<section id="testimoni" class="max-w-6xl mx-auto px-6 py-16">
		<h2 class="text-2xl md:text-3xl font-bold text-gray-900 text-center">Dipercaya Bengkel Lokal</h2>
		<p class="text-gray-600 text-center mt-2">Cerita singkat dari pengguna kami.</p>
		<div class="grid md:grid-cols-3 gap-6 mt-10">
			<figure class="bg-white rounded-xl p-5 shadow-sm border">
				<blockquote class="text-gray-700">“Stok sparepart sekarang rapi, pencatatan order jadi cepat.”</blockquote>
				<figcaption class="mt-4 text-sm text-gray-500">— Andi, Pemilik Bengkel</figcaption>
			</figure>
			<figure class="bg-white rounded-xl p-5 shadow-sm border">
				<blockquote class="text-gray-700">“Tampilan bersih, karyawan gampang belajar pakainya.”</blockquote>
				<figcaption class="mt-4 text-sm text-gray-500">— Rina, Admin Toko</figcaption>
			</figure>
			<figure class="bg-white rounded-xl p-5 shadow-sm border">
				<blockquote class="text-gray-700">“Laporan penjualan jelas. Ambil keputusan lebih cepat.”</blockquote>
				<figcaption class="mt-4 text-sm text-gray-500">— Budi, Owner</figcaption>
			</figure>
		</div>
	</section>

	<!-- CTA -->
	<section class="bg-blue-600">
		<div class="max-w-6xl mx-auto px-6 py-14 text-center text-white">
			<h2 class="text-2xl md:text-3xl font-bold">Siap membuat bengkel lebih efisien?</h2>
			<p class="text-blue-100 mt-2">Daftar gratis, atur produk, dan mulai terima pesanan hari ini.</p>
			<div class="mt-6">
				<a href="register.php" class="inline-block bg-white text-blue-700 font-medium px-6 py-3 rounded hover:bg-blue-50">Buat Akun Sekarang</a>
			</div>
		</div>
	</section>
</main>

<footer class="bg-white border-t">
	<div class="max-w-6xl mx-auto px-6 py-8 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-gray-600">
		<p>&copy; <?= date('Y') ?> Bengkel Motor Pamulang. All rights reserved.</p>
		<div class="flex items-center gap-4">
			<a href="login.php" class="hover:text-blue-600">Login</a>
			<a href="register.php" class="hover:text-blue-600">Daftar</a>
		</div>
	</div>
</footer>
</body>
</html>
