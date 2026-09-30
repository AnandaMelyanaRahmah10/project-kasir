<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}


// ==========================================
// BUAT KERANJANG SESSION
// ==========================================

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Kasir - POS Retail</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
        }

        .kasir-wrapper {
            padding: 25px;
        }

        .kasir-header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .kasir-header h1 {
            margin: 0;
        }

        .kasir-header p {
            color: #777;
        }

        .kasir-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 20px;
        }

        .kasir-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
        }

        .barcode-form {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .barcode-form input {
            flex: 1;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }

        .btn {
            border: none;
            padding: 12px 18px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-success {
            background: #16a34a;
            color: white;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f8fafc;
        }

        .qty-form {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .qty-input {
            width: 50px;
            padding: 7px;
            text-align: center;
        }

        .total-box {
            background: #111827;
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .total-label {
            font-size: 14px;
        }

        .total-harga {
            font-size: 32px;
            font-weight: bold;
            margin-top: 10px;
        }

        .bayar-input {
            width: 100%;
            box-sizing: border-box;
            padding: 14px;
            font-size: 18px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-top: 8px;
            margin-bottom: 15px;
        }

        .kembalian-box {
            padding: 15px;
            background: #f3f4f6;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .kembalian {
            font-size: 24px;
            font-weight: bold;
            margin-top: 5px;
        }

        .btn-bayar {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 7px;
            background: #16a34a;
            color: white;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-batal {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border: none;
            border-radius: 7px;
            background: #dc2626;
            color: white;
            cursor: pointer;
        }

        .hasil-produk {
            margin-bottom: 20px;
        }

        .produk-item {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 7px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #777;
        }

        @media(max-width: 900px) {

            .kasir-grid {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>


<div class="kasir-wrapper">


    <!-- HEADER -->

    <div class="kasir-header">

        <h1>Kasir POS Retail</h1>

        <p>
            Kasir:
            <strong>
                <?= htmlspecialchars($_SESSION['nama'] ?? 'Kasir'); ?>
            </strong>
        </p>

        <a href="../auth/logout.php">
            Logout
        </a>

    </div>



    <div class="kasir-grid">


        <!-- ======================================
             BAGIAN KIRI
        ======================================= -->

        <div class="kasir-card">

            <h2>Transaksi Penjualan</h2>


            <!-- BARCODE -->

            <form
                action="tambah_keranjang.php"
                method="POST"
                class="barcode-form"
            >

                <input
                    type="text"
                    name="barcode"
                    id="barcode"
                    placeholder="Scan barcode produk..."
                    autofocus
                    required
                >

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Scan
                </button>

            </form>



            <!-- CARI PRODUK -->

            <form
                action="cari_produk.php"
                method="GET"
                class="barcode-form"
            >

                <input
                    type="text"
                    name="keyword"
                    placeholder="Cari nama produk..."
                    value="<?= htmlspecialchars($_GET['keyword'] ?? ''); ?>"
                >

                <button
                    type="submit"
                    class="btn btn-secondary"
                >
                    Cari
                </button>

            </form>



            <!-- HASIL PENCARIAN -->

            <?php

            if (isset($_GET['keyword'])):

                $keyword = mysqli_real_escape_string(
                    $conn,
                    $_GET['keyword']
                );

                $hasil = mysqli_query(
                    $conn,
                    "SELECT *
                     FROM produk
                     WHERE nama_produk LIKE '%$keyword%'
                     AND status = 'aktif'
                     ORDER BY nama_produk ASC"
                );

            ?>

                <div class="hasil-produk">

                    <h3>Hasil Pencarian</h3>

                    <?php if (mysqli_num_rows($hasil) > 0): ?>

                        <?php while ($produk = mysqli_fetch_assoc($hasil)): ?>

                            <div class="produk-item">

                                <div>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $produk['nama_produk']
                                        ); ?>
                                    </strong>

                                    <br>

                                    <small>
                                        Barcode:
                                        <?= htmlspecialchars(
                                            $produk['barcode']
                                        ); ?>
                                    </small>

                                    <br>

                                    Rp
                                    <?= number_format(
                                        $produk['harga_jual'],
                                        0,
                                        ',',
                                        '.'
                                    ); ?>

                                    <br>

                                    Stok:
                                    <?= $produk['stok']; ?>

                                </div>


                                <form
                                    action="tambah_keranjang.php"
                                    method="POST"
                                >

                                    <input
                                        type="hidden"
                                        name="barcode"
                                        value="<?= htmlspecialchars(
                                            $produk['barcode']
                                        ); ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-success"
                                    >
                                        + Tambah
                                    </button>

                                </form>

                            </div>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <div class="empty">
                            Produk tidak ditemukan.
                        </div>

                    <?php endif; ?>

                </div>

            <?php endif; ?>



            <!-- ==================================
                 KERANJANG
            =================================== -->

            <h3>Keranjang Belanja</h3>

            <table>

                <thead>

                    <tr>

                        <th>Produk</th>

                        <th>Harga</th>

                        <th>Qty</th>

                        <th>Subtotal</th>

                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody>

                <?php

                $total = 0;

                if (count($_SESSION['keranjang']) > 0):

                    foreach (
                        $_SESSION['keranjang']
                        as $index => $item
                    ):

                        $subtotal =
                            $item['harga'] *
                            $item['qty'];

                        $total += $subtotal;

                ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars(
                                $item['nama_produk']
                            ); ?>
                        </td>


                        <td>
                            Rp
                            <?= number_format(
                                $item['harga'],
                                0,
                                ',',
                                '.'
                            ); ?>
                        </td>


                        <td>

                            <form
                                action="update_keranjang.php"
                                method="POST"
                                class="qty-form"
                            >

                                <input
                                    type="hidden"
                                    name="index"
                                    value="<?= $index; ?>"
                                >

                                <input
                                    type="number"
                                    name="qty"
                                    value="<?= $item['qty']; ?>"
                                    min="1"
                                    max="<?= $item['stok']; ?>"
                                    class="qty-input"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    OK
                                </button>

                            </form>

                        </td>


                        <td>
                            Rp
                            <?= number_format(
                                $subtotal,
                                0,
                                ',',
                                '.'
                            ); ?>
                        </td>


                        <td>

                            <a
                                href="hapus_keranjang.php?index=<?= $index; ?>"
                                class="btn btn-danger"
                            >
                                Hapus
                            </a>

                        </td>

                    </tr>

                <?php

                    endforeach;

                else:

                ?>

                    <tr>

                        <td
                            colspan="5"
                            class="empty"
                        >
                            Keranjang masih kosong.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>


        </div>



        <!-- ======================================
             BAGIAN PEMBAYARAN
        ======================================= -->

        <div>

            <div class="total-box">

                <div class="total-label">
                    TOTAL BELANJA
                </div>

                <div class="total-harga">

                    Rp
                    <?= number_format(
                        $total,
                        0,
                        ',',
                        '.'
                    ); ?>

                </div>

            </div>


            <div class="kasir-card">

                <h2>Pembayaran</h2>


                <form
                    action="proses_bayar.php"
                    method="POST"
                >

                    <label>
                        Uang Pembayaran
                    </label>

                    <input
                        type="number"
                        name="bayar"
                        class="bayar-input"
                        placeholder="Masukkan nominal..."
                        min="<?= $total; ?>"
                        required
                    >


                    <button
                        type="submit"
                        class="btn-bayar"
                    >
                        BAYAR
                    </button>

                </form>


                <form
                    action="kosongkan_keranjang.php"
                    method="POST"
                >

                    <button
                        type="submit"
                        class="btn-batal"
                    >
                        BATAL TRANSAKSI
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>


<script>

document
    .getElementById('barcode')
    .focus();

</script>


</body>
</html>