<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    header("Location: ../kasir/index.php");
    exit;
}


// Ambil data produk
$query = mysqli_query(
    $conn,
    "SELECT 
        produk.*,
        kategori.nama_kategori
     FROM produk
     INNER JOIN kategori
        ON produk.kategori_id = kategori.id
     ORDER BY produk.id DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Produk - POS Retail</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

<nav class="navbar">

    <div>
        <strong>POS RETAIL</strong>
    </div>

    <div>

        <?= htmlspecialchars($_SESSION['nama']); ?>

        |

        <a href="../auth/logout.php">
            Logout
        </a>

    </div>

</nav>


<div class="dashboard-layout">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="sidebar-title">
            MENU ADMIN
        </div>

        <a href="../dashboard/index.php">
            Dashboard
        </a>

        <a href="index.php" class="active">
            Produk
        </a>

        <a href="../kategori/index.php">
            Kategori
        </a>

        <a href="../laporan/penjualan.php">
            Transaksi & Laporan
        </a>

        <a href="../kasir/index.php">
            Kasir
        </a>

    </aside>


    <!-- CONTENT -->

    <main class="dashboard-content">

        <div class="page-header">

            <div>

                <h1>Data Produk</h1>

                <p>
                    Kelola produk yang tersedia di toko.
                </p>

            </div>

            <a
                href="tambah.php"
                class="btn-kasir"
            >
                + Tambah Produk
            </a>

        </div>


        <div class="dashboard-panel">

            <div class="table-responsive">

                <table>

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Barcode</th>

                            <th>Kode</th>

                            <th>Nama Produk</th>

                            <th>Kategori</th>

                            <th>Harga Beli</th>

                            <th>Harga Jual</th>

                            <th>Stok</th>

                            <th>Satuan</th>

                            <th>Status</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php

                    $no = 1;

                    if (mysqli_num_rows($query) > 0):

                        while ($produk = mysqli_fetch_assoc($query)):

                    ?>

                        <tr>

                            <td>
                                <?= $no++; ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $produk['barcode']
                                ); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $produk['kode_produk']
                                ); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $produk['nama_produk']
                                ); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $produk['nama_kategori']
                                ); ?>
                            </td>

                            <td>
                                Rp
                                <?= number_format(
                                    $produk['harga_beli'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </td>

                            <td>
                                Rp
                                <?= number_format(
                                    $produk['harga_jual'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </td>

                            <td>

                                <?php if (
                                    $produk['stok']
                                    <=
                                    $produk['stok_minimal']
                                ): ?>

                                    <strong style="color:red">

                                        <?= $produk['stok']; ?>

                                    </strong>

                                <?php else: ?>

                                    <?= $produk['stok']; ?>

                                <?php endif; ?>

                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $produk['satuan']
                                ); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $produk['status']
                                ); ?>
                            </td>

                            <td>

                                <a
                                    href="edit.php?id=<?= $produk['id']; ?>"
                                >
                                    Edit
                                </a>

                                |

                                <a
                                    href="hapus.php?id=<?= $produk['id']; ?>"
                                    onclick="return confirm(
                                        'Yakin ingin menghapus produk ini?'
                                    )"
                                >
                                    Hapus
                                </a>

                            </td>

                        </tr>

                    <?php

                        endwhile;

                    else:

                    ?>

                        <tr>

                            <td
                                colspan="11"
                                style="text-align:center"
                            >

                                Belum ada produk.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>

</body>

</html>