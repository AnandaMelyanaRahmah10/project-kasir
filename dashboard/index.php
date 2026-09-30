<?php
session_start();
require_once "../config/database.php";

// ==========================================
// CEK LOGIN & ROLE ADMIN
// ==========================================
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    header("Location: ../kasir/index.php");
    exit;
}

// ==========================================
// DATA METRIK UTAMA
// ==========================================
// Total Produk
$query_produk = mysqli_query($conn, "SELECT COUNT(*) AS total FROM produk WHERE status = 'aktif'");
$total_produk = mysqli_fetch_assoc($query_produk)['total'] ?? 0;

// Total Stok
$query_stok = mysqli_query($conn, "SELECT SUM(stok) AS total FROM produk WHERE status = 'aktif'");
$total_stok = mysqli_fetch_assoc($query_stok)['total'] ?? 0;

// Transaksi Hari Ini
$query_transaksi = mysqli_query($conn, "SELECT COUNT(*) AS total FROM penjualan WHERE DATE(tanggal) = CURDATE() AND status = 'selesai'");
$total_transaksi = mysqli_fetch_assoc($query_transaksi)['total'] ?? 0;

// Omzet Hari Ini
$query_omzet = mysqli_query($conn, "SELECT SUM(total) AS total FROM penjualan WHERE DATE(tanggal) = CURDATE() AND status = 'selesai'");
$total_omzet = mysqli_fetch_assoc($query_omzet)['total'] ?? 0;

// ==========================================
// TABEL DATA
// ==========================================
// Stok Menipis
$query_stok_menipis = mysqli_query($conn, "
    SELECT * FROM produk 
    WHERE stok <= stok_minimal AND status = 'aktif' 
    ORDER BY stok ASC LIMIT 5
");

// Transaksi Terbaru (Mengambil 5 Transaksi Terakhir, Tanpa Batas Hari Ini)
$query_transaksi_terbaru = mysqli_query($conn, "
    SELECT p.*, u.nama AS nama_kasir 
    FROM penjualan p 
    LEFT JOIN users u ON p.user_id = u.id 
    WHERE p.status = 'selesai'
    ORDER BY p.tanggal DESC, p.id DESC 
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - POS Retail</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full flex flex-col antialiased text-slate-800">

    <!-- ==========================================
         TOP NAVBAR
    ========================================== -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand -->
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-600 text-white p-2 rounded-xl flex items-center justify-center font-bold text-lg shadow-indigo-200 shadow-md">
                        <i class="fa-solid fa-store me-0.5"></i>
                    </div>
                    <div>
                        <span class="font-bold text-lg tracking-tight text-slate-900 block leading-none">POS RETAIL</span>
                        <span class="text-xs text-slate-500 font-medium">Administrator Panel</span>
                    </div>
                </div>

                <!-- User Profile & Action -->
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex items-center gap-3 pr-4 border-r border-slate-200">
                        <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-semibold text-sm">
                            <?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)); ?>
                        </div>
                        <div class="text-left">
                            <span class="block text-sm font-semibold text-slate-900 leading-tight">
                                <?= htmlspecialchars($_SESSION['nama'] ?? 'Admin'); ?>
                            </span>
                            <span class="inline-block px-2 py-0.5 text-[10px] font-semibold bg-emerald-100 text-emerald-800 rounded-full">
                                Administrator
                            </span>
                        </div>
                    </div>

                    <a href="../auth/logout.php" 
                       onclick="return confirm('Apakah Anda yakin ingin keluar?')"
                       class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="hidden sm:inline">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- ==========================================
                 SIDEBAR NAVIGATION
            ========================================== -->
            <aside class="lg:col-span-3 space-y-1">
                <div class="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
                    <p class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Menu Utama</p>

                    <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium bg-indigo-50 text-indigo-700">
                        <i class="fa-solid fa-chart-pie w-5 text-center text-indigo-600"></i>
                        Dashboard
                    </a>

                    <a href="../kasir/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                        <i class="fa-solid fa-cash-register w-5 text-center text-slate-400"></i>
                        Kasir (POS)
                    </a>

                    <a href="../produk/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                        <i class="fa-solid fa-boxes-stacked w-5 text-center text-slate-400"></i>
                        Kelola Produk
                    </a>

                    <a href="../kategori/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                        <i class="fa-solid fa-tags w-5 text-center text-slate-400"></i>
                        Kategori Produk
                    </a>

                    <a href="../transaksi/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                        <i class="fa-solid fa-receipt w-5 text-center text-slate-400"></i>
                        Riwayat Transaksi
                    </a>

                    <a href="../laporan/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                        <i class="fa-solid fa-chart-line w-5 text-center text-slate-400"></i>
                        Laporan Penjualan
                    </a>
                </div>

                <!-- Banner Informasi Status -->
                <div class="bg-gradient-to-br from-indigo-900 to-slate-900 text-white p-4 rounded-2xl shadow-sm mt-4">
                    <div class="flex items-center gap-2 text-indigo-300 text-xs font-semibold mb-1">
                        <i class="fa-solid fa-shield-halved"></i>
                        Sistem Online
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">Database terhubung secara realtime.</p>
                </div>
            </aside>

            <!-- ==========================================
                 MAIN CONTENT AREA
            ========================================== -->
            <main class="lg:col-span-9 space-y-6">

                <!-- Page Header & Quick CTA -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Dashboard Admin</h1>
                        <p class="text-sm text-slate-500 mt-1">Selamat datang kembali di sistem manajemen POS Retail.</p>
                    </div>
                    <a href="../kasir/index.php" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-xl shadow-lg shadow-indigo-100 transition-all">
                        <i class="fa-solid fa-circle-plus"></i>
                        Transaksi Baru
                    </a>
                </div>

                <!-- ==========================================
                     CARDS STATISTIK
                ========================================== -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Card 1: Total Produk -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Produk</span>
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <i class="fa-solid fa-box text-lg"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900"><?= number_format($total_produk, 0, ',', '.'); ?></h3>
                        <span class="text-xs text-slate-500 mt-1 block">Item aktif di katalog</span>
                    </div>

                    <!-- Card 2: Total Stok -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Stok</span>
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <i class="fa-solid fa-cubes text-lg"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900"><?= number_format($total_stok, 0, ',', '.'); ?></h3>
                        <span class="text-xs text-slate-500 mt-1 block">Unit siap jual</span>
                    </div>

                    <!-- Card 3: Transaksi Hari Ini -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Transaksi (Hari Ini)</span>
                            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                                <i class="fa-solid fa-cart-shopping text-lg"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900"><?= number_format($total_transaksi, 0, ',', '.'); ?></h3>
                        <span class="text-xs text-emerald-600 font-medium mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-circle text-[8px]"></i> Berhasil diproses
                        </span>
                    </div>

                    <!-- Card 4: Omzet Hari Ini -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Omzet (Hari Ini)</span>
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i class="fa-solid fa-wallet text-lg"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900">Rp <?= number_format($total_omzet, 0, ',', '.'); ?></h3>
                        <span class="text-xs text-slate-500 mt-1 block">Pendapatan kotor hari ini</span>
                    </div>

                </div>

                <!-- ==========================================
                     PANEL DUA KOLOM (STOK MENIPIS & MENU CEPAT)
                ========================================== -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- TABEL: Stok Menipis -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
                        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                <h2 class="font-bold text-slate-900">Stok Menipis</h2>
                            </div>
                            <a href="../produk/index.php" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                Lihat Semua &rarr;
                            </a>
                        </div>

                        <div class="overflow-x-auto flex-1">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/60 text-slate-400 text-[11px] uppercase font-semibold">
                                        <th class="py-3 px-5">Produk</th>
                                        <th class="py-3 px-5">Barcode</th>
                                        <th class="py-3 px-5 text-right">Sisa Stok</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    <?php if (mysqli_num_rows($query_stok_menipis) > 0): ?>
                                        <?php while ($produk = mysqli_fetch_assoc($query_stok_menipis)): ?>
                                            <tr class="hover:bg-slate-50/50 transition-colors">
                                                <td class="py-3.5 px-5 font-medium text-slate-800">
                                                    <?= htmlspecialchars($produk['nama_produk']); ?>
                                                </td>
                                                <td class="py-3.5 px-5 font-mono text-xs text-slate-500">
                                                    <?= htmlspecialchars($produk['barcode'] ?? '-'); ?>
                                                </td>
                                                <td class="py-3.5 px-5 text-right">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700">
                                                        <?= $produk['stok']; ?> pcs
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="py-8 text-center text-slate-400 text-sm">
                                                <i class="fa-solid fa-circle-check text-emerald-500 text-xl mb-1 block"></i>
                                                Semua stok produk masih aman 👍
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- GRID: Menu Cepat -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 flex flex-col justify-between">
                        <div>
                            <h2 class="font-bold text-slate-900 mb-4">Menu Cepat</h2>
                            <div class="grid grid-cols-2 gap-3">
                                
                                <a href="../kasir/index.php" class="p-4 rounded-xl border border-slate-200/70 hover:border-indigo-300 hover:bg-indigo-50/30 transition-all group">
                                    <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center mb-3 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </div>
                                    <strong class="block text-slate-900 text-sm font-semibold">Kasir</strong>
                                    <span class="text-xs text-slate-500 mt-0.5 block">Mulai transaksi</span>
                                </a>

                                <a href="../produk/tambah.php" class="p-4 rounded-xl border border-slate-200/70 hover:border-indigo-300 hover:bg-indigo-50/30 transition-all group">
                                    <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                        <i class="fa-solid fa-box font-bold"></i>
                                    </div>
                                    <strong class="block text-slate-900 text-sm font-semibold">Tambah Produk</strong>
                                    <span class="text-xs text-slate-500 mt-0.5 block">Produk baru</span>
                                </a>

                                <a href="../kategori/tambah.php" class="p-4 rounded-xl border border-slate-200/70 hover:border-indigo-300 hover:bg-indigo-50/30 transition-all group">
                                    <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center mb-3 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                                        <i class="fa-solid fa-tags"></i>
                                    </div>
                                    <strong class="block text-slate-900 text-sm font-semibold">Tambah Kategori</strong>
                                    <span class="text-xs text-slate-500 mt-0.5 block">Kategori baru</span>
                                </a>

                                <a href="../laporan/penjualan.php" class="p-4 rounded-xl border border-slate-200/70 hover:border-indigo-300 hover:bg-indigo-50/30 transition-all group">
                                    <div class="w-9 h-9 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center mb-3 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                                        <i class="fa-solid fa-chart-line"></i>
                                    </div>
                                    <strong class="block text-slate-900 text-sm font-semibold">Laporan</strong>
                                    <span class="text-xs text-slate-500 mt-0.5 block">Penjualan</span>
                                </a>

                            </div>
                        </div>
                    </div>

                </div>

                <!-- ==========================================
                     TABEL TRANSAKSI TERBARU (Sistem Real-Time)
                ========================================== -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-indigo-600"></i>
                            <h2 class="font-bold text-slate-900">Transaksi Terbaru</h2>
                        </div>
                        <a href="../transaksi/index.php" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/60 text-slate-400 text-[11px] uppercase font-semibold">
                                    <th class="py-3 px-5">No. Nota / ID</th>
                                    <th class="py-3 px-5">Tanggal & Waktu</th>
                                    <th class="py-3 px-5">Kasir</th>
                                    <th class="py-3 px-5 text-right">Total Bayar</th>
                                    <th class="py-3 px-5 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                <?php if ($query_transaksi && mysqli_num_rows($query_transaksi) > 0): ?>
                                    <?php while ($trx = mysqli_fetch_assoc($query_transaksi)): ?>
                                        <?php 
                                            // Fallback aman untuk berbagai format penamaan kolom nota di database
                                            $no_nota = $trx['nomor_nota'] ?? $trx['no_nota'] ?? $trx['no_faktur'] ?? ('TRX-' . str_pad($trx['id'], 5, '0', STR_PAD_LEFT));
                                            $waktu   = date('d/m/Y H:i', strtotime($trx['tanggal']));
                                        ?>
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="py-3.5 px-5 font-mono text-xs font-bold text-indigo-600">
                                                #<?= htmlspecialchars($no_nota); ?>
                                            </td>
                                            <td class="py-3.5 px-5 text-xs text-slate-600 font-medium">
                                                <?= $waktu; ?> WIB
                                            </td>
                                            <td class="py-3.5 px-5 text-slate-700 font-medium">
                                                <?= htmlspecialchars($trx['nama_kasir'] ?? 'Kasir'); ?>
                                            </td>
                                            <td class="py-3.5 px-5 text-right font-bold text-slate-900">
                                                Rp <?= number_format($trx['total'], 0, ',', '.'); ?>
                                            </td>
                                            <td class="py-3.5 px-5 text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                    Selesai
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400 text-sm">
                                            <i class="fa-solid fa-inbox text-2xl mb-2 block text-slate-300"></i>
                                            Belum ada riwayat transaksi di dalam database.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>

</body>
</html>